// Cart Management with Laravel Sessions
document.addEventListener('DOMContentLoaded', function() {
    updateCartDisplay();
    setupDragAndDrop();
    
    // Add to cart buttons - solo configurar una vez
    document.querySelectorAll('.add-to-cart-btn:not([data-listener-set])').forEach(btn => {
        btn.setAttribute('data-listener-set', 'true');
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation(); // Evitar propagación de eventos
            const productId = this.dataset.productId;
            const productName = this.dataset.productName;
            const productPrice = this.dataset.productPrice;
            
            addToCart(productId, productName, productPrice);
        });
    });
});

// Setup Drag and Drop functionality
function setupDragAndDrop() {
    // Configurar drag en las tarjetas de productos
    const productCards = document.querySelectorAll('[draggable="true"]:not([data-drag-setup])');
    productCards.forEach((card) => {
        // Marcar como configurado para evitar duplicados
        card.setAttribute('data-drag-setup', 'true');
        
        card.addEventListener('dragstart', function(e) {
            const dragEvent = e;
            if (dragEvent.dataTransfer) {
                dragEvent.dataTransfer.setData(
                    'text/plain',
                    JSON.stringify({
                        id: card.dataset.productId,
                        name: card.dataset.productName,
                        price: card.dataset.productPrice,
                    })
                );
                dragEvent.dataTransfer.effectAllowed = 'move';
            }
            card.style.opacity = '0.5';
        });

        card.addEventListener('dragend', function(e) {
            card.style.opacity = '1';
        });

        // Prevenir que el botón dentro de la tarjeta interfiera con el drag
        const addBtn = card.querySelector('.add-to-cart-btn');
        if (addBtn) {
            addBtn.addEventListener('mousedown', function(e) {
                e.stopPropagation();
            });
            
            addBtn.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    });

    // Hacer que el botón del carrito sea una zona de drop (solo una vez)
    const cartBtn = document.getElementById('cart-btn');
    if (cartBtn && !cartBtn.hasAttribute('data-drop-setup')) {
        cartBtn.setAttribute('data-drop-setup', 'true');
        cartBtn.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (e.dataTransfer) {
                e.dataTransfer.dropEffect = 'move';
            }
            this.style.backgroundColor = '#B8860B';
        });

        cartBtn.addEventListener('dragleave', function(e) {
            this.style.backgroundColor = '#CE9704';
        });

        cartBtn.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.style.backgroundColor = '#CE9704';

            try {
                const dragEvent = e;
                if (dragEvent.dataTransfer) {
                    const productData = JSON.parse(
                        dragEvent.dataTransfer.getData('text/plain')
                    );
                    addToCart(productData.id, productData.name, productData.price);
                    // La notificación se muestra dentro de addToCart()
                }
            } catch (error) {
                console.error('Error al procesar el drop:', error);
            }
        });
    }

    // Hacer que el sidebar del carrito también sea una zona de drop (solo una vez)
    const cartSidebar = document.getElementById('cart-sidebar');
    if (cartSidebar && !cartSidebar.hasAttribute('data-drop-setup')) {
        cartSidebar.setAttribute('data-drop-setup', 'true');
        cartSidebar.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (e.dataTransfer) {
                e.dataTransfer.dropEffect = 'move';
            }
            this.style.backgroundColor = '#3A3A3A';
        });

        cartSidebar.addEventListener('dragleave', function(e) {
            this.style.backgroundColor = '#2F2F2F';
        });

        cartSidebar.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.style.backgroundColor = '#2F2F2F';

            try {
                const dragEvent = e;
                if (dragEvent.dataTransfer) {
                    const productData = JSON.parse(
                        dragEvent.dataTransfer.getData('text/plain')
                    );
                    addToCart(productData.id, productData.name, productData.price);
                    // La notificación se muestra dentro de addToCart()
                }
            } catch (error) {
                console.error('Error processing the drop:', error);
            }
        });
    }
}

function addToCart(productId, productName, productPrice) {
    // Mostrar notificación inmediatamente para feedback visual instantáneo
    showNotification(productName + ' added to cart');
    
    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartDisplay();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function updateCartDisplay() {
    fetch('/cart', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.cart && data.products) {
            renderCart(data.cart, data.products, data.total);
            updateCartBadge(data.cart_count);
        }
    })
    .catch(error => {
        // Silently fail if cart is empty or not yet initialized
        console.log('Cart not available:', error);
    });
}

