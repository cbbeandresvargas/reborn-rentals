<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\JobLocation;
use App\Models\Cupon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class CheckoutController extends Controller
{

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
        \Log::info('Checkout store method called', [
            'user_id' => Auth::id(),
            'is_guest' => !Auth::check(),
            'cart' => Session::get('cart', []),
            'request_data' => $request->all()
        ]);

        // Prevent duplicate submissions using session token
        $submissionToken = $request->input('_submission_token');
        $sessionToken = Session::get('checkout_submission_token');
        
        if ($submissionToken && $sessionToken && $submissionToken === $sessionToken) {
            \Log::warning('Duplicate checkout submission detected', [
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
            \Log::warning('Checkout attempted with empty cart');
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
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Checkout validation failed', [
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
            \Log::info('🔄 [CHECKOUT] Starting order creation process', [
                'is_ajax' => $request->ajax(),
                'wants_json' => $request->wantsJson(),
                'cart_items_count' => count($cart),
            ]);
            
            return DB::transaction(function () use ($validated, $cart, $request) {
                \Log::info('🔄 [CHECKOUT] Starting database transaction');
                
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
                    \Log::info('Using default admin user for guest checkout', ['user_id' => $userId]);
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
                
                \Log::info('JobLocation created', ['job_id' => $jobLocation->id]);

                // ====================================================================
                // MANDATORY MINIMUM BILLABLE RENTAL PERIOD: 30 DAYS
                // ====================================================================
                // Business Rule: All rentals must be billed for a minimum of 30 days,
                // regardless of the actual rental period selected by the user.
                // 
                // Why this rule exists:
                // - Ensures consistent minimum billing periods for all equipment rentals
                // - Covers operational costs (delivery, pickup, maintenance)
                // - Standardizes pricing structure across all rental requests
                // - Prevents billing disputes for short-term rentals
                //
                // Implementation: Even if user selects fewer days in the frontend,
                // the backend will enforce a minimum of 30 days for all billing calculations.
                // This ensures the total_amount and line_total always reflect at least 30 days.
                // ====================================================================
                
                // Parse rental days from request (user-selected values)
                $productDays = [];
                if ($request->filled('product_days')) {
                    try {
                        $productDays = json_decode($request->product_days, true) ?? [];
                        
                        // Enforce minimum 30 days for billing - override any value below 30
                        foreach ($productDays as $productId => $days) {
                            if ($days < 30) {
                                \Log::info('Enforcing minimum 30-day billing period', [
                                    'product_id' => $productId,
                                    'user_selected_days' => $days,
                                    'billed_days' => 30
                                ]);
                                $productDays[$productId] = 30;
                            }
                        }
                    } catch (\Exception $e) {
                        \Log::error('Error parsing product_days: ' . $e->getMessage());
                    }
                }
                
                // If no product_days provided, default to 30 days minimum for all products
                // This ensures every product is billed for at least 30 days
                
                // ====================================================================
                // SUBTOTAL ESTIMATE ONLY - NO TAX OR DISCOUNT CALCULATIONS
                // ====================================================================
                // Business Rule: This website only calculates a subtotal estimate.
                // All final totals, taxes, and discounts are calculated and applied in Odoo.
                //
                // Why this rule exists:
                // - Odoo handles all fiscal calculations (taxes, discounts, final totals)
                // - Ensures consistency between website estimates and final invoices
                // - Prevents discrepancies between website and Odoo billing
                // - Allows Odoo to apply complex tax rules and discount policies
                //
                // Implementation: We only calculate the base subtotal (price * quantity * days).
                // No taxes, no discount calculations, no final totals.
                // The total_amount stored is an estimate for reference only.
                // ====================================================================
                
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
                        \Log::info('Coupon code validated (discount will be applied in Odoo)', [
                            'coupon_code' => $cupon->code,
                            'coupon_id' => $cupon->id
                        ]);
                    }
                }

                // Calculate subtotal estimate only (no taxes, no discounts)
                // This is an estimate for reference - final totals are calculated in Odoo
                $subtotal = $this->calculateSubtotal($cart, $productDays);
                $totalAmount = $subtotal; // total_amount equals subtotal (estimate only)

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
                        \Log::warning('Error parsing foreman_details: ' . $e->getMessage());
                    }
                }
                
                if ($request->filled('billing_details')) {
                    try {
                        $billingData = json_decode($request->billing_details, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($billingData)) {
                            $billingDetailsJson = $request->billing_details;
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Error parsing billing_details: ' . $e->getMessage());
                    }
                }

                // ====================================================================
                // ORDER CREATION - NO PAYMENT DATA STORED
                // ====================================================================
                // This website only collects rental requests.
                // - status: 'pending_odoo' - order is pending processing in Odoo
                // - subtotal: base subtotal estimate (price * quantity * days, min 30 days)
                // - total_amount: equals subtotal (estimate only, no taxes/discounts)
                // - tax_total: null (calculated in Odoo)
                // - discount_total: null (calculated in Odoo)
                // - payment_method: null (not collected - handled in Odoo)
                // - payment_method_details_json: null (not collected - handled in Odoo)
                // 
                // Preserved data:
                // - job_id: JobLocation with delivery address and dates
                // - foreman_details_json: Foreman/receiving person information
                // - billing_details_json: Billing address and contact information
                // ====================================================================
                $order = Order::create([
                    'user_id' => $userId,
                    'job_id' => $jobLocation->id, // Preserved: Job location with delivery details
                    'cupon_id' => $cupon?->id, // Store coupon reference for Odoo, but discount not calculated here
                    'subtotal' => $subtotal, // Base subtotal estimate stored separately
                    'total_amount' => $totalAmount, // Equals subtotal (estimate only - final total calculated in Odoo)
                    'status' => 'pending_odoo', // Status indicates order is pending Odoo processing
                    'discount_total' => null, // Discounts calculated and applied in Odoo, not here
                    'tax_total' => null, // Taxes calculated in Odoo, not here
                    'payment_method' => null, // Not collected - handled in Odoo
                    'ordered_at' => now(),
                    'notes' => $validated['notes'] ?? null,
                    'foreman_details_json' => $foremanDetailsJson, // Preserved: Foreman details
                    'billing_details_json' => $billingDetailsJson, // Preserved: Billing details
                    'payment_method_details_json' => null, // Not collected - handled in Odoo
                    'odoo_sync_status' => 'pending', // Initial sync status: pending
                    'odoo_sale_order_id' => null, // Will be set after Odoo sync
                    'odoo_invoice_id' => null, // Will be set after Odoo sync
                ]);
                
                \Log::info('✅ [CHECKOUT] Order created in database', [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'total_amount' => $order->total_amount,
                    'subtotal' => $order->subtotal,
                    'status' => $order->status,
                    'odoo_sync_status' => $order->odoo_sync_status,
                ]);

                // Crear OrderItems
                $productIds = array_keys($cart);
                $products = Product::whereIn('id', $productIds)->get();

                foreach ($products as $product) {
                    $quantity = $cart[$product->id];
                    
                    // Get rental days for this product - enforce minimum 30 days for billing
                    // This is the final enforcement point: even if somehow a value < 30 got through,
                    // we ensure billing always uses at least 30 days
                    $rentalDays = $productDays[$product->id] ?? 30;
                    if ($rentalDays < 30) {
                        \Log::warning('Rental days below minimum detected, enforcing 30-day minimum', [
                            'product_id' => $product->id,
                            'received_days' => $rentalDays,
                            'billed_days' => 30
                        ]);
                        $rentalDays = 30;
                    }
                    
                    // Calculate line total: price * quantity * days
                    // Note: rentalDays is guaranteed to be >= 30 at this point
                    $lineTotal = $product->price * $quantity * $rentalDays;
                    
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'unit_price' => $product->price,
                        'line_total' => $lineTotal,
                    ]);
                    
                    \Log::debug('📦 [CHECKOUT] OrderItem created', [
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity' => $quantity,
                        'rental_days' => $rentalDays,
                        'unit_price' => $product->price,
                        'line_total' => $lineTotal,
                    ]);
                }
                
                \Log::info('✅ [CHECKOUT] All OrderItems created', [
                    'order_id' => $order->id,
                    'items_count' => count($products),
                ]);

                // No payment information is collected or stored - all payment processing is handled in Odoo

                // ====================================================================
                // ODOO INTEGRATION - Complete order processing flow
                // ====================================================================
                // After creating the Laravel order, sync it to Odoo:
                // 1. Create sale order in Odoo
                // 2. Add order lines (rental items with days)
                // 3. Confirm sale order (triggers tax calculation)
                // 4. Generate invoice
                // 5. Send invoice email with payment link
                //
                // Status tracking:
                // - 'pending': Initial state, sync not started
                // - 'synced': Successfully synced to Odoo
                // - 'failed': Sync failed, recoverable
                //
                // If any step fails, order sync_status is set to 'failed'
                // and order remains in 'pending_odoo' status for manual processing or retry.
                // ====================================================================
                
                try {
                    \Log::info('🔄 [ODOO] Starting integration for order', [
                        'order_id' => $order->id,
                        'sync_status' => 'pending',
                    ]);

                    $odoo = new \App\Services\Odoo\OdooClient();

                    // Step 1: Create and confirm sale order in Odoo
                    // This includes:
                    // - Finding/creating customer (partner)
                    // - Creating sale order with jobsite info
                    // - Adding rental line items (days as quantity, daily price)
                    // - Confirming order (triggers tax calculation)
                    \Log::info('🔄 [ODOO] Step 1/4: Creating and confirming sale order', [
                        'order_id' => $order->id,
                    ]);
                    
                    $saleOrderId = $odoo->createAndConfirmSaleOrder($order);

                    // Update order with sale order ID
                    $order->update([
                        'odoo_sale_order_id' => $saleOrderId,
                    ]);

                    \Log::info('✅ [ODOO] Step 1/4: Sale order created and confirmed', [
                        'order_id' => $order->id,
                        'odoo_sale_order_id' => $saleOrderId,
                    ]);

                    // Step 2: Generate invoice from confirmed sale order
                    // Invoice includes all taxes calculated by Odoo
                    \Log::info('🔄 [ODOO] Step 2/4: Generating invoice from sale order', [
                        'order_id' => $order->id,
                        'odoo_sale_order_id' => $saleOrderId,
                    ]);
                    
                    $invoiceId = $odoo->createInvoiceFromSaleOrder($saleOrderId);

                    // Update order with invoice ID
                    $order->update([
                        'odoo_invoice_id' => $invoiceId,
                    ]);

                    \Log::info('✅ [ODOO] Step 2/4: Invoice created successfully', [
                        'order_id' => $order->id,
                        'odoo_sale_order_id' => $saleOrderId,
                        'odoo_invoice_id' => $invoiceId,
                    ]);

                    // Step 3: Generate payment link (for logging purposes only)
                    // Note: Payment link is NOT exposed in Laravel UI
                    \Log::info('🔄 [ODOO] Step 3/4: Generating payment link (logging only)', [
                        'order_id' => $order->id,
                        'odoo_invoice_id' => $invoiceId,
                    ]);
                    
                    $paymentLink = $odoo->generatePaymentLink($invoiceId);

                    \Log::info('✅ [ODOO] Step 3/4: Payment link generated (logged, not exposed)', [
                        'order_id' => $order->id,
                        'odoo_invoice_id' => $invoiceId,
                        'payment_link_length' => strlen($paymentLink),
                        'note' => 'Payment link is for logging only, NOT exposed in Laravel UI',
                    ]);

                    // Step 4: Send invoice email to customer via Odoo
                    // Email includes payment link automatically (handled by Odoo templates)
                    \Log::info('🔄 [ODOO] Step 4/4: Sending invoice email via Odoo', [
                        'order_id' => $order->id,
                        'odoo_invoice_id' => $invoiceId,
                    ]);
                    
                    $odoo->sendInvoiceEmail($invoiceId);

                    \Log::info('✅ [ODOO] Step 4/4: Invoice email sent successfully', [
                        'order_id' => $order->id,
                        'odoo_invoice_id' => $invoiceId,
                        'note' => 'Email sent by Odoo with payment link included automatically',
                    ]);

                    // Order successfully synced to Odoo
                    $order->update([
                        'odoo_sync_status' => 'synced',
                    ]);

                    \Log::info('🎉 [ODOO] Order successfully synced to Odoo - COMPLETE', [
                        'order_id' => $order->id,
                        'odoo_sale_order_id' => $saleOrderId,
                        'odoo_invoice_id' => $invoiceId,
                        'sync_status' => 'synced',
                        'summary' => [
                            'sale_order_created' => true,
                            'sale_order_confirmed' => true,
                            'invoice_created' => true,
                            'payment_link_generated' => true,
                            'email_sent' => true,
                        ],
                    ]);

                } catch (\Exception $odooException) {
                    // Odoo integration failed - mark as failed and keep order in 'pending_odoo' status
                    // This allows manual processing or retry later
                    $order->update([
                        'odoo_sync_status' => 'failed',
                    ]);

                    \Log::error('❌ [ODOO] Integration FAILED for order', [
                        'order_id' => $order->id,
                        'sync_status' => 'failed',
                        'error_message' => $odooException->getMessage(),
                        'error_code' => $odooException->getCode(),
                        'error_file' => $odooException->getFile(),
                        'error_line' => $odooException->getLine(),
                        'trace' => $odooException->getTraceAsString(),
                        'recoverable' => true,
                        'note' => 'Order remains in pending_odoo status. Can be manually synced or retried.',
                        'odoo_sale_order_id' => $order->odoo_sale_order_id,
                        'odoo_invoice_id' => $order->odoo_invoice_id,
                    ]);

                    // Order status remains 'pending_odoo' and sync_status is 'failed'
                    // Admin can manually sync or retry later using the stored IDs if available
                }

                // Limpiar carrito
                Session::forget('cart');
                
                // Clear submission token after successful order
                Session::forget('checkout_submission_token');

                \Log::info('✅ Order completed successfully', [
                    'order_id' => $order->id,
                    'odoo_sync_status' => $order->odoo_sync_status,
                    'odoo_sale_order_id' => $order->odoo_sale_order_id,
                    'odoo_invoice_id' => $order->odoo_invoice_id,
                ]);

                // Always return JSON for AJAX requests
                if ($request->ajax() || $request->wantsJson()) {
                    \Log::info('📤 Returning JSON response for AJAX request', [
                        'order_id' => $order->id,
                    ]);
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Order placed successfully!',
                        'order_id' => $order->id,
                        'redirect_url' => route('order', $order->id),
                        'odoo_sync_status' => $order->odoo_sync_status,
                        'odoo_sale_order_id' => $order->odoo_sale_order_id,
                        'odoo_invoice_id' => $order->odoo_invoice_id,
                    ], 200, ['Content-Type' => 'application/json']);
                }

                return redirect()->route('order', $order->id)
                    ->with('success', 'Order placed successfully!');
            });
        } catch (\Exception $e) {
            \Log::error('Error creating order', [
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
     * Calculate subtotal estimate for cart items
     * 
     * This method calculates ONLY a base subtotal estimate (price * quantity * days).
     * No taxes, no discounts, no final totals are calculated here.
     * 
     * Business Rules:
     * 1. Enforces mandatory minimum 30-day billing period for all products
     * 2. Even if user selects fewer days, billing will use at least 30 days
     * 3. This is an ESTIMATE only - final totals, taxes, and discounts are calculated in Odoo
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
            // Business rule: All rentals must be billed for minimum 30 days
            $rentalDays = $productDays[$product->id] ?? 30;
            
            // Final safety check: ensure minimum 30 days for billing calculations
            if ($rentalDays < 30) {
                $rentalDays = 30;
            }
            
            // Calculate base subtotal: price * quantity * days
            // Note: This is an estimate only. No taxes or discounts applied.
            // rentalDays is guaranteed to be >= 30 at this point
            $subtotal += $product->price * $quantity * $rentalDays;
        }

        return $subtotal;
    }

    // ====================================================================
    // NO TAX OR DISCOUNT CALCULATIONS
    // ====================================================================
    // This website only calculates a subtotal estimate.
    // All final totals, taxes, discounts, and payments are handled in Odoo.
    // This ensures consistency between website estimates and final invoices.
    // ====================================================================
}

