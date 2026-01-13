@extends('layouts.admin')

@section('title', 'Dashboard - Admin Panel')

@section('content')
<div class="ml-0 md:ml-72 bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-gradient-to-r from-white to-gray-50 shadow-lg border-b border-gray-200 sticky top-0 z-40 backdrop-blur-sm">
        <div class="px-4 sm:px-6 lg:px-8 py-4 sm:py-5">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">Dashboard</h1>
                    <p class="text-sm text-gray-500 mt-1">Welcome back, {{ Auth::user()->name }}</p>
                </div>
                <div class="hidden sm:flex items-center space-x-2">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-xs text-gray-600">System Online</span>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Content -->
    <main class="p-4 sm:p-6 lg:p-8">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
            <!-- Total Orders -->
            <div class="group relative bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                <div class="relative p-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-white/80 text-xs sm:text-sm font-medium mb-1">Total Orders</p>
                        <p class="text-3xl sm:text-4xl font-bold text-white">{{ $stats['total_orders'] }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Subtotal Estimate -->
            <div class="group relative bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                <div class="relative p-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-white/80 text-xs sm:text-sm font-medium mb-1">Subtotal Estimate</p>
                        <p class="text-2xl sm:text-3xl font-bold text-white">${{ number_format($stats['total_revenue'], 2) }}</p>
                        <p class="text-white/70 text-xs mt-1">Final totals in Odoo</p>
                    </div>
                </div>
            </div>
            
            <!-- Total Products -->
            <div class="group relative bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                <div class="relative p-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-white/80 text-xs sm:text-sm font-medium mb-1">Total Products</p>
                        <p class="text-3xl sm:text-4xl font-bold text-white">{{ $stats['total_products'] }}</p>
                        <p class="text-xs sm:text-sm text-white/90 mt-1 flex items-center">
                            <span class="w-2 h-2 bg-green-300 rounded-full mr-2"></span>
                            {{ $stats['active_products'] }} active
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Total Users -->
            <div class="group relative bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                <div class="relative p-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-white/80 text-xs sm:text-sm font-medium mb-1">Total Users</p>
                        <p class="text-3xl sm:text-4xl font-bold text-white">{{ $stats['total_users'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-white px-4 sm:px-6 lg:px-8 py-4 sm:py-5 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Recent Orders</h2>
                        <p class="text-sm text-gray-500 mt-1">Latest rental requests overview</p>
                    </div>
                    <a href="{{ route('admin.orders.index') }}" class="hidden sm:flex items-center px-4 py-2 bg-gradient-to-r from-[#CE9704] to-[#B8860B] text-white rounded-lg hover:shadow-lg transition-all duration-200 text-sm font-medium">
                        View All
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Order ID</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">User</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Amount</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden md:table-cell">Date</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden md:table-cell">End Date</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($recentOrders as $order)
                        @php
                            $billingDetails = $order->billing_details_json ? json_decode($order->billing_details_json, true) : null;
                            $customerName = 'N/A';
                            $customerInitial = 'N';
                            
                            if ($billingDetails && isset($billingDetails['firstName'])) {
                                $firstName = $billingDetails['firstName'] ?? '';
                                $lastName = $billingDetails['lastName'] ?? '';
                                $customerName = trim($firstName . ' ' . $lastName) ?: 'N/A';
                                $customerInitial = strtoupper(substr($firstName ?: 'N', 0, 1));
                            } else {
                                // Fallback to user if billing details not available
                                $customerName = $order->user->name ?? 'N/A';
                                $customerInitial = strtoupper(substr($customerName, 0, 1));
                            }
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="text-sm font-bold text-gray-900">#{{ $order->id }}</span>
                                </div>
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gradient-to-br from-[#CE9704] to-[#B8860B] rounded-full flex items-center justify-center text-white text-xs font-bold mr-2">
                                        {{ $customerInitial }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $customerName }}</span>
                                </div>
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold text-gray-900">${{ number_format($order->total_amount, 2) }}</span>
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                <span class="text-sm text-gray-600">{{ $order->ordered_at ? $order->ordered_at->format('M d, Y') : ($order->created_at ? $order->created_at->format('M d, Y') : 'N/A') }}</span>
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                <span class="text-sm text-gray-600">{{ $order->job && $order->job->end_date ? $order->job->end_date->format('M d, Y') : 'N/A' }}</span>
                            </td>
                            
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClass = match($order->status) {
                                        'completed' => 'bg-green-100 text-green-800',
                                        'pending_odoo' => 'bg-yellow-100 text-yellow-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                    $statusDot = match($order->status) {
                                        'completed' => 'bg-green-500',
                                        'pending_odoo' => 'bg-yellow-500',
                                        default => 'bg-gray-500'
                                    };
                                    $statusText = match($order->status) {
                                        'completed' => 'Completed',
                                        'pending_odoo' => 'Pending Odoo',
                                        default => ucfirst($order->status ?? 'Pending')
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                    <span class="w-1.5 h-1.5 mr-1.5 rounded-full {{ $statusDot }}"></span>
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-[#CE9704] to-[#B8860B] text-white rounded-lg hover:shadow-md transition-all duration-200 text-xs font-medium">
                                    View
                                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 sm:px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <p class="text-gray-500 font-medium">No orders found</p>
                                    <p class="text-sm text-gray-400 mt-1">Orders will appear here once they are created</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
@endsection

