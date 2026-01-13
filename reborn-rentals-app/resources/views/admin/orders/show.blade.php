@extends('layouts.admin')

@section('title', 'Order #' . $order->id . ' - Admin Panel')

@section('content')
<div class="ml-0 md:ml-72 bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-gradient-to-r from-white to-gray-50 shadow-lg border-b border-gray-200 sticky top-0 z-40 backdrop-blur-sm">
        <div class="px-4 sm:px-6 lg:px-8 py-4 sm:py-5">
            <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
                    <a href="{{ route('admin.orders.index') }}" class="text-[#CE9704] hover:text-[#B8860B] transition-colors transform hover:scale-110">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">Order #{{ $order->id }}</h1>
                        <p class="text-sm text-gray-500 mt-1">{{ $order->ordered_at ? $order->ordered_at->format('M d, Y \a\t h:i A') : 'N/A' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @php
                        $statusClass = match($order->status) {
                            'completed' => 'bg-green-100 text-green-800',
                            'pending_odoo' => 'bg-yellow-100 text-yellow-800',
                            default => 'bg-gray-100 text-gray-800'
                        };
                        $statusText = match($order->status) {
                            'completed' => 'Completed',
                            'pending_odoo' => 'Pending Odoo',
                            default => ucfirst($order->status ?? 'Pending')
                        };
                    @endphp
                    <span class="px-3 py-1.5 rounded-full text-xs font-semibold {{ $statusClass }}">
                        {{ $statusText }}
                    </span>
                </div>
            </div>
        </div>
    </header>
    
    <main class="p-4 sm:p-6 lg:p-8">
        @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded mb-6 shadow-md">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        </div>
        @endif

        @php
            $foremanDetails = $order->foreman_details_json ? json_decode($order->foreman_details_json, true) : null;
            $billingDetails = $order->billing_details_json ? json_decode($order->billing_details_json, true) : null;
            
            // Parse delivery address from job location
            $deliveryAddress = $order->job->notes ?? 'N/A';
            $googleMapsLink = null;
            $addressText = $deliveryAddress;
            
            // Check if address contains Google Maps link (format: "Address | https://...")
            if (strpos($deliveryAddress, '|') !== false) {
                $parts = explode('|', $deliveryAddress, 2);
                $addressText = trim($parts[0]);
                $googleMapsLink = trim($parts[1]);
            } elseif (strpos($deliveryAddress, 'https://www.google.com/maps') !== false) {
                // If entire string is a link
                $googleMapsLink = $deliveryAddress;
                $addressText = 'View on Google Maps';
            }
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column (2/3 width) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Items -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-6 h-6 text-[#CE9704]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            Order Items ({{ $order->items->count() }})
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                        @foreach($order->items as $item)
                            <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors {{ !$loop->last ? 'border-b border-gray-200 pb-4' : '' }}">
                                <div class="shrink-0">
                            <img src="{{ $item->product->image_url ? asset($item->product->image_url) : asset('Product1.png') }}" 
                                         alt="{{ $item->product->name }}" 
                                         class="w-16 h-16 object-contain bg-white rounded-lg p-2 shadow-sm">
                                </div>
                            <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-900 text-base mb-1">{{ $item->product->name }}</h3>
                                    <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600">
                                        <span>Qty: <strong class="text-gray-900">{{ $item->quantity }}</strong></span>
                                        <span>×</span>
                                        <span>${{ number_format($item->unit_price, 2) }}</span>
                                        @if($item->line_total != ($item->unit_price * $item->quantity))
                                        <span class="text-xs text-gray-500">(includes rental days)</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-gray-900">${{ number_format($item->line_total, 2) }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Delivery Information -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-6 h-6 text-[#CE9704]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Delivery Information
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <!-- Delivery Address -->
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Delivery Address</label>
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <p class="text-gray-900 font-medium mb-2">{{ $addressText }}</p>
                                    @if($googleMapsLink)
                                    <a href="{{ $googleMapsLink }}" target="_blank" rel="noopener noreferrer" 
                                       class="inline-flex items-center gap-2 text-[#CE9704] hover:text-[#B8860B] font-medium text-sm transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                        Open in Google Maps
                                    </a>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Delivery Date & End Date -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($order->job && $order->job->date)
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Start Date</label>
                                    <p class="text-gray-900 font-medium">{{ $order->job->date->format('F d, Y') }}</p>
                                </div>
                                @endif
                                
                                @if($order->job && $order->job->end_date)
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">End Date</label>
                                    <p class="text-gray-900 font-medium">{{ $order->job->end_date->format('F d, Y') }}</p>
                                </div>
                                @endif
                            </div>
                            
                            <!-- Rental Days Counter -->
                            @if($order->job && $order->job->date && $order->job->end_date)
                            @php
                                $startDate = $order->job->date;
                                $endDate = $order->job->end_date;
                                $today = \Carbon\Carbon::today();
                                $totalDays = $startDate->diffInDays($endDate) + 1;
                                
                                // Calcular días restantes
                                $daysRemaining = 0;
                                if ($today->lt($startDate)) {
                                    // La renta aún no ha comenzado
                                    $daysRemaining = $totalDays;
                                } elseif ($today->gte($startDate) && $today->lte($endDate)) {
                                    // La renta está en curso
                                    $daysRemaining = $today->diffInDays($endDate) + 1;
                                } else {
                                    // La renta ya terminó
                                    $daysRemaining = 0;
                                }
                                
                                $isActive = $today->gte($startDate) && $today->lte($endDate);
                                $isUpcoming = $today->lt($startDate);
                                $isCompleted = $today->gt($endDate);
                            @endphp
                            <div class="mt-4 p-4 bg-gradient-to-r {{ $isActive ? 'from-blue-50 to-cyan-50 border-blue-200' : ($isUpcoming ? 'from-amber-50 to-yellow-50 border-amber-200' : 'from-gray-50 to-slate-50 border-gray-200') }} rounded-lg border">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 block">Rental Period</label>
                                        <p class="text-sm text-gray-600">
                                            <span class="font-medium">{{ $totalDays }} day{{ $totalDays != 1 ? 's' : '' }}</span> total
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 block">Days Remaining</label>
                                        <p class="text-2xl font-bold {{ $isActive ? 'text-blue-600' : ($isUpcoming ? 'text-amber-600' : 'text-gray-500') }}">
                                            {{ $daysRemaining }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            @if($isActive)
                                                <span class="flex items-center gap-1">
                                                    <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                                                    Active
                                                </span>
                                            @elseif($isUpcoming)
                                                <span class="flex items-center gap-1">
                                                    <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                                                    Upcoming
                                                </span>
                                            @else
                                                <span class="flex items-center gap-1">
                                                    <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                                                    Completed
                                                </span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            <!-- Coordinates -->
                            @if($order->job && $order->job->latitude && $order->job->longitude)
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Coordinates</label>
                                <p class="text-gray-900 font-medium font-mono text-sm">
                                    {{ number_format($order->job->latitude, 7) }}, {{ number_format($order->job->longitude, 7) }}
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl shadow-lg border border-blue-200 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Summary (Estimate Only)
                    </h2>
                    <div class="space-y-2.5">
                        <div class="flex justify-between text-gray-700">
                            <span>Subtotal:</span>
                            <span class="font-semibold">${{ number_format($order->subtotal ?? $order->total_amount, 2) }}</span>
                        </div>
                        @if($order->cupon)
                        <div class="flex justify-between text-blue-700">
                            <span>Coupon Applied:</span>
                            <span class="font-semibold text-sm">{{ $order->cupon->code }}</span>
                        </div>
                        <div class="text-xs text-gray-500 italic">
                            (Discount calculated in Odoo)
                        </div>
                        @endif
                        <div class="border-t border-gray-300 pt-2.5 mt-2.5">
                            <div class="flex justify-between font-bold text-lg">
                                <span>Total Estimate:</span>
                                <span class="text-[#CE9704]">${{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <p class="text-xs text-gray-600 italic">
                                <strong>Note:</strong> This is a subtotal estimate only. Final totals, taxes, and discounts are calculated and applied in Odoo.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Update Order Status -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#CE9704]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Update Order Status
                    </h2>
                    <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <span class="text-sm font-medium text-gray-700 block mb-1">Order Status</span>
                                <span class="text-xs text-gray-500">Update order status</span>
                            </div>
                            <select name="status" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704] text-sm font-semibold">
                                <option value="pending_odoo" {{ $order->status === 'pending_odoo' ? 'selected' : '' }}>Pending Odoo</option>
                                <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column (1/3 width) -->
            <div class="space-y-6">
                <!-- Customer Information -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#CE9704]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Customer Information
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 block">Name</label>
                                <p class="text-gray-900 font-medium">{{ $order->user->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 block">Email</label>
                                <p class="text-gray-900 font-medium break-words">{{ $order->user->email ?? 'N/A' }}</p>
                            </div>
                            @if($order->user->phone_number)
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 block">Phone</label>
                                <p class="text-gray-900 font-medium">{{ $order->user->phone_number }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Foreman Details -->
                @if($foremanDetails && count(array_filter($foremanDetails)))
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#CE9704]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            Foreman / Receiving Person
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 block">First Name</label>
                                <p class="text-gray-900 font-medium">{{ $foremanDetails['firstName'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 block">Last Name</label>
                                <p class="text-gray-900 font-medium">{{ $foremanDetails['lastName'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 block">Phone</label>
                                <p class="text-gray-900 font-medium">{{ $foremanDetails['phone'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 block">Email</label>
                                <p class="text-gray-900 font-medium break-words text-sm">{{ $foremanDetails['email'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Billing Details -->
                @if($billingDetails && count(array_filter($billingDetails)))
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#CE9704]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            Billing Details
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 block">First Name</label>
                                    <p class="text-gray-900 font-medium">{{ $billingDetails['firstName'] ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 block">Last Name</label>
                                    <p class="text-gray-900 font-medium">{{ $billingDetails['lastName'] ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 block">Email</label>
                                    <p class="text-gray-900 font-medium break-words text-sm">{{ $billingDetails['email'] ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 block">Phone</label>
                                    <p class="text-gray-900 font-medium">{{ $billingDetails['phone'] ?? 'N/A' }}</p>
                                </div>
                            </div>
                            @if(!empty($billingDetails['addressLine1']))
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 block">Address</label>
                                <p class="text-gray-900 font-medium">{{ $billingDetails['addressLine1'] }}</p>
                                @if(!empty($billingDetails['addressLine2']))
                                <p class="text-gray-900 font-medium">{{ $billingDetails['addressLine2'] }}</p>
                                @endif
                            </div>
                            @endif
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 block">City</label>
                                    <p class="text-gray-900 font-medium">{{ $billingDetails['city'] ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 block">State</label>
                                    <p class="text-gray-900 font-medium">{{ $billingDetails['state'] ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 block">ZIP</label>
                                    <p class="text-gray-900 font-medium">{{ $billingDetails['zip'] ?? 'N/A' }}</p>
                                </div>
                            </div>
                            @if(!empty($billingDetails['country']))
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 block">Country</label>
                                <p class="text-gray-900 font-medium">{{ $billingDetails['country'] }}</p>
                            </div>
                            @endif
                            @if(isset($billingDetails['isCompany']) && $billingDetails['isCompany'])
                            <div class="pt-4 border-t border-gray-200">
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Company Information</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-xs text-gray-500 mb-1 block">Company Name</label>
                                        <p class="text-gray-900 font-medium">{{ $billingDetails['companyName'] ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500 mb-1 block">Job Title</label>
                                        <p class="text-gray-900 font-medium">{{ $billingDetails['jobTitle'] ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Order Notes -->
                @if($order->notes)
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#CE9704]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Order Notes
                        </h2>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-700 leading-relaxed">{{ $order->notes }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </main>
                </div>
                
@endsection
