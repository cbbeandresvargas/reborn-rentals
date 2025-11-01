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
    </style>
    
    @stack('styles')
</head>
<body class="m-0 w-full h-full font-sans">
    <!-- Navbar -->
    <nav class="bg-[#4A4A4A] py-3 sm:py-4 md:py-6 shadow-lg relative">
        <div class="max-w-6xl mx-auto px-3 sm:px-4 md:px-8">
            <div class="flex justify-between items-center relative">
                <!-- Logo -->
                <div class="shrink-0 z-10 relative">
                    <a href="{{ route('home') }}" class="block">
                        <img src="{{ asset('Logo.png') }}" alt="Reborn Rentals" class="h-7 sm:h-8 md:h-10 w-auto object-contain" />
                    </a>
                </div>
                
                <!-- Right Side: Auth Links / Menu & Cart -->
                <div class="flex items-center gap-2 md:gap-4 relative z-10">
                    @auth
                        <span class="text-white text-xs sm:text-sm hidden sm:block">{{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">
                            @csrf
                            <button type="submit" class="text-white text-xs sm:text-sm hover:text-[#CE9704] transition-colors cursor-pointer">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-white text-xs sm:text-sm hover:text-[#CE9704] transition-colors px-2 py-1 cursor-pointer relative z-10">Login</a>
                        <a href="{{ route('register') }}" class="text-white text-xs sm:text-sm hover:text-[#CE9704] transition-colors px-2 py-1 cursor-pointer relative z-10">Register</a>
                    @endauth
                    
                    <!-- Menu Button -->
                    <button type="button" class="bg-none border-none text-white cursor-pointer p-2 rounded-lg transition-all duration-300 ease-in-out flex items-center justify-center hover:bg-white/10 hover:scale-105 active:scale-95 focus:outline-none focus:ring-2 focus:ring-white/20 relative z-10" id="menu-btn" aria-label="Menu">
                        <svg width="20" height="20" class="sm:w-5 sm:h-5 md:w-6 md:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>
                    
                    <!-- Cart Button -->
                    <button type="button" class="bg-none border-none text-white cursor-pointer p-2 rounded-lg transition-all duration-300 ease-in-out flex items-center justify-center hover:bg-white/10 hover:scale-105 active:scale-95 focus:outline-none focus:ring-2 focus:ring-white/20 relative z-10" id="cart-btn" aria-label="Carrito">
                        <svg width="20" height="20" class="sm:w-5 sm:h-5 md:w-6 md:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="m1 1 4 4 13 1 2 8H6l-2-8z"></path>
                        </svg>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 items-center justify-center hidden" id="cart-badge">0</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Menu Sidebar (Left) -->
    <div id="menu-sidebar" class="fixed top-0 left-0 h-screen w-full sm:w-80 lg:w-96 max-w-[85vw] bg-[#4A4A4A] shadow-2xl -translate-x-full transition-transform duration-300 ease-in-out overflow-y-auto scrollbar-hide">
        <div class="px-4 sm:px-6 py-6 sm:py-8 min-h-full flex flex-col">
            <!-- Close Button -->
            <div class="flex justify-end mb-6 shrink-0">
                <button type="button" class="bg-none border-none cursor-pointer p-2 sm:p-3 rounded-lg transition-colors duration-300 ease-in-out hover:bg-white/10 text-white" id="close-menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <!-- Logo Section -->
            <div class="flex justify-center mb-10 shrink-0">
                <img src="{{ asset('Logo.png') }}" alt="Reborn Rentals" class="h-20 w-auto object-contain" />
            </div>

            <!-- Navigation Sections -->
            <div class="flex flex-col gap-10 flex-1">
                <!-- About Us Section -->
                <div class="flex gap-6">
                    <div class="w-2 h-36 bg-white rounded-sm shrink-0"></div>
                    <div class="flex-1">
                        <h3 class="text-gray-300 font-bold text-sm uppercase mb-5 tracking-wider">About Us</h3>
                        <nav class="flex flex-col gap-4">
                            <a href="{{ route('faq') }}" class="text-[#CE9704] no-underline text-sm hover:text-white transition-colors duration-300 py-1">FAQ</a>
                            <a href="{{ route('about') }}" class="text-[#CE9704] no-underline text-sm hover:text-white transition-colors duration-300 py-1">About Us</a>
                            <a href="https://grb-group.com/en/" class="text-[#CE9704] no-underline text-sm hover:text-white transition-colors duration-300 py-1" target="_blank">Corporate</a>
                        </nav>
                    </div>
                </div>

                <!-- General Section -->
                <div class="flex gap-6">
                    <div class="w-2 h-36 bg-[#CE9704] rounded-sm shrink-0"></div>
                    <div class="flex-1">
                        <h3 class="text-gray-300 font-bold text-sm uppercase mb-5 tracking-wider">General</h3>
                        <nav class="flex flex-col gap-4">
                            <a href="{{ route('sitemap') }}" class="text-[#CE9704] no-underline text-sm hover:text-white transition-colors duration-300 py-1">Site Map</a>
                            <a href="{{ route('blog') }}" class="text-[#CE9704] no-underline text-sm hover:text-white transition-colors duration-300 py-1">Blog</a>
                            <a href="https://grb-group.com/en/open-opportunities/" class="text-[#CE9704] no-underline text-sm hover:text-white transition-colors duration-300 py-1" target="_blank">Careers</a>
                        </nav>
                    </div>
                </div>

                <!-- Legal Section -->
                <div class="flex gap-6">
                    <div class="w-2 h-36 bg-white rounded-sm shrink-0"></div>
                    <div class="flex-1">
                        <h3 class="text-gray-300 font-bold text-sm uppercase mb-5 tracking-wider">Legal</h3>
                        <nav class="flex flex-col gap-4">
                            <a href="{{ route('terms') }}" class="text-[#CE9704] no-underline text-sm hover:text-white transition-colors duration-300 py-1">Terms & Conditions</a>
                            <a href="{{ route('privacy') }}" class="text-[#CE9704] no-underline text-sm hover:text-white transition-colors duration-300 py-1">Privacy Policy</a>
                            <a href="{{ route('fees') }}" class="text-[#CE9704] no-underline text-sm hover:text-white transition-colors duration-300 py-1">Fees & Surcharges</a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="pt-6 shrink-0">
                <p class="text-white text-xs text-center leading-relaxed">© 2025 Reborn Rentals, All Rights Reserved.</p>
            </div>
        </div>
    </div>

    <!-- Cart Sidebar (Right) -->
    <div id="cart-sidebar-container" class="fixed top-0 right-0 h-screen overflow-visible">
        <div id="cart-sidebar" class="text-white w-full sm:w-80 lg:w-96 max-w-[85vw] bg-[#2F2F2F] shadow-2xl translate-x-full transition-transform duration-300 ease-in-out overflow-y-auto scrollbar-hide h-full relative overflow-visible">
            <!-- Step Indicator on left edge (protruding) -->
            <div id="step-indicator-container" class="absolute -left-[42px] top-[170px] w-[42px] h-[236px] bg-[#2F2F2F] flex flex-col items-center justify-center gap-[21px]" style="border-radius: 26px 0px 0px 26px; display: none;">
                <!-- Step 1 -->
                <div class="w-8 h-12 text-[#CE9704] font-black text-base leading-[48px] text-center flex items-center justify-center" id="step-indicator-1">
                    1
                </div>
                <!-- Step 2 -->
                <div class="w-8 h-12 text-white font-black text-base leading-[48px] text-center flex items-center justify-center" id="step-indicator-2">
                    2
                </div>
                <!-- Step 3 -->
                <div class="w-8 h-12 text-white font-black text-base leading-[48px] text-center flex items-center justify-center" id="step-indicator-3">
                    3
                </div>
            </div>
        
        <div class="p-4 sm:p-6 md:p-8 min-h-full flex flex-col">
            <div class="flex justify-between items-center mb-6 sm:mb-8 pb-3 sm:pb-4 border-b border-gray-300" id="cart-header">
                <h3 class="m-0 text-[#CE9704] text-xl sm:text-2xl">YOUR CART</h3>
                <button type="button" class="bg-none border-none cursor-pointer p-2 rounded transition-colors duration-300 ease-in-out hover:bg-gray-200" id="close-cart">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
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

    <!-- Verification Code Modal -->
    <div id="verification-modal" class="fixed inset-0 bg-black bg-opacity-70 z-50 flex flex-col items-center justify-center p-3 sm:p-4 overflow-y-auto" style="display: none;">
        <!-- Instructions above modal -->
        <div id="verification-instructions" class="text-center text-white text-xs sm:text-sm mb-3 sm:mb-4 w-full max-w-md px-2">
            <p>To proceed with the payment, please enter the verification code sent to your email <span id="instructions-email" class="font-semibold"></span></p>
        </div>
        
        <!-- Modal Container -->
        <div class="bg-white rounded-lg max-w-md w-full max-h-[90vh] overflow-y-auto shadow-2xl relative mb-3 sm:mb-4 mx-2 sm:mx-4">
            <!-- Header with Payment Method Icons and Close Button -->
            <div class="p-4 sm:p-6 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <!-- Generic Card Icon -->
                    <div class="w-8 h-8 bg-gray-200 rounded flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <!-- Mastercard Logo -->
                    <div class="w-8 h-8 rounded bg-red-500 flex items-center justify-center relative overflow-hidden">
                        <div class="absolute left-0 w-4 h-8 bg-orange-500 rounded-l-full"></div>
                        <div class="absolute right-0 w-4 h-8 bg-red-500 rounded-r-full"></div>
                    </div>
                    <!-- American Express Logo -->
                    <div class="w-8 h-6 bg-blue-600 rounded flex items-center justify-center">
                        <span class="text-white text-xs font-bold">AMEX</span>
                    </div>
                </div>
                <button onclick="closeVerificationModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- Title -->
            <div class="px-4 sm:px-6 pt-4 sm:pt-6">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">We need to verify you</h2>
                <p class="text-gray-600 text-xs sm:text-sm mb-4 sm:mb-6">Enter the code Mastercard sent to <span id="verification-email-display" class="font-medium"></span></p>
            </div>
            
            <!-- Verification Code Inputs (5 digits) -->
            <div class="px-4 sm:px-6 mb-4">
                <div class="flex gap-2 sm:gap-3 justify-center">
                    <input type="text" maxlength="1" class="w-12 h-12 sm:w-14 sm:h-14 text-center text-lg sm:text-xl font-semibold border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704]" id="code-0" />
                    <input type="text" maxlength="1" class="w-12 h-12 sm:w-14 sm:h-14 text-center text-lg sm:text-xl font-semibold border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704]" id="code-1" />
                    <input type="text" maxlength="1" class="w-12 h-12 sm:w-14 sm:h-14 text-center text-lg sm:text-xl font-semibold border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704]" id="code-2" />
                    <input type="text" maxlength="1" class="w-12 h-12 sm:w-14 sm:h-14 text-center text-lg sm:text-xl font-semibold border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704]" id="code-3" />
                    <input type="text" maxlength="1" class="w-12 h-12 sm:w-14 sm:h-14 text-center text-lg sm:text-xl font-semibold border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704]" id="code-4" />
                </div>
            </div>
            
            <!-- Resend Code -->
            <div class="px-4 sm:px-6 mb-4">
                <button type="button" id="resend-code-btn" onclick="sendVerificationCode()" class="text-xs sm:text-sm text-blue-600 hover:text-blue-800 underline">Resend Code</button>
            </div>
            
            <!-- Footer Buttons -->
            <div class="px-4 sm:px-6 pb-4 sm:pb-6 flex gap-2 sm:gap-3">
                <button type="button" onclick="closeVerificationModal()" class="flex-1 bg-white border-2 border-gray-300 text-gray-700 py-2 sm:py-3 px-3 sm:px-6 rounded-lg text-sm sm:text-base font-semibold hover:bg-gray-50 transition-colors duration-200">Cancel</button>
                <button type="button" onclick="verifyCode()" class="flex-1 bg-blue-600 text-white py-2 sm:py-3 px-3 sm:px-6 rounded-lg text-sm sm:text-base font-semibold hover:bg-blue-700 transition-colors duration-200">Verify</button>
            </div>
        </div>
        
        <!-- Session Timer (outside modal, below it) -->
        <div class="text-center text-white text-xs sm:text-sm w-full max-w-md px-2">
            <p>For security reasons, your session will expire in <span id="session-timer" class="font-semibold text-[#CE9704]">20:00</span></p>
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
                    }
                    if (overlay) {
                        overlay.classList.remove('opacity-100', 'visible', 'pointer-events-auto');
                        overlay.classList.add('opacity-0', 'invisible', 'pointer-events-none');
                        overlay.style.zIndex = '40';
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
        });

        // CSRF Token for AJAX
        window.axios = {
            defaults: {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }
        };
        
        // Update step indicator based on current page
        function updateStepIndicator() {
            const currentPath = window.location.pathname;
            
            const step1 = document.getElementById('step-indicator-1');
            const step2 = document.getElementById('step-indicator-2');
            const step3 = document.getElementById('step-indicator-3');
            
            // Remove all active states
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
            
            // Set active step based on current page
            if (currentPath === '/' || currentPath.includes('home')) {
                if (step1) {
                    step1.classList.remove('text-white');
                    step1.classList.add('text-[#CE9704]');
                }
            } else if (currentPath.includes('directions')) {
                if (step2) {
                    step2.classList.remove('text-white');
                    step2.classList.add('text-[#CE9704]');
                }
            } else if (currentPath.includes('checkout')) {
                if (step3) {
                    step3.classList.remove('text-white');
                    step3.classList.add('text-[#CE9704]');
                }
            }
        }
        
        // Initialize step indicator on page load
        updateStepIndicator();
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

