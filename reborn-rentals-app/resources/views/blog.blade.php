@extends('layouts.app')

@section('title', 'Blog - Reborn Rentals')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 sm:py-12">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 md:px-8">
        <!-- Header Section -->
        <div class="text-center mb-8 sm:mb-12">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-3 sm:mb-4">Our Blog</h1>
            <p class="text-base sm:text-lg text-gray-600 max-w-2xl mx-auto px-4">
                Stay updated with the latest news, tips, and insights about construction equipment rentals
            </p>
        </div>

        @if(isset($error))
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-8 text-center">
                <p class="text-yellow-800">{{ $error }}</p>
            </div>
        @endif

        @if(empty($posts))
            <div class="text-center py-12">
                <p class="text-gray-600 text-lg">No hay posts disponibles en este momento.</p>
            </div>
        @else
            <!-- Blog Posts Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 mb-8 sm:mb-12">
                @foreach($posts as $post)
                    <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <!-- Featured Image -->
                        <a href="{{ route('blog.post', $post['slug']) }}">
                            @if($post['featured_image'])
                                <div class="h-48 bg-cover bg-center" style="background-image: url('{{ $post['featured_image'] }}');">
                                </div>
                            @else
                                <div class="h-48 bg-gradient-to-br from-[#CE9704] to-[#B8860B] flex items-center justify-center">
                                    <svg class="w-16 h-16 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            @endif
                        </a>
                        
                        <div class="p-4 sm:p-6">
                            <!-- Categories and Date -->
                            <div class="flex flex-wrap items-center gap-2 text-xs sm:text-sm text-gray-500 mb-3">
                                @if(!empty($post['categories']))
                                    @foreach(array_slice($post['categories'], 0, 2) as $category)
                                        <span class="bg-[#CE9704] text-white px-2 sm:px-3 py-1 rounded-full text-xs font-semibold">
                                            {{ $category }}
                                        </span>
                                    @endforeach
                                @endif
                                <time datetime="{{ $post['date'] }}">{{ $post['date_formatted'] }}</time>
                            </div>
                            
                            <!-- Title -->
                            <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-3">
                                <a href="{{ route('blog.post', $post['slug']) }}" class="hover:text-[#CE9704] transition-colors line-clamp-2">
                                    {{ $post['title'] }}
                                </a>
                            </h2>
                            
                            <!-- Excerpt -->
                            <p class="text-gray-600 mb-4 leading-relaxed text-sm sm:text-base line-clamp-3">
                                {{ $post['content'] }}
                            </p>
                            
                            <!-- Author (if available) -->
                            @if($post['author'])
                                <p class="text-xs sm:text-sm text-gray-500 mb-4">
                                    Por: {{ $post['author'] }}
                                </p>
                            @endif
                            
                            <!-- Read More Link -->
                            <a href="{{ route('blog.post', $post['slug']) }}" class="text-[#CE9704] font-semibold hover:underline inline-flex items-center text-sm sm:text-base">
                                Read More
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($totalPages > 1)
                <div class="flex justify-center items-center gap-2 mt-8 sm:mt-12 flex-wrap">
                    <!-- Previous Button -->
                    <a href="{{ route('blog', ['page' => $currentPage - 1]) }}" 
                       class="px-3 sm:px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed text-sm sm:text-base {{ $currentPage <= 1 ? 'pointer-events-none opacity-50' : '' }}">
                        Previous
                    </a>
                    
                    <!-- Page Numbers -->
                    @for($i = 1; $i <= min($totalPages, 5); $i++)
                        @if($i == 1 || $i == $totalPages || ($i >= $currentPage - 1 && $i <= $currentPage + 1))
                            <a href="{{ route('blog', ['page' => $i]) }}" 
                               class="px-3 sm:px-4 py-2 rounded-lg font-semibold text-sm sm:text-base {{ $currentPage == $i ? 'bg-[#CE9704] text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors' }}">
                                {{ $i }}
                            </a>
                        @elseif($i == $currentPage - 2 || $i == $currentPage + 2)
                            <span class="px-2 text-gray-500">...</span>
                        @endif
                    @endfor
                    
                    <!-- Next Button -->
                    <a href="{{ route('blog', ['page' => $currentPage + 1]) }}" 
                       class="px-3 sm:px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed text-sm sm:text-base {{ $currentPage >= $totalPages ? 'pointer-events-none opacity-50' : '' }}">
                        Next
                    </a>
                </div>
                
                <!-- Page Info -->
                <div class="text-center mt-4 text-sm text-gray-600">
                    Página {{ $currentPage }} de {{ $totalPages }} ({{ $totalPosts }} posts)
                </div>
            @endif
        @endif
    </div>
</div>

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush
@endsection