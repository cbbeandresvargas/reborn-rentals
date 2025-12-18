<!-- Menu Sidebar (Left) -->
<div id="menu-sidebar" class="fixed top-0 left-0 h-screen w-full sm:w-80 lg:w-96 max-w-[85vw] bg-[#2f2f2f] shadow-2xl -translate-x-full transition-transform duration-300 ease-in-out overflow-y-auto scrollbar-hide">
    <div class="px-4 sm:px-6 py-6 sm:py-8 min-h-full flex flex-col">
        
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-10 shrink-0">
            <div class="flex justify-start">
                <img src="{{ asset('Logo.png') }}" alt="Reborn Rentals" class="h-12 w-auto object-contain" />
            </div>
            <button type="button" class="bg-none border-none cursor-pointer p-2 sm:p-3 rounded-lg transition-colors duration-300 ease-in-out hover:bg-white/10 text-white" id="close-menu">
                <img src="{{ asset('icons/close.svg') }}" alt="Close" class="w-6 h-6 sm:w-7 sm:h-7 object-contain" />
            </button>
        </div>

        <!-- Navigation Content -->
        <div class="flex flex-col gap-8 flex-1">
            
            <!-- About Reborn Rentals -->
            <div>
                <h3 class="text-white font-bold text-sm uppercase mb-4 tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 bg-[#CE9704] rounded-full"></span>
                    ABOUT REBORN RENTALS
                </h3>
                <nav class="flex flex-col gap-3 ml-4">
                    <a href="{{ route('about') }}" class="text-white no-underline text-sm hover:text-[#CE9704] transition-colors duration-300 py-1 block w-full text-left">About Us</a>
                    <a href="{{ route('faq') }}" class="text-white no-underline text-sm hover:text-[#CE9704] transition-colors duration-300 py-1 block w-full text-left">FAQ</a>
                    <a href="https://grb-group.com/en/" class="text-white no-underline text-sm hover:text-[#CE9704] transition-colors duration-300 py-1 block w-full text-left" target="_blank" rel="noopener noreferrer">Corporate</a>
                </nav>
        </div>

            <!-- Explore -->
            <div>
                <h3 class="text-white font-bold text-sm uppercase mb-4 tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 bg-[#CE9704] rounded-full"></span>
                    EXPLORE
                </h3>
                <nav class="flex flex-col gap-3 ml-4">
                    <a href="{{ route('sitemap') }}" class="text-white no-underline text-sm hover:text-[#CE9704] transition-colors duration-300 py-1 block w-full text-left">Site Map</a>
                    <a href="{{ route('blog') }}" class="text-white no-underline text-sm hover:text-[#CE9704] transition-colors duration-300 py-1 block w-full text-left">Blog</a>
                    <a href="https://grb-group.com/en/open-opportunities/" id="careers-link" class="text-white no-underline text-sm hover:text-[#CE9704] transition-colors duration-300 py-1 block w-full text-left" target="_blank" rel="noopener noreferrer">Careers</a>
                    </nav>
            </div>

            <!-- Legal & Policies -->
            <div>
                <h3 class="text-white font-bold text-sm uppercase mb-4 tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 bg-[#CE9704] rounded-full"></span>
                    LEGAL & POLICIES
                </h3>
                <nav class="flex flex-col gap-3 ml-4">
                    <a href="{{ route('terms') }}" id="terms-link" class="text-white no-underline text-sm hover:text-[#CE9704] transition-colors duration-300 py-1 block w-full text-left">Terms & Conditions</a>
                    <a href="{{ route('privacy') }}" id="privacy-link" class="text-white no-underline text-sm hover:text-[#CE9704] transition-colors duration-300 py-1 block w-full text-left">Privacy Policy</a>
                    <a href="{{ route('fees') }}" id="fees-link" class="text-white no-underline text-sm hover:text-[#CE9704] transition-colors duration-300 py-1 block w-full text-left">Fees & Surcharges</a>
                    <a href="{{ route('sms') }}" id="sms-link" class="text-white no-underline text-sm hover:text-[#CE9704] transition-colors duration-300 py-1 block w-full text-left">SMS Policy</a>
                    </nav>
            </div>

        </div>

        <!-- Footer Section -->
        <div class="mt-auto pt-6 pb-6 shrink-0 border-t border-gray-600">
            
            <!-- Social Media Icons -->
            <div class="flex justify-start gap-4 mb-6">
                <a href="https://www.facebook.com/rebornrentals/" target="_blank" rel="noopener noreferrer" id="facebook-link" class="w-10 h-10 bg-black border border-white rounded flex items-center justify-center hover:bg-gray-800 transition-all duration-300 transform hover:scale-110 cursor-pointer">
                    <span class="text-white font-bold text-lg pointer-events-none">f</span>
                </a>
                <a href="https://www.instagram.com/reborn_rentals/" target="_blank" rel="noopener noreferrer" id="instagram-link" class="w-10 h-10 bg-black border border-white rounded flex items-center justify-center hover:bg-gray-800 transition-all duration-300 transform hover:scale-110 cursor-pointer">
                    <svg class="w-5 h-5 text-white pointer-events-none" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                </a>
                <a href="https://www.linkedin.com/company/reborn-rental/" target="_blank" rel="noopener noreferrer" id="linkedin-link" class="w-10 h-10 bg-black border border-white rounded flex items-center justify-center hover:bg-gray-800 transition-all duration-300 transform hover:scale-110 cursor-pointer">
                    <span class="text-white font-bold text-sm pointer-events-none">in</span>
                </a>
            </div>

            <!-- Payment Cards -->
            <div class="flex justify-start mb-6">
                <img src="{{ asset('icons/cards.svg') }}" alt="Payment Methods" class="max-w-full h-auto" />
            </div>

            <!-- Copyright -->
            <div class="text-left">
                <p class="text-white text-xs leading-relaxed">© 2025 Reborn Rentals,<br />All Rights Reserved.</p>
            </div>

        </div>

    </div>
</div>
