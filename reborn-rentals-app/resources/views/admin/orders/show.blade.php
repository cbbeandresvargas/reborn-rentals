@extends('layouts.app')

@section('title', 'Order #' . $order->id . ' - Admin Panel')

@section('content')
<div class="ml-0 md:ml-64">
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40 px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.orders.index') }}" class="text-[#CE9704] hover:text-[#B8860B] transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Order #{{ $order->id }}</h1>
        </div>
    </header>
    
    <main class="p-6">
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
        @endif

        @php
            $foremanDetails = $order->foreman_details_json ? json_decode($order->foreman_details_json, true) : null;
            $billingDetails = $order->billing_details_json ? json_decode($order->billing_details_json, true) : null;
            $paymentMethodDetails = $order->payment_method_details_json ? json_decode($order->payment_method_details_json, true) : null;
            $paymentMethods = [
                1 => 'Credit/Debit Card',
                2 => 'Direct Debit',
                3 => 'Google Pay',
                4 => 'Apple Pay',
                5 => 'Klarna'
            ];
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="space-y-6">
                <!-- Order Items -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold mb-4">Order Items</h2>
                    <div class="space-y-3">
                        @foreach($order->items as $item)
                        <div class="flex items-center gap-4 py-3 {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                            <img src="{{ $item->product->image_url ? asset($item->product->image_url) : asset('Product1.png') }}" 
                                 alt="{{ $item->product->name }}" class="w-12 h-12 object-contain">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 text-sm">{{ $item->product->name }}</h3>
                                <p class="text-xs text-gray-500">Qty: {{ $item->quantity }} × ${{ number_format($item->unit_price, 2) }}</p>
                            </div>
                            <p class="font-semibold text-gray-900">${{ number_format($item->line_total, 2) }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Summary & Payment -->
                <div class="grid grid-cols-2 gap-6">
                    <!-- Summary -->
                    <div class="bg-white rounded-lg shadow p-4">
                        <h2 class="text-lg font-bold mb-3">Summary</h2>
                        <div class="space-y-1.5 text-sm">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal:</span>
                                <span>${{ number_format($order->total_amount - $order->tax_total - ($order->discount_total ?? 0), 2) }}</span>
                            </div>
                            @if($order->discount_total)
                            <div class="flex justify-between text-green-600">
                                <span>Discount:</span>
                                <span>-${{ number_format($order->discount_total, 2) }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between text-gray-600">
                                <span>Tax:</span>
                                <span>${{ number_format($order->tax_total, 2) }}</span>
                            </div>
                            <div class="border-t pt-2 mt-2">
                                <div class="flex justify-between font-bold">
                                    <span>Total:</span>
                                    <span class="text-[#CE9704]">${{ number_format($order->total_amount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-white rounded-lg shadow p-4">
                        <h2 class="text-lg font-bold mb-3">Payment</h2>
                        <div class="space-y-1.5 text-sm">
                            <p><strong>Method:</strong><br>{{ $paymentMethods[$order->payment_method] ?? 'Unknown' }}</p>
                            @if($paymentMethodDetails && count(array_filter($paymentMethodDetails)))
                                @foreach($paymentMethodDetails as $key => $value)
                                    @if($value !== null && $value !== '')
                                        <p class="text-xs"><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</p>
                                    @endif
                                @endforeach
                            @else
                                <p class="text-xs text-gray-500">No additional details</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Update Form -->
                <div class="bg-white rounded-lg shadow p-4">
                    <h2 class="text-lg font-bold mb-3">Update Order</h2>
                    <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="space-y-3">
                        @csrf
                        @method('PUT')
                        
                        <!-- Toggle Switch for Order Status -->
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Order Status</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="status" value="1" class="sr-only peer" {{ $order->status ? 'checked' : '' }}
                                    onchange="this.form.submit()">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#CE9704] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#CE9704]"></div>
                                <span class="ml-3 text-sm font-medium {{ $order->status ? 'text-green-600' : 'text-gray-500' }}" id="status-label">
                                    {{ $order->status ? 'Completed' : 'Pending' }}
                                </span>
                            </label>
                        </div>
                    </form>
                </div>
                
                <script>
                    // Update label text when switch is toggled
                    document.addEventListener('DOMContentLoaded', function() {
                        const checkbox = document.querySelector('input[name="status"]');
                        const label = document.getElementById('status-label');
                        
                        if (checkbox && label) {
                            checkbox.addEventListener('change', function() {
                                if (this.checked) {
                                    label.textContent = 'Completed';
                                    label.classList.remove('text-gray-500');
                                    label.classList.add('text-green-600');
                                } else {
                                    label.textContent = 'Pending';
                                    label.classList.remove('text-green-600');
                                    label.classList.add('text-gray-500');
                                }
                            });
                        }
                    });
                </script>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Customer -->
                <div class="bg-white rounded-lg shadow p-4">
                    <h2 class="text-lg font-bold mb-3">Customer</h2>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-gray-500">Name:</span>
                            <p class="font-medium">{{ $order->user->name ?? 'null' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Email:</span>
                            <p class="font-medium break-words">{{ $order->user->email ?? 'null' }}</p>
                        </div>
                        <div class="col-span-2">
                            <span class="text-gray-500">Phone:</span>
                            <p class="font-medium">{{ $order->user->phone_number ?? 'null' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Foreman Details -->
                <div class="bg-white rounded-lg shadow p-4">
                    <h2 class="text-lg font-bold mb-3">Foreman / Receiving Person</h2>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-gray-500">First Name:</span>
                            <p class="font-medium">{{ $foremanDetails['firstName'] ?? 'null' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Last Name:</span>
                            <p class="font-medium">{{ $foremanDetails['lastName'] ?? 'null' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Phone:</span>
                            <p class="font-medium">{{ $foremanDetails['phone'] ?? 'null' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Email:</span>
                            <p class="font-medium break-words">{{ $foremanDetails['email'] ?? 'null' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Billing Details -->
                <div class="bg-white rounded-lg shadow p-4">
                    <h2 class="text-lg font-bold mb-3">Billing Details</h2>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-gray-500">First Name:</span>
                            <p class="font-medium">{{ $billingDetails['firstName'] ?? 'null' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Last Name:</span>
                            <p class="font-medium">{{ $billingDetails['lastName'] ?? 'null' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Email:</span>
                            <p class="font-medium break-words">{{ $billingDetails['email'] ?? 'null' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Phone:</span>
                            <p class="font-medium">{{ $billingDetails['phone'] ?? 'null' }}</p>
                        </div>
                        <div class="col-span-2">
                            <span class="text-gray-500">Address Line 1:</span>
                            <p class="font-medium">{{ $billingDetails['addressLine1'] ?? 'null' }}</p>
                        </div>
                        @if(!empty($billingDetails['addressLine2']))
                        <div class="col-span-2">
                            <span class="text-gray-500">Address Line 2:</span>
                            <p class="font-medium">{{ $billingDetails['addressLine2'] ?? 'null' }}</p>
                        </div>
                        @endif
                        <div>
                            <span class="text-gray-500">City:</span>
                            <p class="font-medium">{{ $billingDetails['city'] ?? 'null' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">State:</span>
                            <p class="font-medium">{{ $billingDetails['state'] ?? 'null' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">ZIP:</span>
                            <p class="font-medium">{{ $billingDetails['zip'] ?? 'null' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Country:</span>
                            <p class="font-medium">{{ $billingDetails['country'] ?? 'null' }}</p>
                        </div>
                        <div class="col-span-2">
                            <span class="text-gray-500">Is Company:</span>
                            <p class="font-medium">{{ isset($billingDetails['isCompany']) ? ($billingDetails['isCompany'] ? 'Yes' : 'No') : 'null' }}</p>
                        </div>
                        @if(isset($billingDetails['isCompany']) && $billingDetails['isCompany'])
                        <div>
                            <span class="text-gray-500">Company Name:</span>
                            <p class="font-medium">{{ $billingDetails['companyName'] ?? 'null' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Job Title:</span>
                            <p class="font-medium">{{ $billingDetails['jobTitle'] ?? 'null' }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

@include('admin.sidebar')
@endsection

