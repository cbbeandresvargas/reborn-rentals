@extends('layouts.app')

@section('title', 'My Orders - Reborn Rentals')

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6 mt-4 sm:mt-6 md:mt-8 mb-12 sm:mb-16 md:mb-20">
    <h1 class="text-2xl sm:text-3xl font-bold mb-4 sm:mb-6">My Orders</h1>
    
    @if($orders->count() > 0)
    <div class="space-y-4 sm:space-y-6">
        @foreach($orders as $order)
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start mb-4 gap-3 sm:gap-0">
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold">Order #{{ $order->id }}</h3>
                        <p class="text-gray-600 text-xs sm:text-sm">Date: {{ $order->ordered_at->format('M d, Y') }}</p>
                    </div>
                    <div class="text-left sm:text-right w-full sm:w-auto">
                        <p class="text-xl sm:text-2xl font-bold text-[#CE9704]">${{ number_format($order->total_amount, 2) }}</p>
                        <span class="inline-block px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm {{ $order->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $order->status ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                
                <div class="border-t pt-4">
                    <h4 class="font-semibold mb-2">Items:</h4>
                    <div class="space-y-2">
                        @foreach($order->items as $item)
                        <div class="flex justify-between text-sm">
                            <span>{{ $item->product->name }} x{{ $item->quantity }}</span>
                            <span class="font-semibold">${{ number_format($item->line_total, 2) }}</span>
                        </div>
                        @endforeach
                    </div>
                    
                    @if($order->discount_total > 0)
                    <div class="mt-2 pt-2 border-t text-sm">
                        <div class="flex justify-between text-green-600">
                            <span>Discount:</span>
                            <span>-${{ number_format($order->discount_total, 2) }}</span>
                        </div>
                    </div>
                    @endif
                    
                    <div class="mt-4 pt-4 border-t">
                        <a href="{{ route('order', $order->id) }}" class="text-[#CE9704] hover:underline font-semibold">
                            View Details →
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    @if($orders->hasPages())
    <div class="mt-8">
        {{ $orders->links() }}
    </div>
    @endif
    
    @else
    <div class="bg-white rounded-lg shadow-lg p-8 sm:p-12 text-center">
        <p class="text-gray-600 text-base sm:text-lg mb-4">You haven't made any orders yet.</p>
        <a href="{{ route('home') }}" class="inline-block bg-[#CE9704] text-white px-4 sm:px-6 py-2 sm:py-3 text-sm sm:text-base rounded-lg hover:bg-[#B8860B] transition-colors">
            Start Shopping
        </a>
    </div>
    @endif
</div>
@endsection

