@extends('layouts.app')

@section('title', $post['title'] . ' - Reborn Rentals Blog')

@section('content')
<div class="min-h-screen bg-gray-50 py-4 sm:py-8">
    <div class="max-w-4xl mx-auto px-3 sm:px-4 md:px-8">
        <!-- Back Button -->
        <a href="{{ route('blog') }}" class="inline-flex items-center text-sm sm:text-base text-[#CE9704] hover:underline mb-4 sm:mb-6">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Blog
        </a>

        <!-- Article Header -->
        <article class="bg-white rounded-lg shadow-md overflow-hidden mb-8 sm:mb-12">
            <!-- Featured Image -->
            @if($post['featured_image'])
                <div class="h-48 sm:h-64 md:h-96 bg-cover bg-center w-full" style="background-image: url('{{ $post['featured_image'] }}');">
                </div>
            @endif

            <div class="p-4 sm:p-6 md:p-8">
                <!-- Categories and Date -->
                <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-4 sm:mb-6">
                    @if(!empty($post['categories']))
                        @foreach($post['categories'] as $category)
                            <span class="bg-[#CE9704] text-white px-3 sm:px-4 py-1 sm:py-1.5 rounded-full text-xs sm:text-sm font-semibold">
                                {{ $category }}
                            </span>
                        @endforeach
                    @endif
                    <time datetime="{{ $post['date'] }}" class="text-xs sm:text-sm text-gray-500">
                        {{ $post['date_formatted'] }}
                    </time>
                    @if($post['author'])
                        <span class="text-xs sm:text-sm text-gray-500">
                            By: {{ $post['author'] }}
                        </span>
                    @endif
                </div>

                <!-- Title -->
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-4 sm:mb-6 leading-tight">
                    {{ $post['title'] }}
                </h1>

                <!-- Tags (if available) -->
                @if(!empty($post['tags']))
                    <div class="flex flex-wrap gap-2 mb-4 sm:mb-6">
                        @foreach($post['tags'] as $tag)
                            <span class="bg-gray-100 text-gray-700 px-2 sm:px-3 py-1 rounded text-xs sm:text-sm">
                                #{{ $tag }}
                            </span>
                        @endforeach
                    </div>
                @endif

                <!-- Content -->
                <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                    {!! $post['content'] !!}
                </div>

                <!-- Share Buttons -->
                <div class="mt-8 sm:mt-12 pt-6 sm:pt-8 border-t border-gray-200">
                    <h3 class="text-sm sm:text-base font-semibold text-gray-900 mb-3 sm:mb-4">Share:</h3>
                    <div class="flex gap-3 sm:gap-4">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('blog.post', $post['slug'])) }}" 
                           target="_blank" 
                           class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm sm:text-base">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('blog.post', $post['slug'])) }}&text={{ urlencode($post['title']) }}" 
                           target="_blank" 
                           class="flex items-center gap-2 px-4 py-2 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition-colors text-sm sm:text-base">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                            Twitter
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('blog.post', $post['slug'])) }}" 
                           target="_blank" 
                           class="flex items-center gap-2 px-4 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800 transition-colors text-sm sm:text-base">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                            </svg>
                            LinkedIn
                        </a>
                    </div>
                </div>
            </div>
        </article>

        <!-- Related Posts -->
        @if(!empty($relatedPosts))
            <div class="mb-8 sm:mb-12">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6 sm:mb-8">Related Posts</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                    @foreach($relatedPosts as $relatedPost)
                        <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <!-- Featured Image -->
                            <a href="{{ route('blog.post', $relatedPost['slug']) }}">
                                @if($relatedPost['featured_image'])
                                    <div class="h-48 bg-cover bg-center" style="background-image: url('{{ $relatedPost['featured_image'] }}');">
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
                                <!-- Categories -->
                                @if(!empty($relatedPost['categories']))
                                    <div class="flex flex-wrap gap-2 mb-3">
                                        @foreach(array_slice($relatedPost['categories'], 0, 1) as $category)
                                            <span class="bg-[#CE9704] text-white px-2 sm:px-3 py-1 rounded-full text-xs font-semibold">
                                                {{ $category }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                                
                                <!-- Title -->
                                <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-3">
                                    <a href="{{ route('blog.post', $relatedPost['slug']) }}" class="hover:text-[#CE9704] transition-colors line-clamp-2">
                                        {{ $relatedPost['title'] }}
                                    </a>
                                </h3>
                                
                                <!-- Excerpt -->
                                <p class="text-gray-600 mb-4 leading-relaxed text-sm sm:text-base line-clamp-3">
                                    {{ $relatedPost['content'] }}
                                </p>
                                
                                <!-- Read More Link -->
                                <a href="{{ route('blog.post', $relatedPost['slug']) }}" class="text-[#CE9704] font-semibold hover:underline inline-flex items-center text-sm sm:text-base">
                                    Read More
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Back to Blog -->
        <div class="text-center">
            <a href="{{ route('blog') }}" class="inline-flex items-center px-6 py-3 bg-[#CE9704] text-white rounded-lg hover:bg-[#B8860B] transition-colors text-base sm:text-lg font-semibold">
                View All Posts
            </a>
        </div>
    </div>
</div>

@push('styles')
<style>
    .prose {
        color: #374151;
        max-width: 65ch;
    }
    .prose p {
        margin-top: 1.25em;
        margin-bottom: 1.25em;
    }
    .prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
        color: #111827;
        font-weight: 800;
        margin-top: 2em;
        margin-bottom: 1em;
        line-height: 1.2;
    }
    .prose h1 { font-size: 2.25em; }
    .prose h2 { font-size: 1.875em; }
    .prose h3 { font-size: 1.5em; }
    .prose h4 { font-size: 1.25em; }
    .prose a {
        color: #CE9704;
        text-decoration: underline;
    }
    .prose a:hover {
        color: #B8860B;
    }
    .prose img {
        max-width: 100%;
        height: auto;
        border-radius: 0.5rem;
        margin-top: 2em;
        margin-bottom: 2em;
    }
    .prose ul, .prose ol {
        margin-top: 1.25em;
        margin-bottom: 1.25em;
        padding-left: 1.625em;
    }
    .prose li {
        margin-top: 0.5em;
        margin-bottom: 0.5em;
    }
    .prose blockquote {
        border-left: 4px solid #CE9704;
        padding-left: 1em;
        margin-top: 1.6em;
        margin-bottom: 1.6em;
        font-style: italic;
        color: #6B7280;
    }
    .prose code {
        background-color: #F3F4F6;
        padding: 0.125em 0.375em;
        border-radius: 0.25rem;
        font-size: 0.875em;
        font-family: ui-monospace, SFMono-Regular, "SF Mono", Menlo, Consolas, "Liberation Mono", monospace;
    }
    .prose pre {
        background-color: #1F2937;
        color: #F9FAFB;
        padding: 1em;
        border-radius: 0.5rem;
        overflow-x: auto;
        margin-top: 1.25em;
        margin-bottom: 1.25em;
    }
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
