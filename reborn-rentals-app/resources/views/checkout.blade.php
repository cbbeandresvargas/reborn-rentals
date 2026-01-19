@extends('layouts.app')

@section('title', 'Checkout Summary - Reborn Rentals')

@section('content')
<!-- Step 3: Checkout Summary -->
<div class="max-w-6xl mx-auto px-4 sm:px-6 md:px-8 lg:px-12 mt-4 sm:mt-8 md:mt-12 lg:mt-20 mb-8 sm:mb-12 md:mb-16 lg:mb-20">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden p-4 sm:p-6 md:p-8 lg:p-10">
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Rental Request Summary</h1>
            <p class="text-sm sm:text-base text-gray-600">Review your rental items and submit your request</p>
        </div>
        
        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
        @endif
        
        <form method="POST" action="{{ route('checkout.store') }}">
            @csrf
            <!-- Checkout Content -->
            <div>
                <!-- Rental Period Section -->
                <div class="p-4 sm:p-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg mb-4">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4">Rental Period</h2>
                    <div id="rental-period-info" class="space-y-2">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#CE9704]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-700">Start Date:</span>
                                <span class="text-base font-semibold text-gray-900" id="display-start-date">Loading...</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#CE9704]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-700">End Date:</span>
                                <span class="text-base font-semibold text-gray-900" id="display-end-date">Loading...</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 pt-2 border-t border-gray-200">
                            <svg class="w-5 h-5 text-[#CE9704]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700">Total Rental Period:</span>
                            <span class="text-lg font-bold text-[#CE9704]" id="display-rental-days">Calculating...</span>
                        </div>
                    </div>
                </div>

                <!-- Items Section -->
                <div class="p-4 sm:p-6 border-b border-gray-200">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-3 sm:mb-4">Items: {{ count($cart) }}</h2>
                    
                    <!-- Items List -->
                    <div class="space-y-3 sm:space-y-4">
                        @foreach($cart as $productId => $quantity)
                            @php $product = $products->get($productId); @endphp
                            @if($product)
                            <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-4 p-3 sm:p-4 bg-gray-50 rounded-lg" 
                                 data-product-id="{{ $productId }}"
                                 data-price="{{ $product->price }}"
                                 data-quantity="{{ $quantity }}">
                                <!-- Product Image -->
                                <div class="shrink-0">
                                    <div class="w-16 h-16 bg-white rounded-lg p-2 flex items-center justify-center shadow-sm">
                                        @if($product->image_url)
                                            <img src="{{ asset($product->image_url) }}" alt="{{ $product->name }}" class="w-full h-full object-contain">
                                        @else
                                            <div class="w-full h-full bg-linear-to-br from-yellow-400 to-orange-500 rounded-lg"></div>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Product Details -->
                                <div class="flex-1 min-w-0 w-full sm:w-auto">
                                    <h4 class="text-base sm:text-lg font-semibold text-gray-900 truncate">{{ $product->name }}</h4>
                                    <p class="text-xs sm:text-sm text-gray-600">{{ $product->capacity ?? 'N/A' }}</p>
                                    <div class="flex flex-wrap items-center gap-2 sm:gap-4 mt-2">
                                        <span class="text-[#CE9704] font-semibold text-sm">ID: {{ $product->sku ?? $productId }}</span>
                                        <span class="text-gray-500 text-sm">{{ $quantity }}pc</span>
                                        <span class="text-gray-500 text-sm">${{ number_format($product->price, 2) }} / Day</span>
                                        
                                        <!-- Days Selector -->
                                        <div class="flex items-center space-x-2">
                                            <button type="button" onclick="adjustDays('{{ $productId }}', -1)" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-2 py-1 rounded text-xs font-medium transition-colors duration-200">−</button>
                                            <span class="text-[#CE9704] font-semibold text-sm min-w-[30px] text-center" id="days-{{ $productId }}">30 Days</span>
                                            <button type="button" onclick="adjustDays('{{ $productId }}', 1)" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-2 py-1 rounded text-xs font-medium transition-colors duration-200">+</button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Item Total -->
                                <div class="text-left sm:text-right w-full sm:w-auto">
                                    <div class="text-lg sm:text-xl font-bold text-gray-900" id="item-total-{{ $productId }}">${{ number_format($product->price * $quantity * 30, 2) }}</div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Applied Discount (hidden by default, shown when coupon is applied) -->
                <!-- Note: Discounts are calculated and applied in Odoo, not here -->
                <div id="applied-discount" class="p-4 sm:p-6 border-b border-gray-200 hidden">
                    <div class="flex items-center justify-between bg-blue-50 p-3 rounded-lg">
                        <div>
                            <span class="text-blue-800 font-semibold" id="discount-name">BIGPANS101</span>
                            <span class="text-blue-600 ml-2 text-sm" id="discount-type">(Will be applied in Odoo)</span>
                        </div>
                        <span class="text-blue-800 font-bold text-sm">Applied</span>
                    </div>
                </div>

                <!-- Delivery Fees Section -->
                <div id="delivery-fees-section" class="p-4 sm:p-6 border-b border-gray-200 hidden">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-3 sm:mb-4">Delivery & Pickup Fees</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700 text-sm sm:text-base">Delivery & Pickup (Combined):</span>
                            <span class="text-gray-900 font-bold text-base sm:text-lg" id="total-delivery-fees">$0.00</span>
                        </div>
                        <div id="delivery-distance-info" class="text-xs sm:text-sm text-gray-500 italic hidden"></div>
                    </div>
                </div>

                <!-- Total Section -->
                <div class="p-4 sm:p-6 bg-gray-50">
                    <div class="space-y-3 sm:space-y-4">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 sm:gap-0">
                            <h3 class="text-lg sm:text-xl font-bold text-gray-900">Subtotal:</h3>
                            <div class="text-xl sm:text-2xl font-bold text-gray-900" id="subtotal-amount">${{ number_format($total, 2) }}</div>
                        </div>
                        
                        <div id="delivery-fees-total-row" class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 sm:gap-0 hidden">
                            <h3 class="text-lg sm:text-xl font-bold text-gray-900">Delivery & Pickup Fees:</h3>
                            <div class="text-xl sm:text-2xl font-bold text-gray-900" id="delivery-fees-total-display">$0.00</div>
                        </div>
                        
                        <div class="border-t border-gray-300 pt-3 sm:pt-4">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 sm:gap-0">
                                <h3 class="text-xl sm:text-2xl font-bold text-gray-900">Total:</h3>
                                <div class="text-2xl sm:text-3xl font-bold text-[#CE9704]" id="grand-total">${{ number_format($total, 2) }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Message about invoice -->
                    <div class="text-center text-gray-600 text-sm sm:text-base py-4 border-t border-gray-200 mt-4">
                        <p>An invoice with final amounts will be sent to you via email after your request is processed.</p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@include('components.success-modal')

<script>
// Open cart sidebar automatically when on checkout page (only on desktop/tablet, not mobile)
setTimeout(() => {
    const cartSidebar = document.getElementById('cart-sidebar');
    
    if (cartSidebar) {
        // Only open sidebar on desktop/tablet (>= 640px), keep closed on mobile
        if (window.innerWidth >= 640) {
            cartSidebar.classList.remove('translate-x-full');
            cartSidebar.classList.add('translate-x-0');
            
            // Show step indicator
            const stepIndicatorContainer = document.getElementById('step-indicator-container');
            if (stepIndicatorContainer) {
                stepIndicatorContainer.style.display = 'block';
            }
        } else {
            // On mobile, ensure sidebar stays closed
            cartSidebar.classList.remove('translate-x-0');
            cartSidebar.classList.add('translate-x-full');
        }
        
        // Hide cart header and proceed button on checkout page
        const cartHeader = document.getElementById('cart-header');
        const proceedButtonContainer = document.getElementById('proceed-button-container');
        if (cartHeader) cartHeader.style.display = 'none';
        if (proceedButtonContainer) proceedButtonContainer.style.display = 'none';
    }
}, 500);

// ==================== CHECKOUT SUMMARY ====================
console.log('%c====================================', 'color: #CE9704; font-weight: bold; font-size: 14px;');
console.log('%c  CHECKOUT SUMMARY - COMPLETE ORDER', 'color: #CE9704; font-weight: bold; font-size: 16px;');
console.log('%c====================================', 'color: #CE9704; font-weight: bold; font-size: 14px;');

// Productos del carrito
console.log('%c📦 PRODUCTOS EN EL CARRITO:', 'color: #2563eb; font-weight: bold; font-size: 14px;');
@foreach($cart as $productId => $quantity)
    @php $product = $products->get($productId); @endphp
    @if($product)
    console.log(`
    Producto: {{ $product->name }}
    ID: {{ $product->sku ?? $productId }}
    Cantidad: {{ $quantity }}
    Precio unitario: ${{ number_format($product->price, 2) }}
    Subtotal: ${{ number_format($product->price * $quantity, 2) }}
    Días de alquiler: 30
    ---
    `);
    @endif
@endforeach

// Dirección de entrega desde localStorage
const directionsData = localStorage.getItem('reborn-rentals-directions');
if (directionsData) {
    const directions = JSON.parse(directionsData);
    console.log('%c📍 INFORMACIÓN DE ENTREGA:', 'color: #059669; font-weight: bold; font-size: 14px;');
    console.log(`
    Fecha de inicio: ${directions.startDate || 'No seleccionada'}
    Fecha de fin: ${directions.endDate || 'No seleccionada'}
    Dirección: ${directions.jobsiteAddress || 'No especificada'}
    Opción de recogida: ${directions.pickupOption || 'No especificada'}
    ---
    `);
} else {
    console.log('%c📍 INFORMACIÓN DE ENTREGA:', 'color: #dc2626; font-weight: bold; font-size: 14px;');
    console.log('⚠️ No se encontró información de direcciones guardada');
}

// Resumen de totales
console.log('%c💰 RESUMEN DE TOTALES:', 'color: #CE9704; font-weight: bold; font-size: 14px;');
console.log(`
Subtotal: ${{ number_format($total, 2) }}
Nota: Impuestos y pagos se manejan en Odoo
---`);

console.log('%c====================================', 'color: #CE9704; font-weight: bold; font-size: 14px;');
console.log('');

// Display rental period information
function displayRentalPeriod() {
    const directionsData = localStorage.getItem('reborn-rentals-directions');
    if (directionsData) {
        try {
            const directions = JSON.parse(directionsData);
            const startDateEl = document.getElementById('display-start-date');
            const endDateEl = document.getElementById('display-end-date');
            const rentalDaysEl = document.getElementById('display-rental-days');
            
            if (directions.startDate && directions.endDate) {
                // Format dates
                const startDate = new Date(directions.startDate);
                const endDate = new Date(directions.endDate);
                
                // Format to readable date
                const startFormatted = startDate.toLocaleDateString('en-US', { 
                    weekday: 'short', 
                    year: 'numeric', 
                    month: 'short', 
                    day: 'numeric' 
                });
                const endFormatted = endDate.toLocaleDateString('en-US', { 
                    weekday: 'short', 
                    year: 'numeric', 
                    month: 'short', 
                    day: 'numeric' 
                });
                
                // Calculate days difference
                const timeDiff = endDate.getTime() - startDate.getTime();
                const daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1; // +1 to include both start and end days
                
                if (startDateEl) startDateEl.textContent = startFormatted;
                if (endDateEl) endDateEl.textContent = endFormatted;
                if (rentalDaysEl) {
                    rentalDaysEl.textContent = `${daysDiff} day${daysDiff !== 1 ? 's' : ''}`;
                }
            } else {
                if (startDateEl) startDateEl.textContent = 'Not selected';
                if (endDateEl) endDateEl.textContent = 'Not selected';
                if (rentalDaysEl) rentalDaysEl.textContent = 'N/A';
            }
        } catch (e) {
            console.error('Error parsing directions data:', e);
            const startDateEl = document.getElementById('display-start-date');
            const endDateEl = document.getElementById('display-end-date');
            const rentalDaysEl = document.getElementById('display-rental-days');
            if (startDateEl) startDateEl.textContent = 'Error loading';
            if (endDateEl) endDateEl.textContent = 'Error loading';
            if (rentalDaysEl) rentalDaysEl.textContent = 'N/A';
        }
    } else {
        const startDateEl = document.getElementById('display-start-date');
        const endDateEl = document.getElementById('display-end-date');
        const rentalDaysEl = document.getElementById('display-rental-days');
        if (startDateEl) startDateEl.textContent = 'Not available';
        if (endDateEl) endDateEl.textContent = 'Not available';
        if (rentalDaysEl) rentalDaysEl.textContent = 'N/A';
    }
}

// Calculate and display delivery fees
function calculateAndDisplayDeliveryFees() {
    const directionsData = localStorage.getItem('reborn-rentals-directions');
    if (!directionsData) {
        hideDeliveryFees();
        return;
    }
    
    try {
        const directions = JSON.parse(directionsData);
        const lat = directions.latitude;
        const lon = directions.longitude;
        const selfPickup = directions.selfPickupChecked || false;
        
        if (!lat || !lon) {
            hideDeliveryFees();
            return;
        }
        
        // Calculate fees via AJAX
        fetch('/checkout/calculate-fees', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                latitude: lat,
                longitude: lon,
                is_self_pickup: selfPickup
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.fees) {
                displayDeliveryFees(data.fees);
                updateGrandTotalWithFees(data.fees.total_fees);
            } else {
                hideDeliveryFees();
            }
        })
        .catch(error => {
            console.error('Error calculating delivery fees:', error);
            hideDeliveryFees();
        });
    } catch (e) {
        console.error('Error parsing directions data:', e);
        hideDeliveryFees();
    }
}

