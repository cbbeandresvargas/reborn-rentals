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
                            <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                                <!-- Product Image -->
                                <div class="shrink-0">
                                    <div class="w-16 h-16 bg-white rounded-lg p-2 flex items-center justify-center shadow-sm">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-contain">
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
                                    <div class="text-xl font-bold text-gray-900" id="item-total-{{ $productId }}">{{ number_format($product->price * $quantity * 30, 2) }} $</div>
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
                            <div class="text-lg font-bold text-gray-900">${{ number_format($total * 0.02, 2) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Total Section -->
                <div class="p-6 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl font-bold text-gray-900">Total:</h3>
                        <div class="text-3xl font-bold text-gray-900">${{ number_format($total * 1.02, 2) }}</div>
                    </div>
                </div>

                <!-- Complete Order Button -->
                <div class="p-6">
                    <button type="submit" class="w-full bg-[#CE9704] text-white font-bold py-3 px-6 rounded-lg hover:bg-[#B8860B] transition-colors text-lg">
                        Complete Order
                    </button>
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

// Función para ajustar días
function adjustDays(productId, change) {
    const daysElement = document.getElementById('days-' + productId);
    const itemTotalElement = document.getElementById('item-total-' + productId);
    
    if (daysElement && itemTotalElement) {
        let currentDays = parseInt(daysElement.textContent.replace(' Days', ''));
        const newDays = Math.max(1, currentDays + change);
        daysElement.textContent = newDays + ' Days';
        
        // Update total (you'll need to get the price and quantity from the data)
        // For now, just update the days display
    }
}
</script>
@endsection

