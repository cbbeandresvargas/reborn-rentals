<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{

    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['items.product', 'cupon', 'job'])
            ->latest()
            ->paginate(10);

        return view('orders', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::where('user_id', Auth::id())
            ->with(['items.product', 'cupon', 'job'])
            ->findOrFail($id);

        return view('order', compact('order'));
    }
}

