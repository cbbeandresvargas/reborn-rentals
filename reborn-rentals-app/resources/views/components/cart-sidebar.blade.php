<!-- Cart Sidebar (Right) -->
<div id="cart-sidebar-container" class="fixed top-0 right-0 h-screen overflow-visible z-10">
    <div id="cart-sidebar" class="text-white w-full sm:w-80 lg:w-96 max-w-[85vw] bg-[#2F2F2F] shadow-2xl translate-x-0 transition-transform duration-300 ease-in-out overflow-y-auto scrollbar-hide h-full relative" style="overflow-x: visible;">
   
    
    <div class="p-4 sm:p-6 md:p-8 min-h-full flex flex-col">
        <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-300" id="cart-header">
            <h3 class="m-0 text-[#CE9704] text-xl sm:text-2xl">YOUR CART</h3>
            <button type="button" class="bg-none border-none cursor-pointer p-2 rounded transition-colors duration-300 ease-in-out hover:bg-gray-200" id="close-cart">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        
        <!-- Step Tabs -->
        <div class="flex gap-3 mb-6 pb-4 border-b border-gray-600">
            <button type="button" class="step-tab flex-1 flex flex-col items-center justify-center py-3 px-2 rounded-xl text-xs font-bold transition-all duration-300 bg-gradient-to-br from-[#CE9704] to-[#B8860B] text-white shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 border-2 border-[#CE9704]" data-step="1" id="step-tab-1">
                <span class="text-2xl font-black mb-1 leading-none">1</span>
                <span class="text-[10px] uppercase tracking-wider">Cart</span>
            </button>
            <button type="button" class="step-tab flex-1 flex flex-col items-center justify-center py-3 px-2 rounded-xl text-xs font-bold transition-all duration-300 bg-gray-700 text-gray-300 hover:bg-gray-600 hover:text-white hover:shadow-lg hover:scale-105 active:scale-95 border-2 border-gray-600 hover:border-gray-500" data-step="2" id="step-tab-2">
                <span class="text-2xl font-black mb-1 leading-none">2</span>
                <span class="text-[10px] uppercase tracking-wider">Directions</span>
            </button>
            <button type="button" class="step-tab flex-1 flex flex-col items-center justify-center py-3 px-2 rounded-xl text-xs font-bold transition-all duration-300 bg-gray-700 text-gray-300 hover:bg-gray-600 hover:text-white hover:shadow-lg hover:scale-105 active:scale-95 border-2 border-gray-600 hover:border-gray-500" data-step="3" id="step-tab-3">
                <span class="text-2xl font-black mb-1 leading-none">3</span>
                <span class="text-[10px] uppercase tracking-wider">Checkout</span>
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
        <div class="mt-auto pt-4" id="proceed-button-container">
            <button type="button" class="block w-full bg-gray-600 text-gray-400 py-3 px-4 rounded-lg font-bold text-lg text-center cursor-not-allowed transition-all duration-300" id="when-where-btn" disabled>
                Proceed to Payment
            </button>
        </div>
    </div>
    </div>
</div>

