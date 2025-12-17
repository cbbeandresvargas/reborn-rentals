@extends('layouts.app')

@section('title', 'Site Map - Reborn Rentals')

@section('content')
<main class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-6">
        <!-- Header Section -->
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Site Map</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">Explore all sections of our website and discover everything we have to offer</p>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-16">
            <!-- Products Section -->
            <div class="bg-white rounded-lg border border-gray-200 p-8">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-[#CE9704] rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Products</h2>
                </div>
                <ul class="space-y-3">
                    <li>
                        <a href="{{ route('products.index') }}" class="group flex items-center text-gray-700 hover:text-[#CE9704] transition-colors duration-200">
                            <div class="w-2 h-2 bg-[#CE9704] rounded-full mr-3"></div>
                            <span class="group-hover:translate-x-1 transition-transform duration-200">All Products</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- About Us Section -->
            <div class="bg-white rounded-lg border border-gray-200 p-8">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-[#CE9704] rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">About Us</h2>
                </div>
                <ul class="space-y-3">
                    <li>
                        <a href="{{ route('faq') }}" class="group flex items-center text-gray-700 hover:text-[#CE9704] transition-colors duration-200">
                            <div class="w-2 h-2 bg-[#CE9704] rounded-full mr-3"></div>
                            <span class="group-hover:translate-x-1 transition-transform duration-200">FAQ</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('about') }}" class="group flex items-center text-gray-700 hover:text-[#CE9704] transition-colors duration-200">
                            <div class="w-2 h-2 bg-[#CE9704] rounded-full mr-3"></div>
                            <span class="group-hover:translate-x-1 transition-transform duration-200">About Us</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- General Section -->
            <div class="bg-white rounded-lg border border-gray-200 p-8">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-[#CE9704] rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">General</h2>
                </div>
                <ul class="space-y-3">
                    <li>
                        <a href="{{ route('sitemap') }}" class="group flex items-center text-gray-700 hover:text-[#CE9704] transition-colors duration-200">
                            <div class="w-2 h-2 bg-[#CE9704] rounded-full mr-3"></div>
                            <span class="group-hover:translate-x-1 transition-transform duration-200">Site Map</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('home') }}" class="group flex items-center text-gray-700 hover:text-[#CE9704] transition-colors duration-200">
                            <div class="w-2 h-2 bg-[#CE9704] rounded-full mr-3"></div>
                            <span class="group-hover:translate-x-1 transition-transform duration-200">Home</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Legal Section -->
            <div class="bg-white rounded-lg border border-gray-200 p-8">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-[#CE9704] rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Legal</h2>
                </div>
                <ul class="space-y-3">
                    <li>
                        <a href="{{ route('terms') }}" class="group flex items-center text-gray-700 hover:text-[#CE9704] transition-colors duration-200">
                            <div class="w-2 h-2 bg-[#CE9704] rounded-full mr-3"></div>
                            <span class="group-hover:translate-x-1 transition-transform duration-200">Terms &amp; Conditions</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('privacy') }}" class="group flex items-center text-gray-700 hover:text-[#CE9704] transition-colors duration-200">
                            <div class="w-2 h-2 bg-[#CE9704] rounded-full mr-3"></div>
                            <span class="group-hover:translate-x-1 transition-transform duration-200">Privacy Policy</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('fees') }}" class="group flex items-center text-gray-700 hover:text-[#CE9704] transition-colors duration-200">
                            <div class="w-2 h-2 bg-[#CE9704] rounded-full mr-3"></div>
                            <span class="group-hover:translate-x-1 transition-transform duration-200">Fees &amp; Surcharges</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Affiliate Links Section -->
        <div class="bg-white rounded-lg border border-gray-200 p-8">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-[#CE9704] rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">Affiliate Links</h2>
            </div>
            <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                <li>
                    <a href="https://www.grb-group.com/en/" class="group flex items-center text-gray-700 hover:text-[#CE9704] transition-colors duration-200">
                        <div class="w-2 h-2 bg-[#CE9704] rounded-full mr-3"></div>
                        <span class="group-hover:translate-x-1 transition-transform duration-200">grb-group.com</span>
                    </a>
                </li>
                <li>
                    <a href="https://svaneholmhotel.se" class="group flex items-center text-gray-700 hover:text-[#CE9704] transition-colors duration-200">
                        <div class="w-2 h-2 bg-[#CE9704] rounded-full mr-3"></div>
                        <span class="group-hover:translate-x-1 transition-transform duration-200">svaneholmhotel.se</span>
                    </a>
                </li>
                <li>
                    <a href="https://reborndevelopments.com" class="group flex items-center text-gray-700 hover:text-[#CE9704] transition-colors duration-200">
                        <div class="w-2 h-2 bg-[#CE9704] rounded-full mr-3"></div>
                        <span class="group-hover:translate-x-1 transition-transform duration-200">reborndevelopments.com</span>
                    </a>
                </li>
                <li>
                    <a href="https://rebornhotels.com" class="group flex items-center text-gray-700 hover:text-[#CE9704] transition-colors duration-200">
                        <div class="w-2 h-2 bg-[#CE9704] rounded-full mr-3"></div>
                        <span class="group-hover:translate-x-1 transition-transform duration-200">rebornhotels.com</span>
                    </a>
                </li>
                <li>
                    <a href="https://igloow.com" class="group flex items-center text-gray-700 hover:text-[#CE9704] transition-colors duration-200">
                        <div class="w-2 h-2 bg-[#CE9704] rounded-full mr-3"></div>
                        <span class="group-hover:translate-x-1 transition-transform duration-200">igloow.com</span>
                    </a>
                </li>
                <li>
                    <a href="https://reborn-realty.com" class="group flex items-center text-gray-700 hover:text-[#CE9704] transition-colors duration-200">
                        <div class="w-2 h-2 bg-[#CE9704] rounded-full mr-3"></div>
                        <span class="group-hover:translate-x-1 transition-transform duration-200">reborn-realty.com</span>
                    </a>
                </li>
                <li>
                    <a href="https://slatefort.com" class="group flex items-center text-gray-700 hover:text-[#CE9704] transition-colors duration-200">
                        <div class="w-2 h-2 bg-[#CE9704] rounded-full mr-3"></div>
                        <span class="group-hover:translate-x-1 transition-transform duration-200">slatefort.com</span>
                    </a>
                </li>
                <li>
                    <a href="https://reborn-alpha.com" class="group flex items-center text-gray-700 hover:text-[#CE9704] transition-colors duration-200">
                        <div class="w-2 h-2 bg-[#CE9704] rounded-full mr-3"></div>
                        <span class="group-hover:translate-x-1 transition-transform duration-200">reborn-alpha.com</span>
                    </a>
                </li>
                <li>
                    <a href="https://reborn-bravo.com" class="group flex items-center text-gray-700 hover:text-[#CE9704] transition-colors duration-200">
                        <div class="w-2 h-2 bg-[#CE9704] rounded-full mr-3"></div>
                        <span class="group-hover:translate-x-1 transition-transform duration-200">reborn-bravo.com</span>
                    </a>
                </li>
                <li>
                    <a href="https://reborn-charlie.com" class="group flex items-center text-gray-700 hover:text-[#CE9704] transition-colors duration-200">
                        <div class="w-2 h-2 bg-[#CE9704] rounded-full mr-3"></div>
                        <span class="group-hover:translate-x-1 transition-transform duration-200">reborn-charlie.com</span>
                    </a>
                </li>
                <li>
                    <a href="https://reborn-delta.com" class="group flex items-center text-gray-700 hover:text-[#CE9704] transition-colors duration-200">
                        <div class="w-2 h-2 bg-[#CE9704] rounded-full mr-3"></div>
                        <span class="group-hover:translate-x-1 transition-transform duration-200">reborn-delta.com</span>
                    </a>
                </li>
                <li>
                    <a href="https://rebornbank.com" class="group flex items-center text-gray-700 hover:text-[#CE9704] transition-colors duration-200">
                        <div class="w-2 h-2 bg-[#CE9704] rounded-full mr-3"></div>
                        <span class="group-hover:translate-x-1 transition-transform duration-200">rebornbank.com</span>
                    </a>
                </li>
                <li>
                    <a href="https://rekaliber.com" class="group flex items-center text-gray-700 hover:text-[#CE9704] transition-colors duration-200">
                        <div class="w-2 h-2 bg-[#CE9704] rounded-full mr-3"></div>
                        <span class="group-hover:translate-x-1 transition-transform duration-200">rekaliber.com</span>
                    </a>
                </li>
                <li>
                    <a href="https://refistack.com" class="group flex items-center text-gray-700 hover:text-[#CE9704] transition-colors duration-200">
                        <div class="w-2 h-2 bg-[#CE9704] rounded-full mr-3"></div>
                        <span class="group-hover:translate-x-1 transition-transform duration-200">refistack.com</span>
                    </a>
                </li>
                <li>
                    <a href="https://mytropolskyi.com" class="group flex items-center text-gray-700 hover:text-[#CE9704] transition-colors duration-200">
                        <div class="w-2 h-2 bg-[#CE9704] rounded-full mr-3"></div>
                        <span class="group-hover:translate-x-1 transition-transform duration-200">mytropolskyi.com</span>
                    </a>
                </li>
                <li>
                    <a href="https://rbcmswusa.grb-group.com" class="group flex items-center text-gray-700 hover:text-[#CE9704] transition-colors duration-200">
                        <div class="w-2 h-2 bg-[#CE9704] rounded-full mr-3"></div>
                        <span class="group-hover:translate-x-1 transition-transform duration-200">rbcmswusa.grb-group.com</span>
                    </a>
                </li>
                <li>
                    <a href="https://rbcmneusa.grb-group.com" class="group flex items-center text-gray-700 hover:text-[#CE9704] transition-colors duration-200">
                        <div class="w-2 h-2 bg-[#CE9704] rounded-full mr-3"></div>
                        <span class="group-hover:translate-x-1 transition-transform duration-200">rbcmneusa.grb-group.com</span>
                    </a>
                </li>
                <li>
                    <a href="https://rbcmuk.grb-group.com" class="group flex items-center text-gray-700 hover:text-[#CE9704] transition-colors duration-200">
                        <div class="w-2 h-2 bg-[#CE9704] rounded-full mr-3"></div>
                        <span class="group-hover:translate-x-1 transition-transform duration-200">rbcmuk.grb-group.com</span>
                    </a>
                </li>
                <li>
                    <a href="https://rbcmche.ch" class="group flex items-center text-gray-700 hover:text-[#CE9704] transition-colors duration-200">
                        <div class="w-2 h-2 bg-[#CE9704] rounded-full mr-3"></div>
                        <span class="group-hover:translate-x-1 transition-transform duration-200">rbcmche.ch</span>
                    </a>
                </li>
                <li>
                    <a href="https://rbcmind.grb-group.com" class="group flex items-center text-gray-700 hover:text-[#CE9704] transition-colors duration-200">
                        <div class="w-2 h-2 bg-[#CE9704] rounded-full mr-3"></div>
                        <span class="group-hover:translate-x-1 transition-transform duration-200">rbcmind.grb-group.com</span>
                    </a>
                </li>
            </ul>
        </div>
        </div>
    </div>
</main>
@endsection

