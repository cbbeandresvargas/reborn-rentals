@extends('layouts.app')

@section('title', 'Reborn Rentals - Home')

@section('content')
<!-- Subnavbar -->
<div class="bg-[#BBBBBB] py-1 shadow-md">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex items-center mt-2 gap-5 justify-between">
            <!-- Search Bar -->
            <form method="GET" action="{{ route('products.index') }}" class="flex-1 w-full">
                <div class="relative mb-2">
                    <input
                        type="text"
                        name="search"
                        placeholder="Search products..."
                        value="{{ request('search') }}"
                        class="w-full pl-10 pr-4 py-2 border bg-white border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704] focus:border-transparent"
                        id="search-input"
                    />
                    <button type="submit" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-[#CE9704] transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
            </form>

            <!-- Filters Button -->
            <a href="{{ route('products.index') }}" class="flex items-center gap-2 mb-2 bg-[#CE9704] px-4 py-3 rounded-lg border border-gray-300 hover:border-[#CE9704] transition-all duration-200">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
            </a>
        </div>
    </div>
</div>

<!-- Products -->
<div class="max-w-7xl mx-auto px-6 mt-20 mb-20">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" id="products-grid">
        @forelse($products as $product)
        <div class="bg-white rounded-lg border border-gray-200 overflow-visible hover:shadow-2xl shadow-[#CE9704]/20 transition-shadow duration-300 cursor-move product-card" 
             draggable="true" 
             data-product-id="{{ $product->id }}" 
             data-product-name="{{ $product->name }}" 
             data-product-price="{{ $product->price }}">
            <div class="relative bg-white p-4">
                <button class="absolute top-2 left-2 bg-[#CE9704] p-2 rounded hover:bg-[#B8860B] transition-colors duration-200 group z-10 add-to-cart-btn" 
                        data-product-id="{{ $product->id }}" 
                        data-product-name="{{ $product->name }}" 
                        data-product-price="{{ $product->price }}" 
                        title="Click to add to cart or drag and drop the item into the cart">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="m1 1 4 4 13 1 2 8H6l-2-8z"></path>
                    </svg>
                </button>
                <img src="{{ $product->image_url ? asset('storage/' . $product->image_url) : asset('Product1.png') }}" alt="{{ $product->name }}" class="w-full h-64 object-contain" />
            </div>
            <div class="bg-[#4A4A4A] px-4 py-3 text-center">
                <h3 class="text-white font-bold text-lg">{{ $product->name }}</h3>
            </div>
            <div class="border-t border-gray-500"></div>
            <div class="bg-[#4A4A4A] px-4 py-3 space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-white">ID: <span class="text-[#CE9704]">{{ $product->id }}</span></span>
                    <span class="text-white font-bold text-lg">${{ number_format($product->price, 2) }}/day*</span>
                </div>
                @if($product->description)
                <div>
                    <span class="text-gray-300">Description:</span>
                    <div class="text-white text-sm">{{ $product->description }}</div>
                </div>
                @endif
                @if($product->category)
                <div>
                    <span class="text-gray-300">Category:</span>
                    <div class="text-white text-sm">{{ $product->category->name }}</div>
                </div>
                @endif
            </div>
            <a href="{{ route('products.show', $product->id) }}" class="block w-full bg-[#CE9704] text-white font-bold py-2 px-4 rounded hover:bg-[#B8860B] transition-colors duration-200 text-center">
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

