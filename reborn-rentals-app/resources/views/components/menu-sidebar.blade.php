<!-- Menu Sidebar (Left) -->
<div id="menu-sidebar" class="fixed top-0 left-0 h-screen w-full sm:w-80 lg:w-96 max-w-[85vw] bg-[#4A4A4A] shadow-2xl -translate-x-full transition-transform duration-300 ease-in-out overflow-y-auto scrollbar-hide">
    <div class="px-4 sm:px-6 py-6 sm:py-8 min-h-full flex flex-col">
        <!-- Close Button -->
        <div class="flex justify-end mb-6 shrink-0">
            <button type="button" class="bg-none border-none cursor-pointer p-2 sm:p-3 rounded-lg transition-colors duration-300 ease-in-out hover:bg-white/10 text-white" id="close-menu">
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

