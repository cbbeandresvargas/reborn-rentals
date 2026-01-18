@extends('layouts.auth')

@section('title', 'Login - Reborn Rentals')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-6 sm:py-8 md:py-12 px-3 sm:px-4 md:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-6 sm:space-y-8 bg-black p-6 sm:p-8 rounded-lg shadow-2xl">
        <div class="text-center">
            <img src="{{ asset('Logo.png') }}" alt="Reborn Rentals" class="mx-auto h-12 sm:h-16 mb-4 sm:mb-6" />
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Sign in to your account</h2>
        </div>
        
        @if($errors->any())
        <div class="bg-red-900/50 border border-red-500 text-red-200 px-4 py-3 rounded">
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
                    <label for="email" class="block text-sm font-medium text-white">Email address</label>
                    <input id="email" name="email" type="email" required 
                           class="mt-1 block w-full px-3 py-2 border border-white/30 bg-white/10 backdrop-blur-sm rounded-lg text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white focus:border-white"
                           value="{{ old('email') }}" placeholder="tu@email.com" />
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-white">Password</label>
                    <div class="relative mt-1">
                        <input id="password" name="password" type="password" required 
                               class="block w-full px-3 py-2 pr-10 border border-white/30 bg-white/10 backdrop-blur-sm rounded-lg text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white focus:border-white" placeholder="••••••••" />
                        <button type="button" id="togglePassword" 
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-black hover:text-gray-800 focus:outline-none">
                            <!-- Eye icon (visible when password is hidden) -->
                            <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <!-- Eye slash icon (visible when password is shown) -->
                            <svg id="eyeSlashIcon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" 
                           class="h-4 w-4 text-black focus:ring-black border-white/50 rounded bg-white/20" />
                    <label for="remember" class="ml-2 block text-sm text-white">Remember me</label>
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-r from-[#CE9704] to-[#B8860B] hover:from-[#B8860B] hover:to-[#CE9704] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#CE9704] transition-all duration-200 shadow-lg">
                    Sign in
                </button>
            </div>
            
        
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        const eyeSlashIcon = document.getElementById('eyeSlashIcon');
        
        if (togglePassword && passwordInput && eyeIcon && eyeSlashIcon) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle icons
                if (type === 'text') {
                    eyeIcon.classList.add('hidden');
                    eyeSlashIcon.classList.remove('hidden');
                } else {
                    eyeIcon.classList.remove('hidden');
                    eyeSlashIcon.classList.add('hidden');
                }
            });
        }
    });
</script>
@endsection

