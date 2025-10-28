<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

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
            $products = Product::whereIn('id', $productIds)->get();
            
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
}

