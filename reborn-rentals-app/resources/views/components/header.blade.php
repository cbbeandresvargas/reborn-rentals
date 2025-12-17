<!-- Navbar -->
<nav class="bg-[#4A4A4A] py-3 sm:py-4 md:py-6 shadow-lg relative">
    <div class="max-w-6xl mx-auto px-3 sm:px-4 md:px-8">
        <div class="flex justify-between items-center relative">
            <!-- Logo -->
            <div class="shrink-0 z-10 relative">
                <a href="{{ route('home') }}" class="block">
                    <img src="{{ asset('Logo.png') }}" alt="Reborn Rentals" class="h-7 sm:h-8 md:h-10 w-auto object-contain" />
                </a>
            </div>
            
            <!-- Right Side: Cart & Menu -->
            <div class="flex items-center gap-2 md:gap-4 relative z-10">
                <!-- Cart Button -->
                <button type="button" class="bg-none border-none text-white cursor-pointer p-2 rounded-lg transition-all duration-300 ease-in-out flex items-center justify-center hover:bg-white/10 hover:scale-105 active:scale-95 focus:outline-none focus:ring-2 focus:ring-white/20 relative z-10 group" id="cart-btn" aria-label="Carrito">
                    <div class="relative w-7 h-7 sm:w-8 sm:h-8 md:w-9 md:h-9">
                        <img src="{{ asset('icons/cart.svg') }}" alt="Cart" class="absolute inset-0 w-full h-full object-contain transition-opacity duration-300 ease-in-out group-hover:opacity-0" />
                        <img src="{{ asset('icons/cart-hover.svg') }}" alt="Cart Hover" class="absolute inset-0 w-full h-full object-contain transition-opacity duration-300 ease-in-out opacity-0 group-hover:opacity-100" />
                    </div>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 items-center justify-center hidden" id="cart-badge">0</span>
                        </button>
                
                <!-- Menu Button -->
                <button type="button" class="bg-none border-none text-white cursor-pointer p-2 rounded-lg transition-all duration-300 ease-in-out flex items-center justify-center hover:bg-white/10 hover:scale-105 active:scale-95 focus:outline-none focus:ring-2 focus:ring-white/20 relative z-10 group" id="menu-btn" aria-label="Menu">
                    <div class="relative w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8">
                        <img src="{{ asset('icons/burger.svg') }}" alt="Menu" class="absolute inset-0 w-full h-full object-contain transition-opacity duration-300 ease-in-out group-hover:opacity-0" />
                        <img src="{{ asset('icons/burger-hover.svg') }}" alt="Menu Hover" class="absolute inset-0 w-full h-full object-contain transition-opacity duration-300 ease-in-out opacity-0 group-hover:opacity-100" />
                    </div>
                </button>
            </div>
        </div>
    </div>
</nav>

