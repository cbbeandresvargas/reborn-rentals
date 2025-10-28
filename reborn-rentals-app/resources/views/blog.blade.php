@extends('layouts.app')

@section('title', 'Blog - Reborn Rentals')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 md:px-8">
        <!-- Header Section -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Our Blog</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Stay updated with the latest news, tips, and insights about construction equipment rentals
            </p>
        </div>

        <!-- Blog Posts Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            <!-- Blog Post Card 1 -->
            <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="h-48 bg-gradient-to-br from-[#CE9704] to-[#B8860B] flex items-center justify-center">
                    <svg class="w-16 h-16 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="p-6">
                    <div class="flex items-center text-sm text-gray-500 mb-3">
                        <span class="bg-[#CE9704] text-white px-3 py-1 rounded-full text-xs font-semibold mr-3">Equipment</span>
                        <time datetime="2025-01-15">January 15, 2025</time>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">
                        <a href="#" class="hover:text-[#CE9704] transition-colors">Best Practices for Construction Equipment Maintenance</a>
                    </h2>
                    <p class="text-gray-600 mb-4 leading-relaxed">
                        Learn how to properly maintain your construction equipment to ensure longevity and optimal performance...
                    </p>
                    <a href="#" class="text-[#CE9704] font-semibold hover:underline inline-flex items-center">
                        Read More
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </article>

            <!-- Blog Post Card 2 -->
            <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="h-48 bg-gradient-to-br from-gray-700 to-gray-900 flex items-center justify-center">
                    <svg class="w-16 h-16 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div class="p-6">
                    <div class="flex items-center text-sm text-gray-500 mb-3">
                        <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-xs font-semibold mr-3">Tips</span>
                        <time datetime="2025-01-10">January 10, 2025</time>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">
                        <a href="#" class="hover:text-[#CE9704] transition-colors">How to Choose the Right Equipment for Your Project</a>
                    </h2>
                    <p class="text-gray-600 mb-4 leading-relaxed">
                        A comprehensive guide to selecting the perfect construction equipment based on your project requirements...
                    </p>
                    <a href="#" class="text-[#CE9704] font-semibold hover:underline inline-flex items-center">
                        Read More
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </article>

            <!-- Blog Post Card 3 -->
            <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="h-48 bg-gradient-to-br from-green-600 to-green-800 flex items-center justify-center">
                    <svg class="w-16 h-16 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="p-6">
                    <div class="flex items-center text-sm text-gray-500 mb-3">
                        <span class="bg-green-500 text-white px-3 py-1 rounded-full text-xs font-semibold mr-3">Sustainability</span>
                        <time datetime="2025-01-05">January 5, 2025</time>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">
                        <a href="#" class="hover:text-[#CE9704] transition-colors">Sustainable Construction: Eco-Friendly Equipment Solutions</a>
                    </h2>
                    <p class="text-gray-600 mb-4 leading-relaxed">
                        Discover how modern construction equipment can help reduce your environmental impact while maintaining efficiency...
                    </p>
                    <a href="#" class="text-[#CE9704] font-semibold hover:underline inline-flex items-center">
                        Read More
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </article>

            <!-- Blog Post Card 4 -->
            <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="h-48 bg-gradient-to-br from-purple-600 to-purple-800 flex items-center justify-center">
                    <svg class="w-16 h-16 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                    </svg>
                </div>
                <div class="p-6">
                    <div class="flex items-center text-sm text-gray-500 mb-3">
                        <span class="bg-purple-500 text-white px-3 py-1 rounded-full text-xs font-semibold mr-3">Technology</span>
                        <time datetime="2024-12-28">December 28, 2024</time>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">
                        <a href="#" class="hover:text-[#CE9704] transition-colors">The Future of Construction: Smart Equipment Innovations</a>
                    </h2>
                    <p class="text-gray-600 mb-4 leading-relaxed">
                        Exploring the latest technological advances in construction equipment and how they're revolutionizing the industry...
                    </p>
                    <a href="#" class="text-[#CE9704] font-semibold hover:underline inline-flex items-center">
                        Read More
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </article>

            <!-- Blog Post Card 5 -->
            <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="h-48 bg-gradient-to-br from-red-600 to-red-800 flex items-center justify-center">
                    <svg class="w-16 h-16 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <div class="p-6">
                    <div class="flex items-center text-sm text-gray-500 mb-3">
                        <span class="bg-red-500 text-white px-3 py-1 rounded-full text-xs font-semibold mr-3">Safety</span>
                        <time datetime="2024-12-20">December 20, 2024</time>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">
                        <a href="#" class="hover:text-[#CE9704] transition-colors">Safety First: Essential Guidelines for Equipment Operation</a>
                    </h2>
                    <p class="text-gray-600 mb-4 leading-relaxed">
                        Important safety protocols and best practices every construction worker should know when operating heavy machinery...
                    </p>
                    <a href="#" class="text-[#CE9704] font-semibold hover:underline inline-flex items-center">
                        Read More
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </article>

            <!-- Blog Post Card 6 -->
            <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="h-48 bg-gradient-to-br from-indigo-600 to-indigo-800 flex items-center justify-center">
                    <svg class="w-16 h-16 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="p-6">
                    <div class="flex items-center text-sm text-gray-500 mb-3">
                        <span class="bg-indigo-500 text-white px-3 py-1 rounded-full text-xs font-semibold mr-3">Business</span>
                        <time datetime="2024-12-15">December 15, 2024</time>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">
                        <a href="#" class="hover:text-[#CE9704] transition-colors">Cost-Effective Rental Solutions for Small Contractors</a>
                    </h2>
                    <p class="text-gray-600 mb-4 leading-relaxed">
                        How small contracting businesses can maximize their budget by choosing the right rental equipment strategies...
                    </p>
                    <a href="#" class="text-[#CE9704] font-semibold hover:underline inline-flex items-center">
                        Read More
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </article>
        </div>

        <!-- Pagination (Placeholder) -->
        <div class="flex justify-center items-center gap-2 mt-12">
            <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                Previous
            </button>
            <span class="px-4 py-2 bg-[#CE9704] text-white rounded-lg font-semibold">1</span>
            <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                2
            </button>
            <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                3
            </button>
            <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Next
            </button>
        </div>
    </div>
</div>
@endsection

