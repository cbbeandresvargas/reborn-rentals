@extends('layouts.app')

@section('title', 'Reborn Rentals - Home')

@push('styles')
<style>
        /* Mobile optimizations for home page */
    @media (max-width: 640px) {
        /* Improve touch targets */
        #filters-toggle-btn,
        #search-toggle-btn {
            min-width: 44px;
            min-height: 44px;
        }
        
        /* Better scroll behavior for filters dropdown */
        #filters-dropdown {
            -webkit-overflow-scrolling: touch;
            overscroll-behavior: contain;
        }
        
        /* Smooth animations for mobile */
        #filters-dropdown {
            transition: opacity 0.2s ease, transform 0.2s ease;
        }
        
        #filters-dropdown:not(.hidden) {
            animation: slideDownMobile 0.2s ease-out;
        }
        
        @keyframes slideDownMobile {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Better spacing for search input on mobile */
        #search-form {
            margin-top: 0.5rem;
            width: 100%;
        }
        
        /* Improve product grid spacing on mobile */
        #products-grid {
            padding-bottom: env(safe-area-inset-bottom);
        }
        
        /* Ensure dropdown is visible and properly positioned on mobile */
        /* Dropdown should be below cart sidebar (z-10) but above subbar (z-8) */
        #filters-dropdown {
            position: fixed !important;
            z-index: 9 !important;
        }
        
        /* Ensure subbar stays below cart sidebar */
        .bg-\\[\\#414141\\] {
            z-index: 8 !important;
        }
    }
    
    /* Prevent horizontal scroll on mobile */
    @media (max-width: 640px) {
        body {
            overflow-x: hidden;
        }
    }
    
    /* Improve pagination on mobile */
    @media (max-width: 640px) {
        .pagination {
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .pagination a,
        .pagination span {
            min-width: 40px;
            min-height: 40px;
            padding: 0.5rem;
            font-size: 0.875rem;
        }
    }
</style>
@endpush

@section('content')
<!-- Subnavbar -->
<div class="bg-[#414141] py-3 sm:py-2 md:py-1 shadow-md sticky top-0 sm:relative z-[8]">
    <div class="max-w-7xl mx-auto px-4 sm:px-4 md:px-6">
        <div class="flex flex-row items-center gap-3 sm:gap-4 md:gap-5">
            <!-- Filters Button with Dropdown -->
            <div class="relative flex-shrink-0">
                <button type="button" id="filters-toggle-btn" class="flex items-center justify-center gap-2 bg-transparent p-2.5 sm:px-3 sm:px-4 sm:py-2 sm:py-3 rounded-lg transition-all duration-200 shrink-0 group cursor-pointer touch-manipulation active:scale-95">
                    <div class="relative w-7 h-7 sm:w-6 sm:h-6 md:w-7 md:h-7">
                        <img src="{{ asset('icons/filters.svg') }}" alt="Filters" class="absolute inset-0 w-full h-full object-contain transition-opacity duration-300 ease-in-out group-hover:opacity-0" />
                        <img src="{{ asset('icons/filters-hover.svg') }}" alt="Filters Hover" class="absolute inset-0 w-full h-full object-contain transition-opacity duration-300 ease-in-out opacity-0 group-hover:opacity-100" />
                    </div>
                </button>

                <!-- Filters Dropdown -->
                <div id="filters-dropdown" class="fixed sm:absolute top-auto sm:top-full left-4 sm:left-0 right-4 sm:right-auto mt-0 sm:mt-2 w-auto sm:w-80 max-w-none sm:max-w-none bg-[#414141] shadow-2xl rounded-lg overflow-hidden z-[9] hidden" style="max-height: calc(100vh - 150px); overflow-y: auto;">
                    <div class="p-4 sm:p-6">
                        <h3 class="text-white font-bold text-xs sm:text-sm uppercase mb-3 sm:mb-4 tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 bg-[#CE9704] rounded-full"></span>
                            PRODUCT DESCRIPTIONS
                        </h3>
                        <form id="filters-form">
                            <div class="flex flex-col gap-2.5 sm:gap-3 max-h-[calc(100vh-300px)] sm:max-h-96 overflow-y-auto">
                                @if(isset($productDescriptions) && count($productDescriptions) > 0)
                                    @foreach($productDescriptions as $description)
                                    <label class="flex items-start gap-2.5 sm:gap-3 cursor-pointer group hover:bg-[#4A4A4A] active:bg-[#4A4A4A] p-2.5 sm:p-2 rounded transition-colors touch-manipulation">
                                        <input type="checkbox" name="descriptions[]" value="{{ $description }}" {{ in_array($description, request('descriptions', [])) ? 'checked' : '' }} class="w-5 h-5 sm:w-4 sm:h-4 border-2 border-white rounded bg-transparent text-[#CE9704] focus:ring-[#CE9704] focus:ring-2 cursor-pointer mt-0.5 sm:mt-1 flex-shrink-0 touch-manipulation" />
                                        <span class="text-white text-sm sm:text-sm leading-relaxed group-hover:text-[#CE9704] transition-colors duration-300 flex-1">{{ $description }}</span>
                                    </label>
                                    @endforeach
                                @else
                                    <p class="text-gray-400 text-sm py-2">No product descriptions available</p>
                                @endif
                            </div>
                            <div class="mt-4 pt-4 border-t border-gray-600">
                                <button type="button" id="apply-filters-btn" class="w-full bg-[#CE9704] text-white font-bold py-3.5 sm:py-3 px-4 rounded-lg hover:bg-[#B8860B] active:bg-[#B8860B] transition-colors duration-200 cursor-pointer touch-manipulation active:scale-[0.98] text-base sm:text-base">
                                    Apply Filters
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Search Icon Button -->
            <button type="button" id="search-toggle-btn" class="flex items-center justify-center bg-transparent p-2.5 sm:px-3 sm:px-4 sm:py-2 sm:py-3 rounded-lg transition-all duration-200 shrink-0 touch-manipulation active:scale-95">
                <svg class="w-7 h-7 sm:w-6 sm:h-6 md:w-7 md:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </button>

            <!-- Search Bar (Hidden by default) -->
            <form method="GET" action="{{ route('home') }}" class="flex-1 w-full sm:w-auto hidden" id="search-form">
                <div class="relative">
                    <input
                        type="text"
                        name="search"
                        placeholder="Search products..."
                        value="{{ request('search') }}"
                        class="w-full pl-11 pr-4 py-3 sm:pl-10 sm:py-2 text-base sm:text-sm md:text-base border bg-white border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704] focus:border-transparent"
                        id="search-input"
                    />
                    <button type="submit" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-[#CE9704] active:text-[#CE9704] transition-colors duration-200 touch-manipulation">
                        <svg class="w-5 h-5 sm:w-4 sm:h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Products -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 lg:px-10 xl:px-14 mt-6 sm:mt-10 md:mt-14 lg:mt-20 mb-12 sm:mb-16 md:mb-20 lg:mb-24">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-5 md:gap-6 lg:gap-7 xl:gap-8" id="products-grid">
        @include('partials.products-grid', ['products' => $products])
    </div>

    @if($products->hasPages())
    <div class="mt-8 sm:mt-10 md:mt-12 lg:mt-16 px-2" id="pagination-container">
        {{ $products->links() }}
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/cart.js') }}"></script>
<script>
(function() {
    'use strict';
    
    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    function init() {
        console.log('🚀 Initializing home page scripts...');
        
        // Initialize search toggle
        initSearchToggle();
        
        // Initialize filters dropdown
        initFiltersDropdown();
        
        // Initialize form submissions
        initFormSubmissions();
        
        // Initialize pagination
        initPagination();
        
        console.log('✅ All initializations complete');
    }

    function initSearchToggle() {
        const searchToggleBtn = document.getElementById('search-toggle-btn');
        const searchForm = document.getElementById('search-form');
        const searchInput = document.getElementById('search-input');

        if (!searchToggleBtn || !searchForm) return;

        searchToggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            searchForm.classList.toggle('hidden');
            if (!searchForm.classList.contains('hidden') && searchInput) {
                setTimeout(() => searchInput.focus(), 100);
            }
        });

        // Hide search form when clicking outside
        document.addEventListener('click', function(event) {
            if (searchForm && !searchForm.contains(event.target) && !searchToggleBtn.contains(event.target)) {
                if (!searchForm.classList.contains('hidden')) {
                    // On mobile, close if input is empty. On desktop, keep it open
                    if (window.innerWidth < 640 && searchInput && !searchInput.value) {
                        searchForm.classList.add('hidden');
                    }
                }
            }
        });
    }

    function initFiltersDropdown() {
        const filtersToggleBtn = document.getElementById('filters-toggle-btn');
        const filtersDropdown = document.getElementById('filters-dropdown');

        if (!filtersToggleBtn || !filtersDropdown) {
            return;
        }

        // Toggle dropdown on button click
        filtersToggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            const isHidden = filtersDropdown.classList.contains('hidden');
            
            if (isHidden) {
                filtersDropdown.classList.remove('hidden');
                // Lock body scroll on mobile when dropdown opens
                if (window.innerWidth < 640) {
                    document.body.style.overflow = 'hidden';
                }
            } else {
                filtersDropdown.classList.add('hidden');
                // Restore body scroll on mobile when dropdown closes
                if (window.innerWidth < 640) {
                    document.body.style.overflow = '';
                }
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (filtersDropdown && 
                !filtersDropdown.contains(event.target) && 
                !filtersToggleBtn.contains(event.target)) {
                if (!filtersDropdown.classList.contains('hidden')) {
                    filtersDropdown.classList.add('hidden');
                    // Restore body scroll on mobile when dropdown closes
                    if (window.innerWidth < 640) {
                        document.body.style.overflow = '';
                    }
                }
            }
        });

        // Close dropdown with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && filtersDropdown && !filtersDropdown.classList.contains('hidden')) {
                filtersDropdown.classList.add('hidden');
                // Restore body scroll on mobile when dropdown closes
                if (window.innerWidth < 640) {
                    document.body.style.overflow = '';
                }
            }
        });
    }

    function initFormSubmissions() {
        const filtersForm = document.getElementById('filters-form');
        const searchForm = document.getElementById('search-form');
        const applyButton = document.getElementById('apply-filters-btn');

        console.log('🔧 Initializing form submissions...');
        console.log('Filters form found:', filtersForm);
        console.log('Search form found:', searchForm);
        console.log('Apply button found:', applyButton);

        // Handle Apply Filters button click
        if (applyButton) {
            console.log('✅ Adding click listener to Apply Filters button');
            applyButton.addEventListener('click', function(e) {
                console.log('🖱️ Apply Filters button clicked!');
                e.preventDefault();
                e.stopPropagation();
                applyFilters();
                return false;
            });
        } else {
            console.error('❌ Apply Filters button not found!');
        }

        // Also prevent form submission if it happens
        if (filtersForm) {
            filtersForm.addEventListener('submit', function(e) {
                console.log('🚫 Filters form submit prevented');
                e.preventDefault();
                e.stopPropagation();
                applyFilters();
                return false;
            });
        } else {
            console.error('❌ Filters form not found!');
        }

        if (searchForm) {
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();
                applyFilters();
            });
        }
    }

    function applyFilters() {
        console.log('🔍 applyFilters() called');
        const params = new URLSearchParams();
        
        // Get search value
        const searchInput = document.getElementById('search-input');
        if (searchInput && searchInput.value.trim()) {
            params.append('search', searchInput.value.trim());
            console.log('📝 Search term:', searchInput.value.trim());
        }

        // Get filter values from checkboxes
        const filterForm = document.getElementById('filters-form');
        console.log('📋 Filter form found:', filterForm);
        
        if (filterForm) {
            // Descriptions
            const descriptionCheckboxes = filterForm.querySelectorAll('input[name="descriptions[]"]:checked');
            console.log('📝 Descriptions checked:', descriptionCheckboxes.length);
            descriptionCheckboxes.forEach(cb => {
                params.append('descriptions[]', cb.value);
                console.log('  - Description:', cb.value);
            });
        } else {
            console.error('❌ Filter form not found in applyFilters()');
        }

        console.log('📊 Total params:', params.toString());

        // Show loading
        const productsGrid = document.getElementById('products-grid');
        const paginationContainer = document.getElementById('pagination-container');
        
        if (productsGrid) {
            productsGrid.innerHTML = '<div class="col-span-full text-center py-12 sm:py-16"><div class="inline-block animate-spin rounded-full h-10 w-10 sm:h-12 sm:w-12 border-b-2 border-[#CE9704]"></div><p class="text-gray-600 mt-4 text-sm sm:text-base">Loading products...</p></div>';
        }

        // Make request
        const requestUrl = '{{ route("home") }}?' + params.toString();
        
        fetch(requestUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP error! status: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.html && productsGrid) {
                productsGrid.innerHTML = data.html;
                
                // Reinicializar drag and drop y botones Add to Cart después de actualizar productos
                if (typeof setupDragAndDrop === 'function') {
                    setupDragAndDrop();
                }
                
                // Reinicializar botones Add to Cart
                document.querySelectorAll('.add-to-cart-btn:not([data-listener-set])').forEach(btn => {
                    btn.setAttribute('data-listener-set', 'true');
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        const productId = this.dataset.productId;
                        const productName = this.dataset.productName;
                        const productPrice = this.dataset.productPrice;
                        
                        if (typeof addToCart === 'function') {
                            addToCart(productId, productName, productPrice);
                        }
                    });
                });
            }
            
            if (data.pagination) {
                if (paginationContainer) {
                    paginationContainer.innerHTML = data.pagination;
                } else if (data.pagination.trim() !== '') {
                    const newPaginationContainer = document.createElement('div');
                    newPaginationContainer.className = 'mt-10 sm:mt-12 md:mt-16';
                    newPaginationContainer.id = 'pagination-container';
                    newPaginationContainer.innerHTML = data.pagination;
                    if (productsGrid && productsGrid.parentElement) {
                        productsGrid.parentElement.appendChild(newPaginationContainer);
                    }
                }
            } else {
                if (paginationContainer) {
                    paginationContainer.remove();
                }
            }

            // Scroll to products
            if (productsGrid) {
                setTimeout(() => {
                    productsGrid.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 100);
            }

            // Close filters dropdown
            const filtersDropdown = document.getElementById('filters-dropdown');
            if (filtersDropdown) {
                filtersDropdown.classList.add('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (productsGrid) {
                productsGrid.innerHTML = '<div class="col-span-full text-center py-12 sm:py-16"><p class="text-gray-600 text-base sm:text-lg px-4">Error loading products. Please refresh the page.</p></div>';
            }
        });

        // Update URL
        const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
        window.history.pushState({path: newUrl}, '', newUrl);
    }

    function initPagination() {
        document.addEventListener('click', function(e) {
            const paginationLink = e.target.closest('.pagination a');
            if (!paginationLink) return;

            e.preventDefault();
            e.stopPropagation();

            const url = paginationLink.href;
            const urlObj = new URL(url);
            const params = urlObj.searchParams;
            
            // Preserve current filters and search
            const currentParams = new URLSearchParams(window.location.search);
            const searchParam = currentParams.get('search');
            if (searchParam) {
                params.set('search', searchParam);
            }

            const descriptions = currentParams.getAll('descriptions[]');
            descriptions.forEach(desc => params.append('descriptions[]', desc));

            // Show loading
            const productsGrid = document.getElementById('products-grid');
            const paginationContainer = document.getElementById('pagination-container');
            
            if (productsGrid) {
                productsGrid.innerHTML = '<div class="col-span-full text-center py-12 sm:py-16"><div class="inline-block animate-spin rounded-full h-10 w-10 sm:h-12 sm:w-12 border-b-2 border-[#CE9704]"></div><p class="text-gray-600 mt-4 text-sm sm:text-base">Loading products...</p></div>';
            }

            // Make request
            fetch(urlObj.pathname + '?' + params.toString(), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.html && productsGrid) {
                    productsGrid.innerHTML = data.html;
                    
                    // Reinicializar drag and drop y botones Add to Cart después de actualizar productos
                    if (typeof setupDragAndDrop === 'function') {
                        setupDragAndDrop();
                    }
                    
                    // Reinicializar botones Add to Cart
                    document.querySelectorAll('.add-to-cart-btn:not([data-listener-set])').forEach(btn => {
                        btn.setAttribute('data-listener-set', 'true');
                        btn.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            const productId = this.dataset.productId;
                            const productName = this.dataset.productName;
                            const productPrice = this.dataset.productPrice;
                            
                            if (typeof addToCart === 'function') {
                                addToCart(productId, productName, productPrice);
                            }
                        });
                    });
                }
                if (data.pagination) {
                    if (paginationContainer) {
                        paginationContainer.innerHTML = data.pagination;
                    }
                } else {
                    if (paginationContainer) {
                        paginationContainer.remove();
                    }
                }

                if (productsGrid) {
                    setTimeout(() => {
                        productsGrid.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 100);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    }
})();
</script>
@endpush

