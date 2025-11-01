@extends('layouts.app')

@section('title', 'Login - Reborn Rentals')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-6 sm:py-8 md:py-12 px-3 sm:px-4 md:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-6 sm:space-y-8 bg-white p-6 sm:p-8 rounded-lg shadow-lg">
        <div class="text-center">
            <img src="{{ asset('Logo.png') }}" alt="Reborn Rentals" class="mx-auto h-12 sm:h-16 mb-4 sm:mb-6" />
            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900">Sign in to your account</h2>
        </div>
        
        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        
        <form class="mt-8 space-y-6" method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                    <input id="email" name="email" type="email" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-[#CE9704] focus:border-[#CE9704]"
                           value="{{ old('email') }}" />
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input id="password" name="password" type="password" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-[#CE9704] focus:border-[#CE9704]" />
                </div>
                
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" 
                           class="h-4 w-4 text-[#CE9704] focus:ring-[#CE9704] border-gray-300 rounded" />
                    <label for="remember" class="ml-2 block text-sm text-gray-900">Remember me</label>
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg text-sm font-medium text-white bg-[#CE9704] hover:bg-[#B8860B] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#CE9704]">
                    Sign in
                </button>
            </div>
            
            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Don't have an account? 
                    <a href="{{ route('register') }}" class="font-medium text-[#CE9704] hover:text-[#B8860B]">Register here</a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection

