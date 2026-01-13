@extends('layouts.app')

@section('title', 'Reborn Rentals - Home')

@section('content')
<!-- Subnavbar -->
<div class="bg-[#414141] py-2 sm:py-1 shadow-md">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center mt-2 gap-3 sm:gap-4 md:gap-5">
            <!-- Filters Button with Dropdown -->
            <div class="relative">
                <button type="button" id="filters-toggle-btn" class="flex items-center justify-center gap-2 bg-transparent px-3 sm:px-4 py-2 sm:py-3 rounded-lg transition-all duration-200 shrink-0 group">
                    <div class="relative w-6 h-6 sm:w-7 sm:h-7">
                        <img src="{{ asset('icons/filters.svg') }}" alt="Filters" class="absolute inset-0 w-full h-full object-contain transition-opacity duration-300 ease-in-out group-hover:opacity-0" />
                        <img src="{{ asset('icons/filters-hover.svg') }}" alt="Filters Hover" class="absolute inset-0 w-full h-full object-contain transition-opacity duration-300 ease-in-out opacity-0 group-hover:opacity-100" />
                    </div>
                </button>

                <!-- Filters Dropdown -->
                <div id="filters-dropdown" class="absolute top-full left-0 mt-2 w-80 bg-[#414141] shadow-2xl rounded-lg overflow-hidden z-50 hidden" style="max-height: 70vh; overflow-y: auto;">
                    <form method="GET" action="{{ route('products.index') }}" id="filters-form" class="p-4 sm:p-6">
                        <div class="flex flex-col gap-6">
                            <!-- Category Filter -->
                            <div>
                                <h3 class="text-white font-bold text-sm uppercase mb-4 tracking-wider flex items-center gap-2">
                                    <span class="w-2 h-2 bg-[#CE9704] rounded-full"></span>
                                    CATEGORY
                                </h3>
                                <div class="flex flex-col gap-3 ml-4">
                                    @foreach($categories as $category)
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input type="checkbox" name="categories[]" value="{{ $category->id }}" class="w-4 h-4 border-2 border-white rounded bg-transparent text-[#CE9704] focus:ring-[#CE9704] focus:ring-2 cursor-pointer" />
                                        <span class="text-white text-sm group-hover:text-[#CE9704] transition-colors duration-300">{{ $category->name }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Dimensions Filter -->
                            @if(isset($dimensions) && count($dimensions) > 0)
                            <div>
                                <h3 class="text-white font-bold text-sm uppercase mb-4 tracking-wider flex items-center gap-2">
                                    <span class="w-2 h-2 bg-[#CE9704] rounded-full"></span>
                                    DIMENSIONS
                                </h3>
                                <div class="flex flex-col gap-3 ml-4">
                                    @foreach($dimensions as $dimension)
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input type="checkbox" name="dimensions[]" value="{{ $dimension }}" class="w-4 h-4 border-2 border-white rounded bg-transparent text-[#CE9704] focus:ring-[#CE9704] focus:ring-2 cursor-pointer" />
                                        <span class="text-white text-sm group-hover:text-[#CE9704] transition-colors duration-300">{{ $dimension }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <!-- Tonnage Capacity Filter -->
                            @if(isset($tonnageCapacities) && count($tonnageCapacities) > 0)
                            <div>
                                <h3 class="text-white font-bold text-sm uppercase mb-4 tracking-wider flex items-center gap-2">
                                    <span class="w-2 h-2 bg-[#CE9704] rounded-full"></span>
                                    TONNAGE CAPACITY
                                </h3>
                                <div class="flex flex-col gap-3 ml-4">
                                    @foreach($tonnageCapacities as $tonnage)
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input type="checkbox" name="tonnage[]" value="{{ $tonnage }}" class="w-4 h-4 border-2 border-white rounded bg-transparent text-[#CE9704] focus:ring-[#CE9704] focus:ring-2 cursor-pointer" />
                                        <span class="text-white text-sm group-hover:text-[#CE9704] transition-colors duration-300">{{ $tonnage }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <!-- Gallon Capacity Filter -->
                            @if(isset($gallonCapacities) && count($gallonCapacities) > 0)
                            <div>
                                <h3 class="text-white font-bold text-sm uppercase mb-4 tracking-wider flex items-center gap-2">
                                    <span class="w-2 h-2 bg-[#CE9704] rounded-full"></span>
                                    GALLON CAPACITY
                                </h3>
                                <div class="flex flex-col gap-3 ml-4">
                                    @foreach($gallonCapacities as $gallon)
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input type="checkbox" name="gallons[]" value="{{ $gallon }}" class="w-4 h-4 border-2 border-white rounded bg-transparent text-[#CE9704] focus:ring-[#CE9704] focus:ring-2 cursor-pointer" />
                                        <span class="text-white text-sm group-hover:text-[#CE9704] transition-colors duration-300">{{ $gallon }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <!-- Apply Filters Button -->
                            <div class="mt-4">
                                <button type="submit" class="w-full bg-[#CE9704] text-white font-bold py-3 px-4 rounded-lg hover:bg-[#B8860B] transition-colors duration-200">
                                    Apply Filters
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Search Icon Button -->
            <button type="button" id="search-toggle-btn" class="flex items-center justify-center bg-transparent px-3 sm:px-4 py-2 sm:py-3 rounded-lg transition-all duration-200 shrink-0">
                <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </button>

            <!-- Search Bar (Hidden by default) -->
            <form method="GET" action="{{ route('products.index') }}" class="flex-1 w-full sm:w-auto hidden" id="search-form">
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
        </div>
    </div>
</div>

<!-- Products -->
<div class="max-w-7xl mx-auto px-5 sm:px-8 md:px-10 lg:px-12 xl:px-14 mt-10 sm:mt-14 md:mt-20 mb-16 sm:mb-20 md:mb-24">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 sm:gap-6 md:gap-7 lg:gap-8" id="products-grid">
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
            <a href="{{ route('products.index') }}" class="text-[#CE9704] hover:underline mt-4 inline-block">View all products</a>
        </div>
        @endforelse
    </div>

    @if($products->hasPages())
    <div class="mt-10 sm:mt-12 md:mt-16">
        {{ $products->links() }}
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/cart.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchToggleBtn = document.getElementById('search-toggle-btn');
        const searchForm = document.getElementById('search-form');
        const searchInput = document.getElementById('search-input');

        if (searchToggleBtn && searchForm) {
            searchToggleBtn.addEventListener('click', function() {
                searchForm.classList.toggle('hidden');
                if (!searchForm.classList.contains('hidden')) {
                    // Focus on input when shown
                    setTimeout(() => {
                        searchInput.focus();
                    }, 100);
                }
            });

            // Hide search form when clicking outside
            document.addEventListener('click', function(event) {
                if (!searchForm.contains(event.target) && !searchToggleBtn.contains(event.target)) {
                    if (!searchForm.classList.contains('hidden') && !searchInput.value) {
                        searchForm.classList.add('hidden');
                    }
                }
            });
        }

        // Filters Dropdown Toggle
        const filtersToggleBtn = document.getElementById('filters-toggle-btn');
        const filtersDropdown = document.getElementById('filters-dropdown');

        if (filtersToggleBtn && filtersDropdown) {
            filtersToggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                filtersDropdown.classList.toggle('hidden');
            });
        }

        // Close filters dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (filtersDropdown && !filtersDropdown.contains(event.target) && !filtersToggleBtn.contains(event.target)) {
                if (!filtersDropdown.classList.contains('hidden')) {
                    filtersDropdown.classList.add('hidden');
                }
            }
        });

        // Close filters dropdown with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && filtersDropdown && !filtersDropdown.classList.contains('hidden')) {
                filtersDropdown.classList.add('hidden');
            }
        });
    });
</script>
@endpush

