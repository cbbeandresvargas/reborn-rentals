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
            
            <!-- Right Side: Auth Links / Menu & Cart -->
            <div class="flex items-center gap-2 md:gap-4 relative z-10">
                @auth
                    <span class="text-white text-xs sm:text-sm hidden sm:block">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">
                        @csrf
                        <button type="submit" class="text-white text-xs sm:text-sm hover:text-[#CE9704] transition-colors cursor-pointer">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-white text-xs sm:text-sm hover:text-[#CE9704] transition-colors px-2 py-1 cursor-pointer relative z-10">Login</a>
                @endauth
                
                <!-- Menu Button -->
                <button type="button" class="bg-none border-none text-white cursor-pointer p-2 rounded-lg transition-all duration-300 ease-in-out flex items-center justify-center hover:bg-white/10 hover:scale-105 active:scale-95 focus:outline-none focus:ring-2 focus:ring-white/20 relative z-10" id="menu-btn" aria-label="Menu">
                    <svg width="20" height="20" class="sm:w-5 sm:h-5 md:w-6 md:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
                
                <!-- Cart Button -->
                <button type="button" class="bg-none border-none text-white cursor-pointer p-2 rounded-lg transition-all duration-300 ease-in-out flex items-center justify-center hover:bg-white/10 hover:scale-105 active:scale-95 focus:outline-none focus:ring-2 focus:ring-white/20 relative z-10" id="cart-btn" aria-label="Carrito">
                    <svg width="20" height="20" class="sm:w-5 sm:h-5 md:w-6 md:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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

