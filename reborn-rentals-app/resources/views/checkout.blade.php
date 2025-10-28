@extends('layouts.app')

@section('title', 'Checkout - Reborn Rentals')

@section('content')
<div class="max-w-6xl mx-auto px-6 mt-8 mb-20">
    <h1 class="text-3xl font-bold mb-6">Checkout</h1>
    
    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ session('error') }}
    </div>
    @endif
    
    <form method="POST" action="{{ route('checkout.store') }}" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        @csrf
        
        <!-- Left Column: Order Details -->
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Rental Period</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Start Date</label>
                        <input type="date" name="start_date" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704]" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">End Date</label>
                        <input type="date" name="end_date" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704]" />
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Delivery Location</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Jobsite Address</label>
                        <textarea name="jobsite_address" required rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704]"
                                  placeholder="Enter delivery address"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Latitude (optional)</label>
                            <input type="number" step="any" name="latitude" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704]" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Longitude (optional)</label>
                            <input type="number" step="any" name="longitude" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704]" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Additional Notes</label>
                        <textarea name="notes" rows="2"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704]"
                                  placeholder="Any special instructions..."></textarea>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Payment Method</h2>
                <select name="payment_method" required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704]">
                    <option value="1">Credit Card</option>
                    <option value="2">Debit Card</option>
                    <option value="3">Bank Transfer</option>
                </select>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Discount Code (Optional)</h2>
                <input type="text" name="cupon_code" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704]"
                       placeholder="Enter coupon code" />
            </div>
        </div>
        
        <!-- Right Column: Order Summary -->
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6 sticky top-24">
                <h2 class="text-xl font-bold mb-4">Order Summary</h2>
                <div class="space-y-3 border-b pb-4 mb-4">
                    @foreach($cart as $productId => $quantity)
                        @php $product = $products->get($productId); @endphp
                        @if($product)
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-semibold">{{ $product->name }}</p>
                                <p class="text-sm text-gray-600">Qty: {{ $quantity }}</p>
                            </div>
                            <p class="font-bold">${{ number_format($product->price * $quantity, 2) }}</p>
                        </div>
                        @endif
                    @endforeach
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span>Subtotal:</span>
                        <span class="font-bold">${{ number_format($total, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Tax (2%):</span>
                        <span>${{ number_format($total * 0.02, 2) }}</span>
                    </div>
                    <div class="border-t pt-2 mt-2">
                        <div class="flex justify-between text-xl font-bold">
                            <span>Total:</span>
                            <span class="text-[#CE9704]">${{ number_format($total * 1.02, 2) }}</span>
                        </div>
                    </div>
                </div>
                <button type="submit" class="w-full mt-6 bg-[#CE9704] text-white font-bold py-3 px-6 rounded-lg hover:bg-[#B8860B] transition-colors">
                    Complete Order
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

