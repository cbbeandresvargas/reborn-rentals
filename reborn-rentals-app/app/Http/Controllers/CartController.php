<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Cupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);

        $cart = Session::get('cart', []);
        
        if (isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }

        Session::put('cart', $cart);

        return response()->json([
            'success' => true,
            'cart_count' => count($cart),
            'message' => 'Producto agregado al carrito'
        ]);
    }

    public function update(Request $request, $id)
    {
        $change = $request->input('quantity', 0); // Puede ser +1 o -1
        $cart = Session::get('cart', []);
        
        if (isset($cart[$id])) {
            $newQuantity = $cart[$id] + $change;
            if ($newQuantity <= 0) {
                unset($cart[$id]);
            } else {
                $cart[$id] = $newQuantity;
            }
        }

        Session::put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Carrito actualizado'
        ]);
    }

    public function remove($id)
    {
        $cart = Session::get('cart', []);
        
        if (isset($cart[$id])) {
            unset($cart[$id]);
            Session::put('cart', $cart);
        }

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado del carrito'
        ]);
    }

    public function clear()
    {
        Session::forget('cart');

        return response()->json([
            'success' => true,
            'message' => 'Carrito limpiado'
        ]);
    }

    public function show(Request $request)
    {
        $cart = Session::get('cart', []);
        
        // Si es petición AJAX o JSON, devolver JSON
        if ($request->wantsJson() || $request->expectsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            $productIds = array_keys($cart);
            $products = Product::with('category')->whereIn('id', $productIds)->get();
            
            $total = 0;
            foreach ($products as $product) {
                if (isset($cart[$product->id])) {
                    $total += $product->price * $cart[$product->id];
                }
            }
            
            return response()->json([
                'cart' => $cart,
                'cart_count' => count($cart),
                'products' => $products->toArray(),
                'total' => $total
            ]);
        }
        
        // El carrito se muestra en el sidebar, redirigir a home
        return redirect()->route('home')->with('cart', $cart);
    }

    public function applyCoupon(Request $request)
    {
        $code = $request->input('code');
        $cartTotal = $request->input('cart_total', 0);

        if (!$code) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon code is required'
            ], 400);
        }

        // Buscar el cupón
        $coupon = Cupon::where('code', strtoupper(trim($code)))->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon is not valid'
            ], 404);
        }

        // Verificar si está activo
        if (!$coupon->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon is not valid'
            ], 400);
        }

        // Verificar fecha de inicio
        if ($coupon->starts_at && Carbon::now()->lt($coupon->starts_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon is not valid'
            ], 400);
        }

        // Verificar fecha de expiración
        if ($coupon->expires_at && Carbon::now()->gt($coupon->expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon is not valid'
            ], 400);
        }

        // Verificar orden mínima
        if ($coupon->min_order_total && $cartTotal < $coupon->min_order_total) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum order total of $' . number_format($coupon->min_order_total, 2) . ' required'
            ], 400);
        }

        // Calcular descuento
        $discount = 0;
        if ($coupon->discount_type === 'percentage') {
            $discount = ($cartTotal * $coupon->discount_value) / 100;
        } else {
            $discount = min($coupon->discount_value, $cartTotal); // No puede ser más que el total
        }

        $newTotal = max(0, $cartTotal - $discount);

        // Guardar el cupón en la sesión
        Session::put('applied_coupon', [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'discount_type' => $coupon->discount_type,
            'discount_value' => $coupon->discount_value,
            'discount_amount' => $discount
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully',
            'coupon' => [
                'code' => $coupon->code,
                'discount_type' => $coupon->discount_type,
                'discount_value' => $coupon->discount_value,
                'discount_amount' => round($discount, 2),
                'new_total' => round($newTotal, 2)
            ]
        ]);
    }

    public function removeCoupon()
    {
        Session::forget('applied_coupon');

        return response()->json([
            'success' => true,
            'message' => 'Coupon removed successfully'
        ]);
    }
}

