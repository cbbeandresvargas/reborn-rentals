<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'total_orders' => Order::count(),
            'total_products' => Product::count(),
            'total_users' => User::count(),
            'total_revenue' => Order::sum('total_amount'),
            'pending_orders' => Order::where('status', true)->count(),
            'active_products' => Product::where('active', true)->count(),
        ];

        // Últimas órdenes - ordenar por ordered_at (o created_at si ordered_at es null)
        $recentOrders = Order::with(['user', 'items.product', 'job'])
            ->orderByRaw('COALESCE(ordered_at, created_at) DESC')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders'));
    }
}

