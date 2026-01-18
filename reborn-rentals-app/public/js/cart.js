// HTML Escape Helper Function to prevent XSS attacks
function escapeHtml(text) {
    if (text === null || text === undefined) {
        return '';
    }
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
}

// Cache DOM elements and data
let csrfTokenCache = null;
let cartCache = { data: null, timestamp: 0 };
let updateCartPending = false;
const CART_CACHE_TTL = 300;

let localCartState = {};
let quantityUpdateTimers = {};
let quantityUpdatePending = {};
let productPrices = {};
let serverBaseQuantities = {};

function getCsrfToken() {
    if (!csrfTokenCache) {
        const meta = document.querySelector('meta[name="csrf-token"]');
        csrfTokenCache = meta ? meta.content : '';
    }
    return csrfTokenCache;
}

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
                
                // Lightweight drag image
                const dragImage = card.cloneNode(false);
                const img = card.querySelector('img');
                if (img) {
                    const imgClone = img.cloneNode(true);
                    dragImage.appendChild(imgClone);
                }
                dragImage.style.cssText = 'width:' + card.offsetWidth + 'px;opacity:0.9;transform:rotate(5deg) scale(0.95);box-shadow:0 20px 40px rgba(206,151,4,0.4);border:2px solid #CE9704;border-radius:8px;pointer-events:none;position:absolute;top:-1000px';
                document.body.appendChild(dragImage);
                dragEvent.dataTransfer.setDragImage(dragImage, dragImage.offsetWidth / 2, dragImage.offsetHeight / 2);
                requestAnimationFrame(() => {
                    if (dragImage.parentNode) {
                        dragImage.parentNode.removeChild(dragImage);
                    }
                });
            }
            
            // Efectos visuales mejorados en la tarjeta original
            card.style.opacity = '0.4';
            card.style.transform = 'scale(0.95)';
            card.style.transition = 'all 0.2s ease';
            card.style.filter = 'blur(2px)';
            card.style.cursor = 'grabbing';
            
            // Agregar clase para estilos adicionales
            card.classList.add('dragging');
        });

        card.addEventListener('dragend', function(e) {
            card.style.cssText = card.style.cssText.replace(/opacity:[^;]*;?|transform:[^;]*;?|filter:[^;]*;?|cursor:[^;]*;?/g, '');
            card.style.cssText += ';opacity:1;transform:scale(1);filter:blur(0);cursor:move;transition:all 0.3s ease';
            card.classList.remove('dragging');
            
            // Remover cualquier indicador de drop
            document.querySelectorAll('.drop-zone-active').forEach(zone => {
                zone.classList.remove('drop-zone-active');
            });
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
            
            // Efectos visuales mejorados
            this.classList.add('drop-zone-active');
            this.style.backgroundColor = '#B8860B';
            this.style.transform = 'scale(1.1)';
            this.style.transition = 'all 0.2s ease';
            this.style.boxShadow = '0 0 20px rgba(206, 151, 4, 0.6), 0 0 40px rgba(206, 151, 4, 0.4)';
            this.style.border = '2px solid #FFD700';
        });

        cartBtn.addEventListener('dragleave', function(e) {
            // Solo restaurar si realmente salimos del botón (no solo pasamos sobre un hijo)
            if (!this.contains(e.relatedTarget)) {
                this.classList.remove('drop-zone-active');
                this.style.backgroundColor = '#CE9704';
                this.style.transform = 'scale(1)';
                this.style.boxShadow = '';
                this.style.border = '';
            }
        });

        cartBtn.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Efecto de "éxito" antes de restaurar
            this.style.backgroundColor = '#10b981';
            this.style.transform = 'scale(1.15)';
            this.style.boxShadow = '0 0 30px rgba(16, 185, 129, 0.8)';
            
            // Restaurar después de un momento
            setTimeout(() => {
                this.classList.remove('drop-zone-active');
                this.style.backgroundColor = '#CE9704';
                this.style.transform = 'scale(1)';
                this.style.boxShadow = '';
                this.style.border = '';
            }, 300);

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

    // Configurar cart-sidebar y cart-items como zonas de drop
    const cartSidebar = document.getElementById('cart-sidebar');
    const cartItems = document.querySelector('.cart-items');
    
    function setupDropZone(element) {
        if (!element || element.hasAttribute('data-drop-setup')) {
            return;
        }
        element.setAttribute('data-drop-setup', 'true');
        
        element.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (e.dataTransfer) {
                e.dataTransfer.dropEffect = 'move';
            }
            this.classList.add('drop-zone-active');
        });

        element.addEventListener('dragleave', function(e) {
            if (!this.contains(e.relatedTarget)) {
                this.classList.remove('drop-zone-active');
            }
        });

        element.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('drop-zone-active');

            try {
                if (e.dataTransfer) {
                    const productData = JSON.parse(
                        e.dataTransfer.getData('text/plain')
                    );
                    if (productData && productData.id) {
                        addToCart(productData.id, productData.name, productData.price);
                    }
                }
            } catch (error) {
                console.error('Error processing the drop:', error);
            }
        });
    }
    
    if (cartSidebar) {
        setupDropZone(cartSidebar);
    }
    
    if (cartItems) {
        setupDropZone(cartItems);
    }
}

