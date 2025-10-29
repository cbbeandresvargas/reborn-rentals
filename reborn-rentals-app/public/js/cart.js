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
            showNotification(productName + ' added to cart');
            // Re-configurar drag and drop después de actualizar
            setTimeout(() => setupDragAndDrop(), 100);
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
    
    // Convert products array to object for easy lookup
    const productsMap = {};
    if (Array.isArray(products)) {
        products.forEach(p => {
            productsMap[p.id] = p;
        });
    }
    
    if (!cart || Object.keys(cart).length === 0) {
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
            
            html += `
                <div class="bg-[#4A4A4A] rounded-lg border border-gray-600 mb-3 overflow-hidden shadow-md">
                    <div class="flex items-center p-3">
                        <div class="flex-shrink-0 mr-3">
                            <div class="w-16 h-16 bg-white rounded-lg p-1 flex items-center justify-center">
                                <img src="${(product.image_url ? '/storage/' + product.image_url : '/Product1.png')}" alt="${product.name}" class="w-full h-full object-contain" />
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="text-white font-bold text-base uppercase leading-tight pr-2 truncate">${product.name}</h4>
                                <button onclick="removeFromCart(${product.id})" class="text-gray-400 hover:text-red-400 p-1 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-[#CE9704] font-semibold text-xs">ID: ${product.id}</span>
                                <span class="text-white font-bold text-base">$${product.price}/day*</span>
                            </div>
                            <div class="flex items-center justify-end mb-2">
                                <div class="flex items-center bg-gray-600 rounded-md">
                                    <button onclick="updateQuantity(${product.id}, -1)" class="bg-gray-600 text-white px-2 py-1 rounded-l-md text-xs hover:bg-gray-500">−</button>
                                    <span class="text-white mx-2 font-bold text-sm min-w-[16px] text-center">${quantity}</span>
                                    <button onclick="updateQuantity(${product.id}, 1)" class="bg-gray-600 text-white px-2 py-1 rounded-r-md text-xs hover:bg-gray-500">+</button>
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

// Make functions global
window.updateQuantity = updateQuantity;
window.removeFromCart = removeFromCart;
window.updateCartDisplay = updateCartDisplay;
window.setupDragAndDrop = setupDragAndDrop;
window.addToCart = addToCart;

