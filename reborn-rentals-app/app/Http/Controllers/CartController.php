<?php

namespace App\Http\Controllers;

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
        $quantity = $request->input('quantity');
        
        $cart = Session::get('cart', []);
        
        if ($quantity <= 0) {
            unset($cart[$id]);
        } else {
            $cart[$id] = $quantity;
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

    public function show()
    {
        $cart = Session::get('cart', []);
        
        return view('cart.show', compact('cart'));
    }
}

