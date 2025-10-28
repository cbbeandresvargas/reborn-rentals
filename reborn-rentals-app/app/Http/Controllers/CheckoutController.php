<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\JobLocation;
use App\Models\Cupon;
use App\Models\PaymentInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $cart = Session::get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.show')
                ->with('error', 'Tu carrito está vacío');
        }

        // Obtener productos del carrito
        $productIds = array_keys($cart);
        $products = Product::whereIn('id', $productIds)->get();
        
        $total = 0;
        foreach ($products as $product) {
            $total += $product->price * $cart[$product->id];
        }

        return view('checkout.index', compact('products', 'cart', 'total'));
    }

    public function store(Request $request)
    {
        $cart = Session::get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.show')
                ->with('error', 'Tu carrito está vacío');
        }

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'jobsite_address' => 'required|string|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'cupon_code' => 'nullable|string',
            'payment_method' => 'required|integer',
        ]);

        return DB::transaction(function () use ($validated, $cart, $request) {
            // Crear JobLocation
            $jobLocation = JobLocation::create([
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'notes' => $validated['jobsite_address'],
                'date' => $validated['start_date'],
                'status' => true,
            ]);

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
                    $subtotal = $this->calculateSubtotal($cart);
                    
                    if ($cupon->discount_type === 'percent') {
                        $discountTotal = $subtotal * ($cupon->discount_value / 100);
                    } else {
                        $discountTotal = $cupon->discount_value;
                    }
                }
            }

            // Calcular totales
            $subtotal = $this->calculateSubtotal($cart);
            $taxTotal = ($subtotal - $discountTotal) * 0.02; // 2% tax
            $totalAmount = $subtotal - $discountTotal + $taxTotal;

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
            ]);

            // Crear OrderItems
            $productIds = array_keys($cart);
            $products = Product::whereIn('id', $productIds)->get();

            foreach ($products as $product) {
                $quantity = $cart[$product->id];
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'line_total' => $product->price * $quantity,
                ]);
            }

            // Guardar información de pago si existe
            if ($request->has('card_holder_name')) {
                PaymentInfo::create([
                    'user_id' => Auth::id(),
                    'card_holder_name' => $request->card_holder_name,
                    'card_number' => substr($request->card_number, -4), // Solo últimos 4
                    'card_expiration' => $request->card_expiration,
                    'cvv' => $request->cvv,
                ]);
            }

            // Limpiar carrito
            Session::forget('cart');

            return redirect()->route('orders.show', $order->id)
                ->with('success', '¡Pedido realizado exitosamente!');
        });
    }

    private function calculateSubtotal($cart)
    {
        $productIds = array_keys($cart);
        $products = Product::whereIn('id', $productIds)->get();
        
        $subtotal = 0;
        foreach ($products as $product) {
            $subtotal += $product->price * $cart[$product->id];
        }

        return $subtotal;
    }
}