// Display delivery fees
function displayDeliveryFees(fees) {
    const deliverySection = document.getElementById('delivery-fees-section');
    const totalDeliveryFees = document.getElementById('total-delivery-fees');
    const deliveryFeesTotalRow = document.getElementById('delivery-fees-total-row');
    const deliveryFeesTotalDisplay = document.getElementById('delivery-fees-total-display');
    const distanceInfo = document.getElementById('delivery-distance-info');
    
    if (deliverySection) {
        deliverySection.classList.remove('hidden');
    }
    
    // Display total fees (delivery + pickup combined)
    if (totalDeliveryFees) {
        totalDeliveryFees.textContent = '$' + parseFloat(fees.total_fees || 0).toFixed(2);
    }
    
    if (deliveryFeesTotalRow) {
        deliveryFeesTotalRow.classList.remove('hidden');
    }
    
    if (deliveryFeesTotalDisplay) {
        deliveryFeesTotalDisplay.textContent = '$' + parseFloat(fees.total_fees || 0).toFixed(2);
    }
    
    // Show distance info if available
    if (distanceInfo && fees.distance_miles > 0) {
        distanceInfo.textContent = `Distance: ${fees.distance_miles.toFixed(1)} miles (${fees.calculation_method === 'flat_rate_inside_loop' ? 'Metropolitan Area' : 'Outside Metropolitan Area'})`;
        distanceInfo.classList.remove('hidden');
    } else if (distanceInfo) {
        distanceInfo.classList.add('hidden');
    }
}