function renderCart(cart, products, total) {
    const cartItems = document.querySelector('.cart-items');
    const subtotalSection = document.getElementById('subtotal-section');
    const proceedBtn = document.getElementById('when-where-btn');
    
    if (!cartItems) return;
    
    // Check if we're on checkout page
    const isCheckoutPage = window.location.pathname.includes('checkout');
    
    // Convert products array to object for easy lookup
    const productsMap = {};
    if (Array.isArray(products)) {
        products.forEach(p => {
            productsMap[p.id] = p;
        });
    }
    
    // On checkout page, render payment form
    if (isCheckoutPage) {
        cartItems.innerHTML = `
            <div class="text-white">
                <!-- Title: Payment Details -->
                <h3 class="text-2xl font-bold text-[#CE9704] mb-6">Payment Details</h3>
                
                <!-- Apply Coupon Code -->
                <div class="mb-6">
                    <label class="block text-white text-sm mb-3">Apply Coupon Code</label>
                    <div class="flex gap-3">
                        <input 
                            type="text" 
                            id="sidebar-coupon-code"
                            placeholder="Enter coupon code"
                            class="flex-1 px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"
                        />
                        <button 
                            id="sidebar-apply-coupon"
                            class="bg-[#000000] text-white px-4 py-1.5 rounded-lg font-medium text-sm hover:bg-gray-800 transition-colors duration-200"
                        >
                            Apply Coupon
                        </button>
                    </div>
                </div>
                
                <!-- Divider -->
                <div class="border-t border-gray-500 my-6"></div>
                
                <!-- Foreman Details / Receiving person -->
                <div id="foreman-details-container">
                    <label class="block text-white text-sm mb-4">Foreman Details / Receiving person</label>
                    
                    <!-- First Name -->
                    <div class="mb-3">
                        <label class="block text-white text-xs mb-1.5">First Name</label>
                        <input 
                            type="text" 
                            id="foreman-first-name"
                            placeholder="Enter first name"
                            class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"
                        />
                    </div>
                    
                    <!-- Last Name -->
                    <div class="mb-3">
                        <label class="block text-white text-xs mb-1.5">Last Name</label>
                        <input 
                            type="text" 
                            id="foreman-last-name"
                            placeholder="Enter last name"
                            class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"
                        />
                    </div>
                    
                    <!-- Phone Number -->
                    <div class="mb-3">
                        <label class="block text-white text-xs mb-1.5">Phone Number</label>
                        <input 
                            type="tel" 
                            id="foreman-phone"
                            placeholder="Enter phone number"
                            class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"
                        />
                    </div>
                    
                    <!-- Email -->
                    <div class="mb-4">
                        <label class="block text-white text-xs mb-1.5">Email</label>
                        <input 
                            type="email" 
                            id="foreman-email"
                            placeholder="Enter email"
                            class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"
                        />
                    </div>
                    
                    <!-- Continue Button -->
                    <button 
                        id="foreman-continue-btn"
                        class="w-full bg-[#000000] text-white py-2 rounded-lg font-medium text-sm hover:bg-gray-800 transition-colors duration-200"
                    >
                        Continue
                    </button>
                </div>
                
                <!-- Divider -->
                <div class="border-t border-gray-500 my-6"></div>
                
                <!-- Billing Details -->
                <div id="billing-details-container">
                    <div class="flex items-center justify-between mb-4">
                        <label class="block text-white text-sm">Billing Details</label>
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                    
                    <!-- First Name and Last Name -->
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <input 
                                type="text" 
                                id="billing-first-name"
                                placeholder="First Name*"
                                class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"
                            />
                        </div>
                        <div>
                            <input 
                                type="text" 
                                id="billing-last-name"
                                placeholder="Last Name*"
                                class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"
                            />
                        </div>
                    </div>
                    
                    <!-- Email and Phone Number -->
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <input 
                                type="email" 
                                id="billing-email"
                                placeholder="Email*"
                                class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"
                            />
                        </div>
                        <div>
                            <input 
                                type="tel" 
                                id="billing-phone"
                                placeholder="Phone number*"
                                class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"
                            />
                        </div>
                    </div>
                    
                    <!-- Address Line 1 -->
                    <div class="mb-3">
                        <input 
                            type="text" 
                            id="billing-address-line-1"
                            placeholder="Address line 1"
                            class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"
                        />
                    </div>
                    
                    <!-- Address Line 2 -->
                    <div class="mb-3">
                        <input 
                            type="text" 
                            id="billing-address-line-2"
                            placeholder="Address line 2"
                            class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"
                        />
                    </div>
                    
                    <!-- City -->
                    <div class="mb-3">
                        <input 
                            type="text" 
                            id="billing-city"
                            placeholder="City"
                            class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"
                        />
                    </div>
                    
                    <!-- State / Province -->
                    <div class="mb-3">
                        <input 
                            type="text" 
                            id="billing-state"
                            placeholder="State / Province"
                            class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"
                        />
                    </div>
                    
                    <!-- Zip / Postal Code -->
                    <div class="mb-3">
                        <input 
                            type="text" 
                            id="billing-zip"
                            placeholder="Zip / Postal Code"
                            class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"
                        />
                    </div>
                    
                    <!-- Country -->
                    <div class="mb-4">
                        <input 
                            type="text" 
                            id="billing-country"
                            placeholder="Country"
                            class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"
                        />
                    </div>
                    
                    <!-- Checkbox -->
                    <div class="flex items-center mb-4">
                        <input 
                            type="checkbox" 
                            id="billing-is-company"
                            class="w-4 h-4 bg-white border-gray-300 rounded text-[#CE9704] focus:ring-[#CE9704]"
                        />
                        <label for="billing-is-company" class="ml-2 text-white text-sm">Billed is assigned to a company.</label>
                    </div>
                    
                    <!-- Company Fields (hidden by default) -->
                    <div id="company-fields" class="hidden mb-4">
                        <div class="mb-3">
                            <input 
                                type="text" 
                                id="billing-company-name"
                                placeholder="Company Name"
                                class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"
                            />
                        </div>
                        <div class="mb-4">
                            <input 
                                type="text" 
                                id="billing-job-title"
                                placeholder="Job Title"
                                class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"
                            />
                        </div>
                    </div>
                    
                    <!-- Continue Button -->
                    <button 
                        id="billing-continue-btn"
                        class="w-full bg-[#000000] text-white py-2 rounded-lg font-medium text-sm hover:bg-gray-800 transition-colors duration-200"
                    >
                        Continue
                    </button>
                </div>
                
                <!-- Divider -->
                <div class="border-t border-gray-500 my-6"></div>
                
                <!-- Payment Method -->
                <div id="payment-method-container">
                    <label class="block text-white text-sm mb-4">Payment Method</label>
                    
                    <div class="space-y-3">
                        <!-- Credit/Debit Card -->
                        <div class="flex items-center">
                            <input 
                                type="radio" 
                                id="payment-credit-debit"
                                name="payment-method"
                                value="credit-debit"
                                class="w-4 h-4 bg-white border-gray-300 text-[#CE9704] focus:ring-[#CE9704]"
                            />
                            <label for="payment-credit-debit" class="ml-3 text-white text-sm">Credit/Debit Card</label>
                        </div>
                        
                        <!-- Direct Debit/ACH Wire Transfer -->
                        <div class="flex items-center">
                            <input 
                                type="radio" 
                                id="payment-direct-debit"
                                name="payment-method"
                                value="direct-debit"
                                class="w-4 h-4 bg-white border-gray-300 text-[#CE9704] focus:ring-[#CE9704]"
                            />
                            <label for="payment-direct-debit" class="ml-3 text-white text-sm">Direct Debit/ ACH Wire Transfer</label>
                        </div>
                        
                        <!-- Google Pay -->
                        <div class="flex items-center">
                            <input 
                                type="radio" 
                                id="payment-google-pay"
                                name="payment-method"
                                value="google-pay"
                                class="w-4 h-4 bg-white border-gray-300 text-[#CE9704] focus:ring-[#CE9704]"
                            />
                            <label for="payment-google-pay" class="ml-3 text-white text-sm">Google Pay</label>
                        </div>
                        
                        <!-- Apple Pay -->
                        <div class="flex items-center">
                            <input 
                                type="radio" 
                                id="payment-apple-pay"
                                name="payment-method"
                                value="apple-pay"
                                class="w-4 h-4 bg-white border-gray-300 text-[#CE9704] focus:ring-[#CE9704]"
                            />
                            <label for="payment-apple-pay" class="ml-3 text-white text-sm">Apple Pay</label>
                        </div>
                        
                        <!-- Klarna -->
                        <div class="flex items-center">
                            <input 
                                type="radio" 
                                id="payment-klarna"
                                name="payment-method"
                                value="klarna"
                                class="w-4 h-4 bg-white border-gray-300 text-[#CE9704] focus:ring-[#CE9704]"
                            />
                            <label for="payment-klarna" class="ml-3 text-white text-sm">Klarna</label>
                        </div>
                    </div>
                </div>
                
                <!-- Divider -->
                <div class="border-t border-gray-500 my-6"></div>
                
                <!-- Terms and Conditions -->
                <div class="mb-6">
                    <div class="flex items-center mb-4">
                        <input 
                            type="checkbox" 
                            id="terms-checkbox"
                            class="w-4 h-4 bg-white border-gray-300 rounded text-[#CE9704] focus:ring-[#CE9704]"
                        />
                        <label for="terms-checkbox" class="ml-3 text-white text-sm">
                            By checking out you agree to <a href="/Terms&Conditions" class="text-[#CE9704] hover:underline">Terms and Conditions</a>
                        </label>
                    </div>
                    
                    <!-- Checkout Button -->
                    <button 
                        id="sidebar-checkout-btn"
                        class="w-full bg-[#CE9704] text-white py-3 px-6 rounded-lg font-bold text-lg hover:bg-[#B8860B] transition-colors duration-200"
                    >
                        Checkout
                    </button>
                </div>
            </div>
        `;
        if (subtotalSection) subtotalSection.classList.add('hidden');
        
        // Setup listeners after rendering checkout form
        setTimeout(() => {
            setupCheckoutFormListeners();
        }, 100);
    } else if (!cart || Object.keys(cart).length === 0) {
        cartItems.innerHTML = `
            <div class="flex flex-col items-center justify-center h-full">
                <p class="text-white font-semibold italic text-center">Your cart is empty.</p>
                <p class="text-white text-sm text-center">Looks like you haven't made your choice yet.</p>
                <p class="text-white text-sm text-center">Start by adding items to your cart.</p>
            </div>
        `;
        if (subtotalSection) subtotalSection.classList.add('hidden');
        if (proceedBtn) {
            proceedBtn.disabled = true;
            proceedBtn.classList.add('cursor-not-allowed');
            proceedBtn.style.pointerEvents = 'none';
            proceedBtn.classList.remove('bg-[#CE9704]', 'text-white', 'hover:bg-[#B8860B]');
            proceedBtn.classList.add('bg-gray-600', 'text-gray-400');
        }
    } else {
        let html = '';
        let totalItems = 0;
        
        for (const [productId, quantity] of Object.entries(cart)) {
            const product = productsMap[productId];
            if (!product) continue;
            
            totalItems += quantity;
            const itemTotal = product.price * quantity;
            
            // Disable buttons on checkout page
            const disableClass = isCheckoutPage ? 'opacity-50 cursor-not-allowed' : '';
            const disabledAttr = isCheckoutPage ? 'disabled' : '';
            
            html += `
                <div class="bg-[#4A4A4A] rounded-lg border border-gray-600 mb-3 overflow-hidden shadow-md">
                    <div class="flex items-center p-3">
                        <div class="shrink-0 mr-3">
                            <div class="w-16 h-16 bg-white rounded-lg p-1 flex items-center justify-center">
                                <img src="${(product.image_url ? product.image_url : '/Product1.png')}" alt="${product.name}" class="w-full h-full object-contain" />
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="text-white font-bold text-base uppercase leading-tight pr-2 truncate">${product.name}</h4>
                                ${!isCheckoutPage ? `<button onclick="removeFromCart(${product.id})" class="text-gray-400 hover:text-red-400 p-1 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>` : ''}
                            </div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-[#CE9704] font-semibold text-xs">ID: ${product.id}</span>
                                <span class="text-white font-bold text-base">$${product.price}/day*</span>
                            </div>
                            <div class="flex items-center justify-end mb-2">
                                <div class="flex items-center bg-gray-600 rounded-md">
                                    <button onclick="updateQuantity(${product.id}, -1)" ${disabledAttr} class="bg-gray-600 text-white px-2 py-1 rounded-l-md text-xs hover:bg-gray-500 ${disableClass}" ${disabledAttr ? 'style="pointer-events: none;"' : ''}>−</button>
                                    <span class="text-white mx-2 font-bold text-sm min-w-[16px] text-center">${quantity}</span>
                                    <button onclick="updateQuantity(${product.id}, 1)" ${disabledAttr} class="bg-gray-600 text-white px-2 py-1 rounded-r-md text-xs hover:bg-gray-500 ${disableClass}" ${disabledAttr ? 'style="pointer-events: none;"' : ''}>+</button>
                                </div>
                            </div>
                            ${product.description ? `<div class="bg-gray-800 p-2 rounded text-xs">
                                <div class="text-white">${product.description}</div>
                            </div>` : ''}
                        </div>
                    </div>
                </div>
            `;
        }
        
        cartItems.innerHTML = html;
        
        if (subtotalSection) {
            subtotalSection.classList.remove('hidden');
            document.getElementById('subtotal-amount').textContent = '$' + total.toFixed(2);
            document.getElementById('total-items').textContent = totalItems;
        }
        
        if (proceedBtn) {
            proceedBtn.disabled = false;
            proceedBtn.classList.remove('cursor-not-allowed', 'bg-gray-600', 'text-gray-400');
            proceedBtn.classList.add('bg-[#CE9704]', 'text-white', 'hover:bg-[#B8860B]');
            proceedBtn.style.pointerEvents = 'auto';
        }
    }
}

function updateQuantity(productId, change) {
    fetch(`/cart/${productId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            quantity: change
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartDisplay();
        }
    })
    .catch(error => console.error('Error:', error));
}

function removeFromCart(productId) {
    fetch(`/cart/${productId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartDisplay();
            showNotification('Product removed from cart');
        }
    })
    .catch(error => console.error('Error:', error));
}

function updateCartBadge(count) {
    const badge = document.getElementById('cart-badge');
    if (badge) {
        if (count > 0) {
            badge.textContent = count;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }
}

function showNotification(message) {
    // Crear container si no existe
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'fixed top-4 right-4 z-50 space-y-2';
        document.body.appendChild(toastContainer);
    }

    const toast = document.createElement('div');
    toast.className = 'bg-[#CE9704] text-white px-4 py-2 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300';
    toast.textContent = message;
    toastContainer.appendChild(toast);

    // Animar entrada
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);

    // Remover después de 3 segundos
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 3000);
}

function showErrorNotification(message) {
    // Crear container si no existe
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'fixed top-4 right-4 z-50 space-y-2';
        document.body.appendChild(toastContainer);
    }

    const toast = document.createElement('div');
    toast.className = 'bg-red-600 text-white px-4 py-2 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300';
    toast.textContent = message;
    toastContainer.appendChild(toast);

    // Animar entrada
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);

    // Remover después de 3 segundos
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 3000);
}

// Make functions global
window.updateQuantity = updateQuantity;
window.removeFromCart = removeFromCart;
window.updateCartDisplay = updateCartDisplay;
window.setupDragAndDrop = setupDragAndDrop;
window.addToCart = addToCart;

// Setup checkout page functionality
document.addEventListener('DOMContentLoaded', function() {
    setupCheckoutPage();
});

function setupCheckoutFormListeners() {
    // Setup foreman continue button
    setupForemanContinue();
    
    // Setup billing continue button
    setupBillingContinue();
    
    // Setup billing is company checkbox
    const billingIsCompany = document.getElementById('billing-is-company');
    if (billingIsCompany) {
        // Remove existing listeners to avoid duplicates
        const newCheckbox = billingIsCompany.cloneNode(true);
        billingIsCompany.parentNode.replaceChild(newCheckbox, billingIsCompany);
        
        newCheckbox.addEventListener('change', function() {
            const companyFields = document.getElementById('company-fields');
            if (companyFields) {
                if (this.checked) {
                    companyFields.classList.remove('hidden');
                } else {
                    companyFields.classList.add('hidden');
                }
            }
        });
    }
    
    // Setup payment method radio buttons
    const paymentMethodRadios = document.querySelectorAll('input[name="payment-method"]');
    paymentMethodRadios.forEach(radio => {
        // Remove existing listeners
        const newRadio = radio.cloneNode(true);
        radio.parentNode.replaceChild(newRadio, radio);
        
        newRadio.addEventListener('change', function() {
            if (this.checked) {
                localStorage.setItem('payment-method', this.value);
                openPaymentMethodModal(this.value);
            }
        });
    });
    
    // Setup apply coupon button
    const applyCouponBtn = document.getElementById('sidebar-apply-coupon');
    if (applyCouponBtn) {
        // Remove existing listeners to avoid duplicates
        const newCouponBtn = applyCouponBtn.cloneNode(true);
        applyCouponBtn.parentNode.replaceChild(newCouponBtn, applyCouponBtn);
        
        newCouponBtn.addEventListener('click', function() {
            applyCouponCode();
        });
    }
    
    // Setup checkout button
    const sidebarCheckoutBtn = document.getElementById('sidebar-checkout-btn');
    if (sidebarCheckoutBtn) {
        // Remove existing listeners to avoid duplicates
        const newBtn = sidebarCheckoutBtn.cloneNode(true);
        sidebarCheckoutBtn.parentNode.replaceChild(newBtn, sidebarCheckoutBtn);
        
        newBtn.addEventListener('click', function() {
            // Validate terms checkbox
            const termsCheckbox = document.getElementById('terms-checkbox');
            if (!termsCheckbox || !termsCheckbox.checked) {
                alert('Por favor acepta los Términos y Condiciones para continuar.');
                return;
            }
            
            // Check if all required data is filled
            const foremanDetails = localStorage.getItem('foreman-details');
            const billingDetails = localStorage.getItem('billing-details');
            const paymentMethodDetails = localStorage.getItem('payment-method-details');
            
            if (!foremanDetails) {
                alert('Por favor completa los detalles del Foreman primero.');
                return;
            }
            
            if (!billingDetails) {
                alert('Por favor completa los detalles de facturación primero.');
                return;
            }
            
            if (!paymentMethodDetails) {
                alert('Por favor completa los detalles del método de pago primero.');
                return;
            }
            
            // All validations passed, submit checkout form
            if (typeof submitCheckoutForm === 'function') {
                submitCheckoutForm();
            } else {
                // Fallback: redirect to checkout page
                window.location.href = '/checkout';
            }
        });
    }
}

function setupCheckoutPage() {
    // Check if we're on checkout page and setup listeners
    if (window.location.pathname.includes('checkout')) {
        // Defer setup to let HTML render first
        setTimeout(() => {
            setupCheckoutFormListeners();
            // Load saved data only once on initial setup
            loadSavedCheckoutData();
        }, 800);
    }
}

function setupForemanContinue() {
    const foremanContinueBtn = document.getElementById('foreman-continue-btn');
    if (foremanContinueBtn) {
        // Remove existing listeners to avoid duplicates
        const newBtn = foremanContinueBtn.cloneNode(true);
        foremanContinueBtn.parentNode.replaceChild(newBtn, foremanContinueBtn);
        newBtn.addEventListener('click', handleForemanContinue);
    }
}

function handleForemanContinue() {
    const firstName = document.getElementById('foreman-first-name')?.value || '';
    const lastName = document.getElementById('foreman-last-name')?.value || '';
    const phone = document.getElementById('foreman-phone')?.value || '';
    const email = document.getElementById('foreman-email')?.value || '';
    
    if (!firstName || !lastName || !phone || !email) {
        alert('Por favor completa todos los campos del foreman.');
        return;
    }
    
    // Store data
    const foremanData = { firstName, lastName, phone, email };
    localStorage.setItem('foreman-details', JSON.stringify(foremanData));
    
    // Display summary instead of form
    displayForemanSummary(foremanData);
}

function displayForemanSummary(data) {
    const container = document.getElementById('foreman-details-container');
    if (!container) return;
    
    container.innerHTML = `
        <label class="block text-white text-sm mb-4">Foreman Details / Receiving person</label>
        <div class="bg-white bg-opacity-10 rounded-lg p-4 mb-4">
            <div class="space-y-3">
                <div>
                    <p class="text-white text-xs opacity-75 mb-1">Full Name</p>
                    <p class="text-white font-semibold text-sm">${data.firstName} ${data.lastName}</p>
                </div>
                <div class="border-t border-white border-opacity-20 pt-3">
                    <p class="text-white text-xs opacity-75 mb-1">Phone Number</p>
                    <p class="text-white font-semibold text-sm">${data.phone}</p>
                </div>
                <div class="border-t border-white border-opacity-20 pt-3">
                    <p class="text-white text-xs opacity-75 mb-1">Email</p>
                    <p class="text-white font-semibold text-sm">${data.email}</p>
                </div>
            </div>
        </div>
        <button id="edit-foreman-btn" class="w-full bg-[#CE9704] text-white py-2 rounded-lg font-medium text-sm hover:bg-[#B8860B] transition-colors duration-200">
            Edit Details
        </button>
    `;
    
    // Setup edit button
    document.getElementById('edit-foreman-btn')?.addEventListener('click', () => {
        renderForemanForm(data);
    });
}

function renderForemanForm(savedData) {
    const container = document.getElementById('foreman-details-container');
    if (!container) return;
    
    container.innerHTML = `
        <label class="block text-white text-sm mb-4">Foreman Details / Receiving person</label>
        <div class="mb-3">
            <label class="block text-white text-xs mb-1.5">First Name</label>
            <input type="text" id="foreman-first-name" value="${savedData.firstName || ''}" placeholder="Enter first name" class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
        </div>
        <div class="mb-3">
            <label class="block text-white text-xs mb-1.5">Last Name</label>
            <input type="text" id="foreman-last-name" value="${savedData.lastName || ''}" placeholder="Enter last name" class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
        </div>
        <div class="mb-3">
            <label class="block text-white text-xs mb-1.5">Phone Number</label>
            <input type="tel" id="foreman-phone" value="${savedData.phone || ''}" placeholder="Enter phone number" class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
        </div>
        <div class="mb-4">
            <label class="block text-white text-xs mb-1.5">Email</label>
            <input type="email" id="foreman-email" value="${savedData.email || ''}" placeholder="Enter email" class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
        </div>
        <button id="foreman-continue-btn" class="w-full bg-[#000000] text-white py-2 rounded-lg font-medium text-sm hover:bg-gray-800 transition-colors duration-200">
            Continue
        </button>
    `;
    
    setupForemanContinue();
}

function setupBillingContinue() {
    const billingContinueBtn = document.getElementById('billing-continue-btn');
    if (billingContinueBtn) {
        // Remove existing listeners to avoid duplicates
        const newBtn = billingContinueBtn.cloneNode(true);
        billingContinueBtn.parentNode.replaceChild(newBtn, billingContinueBtn);
        newBtn.addEventListener('click', handleBillingContinue);
    }
}

function handleBillingContinue() {
    const firstName = document.getElementById('billing-first-name')?.value || '';
    const lastName = document.getElementById('billing-last-name')?.value || '';
    const email = document.getElementById('billing-email')?.value || '';
    const phone = document.getElementById('billing-phone')?.value || '';
    const isCompany = document.getElementById('billing-is-company')?.checked || false;
    
    if (!firstName || !lastName || !email || !phone) {
        alert('Por favor completa todos los campos requeridos (marcados con *).');
        return;
    }
    
    const billingData = {
        firstName,
        lastName,
        email,
        phone,
        addressLine1: document.getElementById('billing-address-line-1')?.value || '',
        addressLine2: document.getElementById('billing-address-line-2')?.value || '',
        city: document.getElementById('billing-city')?.value || '',
        state: document.getElementById('billing-state')?.value || '',
        zip: document.getElementById('billing-zip')?.value || '',
        country: document.getElementById('billing-country')?.value || '',
        isCompany,
        companyName: document.getElementById('billing-company-name')?.value || '',
        jobTitle: document.getElementById('billing-job-title')?.value || ''
    };
    
    localStorage.setItem('billing-details', JSON.stringify(billingData));
    displayBillingSummary(billingData);
}

function displayBillingSummary(data) {
    const container = document.getElementById('billing-details-container');
    if (!container) return;
    
    container.innerHTML = `
        <div class="flex items-center justify-between mb-4">
            <label class="block text-white text-sm">Billing Details</label>
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
        <div class="bg-white bg-opacity-10 rounded-lg p-4 mb-4">
            <div class="space-y-3">
                <div>
                    <p class="text-white text-xs opacity-75 mb-1">Full Name</p>
                    <p class="text-white font-semibold text-sm">${data.firstName} ${data.lastName}</p>
                </div>
                <div class="border-t border-white border-opacity-20 pt-3">
                    <p class="text-white text-xs opacity-75 mb-1">Contact</p>
                    <p class="text-white font-semibold text-sm">${data.email}</p>
                    <p class="text-white font-semibold text-sm">${data.phone}</p>
                </div>
                ${data.addressLine1 ? `
                <div class="border-t border-white border-opacity-20 pt-3">
                    <p class="text-white text-xs opacity-75 mb-1">Address</p>
                    <p class="text-white font-semibold text-sm">${data.addressLine1}</p>
                    ${data.addressLine2 ? `<p class="text-white font-semibold text-sm">${data.addressLine2}</p>` : ''}
                    <p class="text-white font-semibold text-sm">${data.city}${data.state ? ', ' + data.state : ''} ${data.zip}</p>
                    <p class="text-white font-semibold text-sm">${data.country}</p>
                </div>
                ` : ''}
                <div class="border-t border-white border-opacity-20 pt-3">
                    <p class="text-white text-xs opacity-75 mb-1">Assigned to Company</p>
                    <p class="text-white font-semibold text-sm">${data.isCompany ? 'Yes' : 'No'}</p>
                    ${data.isCompany && data.companyName ? `
                        <p class="text-white font-semibold text-sm mt-1">${data.companyName}</p>
                        ${data.jobTitle ? `<p class="text-white font-semibold text-sm">${data.jobTitle}</p>` : ''}
                    ` : ''}
                </div>
            </div>
        </div>
        <button id="edit-billing-btn" class="w-full bg-[#CE9704] text-white py-2 rounded-lg font-medium text-sm hover:bg-[#B8860B] transition-colors duration-200">
            Edit Details
        </button>
    `;
    
    document.getElementById('edit-billing-btn')?.addEventListener('click', () => {
        renderBillingForm(data);
    });
}

function renderBillingForm(savedData) {
    const container = document.getElementById('billing-details-container');
    if (!container) return;
    
    const isCompanyChecked = savedData.isCompany ? 'checked' : '';
    const companyFieldsClass = savedData.isCompany ? '' : 'hidden';
    
    container.innerHTML = `
        <div class="flex items-center justify-between mb-4">
            <label class="block text-white text-sm">Billing Details</label>
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
        <div class="grid grid-cols-2 gap-3 mb-3">
            <div>
                <input type="text" id="billing-first-name" value="${savedData.firstName || ''}" placeholder="First Name*" class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
            </div>
            <div>
                <input type="text" id="billing-last-name" value="${savedData.lastName || ''}" placeholder="Last Name*" class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3 mb-3">
            <div>
                <input type="email" id="billing-email" value="${savedData.email || ''}" placeholder="Email*" class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
            </div>
            <div>
                <input type="tel" id="billing-phone" value="${savedData.phone || ''}" placeholder="Phone number*" class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
            </div>
        </div>
        <div class="mb-3">
            <input type="text" id="billing-address-line-1" value="${savedData.addressLine1 || ''}" placeholder="Address line 1" class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
        </div>
        <div class="mb-3">
            <input type="text" id="billing-address-line-2" value="${savedData.addressLine2 || ''}" placeholder="Address line 2" class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
        </div>
        <div class="mb-3">
            <input type="text" id="billing-city" value="${savedData.city || ''}" placeholder="City" class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
        </div>
        <div class="mb-3">
            <input type="text" id="billing-state" value="${savedData.state || ''}" placeholder="State / Province" class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
        </div>
        <div class="mb-3">
            <input type="text" id="billing-zip" value="${savedData.zip || ''}" placeholder="Zip / Postal Code" class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
        </div>
        <div class="mb-4">
            <input type="text" id="billing-country" value="${savedData.country || ''}" placeholder="Country" class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
        </div>
        <div class="flex items-center mb-4">
            <input type="checkbox" id="billing-is-company" ${isCompanyChecked} class="w-4 h-4 bg-white border-gray-300 rounded text-[#CE9704] focus:ring-[#CE9704]"/>
            <label for="billing-is-company" class="ml-2 text-white text-sm">Billed is assigned to a company.</label>
        </div>
        <div id="company-fields" class="${companyFieldsClass} mb-4">
            <div class="mb-3">
                <input type="text" id="billing-company-name" value="${savedData.companyName || ''}" placeholder="Company Name" class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
            </div>
            <div class="mb-4">
                <input type="text" id="billing-job-title" value="${savedData.jobTitle || ''}" placeholder="Job Title" class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
            </div>
        </div>
        <button id="billing-continue-btn" class="w-full bg-[#000000] text-white py-2 rounded-lg font-medium text-sm hover:bg-gray-800 transition-colors duration-200">
            Continue
        </button>
    `;
    
    setupBillingContinue();
    
    // Setup company checkbox listener
    const billingIsCompany = document.getElementById('billing-is-company');
    if (billingIsCompany) {
        billingIsCompany.addEventListener('change', function() {
            const companyFields = document.getElementById('company-fields');
            if (companyFields) {
                if (this.checked) {
                    companyFields.classList.remove('hidden');
                } else {
                    companyFields.classList.add('hidden');
                }
            }
        });
    }
}

function loadSavedCheckoutData() {
    // Load foreman details if exists
    const foremanData = localStorage.getItem('foreman-details');
    if (foremanData) {
        try {
            const data = JSON.parse(foremanData);
            if (data.firstName && data.lastName) {
                displayForemanSummary(data);
            }
        } catch (e) {
            console.error('Error loading foreman data:', e);
        }
    }
    
    // Load billing details if exists
    const billingData = localStorage.getItem('billing-details');
    if (billingData) {
        try {
            const data = JSON.parse(billingData);
            if (data.firstName && data.lastName) {
                displayBillingSummary(data);
            }
        } catch (e) {
            console.error('Error loading billing data:', e);
        }
    }
    
    // Load saved payment method if exists
    const savedPaymentMethod = localStorage.getItem('payment-method');
    const paymentMethodRadios = document.querySelectorAll('input[name="payment-method"]');
    
    if (savedPaymentMethod && paymentMethodRadios.length > 0) {
        paymentMethodRadios.forEach(radio => {
            if (radio.value === savedPaymentMethod) {
                radio.checked = true;
            }
        });
    }
    
    // Add event listeners to payment method radio buttons
    paymentMethodRadios.forEach(radio => {
        radio.addEventListener('change', function(e) {
            if (this.checked) {
                localStorage.setItem('payment-method', this.value);
                openPaymentMethodModal(this.value);
            }
        });
    });
    
    // Check if payment method details exist and display them
    const savedPaymentDetails = localStorage.getItem('payment-method-details');
    if (savedPaymentDetails) {
        try {
            const paymentDetails = JSON.parse(savedPaymentDetails);
            setTimeout(() => {
                displayPaymentMethodDetails(paymentDetails);
            }, 900);
        } catch (e) {
            console.error('Error parsing payment details:', e);
        }
    }
}

// Function to close payment method modal
function closePaymentMethodModal() {
    const modal = document.getElementById('payment-method-modal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Make closePaymentMethodModal globally accessible
window.closePaymentMethodModal = closePaymentMethodModal;

// Function to open payment method modal
function openPaymentMethodModal(method) {
    const modal = document.getElementById('payment-method-modal');
    const formContainer = document.getElementById('payment-method-form-container');
    const modalTitle = document.getElementById('payment-modal-title');
    
    if (!modal || !formContainer || !modalTitle) return;
    
    let formHTML = '';
    let title = '';
    
    switch(method) {
        case 'credit-debit':
            title = 'Credit/Debit Card Details';
            formHTML = `
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Card Number</label>
                        <input type="text" id="payment-card-number" placeholder="1234 5678 9012 3456" maxlength="19" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date</label>
                            <input type="text" id="payment-expiry" placeholder="MM/YY" maxlength="5" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">CVV</label>
                            <input type="text" id="payment-cvv" placeholder="123" maxlength="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cardholder Name</label>
                        <input type="text" id="payment-cardholder" placeholder="John Doe" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button onclick="closePaymentMethodModal()" class="flex-1 bg-white border border-gray-300 text-gray-700 py-2 px-4 rounded-lg font-semibold hover:bg-gray-50 transition-colors">Cancel</button>
                        <button onclick="savePaymentDetails('credit-debit')" class="flex-1 bg-[#CE9704] text-white py-2 px-4 rounded-lg font-semibold hover:bg-[#B8860B] transition-colors">Save</button>
                    </div>
                </div>
            `;
            break;
        case 'direct-debit':
            title = 'Direct Debit / ACH Wire Transfer';
            formHTML = `
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bank Name</label>
                        <input type="text" id="payment-bank-name" placeholder="Enter bank name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Account Number</label>
                        <input type="text" id="payment-account-number" placeholder="Enter account number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Routing Number</label>
                        <input type="text" id="payment-routing-number" placeholder="Enter routing number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button onclick="closePaymentMethodModal()" class="flex-1 bg-white border border-gray-300 text-gray-700 py-2 px-4 rounded-lg font-semibold hover:bg-gray-50 transition-colors">Cancel</button>
                        <button onclick="savePaymentDetails('direct-debit')" class="flex-1 bg-[#CE9704] text-white py-2 px-4 rounded-lg font-semibold hover:bg-[#B8860B] transition-colors">Save</button>
                    </div>
                </div>
            `;
            break;
        case 'google-pay':
        case 'apple-pay':
        case 'klarna':
            title = method === 'google-pay' ? 'Google Pay' : method === 'apple-pay' ? 'Apple Pay' : 'Klarna';
            formHTML = `
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="payment-email" placeholder="your.email@example.com" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" id="payment-phone" placeholder="+1 (555) 000-0000" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button onclick="closePaymentMethodModal()" class="flex-1 bg-white border border-gray-300 text-gray-700 py-2 px-4 rounded-lg font-semibold hover:bg-gray-50 transition-colors">Cancel</button>
                        <button onclick="savePaymentDetails('${method}')" class="flex-1 bg-[#CE9704] text-white py-2 px-4 rounded-lg font-semibold hover:bg-[#B8860B] transition-colors">Save</button>
                    </div>
                </div>
            `;
            break;
    }
    
    modalTitle.textContent = title;
    formContainer.innerHTML = formHTML;
    modal.style.display = 'flex';
}

// Make openPaymentMethodModal globally accessible
window.openPaymentMethodModal = openPaymentMethodModal;

// Function to save payment details
function savePaymentDetails(method) {
    let paymentDetails = { method };
    
    switch(method) {
        case 'credit-debit':
            const cardNumber = document.getElementById('payment-card-number')?.value || '';
            const expiry = document.getElementById('payment-expiry')?.value || '';
            const cvv = document.getElementById('payment-cvv')?.value || '';
            const cardholder = document.getElementById('payment-cardholder')?.value || '';
            
            if (!cardNumber || !expiry || !cvv || !cardholder) {
                alert('Por favor completa todos los detalles de la tarjeta.');
                return;
            }
            
            paymentDetails = {
                method,
                cardNumber: cardNumber.replace(/\s/g, ''),
                expiry,
                cvv,
                cardholder,
                maskedCard: '**** **** **** ' + cardNumber.slice(-4).replace(/\s/g, '')
            };
            break;
        case 'direct-debit':
            const bankName = document.getElementById('payment-bank-name')?.value || '';
            const accountNumber = document.getElementById('payment-account-number')?.value || '';
            const routingNumber = document.getElementById('payment-routing-number')?.value || '';
            
            if (!bankName || !accountNumber || !routingNumber) {
                alert('Por favor completa todos los detalles del banco.');
                return;
            }
            
            paymentDetails = {
                method,
                bankName,
                accountNumber: accountNumber.slice(-4),
                routingNumber
            };
            break;
        case 'google-pay':
        case 'apple-pay':
        case 'klarna':
            const email = document.getElementById('payment-email')?.value || '';
            const phone = document.getElementById('payment-phone')?.value || '';
            
            if (!email || !phone) {
                alert('Por favor completa todos los campos.');
                return;
            }
            
            paymentDetails = {
                method,
                email,
                phone
            };
            break;
    }
    
    localStorage.setItem('payment-method-details', JSON.stringify(paymentDetails));
    localStorage.setItem('payment-method', method);
    closePaymentMethodModal();
    displayPaymentMethodDetails(paymentDetails);
}

// Make savePaymentDetails globally accessible
window.savePaymentDetails = savePaymentDetails;

// Function to display payment method details
function displayPaymentMethodDetails(paymentDetails) {
    const paymentContainer = document.getElementById('payment-method-container');
    if (!paymentContainer) return;
    
    const methodLabels = {
        'credit-debit': 'Credit/Debit Card',
        'direct-debit': 'Direct Debit/ ACH Wire Transfer',
        'google-pay': 'Google Pay',
        'apple-pay': 'Apple Pay',
        'klarna': 'Klarna'
    };
    
    let infoHTML = '';
    switch(paymentDetails.method) {
        case 'credit-debit':
            infoHTML = `
                <p class="text-white font-semibold text-sm">${paymentDetails.maskedCard || '**** **** **** ****'}</p>
                <p class="text-white font-semibold text-sm">${paymentDetails.cardholder || ''}</p>
                <p class="text-white font-semibold text-sm">Expires: ${paymentDetails.expiry || ''}</p>
            `;
            break;
        case 'direct-debit':
            infoHTML = `
                <p class="text-white font-semibold text-sm">${paymentDetails.bankName || ''}</p>
                <p class="text-white font-semibold text-sm">Account: ****${paymentDetails.accountNumber || ''}</p>
                <p class="text-white font-semibold text-sm">Routing: ${paymentDetails.routingNumber || ''}</p>
            `;
            break;
        case 'google-pay':
        case 'apple-pay':
        case 'klarna':
            infoHTML = `
                <p class="text-white font-semibold text-sm">${paymentDetails.email || ''}</p>
                <p class="text-white font-semibold text-sm">${paymentDetails.phone || ''}</p>
            `;
            break;
    }
    
    const detailsHTML = `
        <label class="block text-white text-sm mb-4">Payment Method</label>
        <div class="bg-white bg-opacity-10 rounded-lg p-4 mb-4">
            <div class="space-y-3">
                <div>
                    <p class="text-white text-xs opacity-75 mb-1">Method</p>
                    <p class="text-white font-semibold text-sm">${methodLabels[paymentDetails.method] || paymentDetails.method}</p>
                </div>
                <div class="border-t border-white border-opacity-20 pt-3">
                    <p class="text-white text-xs opacity-75 mb-1">Details</p>
                    ${infoHTML}
                </div>
            </div>
        </div>
        <button id="edit-payment-btn" class="w-full bg-[#CE9704] text-white py-2 rounded-lg font-medium text-sm hover:bg-[#B8860B] transition-colors duration-200">
            Edit Details
        </button>
    `;
    
    paymentContainer.innerHTML = detailsHTML;
    
    // Add edit button listener - go back to payment method selection
    const editBtn = document.getElementById('edit-payment-btn');
    if (editBtn) {
        editBtn.addEventListener('click', function() {
            displayPaymentMethodSelection();
        });
    }
}

// Make displayPaymentMethodDetails globally accessible
window.displayPaymentMethodDetails = displayPaymentMethodDetails;

// Function to display payment method selection (initial state)
function displayPaymentMethodSelection() {
    const paymentContainer = document.getElementById('payment-method-container');
    if (!paymentContainer) return;
    
    const selectionHTML = `
        <label class="block text-white text-sm mb-4">Payment Method</label>
        <div class="space-y-3">
            <!-- Credit/Debit Card -->
            <div class="flex items-center">
                <input 
                    type="radio" 
                    id="payment-credit-debit"
                    name="payment-method"
                    value="credit-debit"
                    class="w-4 h-4 bg-white border-gray-300 text-[#CE9704] focus:ring-[#CE9704]"
                />
                <label for="payment-credit-debit" class="ml-3 text-white text-sm">Credit/Debit Card</label>
            </div>
            
            <!-- Direct Debit/ACH Wire Transfer -->
            <div class="flex items-center">
                <input 
                    type="radio" 
                    id="payment-direct-debit"
                    name="payment-method"
                    value="direct-debit"
                    class="w-4 h-4 bg-white border-gray-300 text-[#CE9704] focus:ring-[#CE9704]"
                />
                <label for="payment-direct-debit" class="ml-3 text-white text-sm">Direct Debit/ ACH Wire Transfer</label>
            </div>
            
            <!-- Google Pay -->
            <div class="flex items-center">
                <input 
                    type="radio" 
                    id="payment-google-pay"
                    name="payment-method"
                    value="google-pay"
                    class="w-4 h-4 bg-white border-gray-300 text-[#CE9704] focus:ring-[#CE9704]"
                />
                <label for="payment-google-pay" class="ml-3 text-white text-sm">Google Pay</label>
            </div>
            
            <!-- Apple Pay -->
            <div class="flex items-center">
                <input 
                    type="radio" 
                    id="payment-apple-pay"
                    name="payment-method"
                    value="apple-pay"
                    class="w-4 h-4 bg-white border-gray-300 text-[#CE9704] focus:ring-[#CE9704]"
                />
                <label for="payment-apple-pay" class="ml-3 text-white text-sm">Apple Pay</label>
            </div>
            
            <!-- Klarna -->
            <div class="flex items-center">
                <input 
                    type="radio" 
                    id="payment-klarna"
                    name="payment-method"
                    value="klarna"
                    class="w-4 h-4 bg-white border-gray-300 text-[#CE9704] focus:ring-[#CE9704]"
                />
                <label for="payment-klarna" class="ml-3 text-white text-sm">Klarna</label>
            </div>
        </div>
    `;
    
    paymentContainer.innerHTML = selectionHTML;
    
    // Add event listeners to payment method radio buttons
    const paymentMethodRadios = document.querySelectorAll('input[name="payment-method"]');
    paymentMethodRadios.forEach(radio => {
        radio.addEventListener('change', function(e) {
            if (this.checked) {
                localStorage.setItem('payment-method', this.value);
                openPaymentMethodModal(this.value);
            }
        });
    });
}

// Make displayPaymentMethodSelection globally accessible
window.displayPaymentMethodSelection = displayPaymentMethodSelection;

// Payment verification functions removed - payments are now handled via Odoo invoices

function clearCart() {
    fetch('/cart', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart display to show empty state
            updateCartDisplay();
        }
    })
    .catch(error => {
        console.error('Error clearing cart:', error);
    });
}


function openSuccessModal() {
    const modal = document.getElementById('success-modal');
    const modalContent = document.getElementById('success-modal-content');
    
    if (modal && modalContent) {
        // Show modal
        modal.style.display = 'flex';
        
        // Trigger animation after a short delay
        setTimeout(() => {
            modal.style.opacity = '1';
            modalContent.style.opacity = '1';
            modalContent.style.transform = 'scale(1) translateY(0)';
        }, 10);
    }
}

function closeSuccessModal() {
    const modal = document.getElementById('success-modal');
    const modalContent = document.getElementById('success-modal-content');
    if (modal && modalContent) {
        // Animate out
        modal.style.opacity = '0';
        modalContent.style.opacity = '0';
        modalContent.style.transform = 'scale(0.95) translateY(20px)';
        
        // Hide after animation
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
    }
}

function goToHomepage() {
    // Clear localStorage
    localStorage.removeItem('reborn-rentals-directions');
    localStorage.removeItem('foreman-details');
    localStorage.removeItem('billing-details');
    localStorage.removeItem('payment-method');
    localStorage.removeItem('payment-method-details');
    
    // Clear cart and wait for it to complete before redirecting
    fetch('/cart', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Redirect to home after cart is cleared
        window.location.href = '/';
    })
    .catch(error => {
        console.error('Error clearing cart:', error);
        // Redirect anyway even if there's an error
        window.location.href = '/';
    });
}

// Make functions globally accessible
window.openSuccessModal = openSuccessModal;
window.closeSuccessModal = closeSuccessModal;
window.goToHomepage = goToHomepage;

// Setup checkout button listener
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.pathname.includes('checkout')) {
        setTimeout(() => {
            const sidebarCheckoutBtn = document.getElementById('sidebar-checkout-btn');
            if (sidebarCheckoutBtn) {
                sidebarCheckoutBtn.addEventListener('click', function() {
                    // Validate terms checkbox
                    const termsCheckbox = document.getElementById('terms-checkbox');
                    if (!termsCheckbox || !termsCheckbox.checked) {
                        alert('Por favor acepta los Términos y Condiciones para continuar.');
                        return;
                    }
                    
                    // Check if all required data is filled
                    const foremanDetails = localStorage.getItem('foreman-details');
                    const billingDetails = localStorage.getItem('billing-details');
                    const paymentMethodDetails = localStorage.getItem('payment-method-details');
                    
                    // Billing details is required (foreman can be empty, will use billing data)
                    if (!billingDetails) {
                        alert('Por favor completa los detalles de facturación primero.');
                        return;
                    }
                    
                    if (!paymentMethodDetails) {
                        alert('Por favor completa los detalles del método de pago primero.');
                        return;
                    }
                    
                    // Foreman details is optional - if empty, billing details will be used
                    
                    // Payment verification removed - submit order directly
                    // Order will be invoiced via Odoo with selected payment method
                    if (typeof submitCheckoutForm === 'function') {
                        submitCheckoutForm();
                    } else {
                        alert('Error: Could not complete order. Please try again.');
                    }
                });
            }
        }, 900);
    }
});

// Apply coupon code function
function applyCouponCode() {
    const couponInput = document.getElementById('sidebar-coupon-code');
    if (!couponInput) return;
    
    const code = couponInput.value.trim();
    if (!code) {
        showErrorNotification('Please enter a coupon code');
        return;
    }
    
    // Calculate subtotal from checkout page items
    let cartTotal = 0;
    if (window.location.pathname.includes('checkout')) {
        // Calculate from item totals on checkout page (already includes days)
        document.querySelectorAll('[data-product-id]').forEach(element => {
            const productId = element.getAttribute('data-product-id');
            const itemTotalElement = document.getElementById('item-total-' + productId);
            if (itemTotalElement) {
                const itemTotalText = itemTotalElement.textContent.replace('$', '').replace(/,/g, '').trim();
                const itemTotal = parseFloat(itemTotalText) || 0;
                cartTotal += itemTotal;
            }
        });
    } else {
        // Get current cart total
        const grandTotalElement = document.getElementById('grand-total');
        if (grandTotalElement) {
            // Extract number from text (remove $ and commas)
            const totalText = grandTotalElement.textContent.replace('$', '').replace(/,/g, '');
            cartTotal = parseFloat(totalText) || 0;
            // Remove tax (2%)
            cartTotal = cartTotal / 1.02;
        }
    }
    
    // Apply coupon via API
    fetch('/cart/apply-coupon', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            code: code,
            cart_total: cartTotal
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI with discount
            updateTotalWithDiscount(data.coupon);
            // Show success message
            showNotification('Coupon applied successfully!');
            // Disable input and button
            couponInput.disabled = true;
            const applyBtn = document.getElementById('sidebar-apply-coupon');
            if (applyBtn) {
                applyBtn.disabled = true;
                applyBtn.textContent = 'Applied';
                applyBtn.classList.add('bg-green-600', 'hover:bg-green-600');
            }
        } else {
            showErrorNotification(data.message || 'Coupon is not valid');
        }
    })
    .catch(error => {
        console.error('Error applying coupon:', error);
        showErrorNotification('Error applying coupon. Please try again.');
    });
}

// Update total with discount
function updateTotalWithDiscount(coupon) {
    if (!coupon) return;
    
    // Store coupon in localStorage for checkout
    localStorage.setItem('applied_coupon', JSON.stringify(coupon));
    
    // Show discount in checkout page if discount section exists
    const discountSection = document.getElementById('applied-discount');
    if (discountSection) {
        discountSection.classList.remove('hidden');
        const discountAmount = document.getElementById('discount-amount');
        if (discountAmount) {
            discountAmount.textContent = '-$' + coupon.discount_amount.toFixed(2);
        }
        const discountName = document.getElementById('discount-name');
        if (discountName) {
            discountName.textContent = coupon.code;
        }
        const discountType = document.getElementById('discount-type');
        if (discountType) {
            if (coupon.discount_type === 'percentage') {
                discountType.textContent = '-' + coupon.discount_value + '% OFF';
            } else {
                discountType.textContent = '-$' + coupon.discount_value + ' OFF';
            }
        }
    }
    
    // Update total using updateGrandTotal function if on checkout page
    if (window.location.pathname.includes('checkout') && typeof updateGrandTotal === 'function') {
        updateGrandTotal();
    }
}