function addToCart(productId, productName, productPrice) {
    productId = String(productId);
    showNotification(productName + ' added to cart');
    
    const currentQty = localCartState[productId] || 0;
    const newQty = currentQty + 1;
    localCartState[productId] = newQty;
    
    if (!productPrices[productId] && productPrice) {
        productPrices[productId] = parseFloat(productPrice) || 0;
    }
    
    const subtotalAmountEl = document.getElementById('subtotal-amount');
    const totalItemsEl = document.getElementById('total-items');
    const badge = document.getElementById('cart-badge');
    
    let newTotal = 0;
    let totalItems = 0;
    for (const [pid, qty] of Object.entries(localCartState)) {
        const p = productPrices[pid] || 0;
        newTotal += p * qty;
        totalItems += qty;
    }
    
    if (subtotalAmountEl) {
        subtotalAmountEl.textContent = '$' + newTotal.toFixed(2);
    }
    if (totalItemsEl) {
        totalItemsEl.textContent = totalItems;
    }
    if (badge) {
        badge.textContent = totalItems;
        badge.classList.remove('hidden');
    }
    
    const subtotalSection = document.getElementById('subtotal-section');
    if (subtotalSection) {
        subtotalSection.classList.remove('hidden');
    }
    
    const proceedBtn = document.getElementById('when-where-btn');
    if (proceedBtn && !window.location.pathname.includes('checkout')) {
        proceedBtn.disabled = false;
        proceedBtn.classList.remove('cursor-not-allowed', 'bg-gray-600', 'text-gray-400');
        proceedBtn.classList.add('bg-[#CE9704]', 'text-white', 'hover:bg-[#B8860B]');
        proceedBtn.style.pointerEvents = 'auto';
    }
    
    cartCache = { data: null, timestamp: 0 };
    
    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
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
            serverBaseQuantities[productId] = (serverBaseQuantities[productId] || 0) + 1;
            if (!cartCache.data || !cartCache.data.cart) {
                updateCartDisplay(true);
            }
        } else {
            const oldQty = currentQty;
            if (oldQty > 0) {
                localCartState[productId] = oldQty;
            } else {
                delete localCartState[productId];
            }
            updateCartDisplay(true);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const oldQty = currentQty;
        if (oldQty > 0) {
            localCartState[productId] = oldQty;
        } else {
            delete localCartState[productId];
        }
        updateCartDisplay(true);
    });
}

