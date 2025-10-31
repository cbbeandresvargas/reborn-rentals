@extends('layouts.app')

@section('title', 'Order #' . $order->id . ' - Reborn Rentals')

@section('content')
<div class="max-w-6xl mx-auto px-6 mt-8 mb-20">
    <a href="{{ route('orders') }}" class="text-[#CE9704] hover:underline mb-4 inline-block">← Back to Orders</a>
    
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="flex justify-between items-start mb-6 pb-6 border-b">
            <div>
                <h1 class="text-3xl font-bold mb-2">Order #{{ $order->id }}</h1>
                <p class="text-gray-600">Ordered on {{ $order->ordered_at->format('F d, Y \a\t g:i A') }}</p>
            </div>
            <div class="text-right">
                <p class="text-3xl font-bold text-[#CE9704] mb-2">${{ number_format($order->total_amount, 2) }}</p>
                <span class="inline-block px-4 py-2 rounded-full text-sm font-semibold {{ $order->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $order->status ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>
        
        <!-- Order Items -->
        <div class="mb-8">
            <h2 class="text-xl font-bold mb-4">Order Items</h2>
            <div class="space-y-4">
                @foreach($order->items as $item)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-4">
                        <img src="{{ $item->product->image_url ? asset($item->product->image_url) : asset('Product1.png') }}" 
                             alt="{{ $item->product->name }}" 
                             class="w-16 h-16 object-contain bg-white rounded p-2" />
                        <div>
                            <h3 class="font-semibold">{{ $item->product->name }}</h3>
                            <p class="text-sm text-gray-600">Quantity: {{ $item->quantity }}</p>
                            <p class="text-sm text-gray-600">${{ number_format($item->unit_price, 2) }} each</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold">${{ number_format($item->line_total, 2) }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- Order Summary -->
        <div class="bg-gray-50 rounded-lg p-6 mb-8">
            <h2 class="text-xl font-bold mb-4">Order Summary</h2>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span>Subtotal:</span>
                    <span class="font-semibold">${{ number_format($order->total_amount - $order->tax_total + ($order->discount_total ?? 0), 2) }}</span>
                </div>
                @if($order->discount_total > 0)
                <div class="flex justify-between text-green-600">
                    <span>Discount:</span>
                    <span>-${{ number_format($order->discount_total, 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Tax:</span>
                    <span>${{ number_format($order->tax_total ?? 0, 2) }}</span>
                </div>
                <div class="border-t pt-2 mt-2">
                    <div class="flex justify-between text-xl font-bold">
                        <span>Total:</span>
                        <span class="text-[#CE9704]">${{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Delivery Info -->
        @if($order->job)
        <div class="mb-8">
            <h2 class="text-xl font-bold mb-4">Delivery Information</h2>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-gray-700">{{ $order->job->notes }}</p>
                @if($order->job->latitude && $order->job->longitude)
                <p class="text-sm text-gray-600 mt-2">
                    Coordinates: {{ number_format($order->job->latitude, 7) }}, {{ number_format($order->job->longitude, 7) }}
                </p>
                @endif
                @if($order->job->date)
                <p class="text-sm text-gray-600">Delivery Date: {{ $order->job->date->format('M d, Y') }}</p>
                @endif
            </div>
        </div>
        @endif
        
        <!-- Payment Info -->
        <div>
            <h2 class="text-xl font-bold mb-4">Payment Information</h2>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-gray-700">Payment Method: 
                    @if($order->payment_method == 1) Credit Card
                    @elseif($order->payment_method == 2) Debit Card
                    @elseif($order->payment_method == 3) Bank Transfer
                    @else Other
                    @endif
                </p>
                @if($order->transaction_id)
                <p class="text-sm text-gray-600 mt-2">Transaction ID: {{ $order->transaction_id }}</p>
                @endif
            </div>
        </div>
        
        @if($order->notes)
        <div class="mt-8">
            <h2 class="text-xl font-bold mb-4">Notes</h2>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-gray-700">{{ $order->notes }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

