@extends('layouts.app')

@section('title', 'Order #' . $order->id . ' - Admin Panel')

@section('content')
<div class="ml-0 md:ml-64">
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
        <div class="px-6 py-4">
            <h1 class="text-2xl font-bold text-gray-800">Order #{{ $order->id }}</h1>
        </div>
    </header>
    
    <main class="p-6">
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Order Details -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold mb-4">Order Items</h2>
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                        <div class="flex items-center border-b pb-4">
                            <img src="{{ $item->product->image_url ? asset('storage/' . $item->product->image_url) : asset('Product1.png') }}" 
                                 alt="{{ $item->product->name }}" class="w-16 h-16 object-contain mr-4">
                            <div class="flex-1">
                                <h3 class="font-semibold">{{ $item->product->name }}</h3>
                                <p class="text-sm text-gray-500">Quantity: {{ $item->quantity }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold">${{ number_format($item->line_total, 2) }}</p>
                                <p class="text-sm text-gray-500">${{ number_format($item->unit_price, 2) }} each</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold mb-4">Order Summary</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-semibold">${{ number_format($order->total_amount - $order->tax_total - ($order->discount_total ?? 0), 2) }}</span>
                        </div>
                        @if($order->discount_total)
                        <div class="flex justify-between text-green-600">
                            <span>Discount:</span>
                            <span>-${{ number_format($order->discount_total, 2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tax:</span>
                            <span class="font-semibold">${{ number_format($order->tax_total, 2) }}</span>
                        </div>
                        <div class="border-t pt-3">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total:</span>
                                <span>${{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold mb-4">Customer Info</h2>
                    <div class="space-y-2 text-sm">
                        <p><strong>Name:</strong> {{ $order->user->name ?? 'N/A' }}</p>
                        <p><strong>Email:</strong> {{ $order->user->email ?? 'N/A' }}</p>
                        <p><strong>Phone:</strong> {{ $order->user->phone_number ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold mb-4">Update Order</h2>
                    <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="status" value="1" {{ $order->status ? 'checked' : '' }}
                                    class="w-4 h-4 text-[#CE9704] border-gray-300 rounded">
                                <span class="ml-2 text-sm">Order Completed</span>
                            </label>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg">{{ old('notes', $order->notes) }}</textarea>
                        </div>
                        <button type="submit" class="w-full bg-[#CE9704] text-white px-4 py-2 rounded-lg hover:bg-[#B8860B] transition-colors">
                            Update Order
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('admin.orders.index') }}" class="text-[#CE9704] hover:underline">← Back to Orders</a>
        </div>
    </main>
</div>

@include('admin.sidebar')
@endsection

