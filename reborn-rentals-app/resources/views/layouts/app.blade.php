<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Reborn Rentals')</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#CE9704',
                        'primary-dark': '#B8860B',
                        'bg-dark': '#4A4A4A',
                        'bg-cart': '#2F2F2F',
                        'bg-light': '#BBBBBB',
                    }
                }
            }
        }
    </script>
    
    <style>
        /* Hide scrollbar but keep functionality */
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        
        /* Asegurar que los elementos del navbar sean clicables */
        nav a,
        nav button {
            pointer-events: auto !important;
            position: relative;
            z-index: 10 !important;
        }
        
        /* Estilos para enlaces de navegación del sidebar */
        #menu-sidebar .flex-1 nav a {
            display: block;
            width: 100%;
            text-align: left;
        }
        
        /* Asegurar que todos los enlaces del sidebar sean clicables */
        #menu-sidebar a {
            cursor: pointer;
        }
        
        /* Asegurar que el overlay no bloquee cuando está oculto */
        #sidebar-overlay[style*="opacity: 0"],
        #sidebar-overlay.invisible {
            pointer-events: none !important;
            z-index: -1 !important;
        }
        
        /* Los sidebars cuando están fuera de la pantalla no deben bloquear clics */
        #menu-sidebar.-translate-x-full {
            pointer-events: none !important;
        }
        
        #cart-sidebar.translate-x-full {
            pointer-events: none !important;
        }
        
        /* Z-index inicial del navbar */
        nav {
            position: relative !important;
            z-index: 10 !important;
            transition: z-index 0s;
        }
        
        /* Z-index inicial de los sidebars (más bajo que header) */
        #menu-sidebar {
            z-index: 9 !important;
            transition: z-index 0s;
        }
        
        #cart-sidebar-container,
        #cart-sidebar {
            z-index: 9 !important;
            transition: z-index 0s;
        }
        
        /* Cuando el sidebar está abierto, tiene z-index 10 y el header baja a 5 */
        nav.sidebar-open {
            z-index: 5 !important;
        }
        
        #menu-sidebar.translate-x-0 {
            z-index: 10 !important;
        }
        
        #cart-sidebar.translate-x-0 ~ *,
        #cart-sidebar-container:has(#cart-sidebar.translate-x-0) {
            z-index: 10 !important;
        }

        /* Contact Float Button Styles */
        #contact-float-container {
            z-index: 8; /* Lower than sidebar (9) and header (10) when sidebar is closed */
        }
        
        /* When sidebar is open, contact button should be below it */
        nav.sidebar-open ~ * #contact-float-container,
        #menu-sidebar.translate-x-0 ~ * #contact-float-container {
            z-index: 5 !important; /* Below sidebar (10) and overlay (9) */
        }

        #contact-panel {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        #contact-panel.open {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
        }

        /* Hover effect for contact button */
        #contact-float-btn:hover #contact-icon-default {
            opacity: 0;
        }

        #contact-float-btn:hover #contact-icon-hover {
            opacity: 1;
        }

        /* When panel is open, keep hover icon visible */
        #contact-float-btn.panel-open #contact-icon-default {
            opacity: 0;
        }

        #contact-float-btn.panel-open #contact-icon-hover {
            opacity: 1;
        }

        /* Responsive adjustments for contact button */
        @media (max-width: 640px) {
            #contact-float-container {
                bottom: 4rem;
                left: 1rem;
            }

            #contact-panel {
                min-width: 180px;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body class="m-0 w-full h-full font-sans">
    @include('components.header')
    @include('components.menu-sidebar')
    @include('components.cart-sidebar')

    <!-- Overlay -->
    <div id="sidebar-overlay" class="fixed top-0 left-0 w-full h-full bg-black/50 opacity-0 invisible pointer-events-none transition-all duration-300 ease-in-out"></div>

    <!-- Payment Method Modal -->
    <div id="payment-method-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-3 sm:p-4 overflow-y-auto" style="display: none;">
        <div class="bg-white rounded-lg max-w-md w-full max-h-[90vh] overflow-y-auto shadow-2xl mx-2 sm:mx-4 my-4">
            <!-- Header -->
            <div class="p-4 sm:p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900" id="payment-modal-title">Payment Method Details</h2>
                    <button onclick="closePaymentMethodModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Content -->
            <div class="p-4 sm:p-6" id="payment-method-form-container">
                <!-- Form content will be inserted here dynamically -->
            </div>
        </div>
    </div>


    <!-- Success Modal -->
    <div id="success-modal" class="fixed inset-0 bg-black bg-opacity-70 backdrop-blur-sm z-50 flex items-center justify-center p-3 sm:p-4 transition-opacity duration-300 overflow-y-auto" style="display: none; opacity: 0;">
        <div class=" from-[#333333] to-[#2a2a2a] rounded-2xl max-w-lg w-full max-h-[95vh] overflow-y-auto mx-2 sm:mx-4 relative shadow-2xl border-2 border-[#CE9704] transform transition-all duration-300 scale-95 my-4 sm:my-0" style="opacity: 0; transform: scale(0.95) translateY(20px);" id="success-modal-content">
            <!-- Decorative Top Border -->
            <div class="absolute top-0 left-0 right-0 h-1  from-[#CE9704] via-[#FFD700] to-[#CE9704] rounded-t-2xl"></div>
            
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
                <div class="w-24 h-0.5  from-transparent via-[#CE9704] to-transparent mx-auto mb-8"></div>
                
                <!-- Details Message -->
                <div class="text-center text-gray-300 mb-8 space-y-3 leading-relaxed">
                    <p class="text-base md:text-lg">We'll email you your reservation confirmation and receipt.</p>
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
                    <button onclick="goToHomepage()" class="w-full  from-[#CE9704] to-[#B8860B] text-white py-4 px-6 rounded-xl font-bold text-lg shadow-lg hover:shadow-xl hover:shadow-[#CE9704]/50 transform hover:scale-[1.02] active:scale-[0.98] transition-all duration-300">
                        Go to Homepage
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="min-h-[calc(100vh-72px)] md:min-h-[calc(100vh-96px)] transition-all duration-300 ease-in-out" id="main-content">
        @yield('content')
    </main>

    <!-- Floating Contact Button -->
    <div class="fixed bottom-6 left-6" id="contact-float-container">
        <!-- Contact Panel (hidden by default) -->
        <div id="contact-panel" class="mb-4 bg-white rounded-lg shadow-2xl p-4 transform transition-all duration-300 ease-in-out opacity-0 invisible translate-y-4" style="min-width: 200px;">
            <div class="flex flex-col gap-3">
                <!-- Messenger -->
                <a href="https://www.messenger.com/t/604615049411143/?messaging_source=source%3Apages%3Amessage_shortlink&source_id=1441792&recurring_notification=0" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-100 transition-colors group">
                    <img src="{{ asset('icons/messenger.svg') }}" alt="Messenger" class="w-10 h-10 flex-shrink-0" />
                    <span class="text-gray-700 font-medium text-sm group-hover:text-[#CE9704]">Messenger</span>
                </a>

                <!-- WhatsApp -->
                <a href="https://api.whatsapp.com/send/?phone=%2B17207349337&text=Hello%2C%20I%27m%20interested%20in%20your%20rental%20services.%20How%20can%20you%20help%20me%20today?&type=phone_number&app_absent=0" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-100 transition-colors group">
                    <img src="{{ asset('icons/whatsapp.svg') }}" alt="WhatsApp" class="w-10 h-10 flex-shrink-0" />
                    <span class="text-gray-700 font-medium text-sm group-hover:text-[#CE9704]">WhatsApp</span>
                </a>

                <!-- Email -->
                <button type="button" onclick="openEmailModal()" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-100 transition-colors group text-left w-full">
                    <img src="{{ asset('icons/email.svg') }}" alt="Email" class="w-10 h-10 flex-shrink-0" />
                    <span class="text-gray-700 font-medium text-sm group-hover:text-[#CE9704]">Email</span>
                </button>

                <!-- QR Code -->
                <button type="button" onclick="showQRCode()" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-100 transition-colors group text-left w-full">
                    <img src="{{ asset('icons/qr.svg') }}" alt="QR Code" class="w-10 h-10 flex-shrink-0" />
                    <span class="text-gray-700 font-medium text-sm group-hover:text-[#CE9704]">QR</span>
                </button>

                <!-- Callback -->
                <button type="button" onclick="openCallback()" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-100 transition-colors group text-left w-full">
                    <img src="{{ asset('icons/support.svg') }}" alt="Callback" class="w-10 h-10 flex-shrink-0" />
                    <span class="text-gray-700 font-medium text-sm group-hover:text-[#CE9704]">Callback</span>
                </button>
            </div>
        </div>

        <!-- Floating Button -->
        <button type="button" id="contact-float-btn" onclick="toggleContactPanel()" class="bg-transparent flex items-center justify-center transition-all duration-300 transform hover:scale-110 focus:outline-none focus:ring-2 focus:ring-[#CE9704] focus:ring-offset-2 relative p-0 border-none w-auto h-auto">
            <img id="contact-icon-default" src="{{ asset('icons/chats.svg') }}" alt="Chat" class="w-12 h-12 transition-opacity duration-300" />
            <img id="contact-icon-hover" src="{{ asset('icons/chatshover.svg') }}" alt="Chat" class="w-12 h-12 transition-opacity duration-300 opacity-0 absolute inset-0 m-auto" />
        </button>
    </div>

    <!-- QR Code Modal -->
    <div id="qr-code-modal" class="fixed inset-0 bg-black bg-opacity-70 z-50 flex items-center justify-center p-3 sm:p-4 overflow-y-auto" style="display: none;">
        <div class="bg-[#2F2F2F] rounded-2xl max-w-md w-full p-5 sm:p-6 md:p-8 relative shadow-2xl my-auto">
            <!-- Logo -->
            <div class="flex justify-center mb-4 sm:mb-5">
                <img src="{{ asset('Logo.png') }}" alt="Reborn Rental" class="h-12 sm:h-16 md:h-20 w-auto object-contain" />
            </div>
            
            <!-- Title -->
            <h3 class="text-xl sm:text-2xl md:text-3xl font-bold text-white text-center mb-3 sm:mb-4">Scan QR Code</h3>
            
            <!-- Description -->
            <p class="text-white text-xs sm:text-sm md:text-base text-center mb-4 sm:mb-5 px-1 sm:px-2 leading-relaxed">
                Scan this QR code to get all our contact information.
            </p>
            
            <!-- QR Code Container -->
            <div class="flex justify-center mb-4 sm:mb-5">
                <div class="bg-white rounded-lg p-3 sm:p-4 flex items-center justify-center">
                    <img src="{{ asset('qr-code.webp') }}" alt="QR Code" class="max-w-full h-auto w-64 sm:w-72 md:w-80" />
                </div>
            </div>
            
            <!-- Done Button -->
            <button onclick="closeQRCode()" class="w-full bg-[#CE9704] hover:bg-[#B8860B] text-white py-2.5 sm:py-3 px-4 sm:px-6 rounded-lg font-semibold text-sm sm:text-base md:text-lg transition-colors duration-200">
                Done
            </button>
        </div>
    </div>

    <!-- Email Modal -->
    <div id="email-modal" class="fixed inset-0 bg-black bg-opacity-70 z-50 flex items-center justify-center p-3 sm:p-4 overflow-y-auto" style="display: none;">
        <div class="bg-[#2F2F2F] rounded-2xl max-w-md w-full p-5 sm:p-6 md:p-8 relative shadow-2xl my-auto">
            <!-- Logo -->
            <div class="flex justify-center mb-4 sm:mb-5">
                <img src="{{ asset('Logo.png') }}" alt="Reborn Rental" class="h-12 sm:h-16 md:h-20 w-auto object-contain" />
            </div>
            
            <!-- Title -->
            <h3 class="text-xl sm:text-2xl md:text-3xl font-bold text-white text-center mb-3 sm:mb-4">Copy Email Address</h3>
            
            <!-- Description -->
            <p class="text-white text-xs sm:text-sm md:text-base text-center mb-4 sm:mb-5 px-1 sm:px-2 leading-relaxed">
                Feel free to email us to get assistance with your purchase, or if you have any questions.
            </p>
            
            <!-- Email Field with Copy Button -->
            <div class="bg-white rounded-lg p-3 sm:p-4 mb-4 sm:mb-5 flex items-center justify-between gap-2 sm:gap-3">
                <span id="email-address" class="text-gray-800 font-medium text-xs sm:text-sm md:text-base flex-1 text-center break-all">support@rebornrental.com</span>
                <button onclick="copyEmail()" class="flex-shrink-0 p-1.5 sm:p-2 hover:bg-gray-100 rounded transition-colors" title="Copy email">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </button>
            </div>
            
            <!-- Done Button -->
            <button onclick="closeEmailModal()" class="w-full bg-[#CE9704] hover:bg-[#B8860B] text-white py-2.5 sm:py-3 px-4 sm:px-6 rounded-lg font-semibold text-sm sm:text-base md:text-lg transition-colors duration-200">
                Done
            </button>
        </div>
    </div>

    <!-- Callback Modal -->
    <div id="callback-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-lg max-w-md w-full p-6 relative">
            <button onclick="closeCallback()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Request a Callback</h3>
            <form id="callback-form" onsubmit="submitCallback(event)">
                <div class="mb-4">
                    <label for="callback-name" class="block text-sm font-medium text-gray-700 mb-2">Your Name</label>
                    <input type="text" id="callback-name" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704]">
                </div>
                <div class="mb-4">
                    <label for="callback-phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input type="tel" id="callback-phone" name="phone" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704]">
                </div>
                <div class="mb-4">
                    <label for="callback-time" class="block text-sm font-medium text-gray-700 mb-2">Preferred Time</label>
                    <select id="callback-time" name="time" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704]">
                        <option value="">Select a time</option>
                        <option value="morning">Morning (9 AM - 12 PM)</option>
                        <option value="afternoon">Afternoon (12 PM - 5 PM)</option>
                        <option value="evening">Evening (5 PM - 8 PM)</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="callback-message" class="block text-sm font-medium text-gray-700 mb-2">Message (Optional)</label>
                    <textarea id="callback-message" name="message" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704]"></textarea>
                </div>
                <button type="submit" class="w-full bg-[#CE9704] hover:bg-[#B8860B] text-white py-2 px-4 rounded-lg font-semibold transition-colors duration-200">
                    Request Callback
                </button>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Load cart on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Wait for cart.js to load
            setTimeout(() => {
                // Update cart display
                if (typeof updateCartDisplay === 'function') {
                    updateCartDisplay();
                }
                // Setup drag and drop
                if (typeof setupDragAndDrop === 'function') {
                    setupDragAndDrop();
                }
            }, 100);
            
            // Sidebar functionality
            const menuBtn = document.getElementById('menu-btn');
            const cartBtn = document.getElementById('cart-btn');
            const menuSidebar = document.getElementById('menu-sidebar');
            const cartSidebar = document.getElementById('cart-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const closeMenuBtn = document.getElementById('close-menu');
            const closeCartBtn = document.getElementById('close-cart');
            
            // Initialize cart sidebar as open by default
            if (cartSidebar && cartSidebar.classList.contains('translate-x-0')) {
                // Set z-index for open cart sidebar
                const nav = document.querySelector('nav');
                if (nav) {
                    nav.classList.add('sidebar-open');
                    nav.style.zIndex = '5';
                }
                const cartContainer = document.getElementById('cart-sidebar-container');
                if (cartContainer) {
                    cartContainer.style.zIndex = '10';
                }
                cartSidebar.style.zIndex = '10';
                
                // Show step indicator
                const stepIndicatorContainer = document.getElementById('step-indicator-container');
                if (stepIndicatorContainer) {
                    stepIndicatorContainer.style.display = 'block';
                }
                
                // Add padding to main content
                const mainContent = document.getElementById('main-content');
                if (mainContent) {
                    if (window.innerWidth >= 640) {
                        mainContent.classList.add('pr-80');
                    }
                    if (window.innerWidth >= 1024) {
                        mainContent.classList.add('lg:pr-96');
                    }
                }
            }

            // Open menu sidebar
            if (menuBtn && menuSidebar) {
                menuBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    menuSidebar.classList.remove('-translate-x-full');
                    menuSidebar.classList.add('translate-x-0');
                    // Cambiar z-index: header baja a 5, sidebar sube a 10, overlay a 9 (debajo del sidebar)
                    const nav = document.querySelector('nav');
                    if (nav) {
                        nav.classList.add('sidebar-open');
                        nav.style.zIndex = '5';
                    }
                    menuSidebar.style.zIndex = '10';
                    overlay.classList.remove('opacity-0', 'invisible', 'pointer-events-none');
                    overlay.classList.add('opacity-100', 'visible', 'pointer-events-auto');
                    overlay.style.zIndex = '9'; // Debajo del sidebar (10) pero por encima del header (5)
                    // Lower contact button z-index when sidebar is open
                    const contactContainer = document.getElementById('contact-float-container');
                    if (contactContainer) {
                        contactContainer.style.zIndex = '5';
                    }
                    document.body.style.overflow = 'hidden';
                });
            }

            // Open cart sidebar
            if (cartBtn && cartSidebar) {
                cartBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    cartSidebar.classList.remove('translate-x-full');
                    cartSidebar.classList.add('translate-x-0');
                    // Cambiar z-index: header baja a 5, sidebar sube a 10
                    const nav = document.querySelector('nav');
                    if (nav) {
                        nav.classList.add('sidebar-open');
                        nav.style.zIndex = '5';
                    }
                    const cartContainer = document.getElementById('cart-sidebar-container');
                    if (cartContainer) {
                        cartContainer.style.zIndex = '10';
                    }
                    cartSidebar.style.zIndex = '10';
                    
                    // Show step indicator
                    const stepIndicatorContainer = document.getElementById('step-indicator-container');
                    if (stepIndicatorContainer) {
                        stepIndicatorContainer.style.display = 'block';
                    }
                    
                    const mainContent = document.getElementById('main-content');
                    if (mainContent) {
                        // Añadir padding derecho solo en pantallas grandes
                        if (window.innerWidth >= 640) {
                            mainContent.classList.add('pr-80');
                        }
                        if (window.innerWidth >= 1024) {
                            mainContent.classList.add('lg:pr-96');
                        }
                    }
                });
            }

            // Close menu sidebar
            if (closeMenuBtn && menuSidebar) {
                closeMenuBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    menuSidebar.classList.remove('translate-x-0');
                    menuSidebar.classList.add('-translate-x-full');
                    // Restaurar z-index: header vuelve a 10, sidebar a 9
                    const nav = document.querySelector('nav');
                    if (nav) {
                        nav.classList.remove('sidebar-open');
                        nav.style.zIndex = '10';
                    }
                    menuSidebar.style.zIndex = '9';
                    overlay.classList.remove('opacity-100', 'visible', 'pointer-events-auto');
                    overlay.classList.add('opacity-0', 'invisible', 'pointer-events-none');
                    overlay.style.zIndex = '9';
                    // Restore contact button z-index when sidebar is closed
                    const contactContainer = document.getElementById('contact-float-container');
                    if (contactContainer) {
                        contactContainer.style.zIndex = '8';
                    }
                    document.body.style.overflow = '';
                });
            }

            // Close cart sidebar
            if (closeCartBtn && cartSidebar) {
                closeCartBtn.addEventListener('click', function() {
                    cartSidebar.classList.remove('translate-x-0');
                    cartSidebar.classList.add('translate-x-full');
                    // Restaurar z-index: header vuelve a 10, sidebar a 9
                    const nav = document.querySelector('nav');
                    if (nav) {
                        nav.classList.remove('sidebar-open');
                        nav.style.zIndex = '10';
                    }
                    const cartContainer = document.getElementById('cart-sidebar-container');
                    if (cartContainer) {
                        cartContainer.style.zIndex = '9';
                    }
                    cartSidebar.style.zIndex = '9';
                    
                    // Hide step indicator
                    const stepIndicatorContainer = document.getElementById('step-indicator-container');
                    if (stepIndicatorContainer) {
                        stepIndicatorContainer.style.display = 'none';
                    }
                    
                    const mainContent = document.getElementById('main-content');
                    if (mainContent) {
                        mainContent.classList.remove('pr-80', 'lg:pr-96');
                    }
                });
            }

            // Close menu sidebar when clicking overlay
            if (overlay && menuSidebar) {
                overlay.addEventListener('click', function(e) {
                    // No cerrar si el clic viene del sidebar o sus elementos
                    if (e.target.closest('#menu-sidebar')) {
                        return;
                    }
                    e.preventDefault();
                    e.stopPropagation();
                    menuSidebar.classList.remove('translate-x-0');
                    menuSidebar.classList.add('-translate-x-full');
                    // Restaurar z-index: header vuelve a 10, sidebar a 9
                    const nav = document.querySelector('nav');
                    if (nav) {
                        nav.classList.remove('sidebar-open');
                        nav.style.zIndex = '10';
                    }
                    menuSidebar.style.zIndex = '9';
                    overlay.classList.remove('opacity-100', 'visible', 'pointer-events-auto');
                    overlay.classList.add('opacity-0', 'invisible', 'pointer-events-none');
                    overlay.style.zIndex = '9';
                    // Restore contact button z-index when sidebar is closed
                    const contactContainer = document.getElementById('contact-float-container');
                    if (contactContainer) {
                        contactContainer.style.zIndex = '8';
                    }
                    document.body.style.overflow = '';
                });
            }

            // Close menu sidebar with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    if (menuSidebar && !menuSidebar.classList.contains('-translate-x-full')) {
                        menuSidebar.classList.remove('translate-x-0');
                        menuSidebar.classList.add('-translate-x-full');
                        // Restaurar z-index: header vuelve a 10, sidebar a 9
                        const nav = document.querySelector('nav');
                        if (nav) {
                            nav.classList.remove('sidebar-open');
                            nav.style.zIndex = '10';
                        }
                        menuSidebar.style.zIndex = '9';
                        // Restore contact button z-index when sidebar is closed
                        const contactContainer = document.getElementById('contact-float-container');
                        if (contactContainer) {
                            contactContainer.style.zIndex = '8';
                        }
                    }
                    if (overlay) {
                        overlay.classList.remove('opacity-100', 'visible', 'pointer-events-auto');
                        overlay.classList.add('opacity-0', 'invisible', 'pointer-events-none');
                        overlay.style.zIndex = '9';
                    }
                    document.body.style.overflow = '';
                }
            });
            
            // Proceed to Payment button - redirect to directions page
            // Only add this listener if we're NOT on the directions page
            // (directions page has its own listener that validates the form first)
            if (!window.location.pathname.includes('/directions')) {
                const whenWhereBtn = document.getElementById('when-where-btn');
                if (whenWhereBtn) {
                    whenWhereBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        if (!this.disabled) {
                            // Redirect to directions page
                            window.location.href = '{{ route("directions") }}';
                        }
                    });
                }
            }
            
            // Setup social media links in menu sidebar
            function setupMenuSidebarSocialLinks() {
                // No necesitamos event listeners personalizados, los enlaces funcionan normalmente
                // Solo prevenimos que el overlay capture los clics
            }
            
            // Setup legal & policies links in menu sidebar
            function setupMenuSidebarLegalLinks() {
                // No necesitamos event listeners personalizados, los enlaces funcionan normalmente
                // Solo prevenimos que el overlay capture los clics
            }
            
            // Setup immediately and also when sidebar opens
            setupMenuSidebarSocialLinks();
            setupMenuSidebarLegalLinks();
            setTimeout(function() {
                setupMenuSidebarSocialLinks();
                setupMenuSidebarLegalLinks();
            }, 500);
            
            // Re-setup when menu sidebar is opened
            if (menuBtn && menuSidebar) {
                const originalMenuClick = menuBtn.onclick;
                menuBtn.addEventListener('click', function() {
                    setTimeout(function() {
                        setupMenuSidebarSocialLinks();
                        setupMenuSidebarLegalLinks();
                    }, 100);
                });
            }
        });

        // CSRF Token for AJAX
        window.axios = {
            defaults: {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }
        };
        
        // Update step indicator and tabs based on current page
        function updateStepIndicator() {
            const currentPath = window.location.pathname;
            
            const step1 = document.getElementById('step-indicator-1');
            const step2 = document.getElementById('step-indicator-2');
            const step3 = document.getElementById('step-indicator-3');
            const tab1 = document.getElementById('step-tab-1');
            const tab2 = document.getElementById('step-tab-2');
            const tab3 = document.getElementById('step-tab-3');
            
            // Remove all active states from indicators
            if (step1) {
                step1.classList.remove('text-[#CE9704]');
                step1.classList.add('text-white');
            }
            if (step2) {
                step2.classList.remove('text-[#CE9704]');
                step2.classList.add('text-white');
            }
            if (step3) {
                step3.classList.remove('text-[#CE9704]');
                step3.classList.add('text-white');
            }
            
            // Remove all active states from tabs
            if (tab1) {
                tab1.classList.remove('bg-gradient-to-br', 'from-[#CE9704]', 'to-[#B8860B]', 'border-[#CE9704]', 'shadow-lg');
                tab1.classList.add('bg-gray-700', 'border-gray-600');
            }
            if (tab2) {
                tab2.classList.remove('bg-gradient-to-br', 'from-[#CE9704]', 'to-[#B8860B]', 'border-[#CE9704]', 'shadow-lg');
                tab2.classList.add('bg-gray-700', 'border-gray-600');
            }
            if (tab3) {
                tab3.classList.remove('bg-gradient-to-br', 'from-[#CE9704]', 'to-[#B8860B]', 'border-[#CE9704]', 'shadow-lg');
                tab3.classList.add('bg-gray-700', 'border-gray-600');
            }
            
            // Set active step based on current page
            if (currentPath === '/' || currentPath.includes('home') || currentPath.includes('products')) {
                if (step1) {
                    step1.classList.remove('text-white');
                    step1.classList.add('text-[#CE9704]');
                }
                if (tab1) {
                    tab1.classList.remove('bg-gray-700', 'border-gray-600');
                    tab1.classList.add('bg-gradient-to-br', 'from-[#CE9704]', 'to-[#B8860B]', 'border-[#CE9704]', 'shadow-lg');
                }
            } else if (currentPath.includes('directions')) {
                if (step2) {
                    step2.classList.remove('text-white');
                    step2.classList.add('text-[#CE9704]');
                }
                if (tab2) {
                    tab2.classList.remove('bg-gray-700', 'border-gray-600');
                    tab2.classList.add('bg-gradient-to-br', 'from-[#CE9704]', 'to-[#B8860B]', 'border-[#CE9704]', 'shadow-lg');
                }
            } else if (currentPath.includes('checkout')) {
                if (step3) {
                    step3.classList.remove('text-white');
                    step3.classList.add('text-[#CE9704]');
                }
                if (tab3) {
                    tab3.classList.remove('bg-gray-700', 'border-gray-600');
                    tab3.classList.add('bg-gradient-to-br', 'from-[#CE9704]', 'to-[#B8860B]', 'border-[#CE9704]', 'shadow-lg');
                }
            }
        }
        
        // Step tabs functionality
        function setupStepTabs() {
            const stepTabs = document.querySelectorAll('.step-tab');
            const stepIndicator1 = document.getElementById('step-indicator-1');
            const stepIndicator2 = document.getElementById('step-indicator-2');
            const stepIndicator3 = document.getElementById('step-indicator-3');
            
            stepTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const step = parseInt(this.getAttribute('data-step'));
                    
                    // Update tab styles
                    stepTabs.forEach(t => {
                        t.classList.remove('bg-gradient-to-br', 'from-[#CE9704]', 'to-[#B8860B]', 'border-[#CE9704]', 'shadow-lg');
                        t.classList.add('bg-gray-700', 'border-gray-600');
                    });
                    this.classList.remove('bg-gray-700', 'border-gray-600');
                    this.classList.add('bg-gradient-to-br', 'from-[#CE9704]', 'to-[#B8860B]', 'border-[#CE9704]', 'shadow-lg');
                    
                    // Update step indicators
                    if (stepIndicator1 && stepIndicator2 && stepIndicator3) {
                        stepIndicator1.classList.remove('text-[#CE9704]');
                        stepIndicator1.classList.add('text-white');
                        stepIndicator2.classList.remove('text-[#CE9704]');
                        stepIndicator2.classList.add('text-white');
                        stepIndicator3.classList.remove('text-[#CE9704]');
                        stepIndicator3.classList.add('text-white');
                        
                        if (step === 1 && stepIndicator1) {
                            stepIndicator1.classList.remove('text-white');
                            stepIndicator1.classList.add('text-[#CE9704]');
                        } else if (step === 2 && stepIndicator2) {
                            stepIndicator2.classList.remove('text-white');
                            stepIndicator2.classList.add('text-[#CE9704]');
                        } else if (step === 3 && stepIndicator3) {
                            stepIndicator3.classList.remove('text-white');
                            stepIndicator3.classList.add('text-[#CE9704]');
                        }
                    }
                    
                    // Navigate to step page
                    if (step === 1) {
                        window.location.href = '{{ route("home") }}';
                    } else if (step === 2) {
                        window.location.href = '{{ route("directions") }}';
                    } else if (step === 3) {
                        window.location.href = '{{ route("checkout") }}';
                    }
                });
            });
        }
        
        // Initialize step indicator and tabs on page load
        updateStepIndicator();
        setupStepTabs();

        // Contact Float Panel functionality
        function toggleContactPanel() {
            const panel = document.getElementById('contact-panel');
            const button = document.getElementById('contact-float-btn');
            const defaultIcon = document.getElementById('contact-icon-default');
            const hoverIcon = document.getElementById('contact-icon-hover');
            
            if (panel.classList.contains('open')) {
                panel.classList.remove('open');
                button.classList.remove('panel-open');
                // Restore default icon
                defaultIcon.style.opacity = '1';
                hoverIcon.style.opacity = '0';
            } else {
                panel.classList.add('open');
                button.classList.add('panel-open');
                // Keep hover icon visible
                defaultIcon.style.opacity = '0';
                hoverIcon.style.opacity = '1';
            }
        }

        function showQRCode() {
            const modal = document.getElementById('qr-code-modal');
            if (modal) {
                modal.style.display = 'flex';
            }
        }

        function closeQRCode() {
            const modal = document.getElementById('qr-code-modal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        function openCallback() {
            const modal = document.getElementById('callback-modal');
            if (modal) {
                modal.style.display = 'flex';
            }
        }

        function closeCallback() {
            const modal = document.getElementById('callback-modal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        function openEmailModal() {
            const modal = document.getElementById('email-modal');
            if (modal) {
                modal.style.display = 'flex';
            }
        }

        function closeEmailModal() {
            const modal = document.getElementById('email-modal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        function copyEmail() {
            const email = 'support@rebornrental.com';
            navigator.clipboard.writeText(email).then(function() {
                // Show feedback
                const emailField = document.getElementById('email-address');
                const originalText = emailField.textContent;
                emailField.textContent = 'Copied!';
                emailField.classList.add('text-green-600');
                
                setTimeout(function() {
                    emailField.textContent = originalText;
                    emailField.classList.remove('text-green-600');
                }, 2000);
            }).catch(function(err) {
                console.error('Failed to copy email:', err);
                alert('Failed to copy email. Please copy manually: ' + email);
            });
        }

        // Close contact panel when clicking outside
        document.addEventListener('click', function(event) {
            const container = document.getElementById('contact-float-container');
            const panel = document.getElementById('contact-panel');
            const button = document.getElementById('contact-float-btn');
            
            if (container && panel && button) {
                // Check if click is outside both the button and the panel
                const isClickInsideButton = button.contains(event.target);
                const isClickInsidePanel = panel.contains(event.target);
                
                if (!isClickInsideButton && !isClickInsidePanel && panel.classList.contains('open')) {
                    panel.classList.remove('open');
                    button.classList.remove('panel-open');
                    const defaultIcon = document.getElementById('contact-icon-default');
                    const hoverIcon = document.getElementById('contact-icon-hover');
                    if (defaultIcon) defaultIcon.style.opacity = '1';
                    if (hoverIcon) hoverIcon.style.opacity = '0';
                }
            }
        });

        // Close QR modal when clicking outside
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('qr-code-modal');
            if (modal && event.target === modal) {
                closeQRCode();
            }
        });

        // Close Callback modal when clicking outside
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('callback-modal');
            if (modal && event.target === modal) {
                closeCallback();
            }
        });

        // Close Email modal when clicking outside
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('email-modal');
            if (modal && event.target === modal) {
                closeEmailModal();
            }
        });

        // Submit callback form
        function submitCallback(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            
            // Here you would typically send the data to your backend
            // For now, we'll just show an alert and close the modal
            alert('Callback request submitted! We will contact you soon.');
            closeCallback();
            form.reset();
        }
    </script>
    
    <!-- Sonner-style Toast Notifications -->
    <style>
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 8px;
            pointer-events: none;
        }
        .toast {
            pointer-events: auto;
            min-width: 300px;
            max-width: 400px;
            padding: 14px 16px;
            border-radius: 8px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 500;
            color: white;
            transform: translateX(400px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            animation: slideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .toast.show {
            transform: translateX(0);
            opacity: 1;
        }
        .toast.success {
            background: #10b981;
        }
        .toast.error {
            background: #ef4444;
        }
        .toast.info {
            background: #3b82f6;
        }
        .toast.warning {
            background: #f59e0b;
        }
        .toast-icon {
            flex-shrink: 0;
            width: 20px;
            height: 20px;
        }
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
    <div id="toast-container" class="toast-container"></div>
    <script>
        // Sonner-style toast implementation
        (function() {
            const container = document.getElementById('toast-container');
            if (!container) {
                const div = document.createElement('div');
                div.id = 'toast-container';
                div.className = 'toast-container';
                document.body.appendChild(div);
            }
            
            function createToast(message, type) {
                const toast = document.createElement('div');
                toast.className = `toast ${type}`;
                
                const iconMap = {
                    success: '<svg class="toast-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
                    error: '<svg class="toast-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
                    info: '<svg class="toast-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                    warning: '<svg class="toast-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>'
                };
                
                toast.innerHTML = (iconMap[type] || '') + '<span>' + message + '</span>';
                
                const toastContainer = document.getElementById('toast-container');
                toastContainer.appendChild(toast);
                
                // Trigger animation
                setTimeout(() => toast.classList.add('show'), 10);
                
                // Auto remove after duration
                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 300);
                }, 4000);
            }
            
            window.toast = {
                success: (msg) => createToast(msg, 'success'),
                error: (msg) => createToast(msg, 'error'),
                info: (msg) => createToast(msg, 'info'),
                warning: (msg) => createToast(msg, 'warning')
            };
        })();
    </script>
    
    <!-- Cart JS -->
    <script src="{{ asset('js/cart.js') }}"></script>
    
    @stack('scripts')
    
    @auth
    <script>
        // Make authenticated user email available to JavaScript
        window.authenticatedUserEmail = @json(Auth::user()->email);
    </script>
    @endauth
</body>
</html>

