<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\JobLocation;
use App\Models\Cupon;
use App\Models\PaymentInfo;
use App\Mail\VerificationCodeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
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
            'payment_verified' => Session::get('payment_verified'),
            'cart' => Session::get('cart', []),
            'request_data' => $request->all()
        ]);

        $cart = Session::get('cart', []);
        
        if (empty($cart)) {
            \Log::warning('Checkout attempted with empty cart');
            return redirect()->route('home')
                ->with('error', 'Your cart is empty');
        }

        // Check if payment is verified
        if (!Session::get('payment_verified')) {
            \Log::warning('Checkout attempted without payment verification', [
                'payment_verified' => Session::get('payment_verified')
            ]);
            return redirect()->route('checkout')
                ->with('error', 'Please verify your payment before completing the order.');
        }

        try {
            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'jobsite_address' => 'required|string|max:500',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'notes' => 'nullable|string',
                'cupon_code' => 'nullable|string',
                'payment_method' => 'required|integer',
                'product_days' => 'nullable|string', // JSON string with product_id => days mapping
                'foreman_details' => 'nullable|string', // JSON string with foreman information
                'billing_details' => 'nullable|string', // JSON string with billing information
                'payment_method_details' => 'nullable|string', // JSON string with payment method details
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Checkout validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            throw $e;
        }

        try {
            return DB::transaction(function () use ($validated, $cart, $request) {
                \Log::info('Starting order creation transaction');
                
                // Crear JobLocation
                $jobLocation = JobLocation::create([
                    'latitude' => $validated['latitude'] ?? null,
                    'longitude' => $validated['longitude'] ?? null,
                    'notes' => $validated['jobsite_address'],
                    'date' => $validated['start_date'],
                    'status' => true,
                ]);
                
                \Log::info('JobLocation created', ['job_id' => $jobLocation->id]);

                // Get rental days for each product
                $productDays = [];
                if ($request->filled('product_days')) {
                    try {
                        $productDays = json_decode($request->product_days, true) ?? [];
                    } catch (\Exception $e) {
                        \Log::error('Error parsing product_days: ' . $e->getMessage());
                    }
                }
                
                // Verificar cupón
                $cupon = null;
                $discountTotal = 0;
                
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

                    if ($cupon) {
                        $subtotal = $this->calculateSubtotal($cart, $productDays);
                        
                        if ($cupon->discount_type === 'percentage') {
                            $discountTotal = $subtotal * ($cupon->discount_value / 100);
                        } else {
                            $discountTotal = $cupon->discount_value;
                        }
                    }
                }

                // Calcular totales (considering rental days)
                $subtotal = $this->calculateSubtotal($cart, $productDays);
                $taxTotal = ($subtotal - $discountTotal) * 0.02; // 2% tax
                $totalAmount = $subtotal - $discountTotal + $taxTotal;

                // Parse and validate JSON data
                $foremanDetailsJson = null;
                $billingDetailsJson = null;
                $paymentMethodDetailsJson = null;
                
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
                
                if ($request->filled('payment_method_details')) {
                    try {
                        $paymentData = json_decode($request->payment_method_details, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($paymentData)) {
                            $paymentMethodDetailsJson = $request->payment_method_details;
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Error parsing payment_method_details: ' . $e->getMessage());
                    }
                }

                // Crear Orden
                $order = Order::create([
                    'user_id' => Auth::id(),
                    'job_id' => $jobLocation->id,
                    'cupon_id' => $cupon?->id,
                    'total_amount' => $totalAmount,
                    'status' => true,
                    'discount_total' => $discountTotal,
                    'tax_total' => $taxTotal,
                    'payment_method' => $validated['payment_method'],
                    'ordered_at' => now(),
                    'notes' => $validated['notes'] ?? null,
                    'foreman_details_json' => $foremanDetailsJson,
                    'billing_details_json' => $billingDetailsJson,
                    'payment_method_details_json' => $paymentMethodDetailsJson,
                ]);
                
                \Log::info('Order created successfully', [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'total_amount' => $order->total_amount
                ]);

                // Crear OrderItems
                $productIds = array_keys($cart);
                $products = Product::whereIn('id', $productIds)->get();

                foreach ($products as $product) {
                    $quantity = $cart[$product->id];
                    
                    // Get rental days for this product (default to 30 if not specified)
                    $rentalDays = $productDays[$product->id] ?? 30;
                    
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

                // Guardar información de pago si existe
                if ($request->has('card_holder_name')) {
                    PaymentInfo::create([
                        'user_id' => Auth::id(),
                        'card_holder_name' => $request->card_holder_name,
                        'card_number' => substr($request->card_number, -4), // Solo últimos 4 -- Marcar con Hashing
                        'card_expiration' => $request->card_expiration,
                        'cvv' => $request->cvv,
                    ]);
                }

                // Limpiar carrito y verificación
                Session::forget('cart');
                Session::forget('payment_verified');
                Session::forget('payment_verified_at');

                \Log::info('Order completed successfully', ['order_id' => $order->id]);

                // If it's an AJAX request, return JSON response
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Order placed successfully!',
                        'order_id' => $order->id,
                        'redirect_url' => route('order', $order->id)
                    ]);
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
            
            return redirect()->route('checkout')
                ->with('error', 'An error occurred while processing your order. Please try again.');
        }
    }

    private function calculateSubtotal($cart, $productDays = [])
    {
        $productIds = array_keys($cart);
        $products = Product::whereIn('id', $productIds)->get();
        
        $subtotal = 0;
        foreach ($products as $product) {
            $quantity = $cart[$product->id];
            
            // Get rental days for this product (default to 30 if not specified)
            $rentalDays = $productDays[$product->id] ?? 30;
            
            // Calculate: price * quantity * days
            $subtotal += $product->price * $quantity * $rentalDays;
        }

        return $subtotal;
    }

    /**
     * Send verification code to user's email
     */
    public function sendVerificationCode(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        // Generate 5-digit verification code
        $code = str_pad((string) rand(0, 99999), 5, '0', STR_PAD_LEFT);
        
        // Store code in cache with 10 minute expiration
        $cacheKey = 'verification_code_' . $user->id;
        Cache::put($cacheKey, $code, now()->addMinutes(10));
        
        try {
            // Send email
            Mail::to($user->email)->send(new VerificationCodeMail($code, $user->name));
            
            return response()->json([
                'success' => true,
                'message' => 'Verification code sent to your email'
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send verification code: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification code. Please try again.'
            ], 500);
        }
    }

    /**
     * Verify the code entered by user
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:5'
        ]);

        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $cacheKey = 'verification_code_' . $user->id;
        $storedCode = Cache::get($cacheKey);
        
        if (!$storedCode) {
            return response()->json([
                'success' => false,
                'message' => 'Verification code has expired. Please request a new one.'
            ], 400);
        }

        if ($storedCode !== $request->code) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification code. Please try again.'
            ], 400);
        }

        // Code is valid, mark as verified in session
        Session::put('payment_verified', true);
        Session::put('payment_verified_at', now()->toDateTimeString());
        
        // Delete the code from cache
        Cache::forget($cacheKey);
        
        return response()->json([
            'success' => true,
            'message' => 'Verification successful'
        ]);
    }
}

