@extends('layouts.app')

@section('title', 'Products - Reborn Rentals')

@section('content')
<!-- Filters and Search -->
<div class="bg-[#BBBBBB] py-4 shadow-md">
    <div class="max-w-7xl mx-auto px-6">
        <form method="GET" action="{{ route('products.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <input type="text" 
                           name="search" 
                           placeholder="Search products..." 
                           value="{{ request('search') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704]" />
                </div>
                
                <!-- Category Filter -->
                <div>
                    <select name="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704]">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Price Filter -->
                <div>
                    <input type="number" 
                           name="max_price" 
                           placeholder="Max Price" 
                           value="{{ request('max_price') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704]" />
                </div>
            </div>
            
            <button type="submit" class="bg-[#CE9704] text-white px-6 py-2 rounded-lg hover:bg-[#B8860B] transition-colors">
                Apply Filters
            </button>
        </form>
    </div>
</div>

<!-- Products Grid -->
<div class="max-w-7xl mx-auto px-6 mt-8 mb-20">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($products as $product)
        <div class="bg-white rounded-lg border border-gray-200 overflow-visible hover:shadow-2xl shadow-[#CE9704]/20 transition-shadow duration-300 cursor-move" 
             draggable="true" 
             data-product-id="{{ $product->id }}" 
             data-product-name="{{ $product->name }}" 
             data-product-price="{{ $product->price }}">
            <div class="relative bg-white p-4">
                <button class="absolute top-2 left-2 bg-[#CE9704] p-2 rounded hover:bg-[#B8860B] transition-colors duration-200 add-to-cart-btn" 
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
                <img src="{{ $product->image_url ? asset('storage/' . $product->image_url) : asset('Product1.png') }}" 
                     alt="{{ $product->name }}" 
                     class="w-full h-64 object-contain" />
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
            <a href="{{ route('products.index') }}" class="text-[#CE9704] hover:underline mt-4 inline-block">Clear filters</a>
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

