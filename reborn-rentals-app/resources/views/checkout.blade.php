@extends('layouts.app')

@section('title', 'Checkout Summary - Reborn Rentals')

@section('content')
<main class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-6">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Checkout Summary</h1>
            <p class="text-gray-600">Review your rental items and complete your order</p>
        </div>

        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
        @endif

        <form method="POST" action="{{ route('checkout.store') }}">
            @csrf
            
            <!-- Checkout Content -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Items Section -->
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Items: {{ count($cart) }}</h2>
                    
                    <!-- Items List -->
                    <div class="space-y-4">
                        @foreach($cart as $productId => $quantity)
                            @php $product = $products->get($productId); @endphp
                            @if($product)
                            <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg" 
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
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-lg font-semibold text-gray-900 truncate">{{ $product->name }}</h4>
                                    <p class="text-sm text-gray-600">{{ $product->capacity ?? 'N/A' }}</p>
                                    <div class="flex items-center space-x-4 mt-2">
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
                                <div class="text-right">
                                    <div class="text-xl font-bold text-gray-900" id="item-total-{{ $productId }}">${{ number_format($product->price * $quantity * 30, 2) }}</div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Applied Discount (hidden by default, shown when coupon is applied) -->
                <div id="applied-discount" class="p-6 border-b border-gray-200 hidden">
                    <div class="flex items-center justify-between bg-green-50 p-3 rounded-lg">
                        <div>
                            <span class="text-green-800 font-semibold" id="discount-name">BIGPANS101</span>
                            <span class="text-green-600 ml-2" id="discount-type">-20% OFF</span>
                        </div>
                        <span class="text-green-800 font-bold" id="discount-amount">-$0.00</span>
                    </div>
                </div>

                <!-- Sales Tax Section -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Sales Tax</h3>
                            <p class="text-sm text-gray-600">Based on your location</p>
                        </div>
                        <div class="text-right">
                            <span class="text-[#CE9704] font-semibold">2% Tax</span>
                            <div class="text-lg font-bold text-gray-900" id="sales-tax">${{ number_format($total * 0.02, 2) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Total Section -->
                <div class="p-6 bg-gray-50">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-2xl font-bold text-gray-900">Total:</h3>
                        <div class="text-3xl font-bold text-gray-900" id="grand-total">${{ number_format($total * 1.02, 2) }}</div>
                    </div>
                    
                    <!-- Message about verification -->
                    <div class="text-center text-gray-600 text-sm py-4">
                        <p>Your order will be completed automatically after payment verification.</p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>

<script>
// Open cart sidebar automatically when on checkout page
setTimeout(() => {
    const cartSidebar = document.getElementById('cart-sidebar');
    
    if (cartSidebar) {
        cartSidebar.classList.remove('translate-x-full');
        cartSidebar.classList.add('translate-x-0');
        
        // Show step indicator
        const stepIndicatorContainer = document.getElementById('step-indicator-container');
        if (stepIndicatorContainer) {
            stepIndicatorContainer.style.display = 'block';
        }
        
        // Adjust main content margin
        const mainContent = document.getElementById('main-content');
        if (mainContent) {
            mainContent.classList.add('cart-open');
            if (window.innerWidth >= 1024) {
                mainContent.style.marginRight = '384px';
            } else if (window.innerWidth >= 640) {
                mainContent.style.marginRight = '320px';
            }
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
Impuesto (2%): ${{ number_format($total * 0.02, 2) }}
TOTAL A PAGAR: ${{ number_format($total * 1.02, 2) }}
---`);

console.log('%c====================================', 'color: #CE9704; font-weight: bold; font-size: 14px;');
console.log('');

// Calculate initial total correctly when page loads (with 30 days)
document.addEventListener('DOMContentLoaded', function() {
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

// Función para ajustar días
function adjustDays(productId, change) {
    const daysElement = document.getElementById('days-' + productId);
    const itemTotalElement = document.getElementById('item-total-' + productId);
    
    if (daysElement && itemTotalElement) {
        let currentDays = parseInt(daysElement.textContent.replace(' Days', ''));
        const newDays = Math.max(1, currentDays + change);
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
function updateGrandTotal() {
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
    
    // Check if there's an applied coupon - only if coupon input is disabled (meaning it was applied in this session)
    let discountAmount = 0;
    const couponInput = document.getElementById('sidebar-coupon-code');
    const applyCouponBtn = document.getElementById('sidebar-apply-coupon');
    
    // Only apply discount if coupon input is disabled (meaning a coupon was successfully applied)
    if (couponInput && couponInput.disabled && applyCouponBtn && applyCouponBtn.textContent === 'Applied') {
        const appliedCoupon = localStorage.getItem('applied_coupon');
        if (appliedCoupon) {
            try {
                const coupon = JSON.parse(appliedCoupon);
                discountAmount = parseFloat(coupon.discount_amount) || 0;
            } catch (e) {
                console.error('Error parsing applied coupon:', e);
            }
        }
    }
    
    // Calculate subtotal after discount
    const subtotalAfterDiscount = subtotal - discountAmount;
    
    // Calculate sales tax (2% on subtotal after discount)
    const salesTax = subtotalAfterDiscount * 0.02;
    const grandTotal = subtotalAfterDiscount + salesTax;
    
    // Update UI
    const salesTaxElement = document.getElementById('sales-tax');
    const grandTotalElement = document.getElementById('grand-total');
    
    if (salesTaxElement) {
        salesTaxElement.textContent = '$' + salesTax.toFixed(2);
    }
    if (grandTotalElement) {
        grandTotalElement.textContent = '$' + grandTotal.toFixed(2);
    }
}

// Submit checkout form with all data
function submitCheckoutForm() {
    console.log('submitCheckoutForm() called');
    
    // Note: Payment verification is checked server-side in CheckoutController
    // The modal should have been opened and code verified before reaching here
    
    // Get directions data
    const directionsData = localStorage.getItem('reborn-rentals-directions');
    console.log('Directions data:', directionsData);
    
    if (!directionsData) {
        alert('Please provide delivery information first.');
        console.error('No directions data found');
        return;
    }
    
    let directions;
    try {
        directions = JSON.parse(directionsData);
    } catch (e) {
        alert('Invalid delivery information. Please try again.');
        return;
    }
    
    // Validate required fields
    if (!directions.startDate || !directions.endDate || !directions.jobsiteAddress) {
        alert('Please complete all required delivery information.');
        return;
    }
    
    // Get payment method
    const paymentMethod = localStorage.getItem('payment-method');
    console.log('Payment method:', paymentMethod);
    
    if (!paymentMethod) {
        alert('Please select a payment method.');
        console.error('No payment method found');
        return;
    }
    
    // Get coupon code if applied
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
    
    // Get Foreman Details
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
    
    // Get Billing Details
    let billingDetails = {};
    const billingData = localStorage.getItem('billing-details');
    if (billingData) {
        try {
            billingDetails = JSON.parse(billingData);
            console.log('Billing details found:', billingDetails);
        } catch (e) {
            console.error('Error parsing billing details:', e);
        }
    }
    
    // Get Payment Method Details
    let paymentMethodDetails = {};
    const paymentDetailsData = localStorage.getItem('payment-method-details');
    if (paymentDetailsData) {
        try {
            paymentMethodDetails = JSON.parse(paymentDetailsData);
            console.log('Payment method details found:', paymentMethodDetails);
        } catch (e) {
            console.error('Error parsing payment method details:', e);
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
        alert('Checkout form not found.');
        console.error('Form not found in DOM');
        return;
    }
    
    // Collect all product days data
    const productDays = {};
    document.querySelectorAll('[data-product-id]').forEach(element => {
        const productId = element.getAttribute('data-product-id');
        const daysElement = document.getElementById('days-' + productId);
        if (daysElement) {
            const days = parseInt(daysElement.textContent.replace(' Days', ''));
            productDays[productId] = days || 30;
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
    
    // Add all required form fields
    addHiddenField(form, 'start_date', directions.startDate);
    addHiddenField(form, 'end_date', directions.endDate);
    addHiddenField(form, 'jobsite_address', directions.jobsiteAddress);
    addHiddenField(form, 'latitude', directions.latitude || '');
    addHiddenField(form, 'longitude', directions.longitude || '');
    addHiddenField(form, 'notes', directions.notes || '');
    addHiddenField(form, 'cupon_code', couponCode);
    addHiddenField(form, 'payment_method', getPaymentMethodId(paymentMethod));
    
    // Add Foreman Details as JSON
    addHiddenField(form, 'foreman_details', JSON.stringify(foremanDetails));
    
    // Add Billing Details as JSON
    addHiddenField(form, 'billing_details', JSON.stringify(billingDetails));
    
    // Add Payment Method Details as JSON
    addHiddenField(form, 'payment_method_details', JSON.stringify(paymentMethodDetails));
    
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
    .then(response => {
        console.log('Response status:', response.status);
        
        // Check if response is JSON (success) or redirect
        if (response.headers.get('content-type')?.includes('application/json')) {
            return response.json().then(data => {
                if (data.success) {
                    console.log('Order created successfully, opening success modal...');
                    
                    // Clear all localStorage data
                    localStorage.removeItem('reborn-rentals-directions');
                    localStorage.removeItem('foreman-details');
                    localStorage.removeItem('billing-details');
                    localStorage.removeItem('payment-method');
                    localStorage.removeItem('payment-method-details');
                    localStorage.removeItem('applied_coupon');
                    
                    // Close verification modal if open
                    if (typeof closeVerificationModal === 'function') {
                        closeVerificationModal();
                    }
                    
                    // Show success modal
                    if (typeof openSuccessModal === 'function') {
                        openSuccessModal();
                    } else {
                        // Fallback: redirect to order page
                        window.location.href = data.redirect_url || form.action.replace('/checkout', '/orders');
                    }
                } else {
                    throw new Error(data.message || 'Error creating order');
                }
            });
        } else if (response.redirected || response.ok) {
            // Handle redirect response (fallback)
            console.log('Order created successfully, redirecting...');
            window.location.href = response.url || form.action.replace('/checkout', '/orders');
        } else {
            // Handle errors
            return response.json().then(data => {
                throw new Error(data.message || 'Error creating order');
            }).catch(() => {
                throw new Error('Error creating order. Please try again.');
            });
        }
    })
    .catch(error => {
        console.error('Error submitting order:', error);
        alert(error.message || 'Error creating order. Please try again.');
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

// Map payment method string to ID
function getPaymentMethodId(method) {
    const methodMap = {
        'credit-debit': 1,
        'direct-debit': 2,
        'google-pay': 3,
        'apple-pay': 4,
        'klarna': 5
    };
    return methodMap[method] || 1;
}

// Make functions globally accessible
window.submitCheckoutForm = submitCheckoutForm;
window.adjustDays = adjustDays;
window.updateGrandTotal = updateGrandTotal;
</script>
@endsection