// Hide delivery fees section
function hideDeliveryFees() {
    const deliverySection = document.getElementById('delivery-fees-section');
    const deliveryFeesTotalRow = document.getElementById('delivery-fees-total-row');
    
    if (deliverySection) {
        deliverySection.classList.add('hidden');
    }
    
    if (deliveryFeesTotalRow) {
        deliveryFeesTotalRow.classList.add('hidden');
    }
    
    // Reset total to subtotal only
    updateGrandTotal();
}

// Update grand total including delivery fees
function updateGrandTotalWithFees(deliveryFees) {
    // Store delivery fees globally
    currentDeliveryFees = parseFloat(deliveryFees) || 0;
    
    // Recalculate grand total (which will include the fees)
    updateGrandTotal();
}

// Calculate initial total correctly when page loads (with 30 days)
document.addEventListener('DOMContentLoaded', function() {
    // Display rental period information
    displayRentalPeriod();
    
    // Calculate and display delivery fees
    calculateAndDisplayDeliveryFees();
    
    // Wait for sidebar to be rendered before checking coupon status
    setTimeout(function() {
        // Clear any old coupon from localStorage on page load
        // The coupon should only be applied when user explicitly applies it
        const couponInput = document.getElementById('sidebar-coupon-code');
        const applyCouponBtn = document.getElementById('sidebar-apply-coupon');
        
        // If coupon input is not disabled, clear localStorage (no coupon applied in this session)
        if (couponInput && !couponInput.disabled) {
            localStorage.removeItem('applied_coupon');
            // Hide discount section if it's visible
            const discountSection = document.getElementById('applied-discount');
            if (discountSection) {
                discountSection.classList.add('hidden');
            }
        }
    }, 600); // Wait for sidebar to render
    
    // Calculate and update total (without discount initially)
    setTimeout(function() {
        updateGrandTotal();
    }, 600);
});

