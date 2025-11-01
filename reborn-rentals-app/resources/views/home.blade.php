@extends('layouts.app')

@section('title', 'Reborn Rentals - Home')

@section('content')
<!-- Subnavbar -->
<div class="bg-[#BBBBBB] py-2 sm:py-1 shadow-md">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center mt-2 gap-3 sm:gap-4 md:gap-5 justify-between">
            <!-- Search Bar -->
            <form method="GET" action="{{ route('products.index') }}" class="flex-1 w-full sm:w-auto">
                <div class="relative">
                    <input
                        type="text"
                        name="search"
                        placeholder="Search products..."
                        value="{{ request('search') }}"
                        class="w-full pl-10 pr-4 py-2 sm:py-2 text-sm sm:text-base border bg-white border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704] focus:border-transparent"
                        id="search-input"
                    />
                    <button type="submit" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-[#CE9704] transition-colors duration-200">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
            </form>

            <!-- Filters Button -->
            <a href="{{ route('products.index') }}" class="flex items-center justify-center gap-2 bg-[#CE9704] px-3 sm:px-4 py-2 sm:py-3 rounded-lg border border-gray-300 hover:border-[#CE9704] transition-all duration-200 shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
            </a>
        </div>
    </div>
</div>

<!-- Products -->
<div class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6 mt-8 sm:mt-12 md:mt-20 mb-12 sm:mb-16 md:mb-20">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-5 md:gap-6" id="products-grid">
        @forelse($products as $product)
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-2xl shadow-[#CE9704]/20 transition-shadow duration-300 cursor-move product-card flex flex-col h-full" 
             draggable="true" 
             data-product-id="{{ $product->id }}" 
             data-product-name="{{ $product->name }}" 
             data-product-price="{{ $product->price }}">
            <div class="relative bg-white p-4">
                <button class="absolute top-2 left-2 bg-[#CE9704] p-2 rounded hover:bg-[#B8860B] transition-colors duration-200 group z-0 add-to-cart-btn" 
                        data-product-id="{{ $product->id }}" 
                        data-product-name="{{ $product->name }}" 
                        data-product-price="{{ $product->price }}" 
                        style="z-index: 1;"
                        title="Click to add to cart or drag and drop the item into the cart">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="m1 1 4 4 13 1 2 8H6l-2-8z"></path>
                    </svg>
                </button>
                <img src="{{ $product->image_url ? asset($product->image_url) : asset('Product1.png') }}" alt="{{ $product->name }}" class="w-full h-48 sm:h-56 md:h-64 object-contain" />
            </div>
            <div class="bg-[#4A4A4A] px-3 sm:px-4 py-2 sm:py-3 text-center">
                <h3 class="text-white font-bold text-base sm:text-lg">{{ $product->name }}</h3>
            </div>
            <div class="border-t border-gray-500"></div>
            <div class="bg-[#4A4A4A] px-3 sm:px-4 py-2 sm:py-3 space-y-2 flex-1">
                <div class="flex justify-between items-center flex-wrap gap-1 sm:gap-0">
                    <span class="text-white text-sm sm:text-base">ID: <span class="text-[#CE9704]">{{ $product->id }}</span></span>
                    <span class="text-white font-bold text-base sm:text-lg">${{ number_format($product->price, 2) }}/day*</span>
                </div>
                @if($product->description)
                <div>
                    <span class="text-gray-300">Description:</span>
                    <div class="text-white text-sm line-clamp-2">{{ $product->description }}</div>
                </div>
                @endif
                @if($product->category)
                <div>
                    <span class="text-gray-300">Category:</span>
                    <div class="text-white text-sm">{{ $product->category->name }}</div>
                </div>
                @endif
            </div>
            <a href="{{ route('products.show', $product->id) }}" class="block w-full bg-[#CE9704] text-white font-bold py-2 sm:py-2.5 px-3 sm:px-4 rounded text-sm sm:text-base hover:bg-[#B8860B] transition-colors duration-200 text-center mt-auto">
                SEE SPECIFICATION
            </a>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <p class="text-gray-600 text-lg">No products found.</p>
            <a href="{{ route('products.index') }}" class="text-[#CE9704] hover:underline mt-4 inline-block">View all products</a>
        </div>
        @endforelse
    </div>

    @if($products->hasPages())
    <div class="mt-8">
        {{ $products->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/cart.js') }}"></script>
@endpush

