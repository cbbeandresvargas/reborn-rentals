@forelse($products as $product)
<div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-2xl shadow-[#CE9704]/20 transition-all duration-300 cursor-grab product-card flex flex-col h-full group" 
     draggable="true" 
     data-product-id="{{ $product->id }}" 
     data-product-name="{{ $product->name }}" 
     data-product-price="{{ $product->price }}">
    <div class="relative bg-white p-5 sm:p-6">
        <!-- Drag indicator badge -->
        <div class="absolute top-2 right-2 bg-[#CE9704] text-white text-xs font-bold px-2 py-1 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none z-10 flex items-center gap-1">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
            </svg>
            Drag
        </div>
        <button class="absolute top-2 left-2 bg-[#CE9704] p-2 rounded hover:bg-[#B8860B] transition-all duration-200 z-0 add-to-cart-btn hover:scale-110 hover:shadow-lg" 
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
        <img src="{{ $product->image_url ? asset($product->image_url) : asset('Product1.png') }}" alt="{{ $product->name }}" class="w-full h-48 sm:h-56 md:h-64 object-contain transition-transform duration-300 group-hover:scale-105" />
    </div>
    <div class="bg-[#4A4A4A] px-4 sm:px-5 py-3 sm:py-4 text-center">
        <h3 class="text-white font-bold text-base sm:text-lg">{{ $product->name }}</h3>
    </div>
    <div class="border-t border-gray-500"></div>
    <div class="bg-[#4A4A4A] px-4 sm:px-5 py-3 sm:py-4 space-y-2 flex-1">
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
    <a href="{{ route('products.show', $product->id) }}" class="block w-full bg-[#CE9704] text-white font-bold py-3 sm:py-3.5 px-4 sm:px-5 rounded text-sm sm:text-base hover:bg-[#B8860B] transition-colors duration-200 text-center mt-auto">
        SEE SPECIFICATION
    </a>
</div>
@empty
<div class="col-span-full text-center py-12">
    <p class="text-gray-600 text-lg">No products found.</p>
    <a href="{{ route('home') }}" class="text-[#CE9704] hover:underline mt-4 inline-block">Clear filters</a>
</div>
@endforelse
