@extends('layouts.app')

@section('title', 'Register - Reborn Rentals')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow-lg">
        <div class="text-center">
            <img src="{{ asset('Logo.png') }}" alt="Reborn Rentals" class="mx-auto h-16 mb-6" />
            <h2 class="text-3xl font-extrabold text-gray-900">Create your account</h2>
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
        
        <form class="mt-8 space-y-6" method="POST" action="{{ route('register') }}">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input id="name" name="name" type="text" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-[#CE9704] focus:border-[#CE9704]"
                           value="{{ old('name') }}" />
                </div>
                
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name (optional)</label>
                    <input id="last_name" name="last_name" type="text" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-[#CE9704] focus:border-[#CE9704]"
                           value="{{ old('last_name') }}" />
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                    <input id="email" name="email" type="email" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-[#CE9704] focus:border-[#CE9704]"
                           value="{{ old('email') }}" />
                </div>
                
                <div>
                    <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number (optional)</label>
                    <input id="phone_number" name="phone_number" type="tel" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-[#CE9704] focus:border-[#CE9704]"
                           value="{{ old('phone_number') }}" />
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input id="password" name="password" type="password" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-[#CE9704] focus:border-[#CE9704]" />
                </div>
                
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-[#CE9704] focus:border-[#CE9704]" />
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg text-sm font-medium text-white bg-[#CE9704] hover:bg-[#B8860B] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#CE9704]">
                    Create Account
                </button>
            </div>
            
            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="font-medium text-[#CE9704] hover:text-[#B8860B]">Sign in here</a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection

