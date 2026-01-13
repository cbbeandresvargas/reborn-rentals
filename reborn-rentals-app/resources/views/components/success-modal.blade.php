<!-- Success Modal -->
<div id="success-modal" class="fixed inset-0 bg-black bg-opacity-70 backdrop-blur-sm z-50 flex items-center justify-center p-3 sm:p-4 transition-opacity duration-300 overflow-y-auto" style="display: none;">
    <div class="bg-gradient-to-b from-[#333333] to-[#2a2a2a] rounded-2xl max-w-lg w-full max-h-[95vh] overflow-y-auto mx-2 sm:mx-4 relative shadow-2xl border-2 border-[#CE9704] transform transition-all duration-300 my-4 sm:my-0" id="success-modal-content" style="background: linear-gradient(to bottom, #333333, #2a2a2a) !important; opacity: 1 !important;">
        <!-- Decorative Top Border -->
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-[#CE9704] via-[#FFD700] to-[#CE9704] rounded-t-2xl"></div>
        
        <!-- Close Button -->
        <div class="absolute top-5 right-5 z-10">
            <button onclick="closeSuccessModal()" class="text-white hover:text-[#CE9704] p-2 rounded-full hover:bg-white/10 transition-all duration-200 transform hover:scale-110">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <!-- Content Container -->
        <div class="p-6 sm:p-8 md:p-10 lg:p-12">
            <!-- Success Icon with Animation -->
            <div class="flex justify-center mb-4 sm:mb-6">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-[#CE9704] rounded-full flex items-center justify-center shadow-lg transform transition-all duration-500 hover:scale-110">
                    <svg class="w-10 h-10 sm:w-12 sm:h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
            
            <!-- Logo -->
            <div class="flex justify-center mb-6 sm:mb-8">
                <img src="{{ asset('Logo.png') }}" alt="Reborn Rental" class="h-16 sm:h-20 w-auto object-contain drop-shadow-lg" />
            </div>
            
            <!-- Success Message -->
            <div class="text-center mb-6 sm:mb-8">
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-white mb-2 sm:mb-3 drop-shadow-lg">
                    Success<span class="text-[#CE9704] animate-pulse">!!!</span>
                </h1>
                <p class="text-lg sm:text-xl md:text-2xl font-bold text-white mb-4 sm:mb-6 leading-tight">
                    Your Reservation is in place!<br />you're all set!
                </p>
            </div>
            
            <!-- Divider -->
            <div class="w-24 h-0.5 bg-gradient-to-r from-transparent via-[#CE9704] to-transparent mx-auto mb-8"></div>
            
            <!-- Details Message -->
            <div class="text-center text-gray-300 mb-8 space-y-3 leading-relaxed">
                <p class="text-base md:text-lg">Your rental request has been submitted. An invoice will be sent to you via Odoo after processing.</p>
                <p class="text-base md:text-lg font-medium text-white">Thank you for choosing RebornRental as your Rental Partner.</p>
            </div>
            
            <!-- Social Media Section -->
            <div class="text-center mb-8">
                <p class="text-white font-semibold mb-5 tracking-wider text-sm uppercase">Follow Us On</p>
                <div class="flex justify-center gap-5">
                    <!-- Facebook -->
                    <a href="https://www.facebook.com/rebornrentals/" target="_blank" rel="noopener noreferrer" class="w-14 h-14 bg-black border-2 border-[#CE9704] rounded-full flex items-center justify-center hover:bg-[#CE9704] hover:border-white transition-all duration-300 transform hover:scale-110 hover:shadow-lg hover:shadow-[#CE9704]/50">
                        <span class="text-white font-bold text-xl">f</span>
                    </a>
                    <!-- Instagram -->
                    <a href="https://www.instagram.com/reborn_rentals/" target="_blank" rel="noopener noreferrer" class="w-14 h-14 bg-black border-2 border-[#CE9704] rounded-full flex items-center justify-center hover:bg-[#CE9704] hover:border-white transition-all duration-300 transform hover:scale-110 hover:shadow-lg hover:shadow-[#CE9704]/50">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </a>
                    <!-- LinkedIn -->
                    <a href="https://www.linkedin.com/company/reborn-rental/" target="_blank" rel="noopener noreferrer" class="w-14 h-14 bg-black border-2 border-[#CE9704] rounded-full flex items-center justify-center hover:bg-[#CE9704] hover:border-white transition-all duration-300 transform hover:scale-110 hover:shadow-lg hover:shadow-[#CE9704]/50">
                        <span class="text-white font-bold text-base">in</span>
                    </a>
                </div>
            </div>
            
            <!-- Go to Homepage Button -->
            <div class="w-full mt-10">
                <button onclick="goToHomepage()" class="w-full bg-gradient-to-r from-[#CE9704] to-[#B8860B] text-white py-4 px-6 rounded-xl font-bold text-lg shadow-lg hover:shadow-xl hover:shadow-[#CE9704]/50 transform hover:scale-[1.02] active:scale-[0.98] transition-all duration-300">
                    Go to Homepage
                </button>
            </div>
        </div>
    </div>
</div>
