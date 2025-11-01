@extends('layouts.app')

@section('title', $product->name . ' - Reborn Rentals')

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6 mt-4 sm:mt-6 md:mt-8 mb-12 sm:mb-16 md:mb-20">
    <a href="{{ route('home') }}" class="text-[#CE9704] hover:underline mb-3 sm:mb-4 inline-block text-sm sm:text-base">← Back to Products</a>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
        <!-- Product Image -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
            <img src="{{ $product->image_url ? asset($product->image_url) : asset('Product1.png') }}" 
                 alt="{{ $product->name }}" 
                 class="w-full h-auto object-contain rounded-lg" />
        </div>
        
        <!-- Product Info -->
        <div class="space-y-4 sm:space-y-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>
                <p class="text-xl sm:text-2xl font-bold text-[#CE9704]">${{ number_format($product->price, 2) }}/day*</p>
            </div>
            
            @if($product->description)
            <div>
                <h3 class="text-lg font-semibold mb-2">Description</h3>
                <p class="text-gray-700">{{ $product->description }}</p>
            </div>
            @endif
            
            @if($product->category)
            <div>
                <h3 class="text-lg font-semibold mb-2">Category</h3>
                <span class="inline-block bg-gray-200 px-3 py-1 rounded-full text-sm">{{ $product->category->name }}</span>
            </div>
            @endif
            
            <!-- Add to Cart -->
            <div class="border-t pt-4 sm:pt-6">
                <button class="w-full bg-[#CE9704] text-white font-bold py-3 sm:py-4 px-4 sm:px-6 rounded-lg text-base sm:text-lg hover:bg-[#B8860B] transition-colors duration-200 add-to-cart-btn" 
                        data-product-id="{{ $product->id }}" 
                        data-product-name="{{ $product->name }}" 
                        data-product-price="{{ $product->price }}">
                    Add to Cart
                </button>
            </div>
            
            <!-- Terms -->
            <div class="bg-gray-100 p-4 rounded-lg text-sm text-gray-600">
                <p class="font-medium mb-2">*Minimum Order is 30 days.</p>
                <p>*Additional charges for delivery, rough terrain and express orders may apply.</p>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <div class="mt-8 sm:mt-12 md:mt-16">
        <h2 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6">Related Products</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-5 md:gap-6">
            @foreach($relatedProducts as $related)
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">
                <a href="{{ route('products.show', $related->id) }}">
                    <img src="{{ $related->image_url ? asset($related->image_url) : asset('Product1.png') }}" 
                         alt="{{ $related->name }}" 
                         class="w-full h-48 object-contain p-4" />
                </a>
                <div class="p-4">
                    <h3 class="font-bold text-lg mb-2">{{ $related->name }}</h3>
                    <p class="text-[#CE9704] font-bold">${{ number_format($related->price, 2) }}/day*</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/cart.js') }}"></script>
@endpush