function updateCartDisplay(forceRefresh) {
    const now = Date.now();
    if (!forceRefresh && cartCache.data && (now - cartCache.timestamp) < CART_CACHE_TTL) {
        const data = cartCache.data;
        if (data.cart && data.products) {
            renderCart(data.cart, data.products, data.total);
            updateCartBadge(data.cart_count);
        }
        return;
    }
    
    if (updateCartPending) return;
    updateCartPending = true;
    
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
        cartCache = { data: data, timestamp: Date.now() };
        if (data.cart && data.products) {
            if (data.cart) {
                localCartState = Object.assign({}, data.cart);
                serverBaseQuantities = Object.assign({}, data.cart);
            }
            if (Array.isArray(data.products)) {
                data.products.forEach(p => {
                    productPrices[p.id] = parseFloat(p.price) || 0;
                });
            }
            renderCart(data.cart, data.products, data.total);
            updateCartBadge(data.cart_count);
        }
        updateCartPending = false;
    })
    .catch(error => {
        console.log('Cart not available:', error);
        updateCartPending = false;
    });
}

function renderCart(cart, products, total) {
    const cartItems = document.querySelector('.cart-items');
    const subtotalSection = document.getElementById('subtotal-section');
    const proceedBtn = document.getElementById('when-where-btn');
    
    if (!cartItems) return;
    
    // Check if we're on checkout page or directions page
    const isCheckoutPage = window.location.pathname.includes('checkout');
    const isDirectionsPage = window.location.pathname.includes('directions');
    
    // Convert products array to object for easy lookup
    const productsMap = {};
    if (Array.isArray(products)) {
        products.forEach(p => {
            productsMap[p.id] = p;
        });
    }
    
    // On checkout page, render checkout form
    if (isCheckoutPage) {
        cartItems.innerHTML = `
            <div class="text-white">
                <!-- Title: Checkout Details -->
                <h3 class="text-2xl font-bold text-[#CE9704] mb-6">Checkout Details</h3>
                
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
                
                <!-- Payment Notice - Prominent Warning -->
                <div class="mb-6 p-4 bg-yellow-50 border-2 border-yellow-400 rounded-lg">
                    <div class="flex items-start mb-3">
                        <svg class="w-6 h-6 text-yellow-600 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div class="flex-1">
                            <h4 class="text-yellow-900 font-bold text-sm mb-2">Important Payment Information</h4>
                            <ul class="text-yellow-800 text-xs space-y-1.5">
                                <li class="flex items-start">
                                    <span class="mr-2">•</span>
                                    <span><strong>No payment is collected on this website.</strong></span>
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2">•</span>
                                    <span>Payment details will be sent to you by email via invoice after your request is processed.</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2">•</span>
                                    <span>Taxes will be calculated separately and included in your invoice.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
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
                        Submit Rental Request
                    </button>
                </div>
            </div>
        `;
        if (subtotalSection) subtotalSection.classList.add('hidden');
        
        // Ocultar el scrollbar del contenedor de checkout pero mantener el scroll funcional
        cartItems.classList.add('scrollbar-hide');
        cartItems.classList.add('overflow-y-auto');
        
        // Ocultar el botón "when-where-btn" en checkout porque el formulario tiene su propio botón
        if (proceedBtn) {
            proceedBtn.style.display = 'none';
        }
        
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
        cartItems.classList.remove('flex-col', 'overflow-y-auto', 'scrollbar-hide');
        cartItems.classList.add('flex', 'items-center', 'justify-center');
        if (subtotalSection) subtotalSection.classList.add('hidden');
        if (proceedBtn) {
            // Ocultar el botón en checkout
            if (isCheckoutPage) {
                proceedBtn.style.display = 'none';
            } else {
                proceedBtn.style.display = 'block';
                proceedBtn.disabled = true;
                proceedBtn.classList.add('cursor-not-allowed');
                proceedBtn.style.pointerEvents = 'none';
                proceedBtn.classList.remove('bg-[#CE9704]', 'text-white', 'hover:bg-[#B8860B]');
                proceedBtn.classList.add('bg-gray-600', 'text-gray-400');
            }
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
            
            // Escape all user-provided data to prevent XSS
            const escapedName = escapeHtml(product.name);
            const escapedDescription = product.description ? escapeHtml(product.description) : '';
            const escapedImageUrl = product.image_url ? escapeHtml(product.image_url) : '/Product1.png';
            // Ensure productId is a number (it comes from the loop, but parse to be safe)
            const safeProductId = parseInt(productId);
            
            html += `
                <div class="bg-[#4A4A4A] rounded-lg border border-gray-600 mb-3 w-full overflow-hidden shadow-md">
                    <div class="flex items-center p-3">
                        <div class="shrink-0 mr-3">
                            <div class="w-16 h-16 bg-white rounded-lg p-1 flex items-center justify-center">
                                <img src="${escapedImageUrl}" alt="${escapedName}" class="w-full h-full object-contain" />
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="text-white font-bold text-base uppercase leading-tight pr-2 truncate">${escapedName}</h4>
                                ${!isCheckoutPage ? `<button onclick="removeFromCart(${safeProductId})" class="text-gray-400 hover:text-red-400 p-1 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>` : ''}
                            </div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-[#CE9704] font-semibold text-xs">ID: ${safeProductId}</span>
                                <span class="text-white font-bold text-base">$${parseFloat(product.price).toFixed(2)}/day*</span>
                            </div>
                            <div class="flex items-center justify-end mb-2">
                                <div class="flex items-center bg-gray-600 rounded-md">
                                    <button onclick="updateQuantity(${safeProductId}, -1)" ${disabledAttr} class="bg-gray-600 text-white px-2 py-1 rounded-l-md text-xs hover:bg-gray-500 ${disableClass}" ${disabledAttr ? 'style="pointer-events: none;"' : ''}>−</button>
                                    <span id="quantity-${safeProductId}" class="quantity-display text-white mx-2 font-bold text-sm min-w-[16px] text-center">${quantity}</span>
                                    <button onclick="updateQuantity(${safeProductId}, 1)" ${disabledAttr} class="bg-gray-600 text-white px-2 py-1 rounded-r-md text-xs hover:bg-gray-500 ${disableClass}" ${disabledAttr ? 'style="pointer-events: none;"' : ''}>+</button>
                                </div>
                            </div>
                            ${escapedDescription ? `<div class="bg-gray-800 p-2 rounded text-xs">
                                <div class="text-white">${escapedDescription}</div>
                            </div>` : ''}
                        </div>
                    </div>
                </div>
            `;
        }
        
        // Asegurar que el contenedor tenga las clases correctas para mostrar items verticalmente
        cartItems.classList.remove('items-center', 'justify-center', 'scrollbar-hide');
        cartItems.classList.add('flex-col', 'overflow-y-auto');
        
        cartItems.innerHTML = html;
        
        if (subtotalSection) {
            subtotalSection.classList.remove('hidden');
            const subtotalAmountEl = document.getElementById('subtotal-amount');
            const totalItemsEl = document.getElementById('total-items');
            if (subtotalAmountEl) {
                subtotalAmountEl.textContent = '$' + total.toFixed(2);
            }
            if (totalItemsEl) {
                totalItemsEl.textContent = totalItems;
            }
        }
        
        if (proceedBtn) {
            // Ocultar el botón en checkout porque el formulario tiene su propio botón
            if (isCheckoutPage) {
                proceedBtn.style.display = 'none';
            } else {
                proceedBtn.style.display = 'block';
                proceedBtn.disabled = false;
                proceedBtn.classList.remove('cursor-not-allowed', 'bg-gray-600', 'text-gray-400');
                proceedBtn.classList.add('bg-[#CE9704]', 'text-white', 'hover:bg-[#B8860B]');
                proceedBtn.style.pointerEvents = 'auto';
            }
        }
    }
}

function updateQuantity(productId, change) {
    productId = String(productId);
    const quantitySpan = document.getElementById(`quantity-${productId}`);
    if (!quantitySpan) {
        console.error('Quantity span not found for product:', productId);
        return;
    }
    
    const currentQuantity = localCartState[productId] || parseInt(quantitySpan.textContent.trim()) || 0;
    const newQuantity = Math.max(0, currentQuantity + change);
    
    if (change < 0) {
        updateQuantityDirectly(productId, change, currentQuantity, newQuantity);
        return;
    }
    
    if (change > 0) {
        const directionsData = localStorage.getItem('reborn-rentals-directions');
        if (directionsData) {
            try {
                const directions = JSON.parse(directionsData);
                if (directions.startDate && directions.endDate) {
                    checkStockBeforeQuantityUpdate(productId, newQuantity, directions.startDate, directions.endDate, change, currentQuantity);
                    return;
                }
            } catch (e) {
                console.error('Error parsing directions data:', e);
            }
        }
        
        updateQuantityDirectly(productId, change, currentQuantity, newQuantity);
    } else {
        updateQuantityDirectly(productId, change, currentQuantity, newQuantity);
    }
}

// Check stock before updating quantity
function checkStockBeforeQuantityUpdate(productId, requestedQuantity, startDate, endDate, change, currentQuantity) {
    productId = String(productId);
    updateQuantityDirectly(productId, change, currentQuantity, requestedQuantity);
    
    fetch(`/stock/check?product_id=${productId}&start_date=${startDate}&end_date=${endDate}&quantity=${requestedQuantity}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (!data.allowed) {
            const availableStock = data.available_stock || 0;
            const message = data.message || 'Not enough stock available';
            
            const revertedQty = currentQuantity;
            updateQuantityDirectly(productId, -(requestedQuantity - currentQuantity), requestedQuantity, revertedQty);
            
            if (typeof window.showStockNotification === 'function') {
                window.showStockNotification(
                    'error',
                    'Stock Not Available',
                    `Cannot increase quantity. Available: ${availableStock} units. ${message}`,
                    data.reservation_periods || null,
                    null
                );
            } else if (typeof window.showErrorNotification === 'function') {
                showErrorNotification(`Not enough stock available. Available: ${availableStock} units.`);
            } else {
                alert(`Not enough stock available. Available: ${availableStock} units.`);
            }
        }
    })
    .catch(error => {
        console.error('Error checking stock:', error);
    });
}

function updateQuantityDirectly(productId, change, currentQuantity, newQuantity) {
    const quantitySpan = document.getElementById(`quantity-${productId}`);
    if (!quantitySpan) return;
    
    productId = String(productId);
    
    quantitySpan.textContent = newQuantity;
    
    if (newQuantity > 0) {
        localCartState[productId] = newQuantity;
    } else {
        delete localCartState[productId];
    }
    
    const price = productPrices[productId] || 0;
    const subtotalAmountEl = document.getElementById('subtotal-amount');
    const totalItemsEl = document.getElementById('total-items');
    
    let newTotal = 0;
    let totalItems = 0;
    for (const [pid, qty] of Object.entries(localCartState)) {
        const p = productPrices[pid] || 0;
        newTotal += p * qty;
        totalItems += qty;
    }
    
    if (subtotalAmountEl) {
        subtotalAmountEl.textContent = '$' + newTotal.toFixed(2);
    }
    if (totalItemsEl) {
        totalItemsEl.textContent = totalItems;
    }
    
    const badge = document.getElementById('cart-badge');
    if (badge) {
        if (totalItems > 0) {
            badge.textContent = totalItems;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }
    
    if (newQuantity === 0) {
        const productCard = document.querySelector(`[data-product-id="${productId}"]`);
        if (productCard) {
            const buttons = productCard.querySelectorAll('button');
            buttons.forEach(btn => {
                btn.disabled = true;
                btn.style.opacity = '0.5';
            });
        }
        if (quantityUpdateTimers[productId]) {
            clearTimeout(quantityUpdateTimers[productId]);
            delete quantityUpdateTimers[productId];
        }
        quantityUpdatePending[productId] = true;
        syncQuantityToServer(productId, 0, () => {
            updateCartDisplay(true);
        });
        return;
    }
    
    if (quantityUpdateTimers[productId]) {
        clearTimeout(quantityUpdateTimers[productId]);
    }
    
    quantityUpdateTimers[productId] = setTimeout(() => {
        if (quantityUpdatePending[productId]) return;
        quantityUpdatePending[productId] = true;
        syncQuantityToServer(productId, null, () => {
            delete quantityUpdatePending[productId];
        });
    }, 250);
}

function syncQuantityToServer(productId, change, callback) {
    const currentLocalQty = localCartState[productId] || 0;
    const serverQty = serverBaseQuantities[productId] || 0;
    const actualChange = currentLocalQty - serverQty;
    
    if (actualChange === 0) {
        if (callback) callback();
        return;
    }
    
    fetch(`/cart/${productId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            quantity: actualChange
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            serverBaseQuantities[productId] = currentLocalQty;
        } else {
            updateCartDisplay(true);
        }
        if (callback) callback();
    })
    .catch(error => {
        console.error('Error updating quantity:', error);
        updateCartDisplay(true);
        if (callback) callback();
    });
}


function updateCartBadgeFromServer() {
    let totalItems = 0;
    for (const qty of Object.values(localCartState)) {
        totalItems += qty;
    }
    updateCartBadge(totalItems);
}

function removeFromCart(productId) {
    productId = String(productId);
    delete localCartState[productId];
    delete serverBaseQuantities[productId];
    
    if (quantityUpdateTimers[productId]) {
        clearTimeout(quantityUpdateTimers[productId]);
        delete quantityUpdateTimers[productId];
    }
    delete quantityUpdatePending[productId];
    
    cartCache = { data: null, timestamp: 0 };
    fetch(`/cart/${productId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartDisplay(true);
            showNotification('Product removed from cart');
        } else {
            updateCartDisplay(true);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        updateCartDisplay(true);
    });
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
                if (typeof toast !== 'undefined') {
                    toast.error('Please accept the Terms and Conditions to continue.');
                } else {
                    alert('Please accept the Terms and Conditions to continue.');
                }
                return;
            }
            
            // Check if all required data is filled
            const foremanDetails = localStorage.getItem('foreman-details');
            const billingDetails = localStorage.getItem('billing-details');
            
            // Billing details is required (foreman can be empty, will use billing data)
            if (!billingDetails) {
                if (typeof toast !== 'undefined') {
                    toast.error('Please complete billing details first.');
                } else {
                    alert('Please complete billing details first.');
                }
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
        if (typeof toast !== 'undefined') {
            toast.error('Please complete all foreman fields.');
        } else {
            alert('Please complete all foreman fields.');
        }
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
    
    // Escape all user input to prevent XSS
    const escapedFirstName = escapeHtml(data.firstName || '');
    const escapedLastName = escapeHtml(data.lastName || '');
    const escapedPhone = escapeHtml(data.phone || '');
    const escapedEmail = escapeHtml(data.email || '');
    
    container.innerHTML = `
        <label class="block text-white text-sm mb-4">Foreman Details / Receiving person</label>
        <div class="bg-white bg-opacity-10 rounded-lg p-4 mb-4">
            <div class="space-y-3">
                <div>
                    <p class="text-white text-xs opacity-75 mb-1">Full Name</p>
                    <p class="text-white font-semibold text-sm">${escapedFirstName} ${escapedLastName}</p>
                </div>
                <div class="border-t border-white border-opacity-20 pt-3">
                    <p class="text-white text-xs opacity-75 mb-1">Phone Number</p>
                    <p class="text-white font-semibold text-sm">${escapedPhone}</p>
                </div>
                <div class="border-t border-white border-opacity-20 pt-3">
                    <p class="text-white text-xs opacity-75 mb-1">Email</p>
                    <p class="text-white font-semibold text-sm">${escapedEmail}</p>
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
    
    // Escape all user input to prevent XSS
    const escapedFirstName = escapeHtml(savedData.firstName || '');
    const escapedLastName = escapeHtml(savedData.lastName || '');
    const escapedPhone = escapeHtml(savedData.phone || '');
    const escapedEmail = escapeHtml(savedData.email || '');
    
    container.innerHTML = `
        <label class="block text-white text-sm mb-4">Foreman Details / Receiving person</label>
        <div class="mb-3">
            <label class="block text-white text-xs mb-1.5">First Name</label>
            <input type="text" id="foreman-first-name" value="${escapedFirstName}" placeholder="Enter first name" class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
        </div>
        <div class="mb-3">
            <label class="block text-white text-xs mb-1.5">Last Name</label>
            <input type="text" id="foreman-last-name" value="${escapedLastName}" placeholder="Enter last name" class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
        </div>
        <div class="mb-3">
            <label class="block text-white text-xs mb-1.5">Phone Number</label>
            <input type="tel" id="foreman-phone" value="${escapedPhone}" placeholder="Enter phone number" class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
        </div>
        <div class="mb-4">
            <label class="block text-white text-xs mb-1.5">Email</label>
            <input type="email" id="foreman-email" value="${escapedEmail}" placeholder="Enter email" class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
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
        if (typeof toast !== 'undefined') {
            toast.error('Please complete all required fields (marked with *).');
        } else {
            alert('Please complete all required fields (marked with *).');
        }
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
    
    // Escape all user input to prevent XSS
    const escapedFirstName = escapeHtml(data.firstName || '');
    const escapedLastName = escapeHtml(data.lastName || '');
    const escapedEmail = escapeHtml(data.email || '');
    const escapedPhone = escapeHtml(data.phone || '');
    const escapedAddressLine1 = escapeHtml(data.addressLine1 || '');
    const escapedAddressLine2 = escapeHtml(data.addressLine2 || '');
    const escapedCity = escapeHtml(data.city || '');
    const escapedState = escapeHtml(data.state || '');
    const escapedZip = escapeHtml(data.zip || '');
    const escapedCountry = escapeHtml(data.country || '');
    const escapedCompanyName = escapeHtml(data.companyName || '');
    const escapedJobTitle = escapeHtml(data.jobTitle || '');
    
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
                    <p class="text-white font-semibold text-sm">${escapedFirstName} ${escapedLastName}</p>
                </div>
                <div class="border-t border-white border-opacity-20 pt-3">
                    <p class="text-white text-xs opacity-75 mb-1">Contact</p>
                    <p class="text-white font-semibold text-sm">${escapedEmail}</p>
                    <p class="text-white font-semibold text-sm">${escapedPhone}</p>
                </div>
                ${escapedAddressLine1 ? `
                <div class="border-t border-white border-opacity-20 pt-3">
                    <p class="text-white text-xs opacity-75 mb-1">Address</p>
                    <p class="text-white font-semibold text-sm">${escapedAddressLine1}</p>
                    ${escapedAddressLine2 ? `<p class="text-white font-semibold text-sm">${escapedAddressLine2}</p>` : ''}
                    <p class="text-white font-semibold text-sm">${escapedCity}${escapedState ? ', ' + escapedState : ''} ${escapedZip}</p>
                    <p class="text-white font-semibold text-sm">${escapedCountry}</p>
                </div>
                ` : ''}
                <div class="border-t border-white border-opacity-20 pt-3">
                    <p class="text-white text-xs opacity-75 mb-1">Assigned to Company</p>
                    <p class="text-white font-semibold text-sm">${data.isCompany ? 'Yes' : 'No'}</p>
                    ${data.isCompany && escapedCompanyName ? `
                        <p class="text-white font-semibold text-sm mt-1">${escapedCompanyName}</p>
                        ${escapedJobTitle ? `<p class="text-white font-semibold text-sm">${escapedJobTitle}</p>` : ''}
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
    
    // Escape all user input to prevent XSS
    const escapedFirstName = escapeHtml(savedData.firstName || '');
    const escapedLastName = escapeHtml(savedData.lastName || '');
    const escapedEmail = escapeHtml(savedData.email || '');
    const escapedPhone = escapeHtml(savedData.phone || '');
    const escapedAddressLine1 = escapeHtml(savedData.addressLine1 || '');
    const escapedAddressLine2 = escapeHtml(savedData.addressLine2 || '');
    const escapedCity = escapeHtml(savedData.city || '');
    const escapedState = escapeHtml(savedData.state || '');
    const escapedZip = escapeHtml(savedData.zip || '');
    const escapedCountry = escapeHtml(savedData.country || '');
    const escapedCompanyName = escapeHtml(savedData.companyName || '');
    const escapedJobTitle = escapeHtml(savedData.jobTitle || '');
    
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
                <input type="text" id="billing-first-name" value="${escapedFirstName}" placeholder="First Name*" class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
            </div>
            <div>
                <input type="text" id="billing-last-name" value="${escapedLastName}" placeholder="Last Name*" class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3 mb-3">
            <div>
                <input type="email" id="billing-email" value="${escapedEmail}" placeholder="Email*" class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
            </div>
            <div>
                <input type="tel" id="billing-phone" value="${escapedPhone}" placeholder="Phone number*" class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
            </div>
        </div>
        <div class="mb-3">
            <input type="text" id="billing-address-line-1" value="${escapedAddressLine1}" placeholder="Address line 1" class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
        </div>
        <div class="mb-3">
            <input type="text" id="billing-address-line-2" value="${escapedAddressLine2}" placeholder="Address line 2" class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
        </div>
        <div class="mb-3">
            <input type="text" id="billing-city" value="${escapedCity}" placeholder="City" class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
        </div>
        <div class="mb-3">
            <input type="text" id="billing-state" value="${escapedState}" placeholder="State / Province" class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
        </div>
        <div class="mb-3">
            <input type="text" id="billing-zip" value="${escapedZip}" placeholder="Zip / Postal Code" class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
        </div>
        <div class="mb-4">
            <input type="text" id="billing-country" value="${escapedCountry}" placeholder="Country" class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
        </div>
        <div class="flex items-center mb-4">
            <input type="checkbox" id="billing-is-company" ${isCompanyChecked} class="w-4 h-4 bg-white border-gray-300 rounded text-[#CE9704] focus:ring-[#CE9704]"/>
            <label for="billing-is-company" class="ml-2 text-white text-sm">Billed is assigned to a company.</label>
        </div>
        <div id="company-fields" class="${companyFieldsClass} mb-4">
            <div class="mb-3">
                <input type="text" id="billing-company-name" value="${escapedCompanyName}" placeholder="Company Name" class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
            </div>
            <div class="mb-4">
                <input type="text" id="billing-job-title" value="${escapedJobTitle}" placeholder="Job Title" class="w-full px-3 py-1.5 bg-white border-none rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#CE9704]"/>
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
    
}

// Payment processing removed - payments and taxes are handled in Odoo
// This website only collects rental requests

function clearCart() {
    cartCache = { data: null, timestamp: 0 };
    fetch('/cart', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartDisplay(true);
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
            // Reset styles for next open
            modal.style.opacity = '';
            modalContent.style.opacity = '';
            modalContent.style.transform = '';
        }, 300);
    }
}

function goToHomepage() {
    // Clear localStorage
    localStorage.removeItem('reborn-rentals-directions');
    localStorage.removeItem('foreman-details');
    localStorage.removeItem('billing-details');
    
    cartCache = { data: null, timestamp: 0 };
    fetch('/cart', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': getCsrfToken(),
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

// Note: Checkout button listener is now handled in setupCheckoutFormListeners()
// to avoid duplicate event listeners

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
            // Note: No tax calculations - this is a subtotal estimate only
            // Final totals and taxes are calculated in Odoo
        }
    }
    
    cartCache = { data: null, timestamp: 0 };
    fetch('/cart/apply-coupon', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
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
