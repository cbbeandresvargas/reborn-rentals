<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Reborn Rentals')</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#CE9704',
                        'primary-dark': '#B8860B',
                        'bg-dark': '#4A4A4A',
                        'bg-cart': '#2F2F2F',
                        'bg-light': '#BBBBBB',
                    }
                }
            }
        }
    </script>
    
    <style>
        /* Hide scrollbar but keep functionality */
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
    </style>
    
    @stack('styles')
</head>
<body class="m-0 w-full h-full font-sans">
    <!-- Navbar -->
    <nav class="bg-[#4A4A4A] py-4 md:py-6 shadow-lg sticky top-0 z-30">
        <div class="max-w-6xl mx-auto px-4 md:px-8">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <div class="shrink-0">
                    <a href="{{ route('home') }}" class="block">
                        <img src="{{ asset('Logo.png') }}" alt="Reborn Rentals" class="h-8 md:h-10 w-auto object-contain" />
                    </a>
                </div>
                
                <!-- Right Side: Auth Links / Menu & Cart -->
                <div class="flex items-center gap-2 md:gap-4">
                    @auth
                        <span class="text-white text-sm hidden md:block">{{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}" class="hidden md:block">
                            @csrf
                            <button type="submit" class="text-white text-sm hover:text-[#CE9704] transition-colors">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-white text-sm hidden md:block hover:text-[#CE9704] transition-colors">Login</a>
                        <a href="{{ route('register') }}" class="text-white text-sm hidden md:block hover:text-[#CE9704] transition-colors">Register</a>
                    @endauth
                    
                    <!-- Menu Button -->
                    <button class="bg-none border-none text-white cursor-pointer p-2 rounded-lg transition-all duration-300 ease-in-out flex items-center justify-center hover:bg-white/10 hover:scale-105 active:scale-95 focus:outline-none focus:ring-2 focus:ring-white/20" id="menu-btn" aria-label="Menu">
                        <svg width="20" height="20" class="md:w-6 md:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>
                    
                    <!-- Cart Button -->
                    <button class="bg-none border-none text-white cursor-pointer p-2 rounded-lg transition-all duration-300 ease-in-out flex items-center justify-center hover:bg-white/10 hover:scale-105 active:scale-95 focus:outline-none focus:ring-2 focus:ring-white/20 relative" id="cart-btn" aria-label="Carrito">
                        <svg width="20" height="20" class="md:w-6 md:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="m1 1 4 4 13 1 2 8H6l-2-8z"></path>
                        </svg>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 items-center justify-center hidden" id="cart-badge">0</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Menu Sidebar (Left) -->
    <div id="menu-sidebar" class="fixed top-0 left-0 h-screen w-full sm:w-80 lg:w-96 max-w-[90vw] bg-[#4A4A4A] shadow-2xl -translate-x-full transition-transform duration-300 ease-in-out z-50 overflow-y-auto scrollbar-hide">
        <div class="px-6 py-8 min-h-full flex flex-col">
            <!-- Close Button -->
            <div class="flex justify-end mb-6 shrink-0">
                <button class="bg-none border-none cursor-pointer p-3 rounded-lg transition-colors duration-300 ease-in-out hover:bg-white/10 text-white" id="close-menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <!-- Logo Section -->
            <div class="flex justify-center mb-10 shrink-0">
                <img src="{{ asset('Logo.png') }}" alt="Reborn Rentals" class="h-20 w-auto object-contain" />
            </div>

            <!-- Navigation Sections -->
            <div class="flex flex-col gap-10 flex-1">
                <!-- About Us Section -->
                <div class="flex gap-6">
                    <div class="w-2 h-36 bg-white rounded-sm shrink-0"></div>
                    <div class="flex-1">
                        <h3 class="text-gray-300 font-bold text-sm uppercase mb-5 tracking-wider">About Us</h3>
                        <nav class="flex flex-col gap-4">
                            <a href="{{ route('faq') }}" class="text-[#CE9704] no-underline text-sm hover:text-white transition-colors duration-300 py-1">FAQ</a>
                            <a href="{{ route('about') }}" class="text-[#CE9704] no-underline text-sm hover:text-white transition-colors duration-300 py-1">About Us</a>
                            <a href="https://grb-group.com/en/" class="text-[#CE9704] no-underline text-sm hover:text-white transition-colors duration-300 py-1" target="_blank">Corporate</a>
                        </nav>
                    </div>
                </div>

                <!-- General Section -->
                <div class="flex gap-6">
                    <div class="w-2 h-36 bg-[#CE9704] rounded-sm shrink-0"></div>
                    <div class="flex-1">
                        <h3 class="text-gray-300 font-bold text-sm uppercase mb-5 tracking-wider">General</h3>
                        <nav class="flex flex-col gap-4">
                            <a href="{{ route('sitemap') }}" class="text-[#CE9704] no-underline text-sm hover:text-white transition-colors duration-300 py-1">Site Map</a>
                            <a href="{{ route('blog') }}" class="text-[#CE9704] no-underline text-sm hover:text-white transition-colors duration-300 py-1">Blog</a>
                            <a href="https://grb-group.com/en/open-opportunities/" class="text-[#CE9704] no-underline text-sm hover:text-white transition-colors duration-300 py-1" target="_blank">Careers</a>
                        </nav>
                    </div>
                </div>

                <!-- Legal Section -->
                <div class="flex gap-6">
                    <div class="w-2 h-36 bg-white rounded-sm shrink-0"></div>
                    <div class="flex-1">
                        <h3 class="text-gray-300 font-bold text-sm uppercase mb-5 tracking-wider">Legal</h3>
                        <nav class="flex flex-col gap-4">
                            <a href="{{ route('terms') }}" class="text-[#CE9704] no-underline text-sm hover:text-white transition-colors duration-300 py-1">Terms & Conditions</a>
                            <a href="{{ route('privacy') }}" class="text-[#CE9704] no-underline text-sm hover:text-white transition-colors duration-300 py-1">Privacy Policy</a>
                            <a href="{{ route('fees') }}" class="text-[#CE9704] no-underline text-sm hover:text-white transition-colors duration-300 py-1">Fees & Surcharges</a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="pt-6 shrink-0">
                <p class="text-white text-xs text-center leading-relaxed">© 2025 Reborn Rentals, All Rights Reserved.</p>
            </div>
        </div>
    </div>

    <!-- Cart Sidebar (Right) -->
    <div id="cart-sidebar" class="fixed top-0 right-0 h-screen text-white w-full sm:w-80 lg:w-96 max-w-[90vw] bg-[#2F2F2F] shadow-2xl translate-x-full transition-transform duration-300 ease-in-out z-30 overflow-y-auto scrollbar-hide">
        <div class="p-8 min-h-full flex flex-col">
            <div class="flex justify-between items-center mb-8 pb-4 border-b border-gray-300">
                <h3 class="m-0 text-[#CE9704] text-2xl">YOUR CART</h3>
                <button class="bg-none border-none cursor-pointer p-2 rounded transition-colors duration-300 ease-in-out hover:bg-gray-200" id="close-cart">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            
            <!-- Cart Items -->
            <div class="flex-1 cart-items">
                <div class="flex flex-col items-center justify-center h-full">
                    <p class="text-white font-semibold italic text-center">Your cart is empty.</p>
                    <p class="text-white text-sm text-center">Looks like you haven't made your choice yet.</p>
                    <p class="text-white text-sm text-center">Start by adding items to your cart.</p>
                </div>
            </div>
            
            <!-- Subtotal Section -->
            <div class="border-t border-gray-600 pt-4 mb-4 hidden" id="subtotal-section">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-white font-semibold">Subtotal:</span>
                    <span class="text-[#CE9704] font-bold text-lg" id="subtotal-amount">$0</span>
                </div>
                <div class="flex justify-between items-center mb-3">
                    <span class="text-gray-300 text-sm">Items:</span>
                    <span class="text-gray-300 text-sm" id="total-items">0</span>
                </div>
                
                <!-- Terms and Conditions -->
                <div class="text-gray-300 text-xs space-y-1">
                    <p class="font-medium">*Additional charges for delivery, rough terrain and express orders may apply.</p>
                </div>
            </div>
            
            <!-- Proceed Button -->
            <div class="mt-auto pt-4">
                <button type="button" class="block w-full bg-gray-600 text-gray-400 py-3 px-4 rounded-lg font-bold text-lg text-center cursor-not-allowed transition-all duration-300" id="when-where-btn" disabled>
                    Proceed to Payment
                </button>
            </div>
        </div>
    </div>

    <!-- Overlay -->
    <div id="sidebar-overlay" class="fixed top-0 left-0 w-full h-full bg-black/50 z-40 opacity-0 invisible transition-all duration-300 ease-in-out"></div>

    <!-- Main Content -->
    <main class="min-h-[calc(100vh-72px)] md:min-h-[calc(100vh-96px)] transition-all duration-300 ease-in-out" id="main-content">
        @yield('content')
    </main>

    <!-- Scripts -->
    <script>
        // Load cart on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Wait for cart.js to load
            setTimeout(() => {
                // Update cart display
                if (typeof updateCartDisplay === 'function') {
                    updateCartDisplay();
                }
                // Setup drag and drop
                if (typeof setupDragAndDrop === 'function') {
                    setupDragAndDrop();
                }
            }, 100);
            
            // Sidebar functionality
            const menuBtn = document.getElementById('menu-btn');
            const cartBtn = document.getElementById('cart-btn');
            const menuSidebar = document.getElementById('menu-sidebar');
            const cartSidebar = document.getElementById('cart-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const closeMenuBtn = document.getElementById('close-menu');
            const closeCartBtn = document.getElementById('close-cart');

            // Open menu sidebar
            if (menuBtn && menuSidebar) {
                menuBtn.addEventListener('click', function() {
                    menuSidebar.classList.remove('-translate-x-full');
                    menuSidebar.classList.add('translate-x-0');
                    overlay.classList.remove('opacity-0', 'invisible');
                    overlay.classList.add('opacity-100', 'visible');
                    document.body.style.overflow = 'hidden';
                });
            }

            // Open cart sidebar
            if (cartBtn && cartSidebar) {
                cartBtn.addEventListener('click', function() {
                    cartSidebar.classList.remove('translate-x-full');
                    cartSidebar.classList.add('translate-x-0');
                });
            }

            // Close menu sidebar
            if (closeMenuBtn && menuSidebar) {
                closeMenuBtn.addEventListener('click', function() {
                    menuSidebar.classList.remove('translate-x-0');
                    menuSidebar.classList.add('-translate-x-full');
                    overlay.classList.remove('opacity-100', 'visible');
                    overlay.classList.add('opacity-0', 'invisible');
                    document.body.style.overflow = '';
                });
            }

            // Close cart sidebar
            if (closeCartBtn && cartSidebar) {
                closeCartBtn.addEventListener('click', function() {
                    cartSidebar.classList.remove('translate-x-0');
                    cartSidebar.classList.add('translate-x-full');
                });
            }

            // Close menu sidebar when clicking overlay
            if (overlay && menuSidebar) {
                overlay.addEventListener('click', function() {
                    menuSidebar.classList.remove('translate-x-0');
                    menuSidebar.classList.add('-translate-x-full');
                    overlay.classList.remove('opacity-100', 'visible');
                    overlay.classList.add('opacity-0', 'invisible');
                    document.body.style.overflow = '';
                });
            }

            // Close menu sidebar with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    if (menuSidebar) {
                        menuSidebar.classList.remove('translate-x-0');
                        menuSidebar.classList.add('-translate-x-full');
                    }
                    if (overlay) {
                        overlay.classList.remove('opacity-100', 'visible');
                        overlay.classList.add('opacity-0', 'invisible');
                    }
                    document.body.style.overflow = '';
                }
            });
            
            // Proceed to Payment button - redirect to directions page
            // Only add this listener if we're NOT on the directions page
            // (directions page has its own listener that validates the form first)
            if (!window.location.pathname.includes('/directions')) {
                const whenWhereBtn = document.getElementById('when-where-btn');
                if (whenWhereBtn) {
                    whenWhereBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        if (!this.disabled) {
                            // Redirect to directions page
                            window.location.href = '{{ route("directions") }}';
                        }
                    });
                }
            }
        });

        // CSRF Token for AJAX
        window.axios = {
            defaults: {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }
        };
    </script>
    
    <!-- Cart JS -->
    <script src="{{ asset('js/cart.js') }}"></script>
    
    @stack('scripts')
</body>
</html>

