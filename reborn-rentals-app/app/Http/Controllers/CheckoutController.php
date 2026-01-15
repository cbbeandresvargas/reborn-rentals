<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\JobLocation;
use App\Models\Cupon;
use App\Models\User;
use App\Services\Delivery\DeliveryCalculator;
use App\Services\StockAvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckoutController extends Controller
{
    protected $deliveryCalculator;
    protected $stockService;

    public function __construct(DeliveryCalculator $deliveryCalculator, StockAvailabilityService $stockService)
    {
        $this->deliveryCalculator = $deliveryCalculator;
        $this->stockService = $stockService;
    }

    public function index()
    {
        $cart = Session::get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('home')
                ->with('error', 'Your cart is empty');
        }

        // Obtener productos del carrito
        $productIds = array_keys($cart);
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        
        // Calcular total
        $total = 0;
        foreach ($cart as $productId => $quantity) {
            if ($products->has($productId)) {
                $product = $products->get($productId);
                $total += $product->price * $quantity;
            }
        }

        return view('checkout', compact('products', 'cart', 'total'));
    }

    public function store(Request $request)
    {
        Log::info('Checkout store method called', [
            'user_id' => Auth::id(),
            'is_guest' => !Auth::check(),
            'cart' => Session::get('cart', []),
            'request_data' => $request->all()
        ]);

        // Prevent duplicate submissions using session token
        $submissionToken = $request->input('_submission_token');
        $sessionToken = Session::get('checkout_submission_token');
        
        if ($submissionToken && $sessionToken && $submissionToken === $sessionToken) {
            Log::warning('Duplicate checkout submission detected', [
                'token' => $submissionToken,
                'user_id' => Auth::id()
            ]);
            
            // Clear the token to prevent reuse
            Session::forget('checkout_submission_token');
            
            // Return error for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This order has already been processed. Please check your orders.'
                ], 400);
            }
            
            return redirect()->route('checkout')
                ->with('error', 'This order has already been processed. Please check your orders.');
        }
        
        // Generate new token for this submission
        $newToken = uniqid('checkout_', true);
        Session::put('checkout_submission_token', $newToken);

        $cart = Session::get('cart', []);
        
        if (empty($cart)) {
            Log::warning('Checkout attempted with empty cart');
            return redirect()->route('home')
                ->with('error', 'Your cart is empty');
        }

        // Payment processing removed - orders will be invoiced via Odoo
        // This website only collects rental requests

        try {
            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'jobsite_address' => 'required|string|max:500',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'notes' => 'nullable|string',
                'cupon_code' => 'nullable|string',
                'product_days' => 'nullable|string', // JSON string with product_id => days mapping
                'foreman_details' => 'nullable|string', // JSON string with foreman information
                'billing_details' => 'nullable|string', // JSON string with billing information
                'is_self_pickup' => 'nullable|boolean',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Checkout validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            // If it's an AJAX request, return JSON response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed. Please check your input.',
                    'errors' => $e->errors(),
                ], 422);
            }
            
            throw $e;
        }

        try {
            Log::info('🔄 [CHECKOUT] Starting order creation process', [
                'is_ajax' => $request->ajax(),
                'wants_json' => $request->wantsJson(),
                'cart_items_count' => count($cart),
            ]);
            
            return DB::transaction(function () use ($validated, $cart, $request) {
                Log::info('🔄 [CHECKOUT] Starting database transaction');
                
                // Get or create default admin user for guest checkout
                $userId = Auth::id();
                if (!$userId) {
                    // Find first admin user or create a default one
                    $adminUser = User::where('role', 'admin')->first();
                    if (!$adminUser) {
                        // Create a default admin user if none exists
                        $adminUser = User::create([
                            'name' => 'Guest',
                            'email' => 'guest@rebornrentals.com',
                            'password' => bcrypt('default'),
                            'role' => 'admin',
                        ]);
                    }
                    $userId = $adminUser->id;
                    Log::info('Using default admin user for guest checkout', ['user_id' => $userId]);
                }
                
                // Crear JobLocation
                $jobLocation = JobLocation::create([
                    'latitude' => $validated['latitude'] ?? null,
                    'longitude' => $validated['longitude'] ?? null,
                    'notes' => $validated['jobsite_address'],
                    'date' => $validated['start_date'],
                    'end_date' => $validated['end_date'],
                    'status' => true,
                ]);
                
                Log::info('JobLocation created', ['job_id' => $jobLocation->id]);

                // ====================================================================
                // MANDATORY MINIMUM BILLABLE RENTAL PERIOD: 30 DAYS
                // ====================================================================
                // Business Rule: All rentals must be billed for a minimum of 30 days,
                // regardless of the actual rental period selected by the user.
                
                // Parse rental days from request (user-selected values)
                $productDays = [];
                if ($request->filled('product_days')) {
                    try {
                        $productDays = json_decode($request->product_days, true) ?? [];
                        
                        // Enforce minimum 30 days for billing - override any value below 30
                        foreach ($productDays as $productId => $days) {
                            if ($days < 30) {
                                Log::info('Enforcing minimum 30-day billing period', [
                                    'product_id' => $productId,
                                    'user_selected_days' => $days,
                                    'billed_days' => 30
                                ]);
                                $productDays[$productId] = 30;
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error('Error parsing product_days: ' . $e->getMessage());
                    }
                }
                
                // If no product_days provided, default to 30 days minimum for all products
                // This ensures every product is billed for at least 30 days
                
                // ====================================================================
                // SUBTOTAL ESTIMATE ONLY - NO TAX OR DISCOUNT CALCULATIONS
                // ====================================================================
                // Business Rule: This website only calculates a subtotal estimate.
                // All final totals, taxes, and discounts are calculated and applied in Odoo.
                
                // Validate coupon code if provided (for reference only, not for calculation)
                // Note: Coupon validation is done but discount is NOT calculated here
                // All discount calculations and applications are handled in Odoo
                $cupon = null;
                if ($request->filled('cupon_code')) {
                    $cupon = Cupon::where('code', $request->cupon_code)
                        ->where('is_active', true)
                        ->where(function($q) {
                            $q->whereNull('starts_at')
                                ->orWhere('starts_at', '<=', now());
                        })
                        ->where(function($q) {
                            $q->whereNull('expires_at')
                                ->orWhere('expires_at', '>=', now());
                        })
                        ->first();
                    
                    // Log coupon found for reference, but do NOT calculate discount
                    if ($cupon) {
                        Log::info('Coupon code validated (discount will be applied in Odoo)', [
                            'coupon_code' => $cupon->code,
                            'coupon_id' => $cupon->id
                        ]);
                    }
                }

                // ====================================================================
                // STOCK AVAILABILITY VALIDATION
                // ====================================================================
                // Validate stock availability for all products in cart before creating order
                $startDate = $validated['start_date'];
                $endDate = $validated['end_date'];
                $unavailableProducts = [];
                
                foreach ($cart as $productId => $quantity) {
                    $stockCheck = $this->stockService->checkAvailability(
                        $productId,
                        $startDate,
                        $endDate,
                        $quantity
                    );
                    
                    if (!$stockCheck['allowed']) {
                        $product = Product::find($productId);
                        $unavailableProducts[] = [
                            'id' => $productId,
                            'name' => $product ? $product->name : "Product #{$productId}",
                            'requested' => $quantity,
                            'available' => $stockCheck['available_stock'],
                            'message' => $stockCheck['message']
                        ];
                    }
                }
                
                if (!empty($unavailableProducts)) {
                    Log::warning('Checkout blocked due to insufficient stock', [
                        'unavailable_products' => $unavailableProducts,
                        'start_date' => $startDate,
                        'end_date' => $endDate
                    ]);
                    
                    $errorMessage = 'Some products are not available for the selected dates.';
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => $errorMessage,
                            'unavailable_products' => $unavailableProducts
                        ], 422);
                    }
                    
                    return redirect()->route('checkout')
                        ->with('error', $errorMessage)
                        ->with('unavailable_products', $unavailableProducts);
                }
                
                Log::info('✅ Stock validation passed for all products', [
                    'products_count' => count($cart),
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]);
                
                // Calculate subtotal estimate only (no taxes, no discounts)
                // This is an estimate for reference - final totals are calculated in Odoo
                $subtotal = $this->calculateSubtotal($cart, $productDays);
                
                // Calculate Delivery Fees
                $selfPickup = $request->boolean('is_self_pickup', false);
                $lat = $validated['latitude'] ?? null;
                $lon = $validated['longitude'] ?? null;
                
                // Use DeliveryCalculator service
                $fees = $this->deliveryCalculator->calculate($lat, $lon, $selfPickup);
                $deliveryFee = $fees['delivery_fee'];
                $pickupFee = $fees['pickup_fee'];
                $totalFees = $fees['total_fees'];

                // Add fees to total_amount
                // Note: "Todos estos cargos deben ser prepagados" -> Added to total
                $totalAmount = $subtotal + $totalFees;

                // Parse and validate JSON data
                $foremanDetailsJson = null;
                $billingDetailsJson = null;
                
                if ($request->filled('foreman_details')) {
                    try {
                        $foremanData = json_decode($request->foreman_details, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($foremanData)) {
                            $foremanDetailsJson = $request->foreman_details;
                        }
                    } catch (\Exception $e) {
                        Log::warning('Error parsing foreman_details: ' . $e->getMessage());
                    }
                }
                
                if ($request->filled('billing_details')) {
                    try {
                        $billingData = json_decode($request->billing_details, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($billingData)) {
                            $billingDetailsJson = $request->billing_details;
                        }
                    } catch (\Exception $e) {
                        Log::warning('Error parsing billing_details: ' . $e->getMessage());
                    }
                }

                // ====================================================================
                // ORDER CREATION
                // ====================================================================
                
                // NOTE: Using a notes field or handling fee storage is not explicit in the existing schema
                // so we will append the fee breakdown to the 'notes' field for reference in the admin panel/Odoo.
                $notes = $validated['notes'] ?? '';
                $feeNote = "\n\n[Delivery Fees]: Delivery: \${$deliveryFee}, Pickup: \${$pickupFee}. Method: {$fees['calculation_method']}. " . 
                           ($fees['distance_miles'] > 0 ? "Distance: {$fees['distance_miles']} miles." : "");
                $finalNotes = trim($notes . $feeNote);

                $order = Order::create([
                    'user_id' => $userId,
                    'job_id' => $jobLocation->id, // Preserved: Job location with delivery details
                    'cupon_id' => $cupon?->id, // Store coupon reference for Odoo, but discount not calculated here
                    'subtotal' => $subtotal, 
                    'total_amount' => $totalAmount, // Includes delivery fees
                    'status' => 'pending_odoo', // Status indicates order is pending Odoo processing
                    'discount_total' => null, // Discounts calculated and applied in Odoo, not here
                    'tax_total' => null, // Taxes calculated in Odoo, not here
                    'payment_method' => null, // Not collected - handled in Odoo
                    'ordered_at' => now(),
                    'notes' => $finalNotes,
                    'foreman_details_json' => $foremanDetailsJson, // Preserved: Foreman details
                    'billing_details_json' => $billingDetailsJson, // Preserved: Billing details
                    'payment_method_details_json' => null, // Not collected - handled in Odoo
                    'odoo_sync_status' => 'pending', // Initial sync status: pending
                    'odoo_sale_order_id' => null, // Will be set after Odoo sync
                    'odoo_invoice_id' => null, // Will be set after Odoo sync
                ]);
                
                Log::info('✅ [CHECKOUT] Order created in database', [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'total_amount' => $order->total_amount,
                    'subtotal' => $order->subtotal,
                    'delivery_fees' => $totalFees,
                    'status' => $order->status,
                ]);

                // Crear OrderItems
                $productIds = array_keys($cart);
                $products = Product::whereIn('id', $productIds)->get();

                foreach ($products as $product) {
                    $quantity = $cart[$product->id];
                    
                    // Get rental days for this product - enforce minimum 30 days for billing
                    $rentalDays = $productDays[$product->id] ?? 30;
                    if ($rentalDays < 30) {
                        $rentalDays = 30;
                    }
                    
                    // Calculate line total: price * quantity * days
                    $lineTotal = $product->price * $quantity * $rentalDays;
                    
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'unit_price' => $product->price,
                        'line_total' => $lineTotal,
                    ]);
                }
                
                Log::info('✅ [CHECKOUT] All OrderItems created');

                // ====================================================================
                // ODOO INTEGRATION - Complete order processing flow
                // ====================================================================
                
                try {
                    Log::info('🔄 [ODOO] Starting integration for order', [
                        'order_id' => $order->id,
                        'sync_status' => 'pending',
                    ]);

                    $odoo = new \App\Services\Odoo\OdooClient();

                    // Step 1: Create and confirm sale order in Odoo
                    Log::info('🔄 [ODOO] Step 1/4: Creating and confirming sale order');
                    
                    $saleOrderId = $odoo->createAndConfirmSaleOrder($order);

                    // Update order with sale order ID
                    $order->update([
                        'odoo_sale_order_id' => $saleOrderId,
                    ]);

                    // Step 2: Generate invoice from confirmed sale order
                    $invoiceId = $odoo->createInvoiceFromSaleOrder($saleOrderId);

                    // Update order with invoice ID
                    $order->update([
                        'odoo_invoice_id' => $invoiceId,
                    ]);

                    // Step 3: Generate payment link (for logging purposes only)
                    $paymentLink = $odoo->generatePaymentLink($invoiceId);

                    // Step 4: Send invoice email to customer via Odoo
                    $odoo->sendInvoiceEmail($invoiceId);

                    // Order successfully synced to Odoo
                    $order->update([
                        'odoo_sync_status' => 'synced',
                    ]);

                } catch (\Exception $odooException) {
                    // Odoo integration failed
                    $order->update([
                        'odoo_sync_status' => 'failed',
                    ]);

                    Log::error('❌ [ODOO] Integration FAILED for order', [
                        'order_id' => $order->id,
                        'error' => $odooException->getMessage(),
                    ]);
                }

                // Limpiar carrito
                Session::forget('cart');
                Session::forget('checkout_submission_token');

                Log::info('✅ Order completed successfully');

                // Always return JSON for AJAX requests
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Order placed successfully!',
                        'order_id' => $order->id,
                        'redirect_url' => route('order', $order->id),
                        'odoo_sync_status' => $order->odoo_sync_status,
                        'delivery_fee_breakdown' => $fees, // Return the calculated fees breakdown
                    ], 200, ['Content-Type' => 'application/json']);
                }

                return redirect()->route('order', $order->id)
                    ->with('success', 'Order placed successfully!');
            });
        } catch (\Exception $e) {
            Log::error('Error creating order', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            // If it's an AJAX request, return JSON response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while processing your order. Please try again.',
                    'error' => $e->getMessage(),
                ], 500);
            }
            
            return redirect()->route('checkout')
                ->with('error', 'An error occurred while processing your order. Please try again.');
        }
    }

    /**
     * Endpoint to calculate delivery fees via AJAX.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calculateFees(Request $request)
    {
        $request->validate([
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_self_pickup' => 'nullable|boolean',
        ]);

        $lat = $request->input('latitude');
        $lon = $request->input('longitude');
        $selfPickup = $request->boolean('is_self_pickup', false);

        try {
            $fees = $this->deliveryCalculator->calculate($lat, $lon, $selfPickup);
            
            return response()->json([
                'success' => true,
                'fees' => $fees,
            ]);
        } catch (\Exception $e) {
            Log::error('Fee calculation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Could not calculate fees.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Calculate subtotal estimate for cart items
     * 
     * @param array $cart Cart items (product_id => quantity)
     * @param array $productDays User-selected rental days per product (product_id => days)
     * @return float Subtotal estimate (no taxes, no discounts)
     */
    private function calculateSubtotal($cart, $productDays = [])
    {
        $productIds = array_keys($cart);
        $products = Product::whereIn('id', $productIds)->get();
        
        $subtotal = 0;
        foreach ($products as $product) {
            $quantity = $cart[$product->id];
            
            // Get rental days for this product - enforce minimum 30 days for billing
            $rentalDays = $productDays[$product->id] ?? 30;
            
            // Final safety check: ensure minimum 30 days for billing calculations
            if ($rentalDays < 30) {
                $rentalDays = 30;
            }
            
            // Calculate base subtotal: price * quantity * days
            $subtotal += $product->price * $quantity * $rentalDays;
        }

        return $subtotal;
    }
}
