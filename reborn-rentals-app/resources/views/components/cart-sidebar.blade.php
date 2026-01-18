<!-- Cart Sidebar (Right) -->
<div id="cart-sidebar-container" class="fixed top-0 right-0 h-screen z-10 overflow-visible">
    <div id="cart-sidebar"
        class="text-white w-full sm:w-80 lg:w-96 max-w-[100vw] sm:max-w-[85vw] bg-transparent shadow-2xl translate-x-full sm:translate-x-0 transition-transform duration-300 ease-in-out h-full relative flex overflow-visible">

        <!-- CONTENIDO PRINCIPAL -->
        <div
            id="cart-sidebar-part-2"
            class="relative flex-1 p-3 sm:p-4 md:p-6 lg:p-8 min-h-full flex flex-col bg-[#2F2F2F] border-l border-gray-600 overflow-visible">

            <!-- STEP TABS - Desktop (SOBRESALIDOS) -->
            <div
                class="hidden sm:flex absolute top-1/2 -translate-y-1/2 -left-10 flex-col items-center z-20 gap-1.5"
                style="pointer-events: auto;">
                <button
                    type="button"
                    id="step-tab-1"
                    data-step="1"
                    class="step-tab w-8 h-8 flex items-center justify-center text-sm font-semibold bg-gradient-to-br from-[#CE9704] to-[#B8860B] hover:scale-110 text-white rounded-t-lg backdrop-blur-sm shadow-lg transition-all duration-200 border border-[#CE9704]"
                    style="pointer-events: auto; cursor: pointer;">
                    1
                </button>
                <button
                    type="button"
                    id="step-tab-2"
                    data-step="2"
                    class="step-tab w-8 h-8 flex items-center justify-center text-sm font-semibold bg-gray-800/80 hover:bg-[#CE9704] hover:scale-110 text-gray-300 hover:text-white backdrop-blur-sm shadow-lg transition-all duration-200 border border-gray-700/50 hover:border-[#CE9704]"
                    style="pointer-events: auto; cursor: pointer;">
                    2
                </button>
                <button
                    type="button"
                    id="step-tab-3"
                    data-step="3"
                    class="step-tab w-8 h-8 flex items-center justify-center text-sm font-semibold bg-gray-800/80 hover:bg-[#CE9704] hover:scale-110 text-gray-300 hover:text-white rounded-b-lg backdrop-blur-sm shadow-lg transition-all duration-200 border border-gray-700/50 hover:border-[#CE9704]"
                    style="pointer-events: auto; cursor: pointer;">
                    3
                </button>
            </div>

            <!-- STEP TABS - Mobile (HORIZONTAL) -->
            <div class="flex sm:hidden mb-3 pb-3 border-b border-gray-600/50 gap-2">
                <button
                    type="button"
                    id="step-tab-mobile-1"
                    data-step="1"
                    class="step-tab flex-1 py-2.5 px-3 flex items-center justify-center text-sm font-semibold bg-gradient-to-br from-[#CE9704] to-[#B8860B] text-white rounded-lg shadow-md transition-all duration-200 border border-[#CE9704] active:scale-95">
                    <span class="mr-1.5">1</span>
                    <span class="text-xs">Cart</span>
                </button>
                <button
                    type="button"
                    id="step-tab-mobile-2"
                    data-step="2"
                    class="step-tab flex-1 py-2.5 px-3 flex items-center justify-center text-sm font-semibold bg-gray-700/80 text-gray-300 rounded-lg shadow-md transition-all duration-200 border border-gray-600/50 active:scale-95">
                    <span class="mr-1.5">2</span>
                    <span class="text-xs">Details</span>
                </button>
                <button
                    type="button"
                    id="step-tab-mobile-3"
                    data-step="3"
                    class="step-tab flex-1 py-2.5 px-3 flex items-center justify-center text-sm font-semibold bg-gray-700/80 text-gray-300 rounded-lg shadow-md transition-all duration-200 border border-gray-600/50 active:scale-95">
                    <span class="mr-1.5">3</span>
                    <span class="text-xs">Checkout</span>
                </button>
            </div>

            <!-- HEADER -->
            <div class="flex justify-between items-center mb-3 sm:mb-4 pb-2 sm:pb-3 border-b border-gray-300/50 sm:border-gray-300">
                <h3 class="m-0 text-[#CE9704] text-lg sm:text-xl md:text-2xl font-bold">
                    YOUR CART
                </h3>
                <button
                    type="button"
                    class="p-2.5 sm:p-2 rounded-lg sm:rounded transition-all active:scale-95 hover:bg-gray-700/50 sm:hover:bg-gray-200 text-white sm:text-black touch-manipulation"
                    id="close-cart"
                    aria-label="Close cart"
                    style="pointer-events: auto; cursor: pointer;">
                    <svg width="22" height="22" class="sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <!-- CART ITEMS -->
            <div class="flex-1 cart-items flex flex-col overflow-y-auto text-center min-h-0 -mx-3 sm:mx-0 px-3 sm:px-0" style="pointer-events: auto;">
                <!-- Contenido renderizado dinámicamente por JavaScript -->
            </div>

            <!-- SUBTOTAL -->
            <div class="border-t border-gray-600 pt-3 sm:pt-4 mb-3 sm:mb-4 hidden" id="subtotal-section">
                <div class="flex justify-between items-center mb-2">
                    <span class="font-semibold text-sm sm:text-base">Subtotal:</span>
                    <span id="subtotal-amount" class="text-[#CE9704] font-bold text-lg sm:text-xl">$0</span>
                </div>
                <div class="flex justify-between items-center mb-2 sm:mb-3">
                    <span class="text-gray-300 text-xs sm:text-sm">Items:</span>
                    <span id="total-items" class="text-gray-300 text-xs sm:text-sm font-medium">0</span>
                </div>
                <p class="text-gray-400 text-[10px] sm:text-xs leading-relaxed">
                    *Additional charges for delivery, rough terrain and express orders may apply.
                </p>
            </div>

            <!-- CHECKOUT -->
            <div class="mt-auto pt-3 sm:pt-4 pb-2 sm:pb-0">
                <button
                    type="button"
                    id="when-where-btn"
                    disabled
                    class="block w-full bg-gray-600 text-gray-400 py-3.5 sm:py-3 px-4 rounded-xl sm:rounded-lg font-bold text-base sm:text-lg cursor-not-allowed transition-all active:scale-[0.98] touch-manipulation shadow-lg disabled:opacity-60">
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Continue to Delivery
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