// Función para ajustar días (mínimo 30 días)
function adjustDays(productId, change) {
    const daysElement = document.getElementById('days-' + productId);
    const itemTotalElement = document.getElementById('item-total-' + productId);
    
    if (daysElement && itemTotalElement) {
        let currentDays = parseInt(daysElement.textContent.replace(' Days', ''));
        const newDays = Math.max(30, currentDays + change); // Mínimo 30 días
        daysElement.textContent = newDays + ' Days';
        
        // Get product data
        const productElement = document.querySelector(`[data-product-id="${productId}"]`);
        if (productElement) {
            const price = parseFloat(productElement.getAttribute('data-price'));
            const quantity = parseFloat(productElement.getAttribute('data-quantity'));
            
            // Calculate new item total
            const itemTotal = price * quantity * newDays;
            itemTotalElement.textContent = '$' + itemTotal.toFixed(2);
            
            // Recalculate grand total
            updateGrandTotal();
        }
    }
}

// Update grand total after day changes
// Store delivery fees globally
let currentDeliveryFees = 0;

function updateGrandTotal() {
    // First calculate subtotal
    let subtotal = 0;
    
    // Sum all item totals
    document.querySelectorAll('[data-product-id]').forEach(element => {
        const productId = element.getAttribute('data-product-id');
        const itemTotalElement = document.getElementById('item-total-' + productId);
        if (itemTotalElement) {
            // Remove commas and $ sign, then parse
            const itemTotalText = itemTotalElement.textContent.replace('$', '').replace(/,/g, '').trim();
            const itemTotal = parseFloat(itemTotalText) || 0;
            subtotal += itemTotal;
        }
    });
    
    // Update subtotal display
    const subtotalElement = document.getElementById('subtotal-amount');
    if (subtotalElement) {
        subtotalElement.textContent = '$' + subtotal.toFixed(2);
    }
    
    // Note: Coupon codes can be entered but discounts are NOT calculated here
    // All discount calculations and applications are handled in Odoo
    // The coupon code will be sent to backend for reference only
    
    // Calculate grand total: subtotal + delivery fees
    // Note: This is an estimate only. Final totals, taxes, and discounts are calculated in Odoo.
    const grandTotal = subtotal + currentDeliveryFees;
    
    // Update UI
    const grandTotalElement = document.getElementById('grand-total');
    
    if (grandTotalElement) {
        grandTotalElement.textContent = '$' + grandTotal.toFixed(2);
    }
}

