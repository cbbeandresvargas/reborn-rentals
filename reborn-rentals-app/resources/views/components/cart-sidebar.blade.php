<!-- Cart Sidebar (Right) -->
<div id="cart-sidebar-container" class="fixed top-0 right-0 h-screen z-10 overflow-visible">
    <div id="cart-sidebar"
        class="text-white w-full sm:w-80 lg:w-96 max-w-[85vw] bg-transparent shadow-2xl translate-x-0 transition-transform duration-300 ease-in-out h-full relative flex overflow-visible">

        <!-- CONTENIDO PRINCIPAL -->
        <div
            id="cart-sidebar-part-2"
            class="relative flex-1 p-4 sm:p-6 md:p-8 min-h-full flex flex-col bg-[#2F2F2F] border-l border-gray-600 overflow-visible">

            <!-- STEP TABS (SOBRESALIDOS) -->
            <div
                class="absolute top-1/2 -translate-y-1/2 -left-10 flex flex-col items-center z-20 gap-1.5">
                <button
                    type="button"
                    id="step-tab-1"
                    data-step="1"
                    class="step-tab w-8 h-8 flex items-center justify-center text-sm font-semibold bg-gradient-to-br from-[#CE9704] to-[#B8860B] hover:scale-110 text-white rounded-t-lg backdrop-blur-sm shadow-lg transition-all duration-200 border border-[#CE9704]">
                    1
                </button>
                <button
                    type="button"
                    id="step-tab-2"
                    data-step="2"
                    class="step-tab w-8 h-8 flex items-center justify-center text-sm font-semibold bg-gray-800/80 hover:bg-[#CE9704] hover:scale-110 text-gray-300 hover:text-white backdrop-blur-sm shadow-lg transition-all duration-200 border border-gray-700/50 hover:border-[#CE9704]">
                    2
                </button>
                <button
                    type="button"
                    id="step-tab-3"
                    data-step="3"
                    class="step-tab w-8 h-8 flex items-center justify-center text-sm font-semibold bg-gray-800/80 hover:bg-[#CE9704] hover:scale-110 text-gray-300 hover:text-white rounded-b-lg backdrop-blur-sm shadow-lg transition-all duration-200 border border-gray-700/50 hover:border-[#CE9704]">
                    3
                </button>
            </div>

            <!-- HEADER -->
            <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-300">
                <h3 class="m-0 text-[#CE9704] text-xl sm:text-2xl">
                    YOUR CART
                </h3>
                <button
                    type="button"
                    class="p-2 rounded transition hover:bg-gray-200 text-black"
                    id="close-cart">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <!-- CART ITEMS -->
            <div class="flex-1 cart-items flex flex-col overflow-y-auto text-center">
                <div>
                    <p class="text-white font-semibold italic">
                        Your cart is empty.
                    </p>
                    <p class="text-white text-sm">
                        Looks like you haven't made your choice yet.
                    </p>
                    <p class="text-white text-sm">
                        Start by adding items to your cart.
                    </p>
                </div>
            </div>

            <!-- SUBTOTAL -->
            <div class="border-t border-gray-600 pt-4 mb-4 hidden" id="subtotal-section">
                <div class="flex justify-between items-center mb-2">
                    <span class="font-semibold">Subtotal:</span>
                    <span id="subtotal-amount" class="text-[#CE9704] font-bold text-lg">$0</span>
                </div>
                <div class="flex justify-between items-center mb-3">
                    <span class="text-gray-300 text-sm">Items:</span>
                    <span id="total-items" class="text-gray-300 text-sm">0</span>
                </div>
                <p class="text-gray-300 text-xs">
                    *Additional charges for delivery, rough terrain and express orders may apply.
                </p>
            </div>

            <!-- CHECKOUT -->
            <div class="mt-auto pt-4">
                <button
                    type="button"
                    id="when-where-btn"
                    disabled
                    class="block w-full bg-gray-600 text-gray-400 py-3 rounded-lg font-bold text-lg cursor-not-allowed transition-all">
                    Proceed to Payment
                </button>
            </div>
        </div>
    </div>
</div>