// Submit checkout form with all data
let isSubmittingCheckout = false; // Flag to prevent double submission

function submitCheckoutForm() {
    console.log('submitCheckoutForm() called');
    
    // Prevent double submission
    if (isSubmittingCheckout) {
        console.log('Checkout already in progress, ignoring duplicate call');
        return;
    }
    
    isSubmittingCheckout = true;
    
    // Disable checkout button to prevent multiple clicks
    const checkoutBtn = document.getElementById('sidebar-checkout-btn');
    // Store original text in a way that's accessible throughout the function
    const originalText = checkoutBtn ? (checkoutBtn.textContent || 'Submit Rental Request') : 'Submit Rental Request';
    
    // Helper function to restore button state
    const restoreButton = function() {
        const btn = document.getElementById('sidebar-checkout-btn');
        if (btn) {
            btn.disabled = false;
            btn.style.opacity = '1';
            btn.style.cursor = 'pointer';
            btn.textContent = originalText;
        }
    };
    
    if (checkoutBtn) {
        checkoutBtn.disabled = true;
        checkoutBtn.style.opacity = '0.5';
        checkoutBtn.style.cursor = 'not-allowed';
        checkoutBtn.textContent = 'Processing...';
    }
    
    // ====================================================================
    // VALIDATION: Only validate directions, billing, and cart data
    // Payment data is NOT required - payments are handled in Odoo
    // ====================================================================
    
    // 1. VALIDATE CART DATA - Ensure cart is not empty
    const productElements = document.querySelectorAll('[data-product-id]');
    if (!productElements || productElements.length === 0) {
        if (typeof toast !== 'undefined') {
            toast.error('Your cart is empty. Please add items to your cart first.');
        } else {
            alert('Your cart is empty. Please add items to your cart first.');
        }
        console.error('Cart is empty');
        isSubmittingCheckout = false;
        restoreButton();
        return;
    }
    
    // 2. VALIDATE DIRECTIONS DATA
    const directionsData = localStorage.getItem('reborn-rentals-directions');
    console.log('Directions data:', directionsData);
    
    if (!directionsData) {
        if (typeof toast !== 'undefined') {
            toast.error('Please provide delivery information first.');
        } else {
            alert('Please provide delivery information first.');
        }
        console.error('No directions data found');
        isSubmittingCheckout = false;
        restoreButton();
        return;
    }
    
    let directions;
    try {
        directions = JSON.parse(directionsData);
    } catch (e) {
        if (typeof toast !== 'undefined') {
            toast.error('Invalid delivery information. Please try again.');
        } else {
            alert('Invalid delivery information. Please try again.');
        }
        isSubmittingCheckout = false;
        restoreButton();
        return;
    }
    
    // Validate required direction fields
    if (!directions.startDate || !directions.endDate || !directions.jobsiteAddress) {
        if (typeof toast !== 'undefined') {
            toast.error('Please complete all required delivery information.');
        } else {
            alert('Please complete all required delivery information.');
        }
        isSubmittingCheckout = false;
        restoreButton();
        return;
    }
    
    // 3. VALIDATE BILLING DATA
    const billingData = localStorage.getItem('billing-details');
    if (!billingData) {
        if (typeof toast !== 'undefined') {
            toast.error('Please complete billing details first.');
        } else {
            alert('Please complete billing details first.');
        }
        console.error('No billing details found');
        isSubmittingCheckout = false;
        restoreButton();
        return;
    }
    
    let billingDetails;
    try {
        billingDetails = JSON.parse(billingData);
        console.log('Billing details found:', billingDetails);
        
        // Validate required billing fields
        if (!billingDetails.firstName || !billingDetails.lastName || !billingDetails.email) {
            if (typeof toast !== 'undefined') {
                toast.error('Please complete all required billing information (First Name, Last Name, Email).');
            } else {
                alert('Please complete all required billing information (First Name, Last Name, Email).');
            }
            isSubmittingCheckout = false;
            restoreButton();
            return;
        }
    } catch (e) {
        if (typeof toast !== 'undefined') {
            toast.error('Invalid billing information. Please try again.');
        } else {
            alert('Invalid billing information. Please try again.');
        }
        console.error('Error parsing billing details:', e);
        isSubmittingCheckout = false;
        restoreButton();
        return;
    }
    
    // Note: Payment data is NOT validated - payments are handled in Odoo
    // This website only collects rental requests
    
    // Get coupon code if applied (optional)
    let couponCode = '';
    const appliedCoupon = localStorage.getItem('applied_coupon');
    if (appliedCoupon) {
        try {
            const coupon = JSON.parse(appliedCoupon);
            couponCode = coupon.code || '';
        } catch (e) {
            console.error('Error parsing coupon:', e);
        }
    }
    
    // Get Foreman Details - if empty, use billing details for name and email
    let foremanDetails = {};
    const foremanData = localStorage.getItem('foreman-details');
    if (foremanData) {
        try {
            foremanDetails = JSON.parse(foremanData);
            console.log('Foreman details found:', foremanDetails);
        } catch (e) {
            console.error('Error parsing foreman details:', e);
        }
    }
    
    // If foreman details are empty or missing name/email, use billing details
    if (!foremanDetails || Object.keys(foremanDetails).length === 0 || 
        (!foremanDetails.firstName && !foremanDetails.lastName && !foremanDetails.email)) {
        if (billingDetails && Object.keys(billingDetails).length > 0) {
            foremanDetails = {
                firstName: billingDetails.firstName || '',
                lastName: billingDetails.lastName || '',
                email: billingDetails.email || '',
                phone: foremanDetails.phone || billingDetails.phone || ''
            };
            console.log('Using billing details for foreman:', foremanDetails);
        }
    }
    
    // Get rental days for each product and calculate totals
    // Try multiple selectors to find the form
    let form = document.querySelector('form[action*="checkout"]');
    if (!form) {
        form = document.querySelector('form[method="POST"]');
    }
    if (!form) {
        form = document.querySelector('form');
    }
    console.log('Form found:', form);
    
    if (!form) {
        if (typeof toast !== 'undefined') {
            toast.error('Checkout form not found.');
        } else {
            alert('Checkout form not found.');
        }
        console.error('Form not found in DOM');
        isSubmittingCheckout = false;
        restoreButton();
        return;
    }
    
    // Collect all product days data (minimum 30 days)
    const productDays = {};
    document.querySelectorAll('[data-product-id]').forEach(element => {
        const productId = element.getAttribute('data-product-id');
        const daysElement = document.getElementById('days-' + productId);
        if (daysElement) {
            const days = parseInt(daysElement.textContent.replace(' Days', ''));
            productDays[productId] = Math.max(30, days || 30); // Ensure minimum 30 days
        }
    });
    console.log('Product days:', productDays);
    
    // Store product days in hidden input for backend processing
    let productDaysInput = document.getElementById('product-days-data');
    if (!productDaysInput) {
        productDaysInput = document.createElement('input');
        productDaysInput.type = 'hidden';
        productDaysInput.id = 'product-days-data';
        productDaysInput.name = 'product_days';
        form.appendChild(productDaysInput);
    }
    productDaysInput.value = JSON.stringify(productDays);
    
    // Generate unique submission token to prevent duplicate orders
    const submissionToken = 'checkout_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    addHiddenField(form, '_submission_token', submissionToken);
    
    // Add all required form fields
    addHiddenField(form, 'start_date', directions.startDate);
    addHiddenField(form, 'end_date', directions.endDate);
    addHiddenField(form, 'jobsite_address', directions.jobsiteAddress);
    addHiddenField(form, 'latitude', directions.latitude || '');
    addHiddenField(form, 'longitude', directions.longitude || '');
    addHiddenField(form, 'notes', directions.notes || '');
    addHiddenField(form, 'cupon_code', couponCode);
    
    // Add self-pickup flag
    const selfPickup = directions.selfPickupChecked || false;
    addHiddenField(form, 'is_self_pickup', selfPickup ? '1' : '0');
    
    // Add Foreman Details as JSON
    addHiddenField(form, 'foreman_details', JSON.stringify(foremanDetails));
    
    // Add Billing Details as JSON
    addHiddenField(form, 'billing_details', JSON.stringify(billingDetails));
    
    console.log('Form data prepared, submitting via AJAX...');
    console.log('Form action:', form.action);
    console.log('Form method:', form.method);
    
    // Collect form data
    const formData = new FormData(form);
    
    // Submit via AJAX to show success modal
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(async response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', {
            'content-type': response.headers.get('content-type'),
            'location': response.headers.get('location'),
        });
        
        // Try to parse as JSON first
        const contentType = response.headers.get('content-type') || '';
        let data;
        
        if (contentType.includes('application/json')) {
            try {
                data = await response.json();
                console.log('Response data (JSON):', data);
            } catch (e) {
                console.error('Error parsing JSON response:', e);
                throw new Error('Invalid response from server');
            }
        } else {
            // If not JSON, try to get text to see what we got
            const text = await response.text();
            console.error('Non-JSON response received:', text.substring(0, 200));
            throw new Error('Server returned non-JSON response. Please check server logs.');
        }
        
        // Handle response data
        if (data.success) {
            console.log('✅ Order created successfully!', {
                order_id: data.order_id,
                redirect_url: data.redirect_url,
            });
            
            // Clear all localStorage data
            localStorage.removeItem('reborn-rentals-directions');
            localStorage.removeItem('foreman-details');
            localStorage.removeItem('billing-details');
            localStorage.removeItem('applied_coupon');
            
            // Clear cart from session
            if (typeof clearCart === 'function') {
                clearCart();
            }
            
            // Show success modal
            if (typeof openSuccessModal === 'function') {
                openSuccessModal();
                // Redirect after showing modal
                setTimeout(() => {
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        window.location.href = form.action.replace('/checkout', '/orders/' + data.order_id);
                    }
                }, 2000);
            } else {
                // Fallback: redirect to order page
                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else if (data.order_id) {
                    window.location.href = '/orders/' + data.order_id;
                } else {
                    window.location.href = form.action.replace('/checkout', '/orders');
                }
            }
        } else {
            // Re-enable button on error
            isSubmittingCheckout = false;
            restoreButton();
            
            const errorMessage = data.message || data.error || 'Error creating order. Please try again.';
            console.error('Order creation failed:', errorMessage);
            throw new Error(errorMessage);
        }
    })
    .catch(error => {
        console.error('❌ Error submitting order:', error);
        console.error('Error details:', {
            message: error.message,
            stack: error.stack,
        });
        
        // Re-enable button on error
        isSubmittingCheckout = false;
        restoreButton();
        
        // Show error message
        const errorMessage = error.message || 'Error creating order. Please try again.';
        if (typeof toast !== 'undefined') {
            toast.error(errorMessage);
        } else {
            alert(errorMessage);
        }
        
        // Log error for debugging
        console.error('Full error object:', error);
    })
    .finally(() => {
        // Reset flag after a delay to allow for redirect
        setTimeout(() => {
            isSubmittingCheckout = false;
        }, 2000);
    });
}

// Helper function to add hidden fields
function addHiddenField(form, name, value) {
    let field = form.querySelector(`input[name="${name}"]`);
    if (!field) {
        field = document.createElement('input');
        field.type = 'hidden';
        field.name = name;
        form.appendChild(field);
    }
    field.value = value;
}


// Make functions globally accessible
window.submitCheckoutForm = submitCheckoutForm;
window.adjustDays = adjustDays;
window.updateGrandTotal = updateGrandTotal;
</script>
@endsection

