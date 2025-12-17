<!-- Sidebar Admin - Modern High-End Tech Design -->
<div class="fixed inset-y-0 left-0 w-72 bg-gradient-to-b from-[#1a1a1a] via-[#2d2d2d] to-[#1a1a1a] text-white z-50 hidden md:block shadow-2xl border-r border-[#CE9704]/20 backdrop-blur-xl" style="background: linear-gradient(180deg, #1a1a1a 0%, #2d2d2d 50%, #1a1a1a 100%);">
    <!-- Header Section with Gradient -->
    <div class="p-6 border-b border-[#CE9704]/30 bg-gradient-to-r from-[#CE9704]/10 via-[#CE9704]/5 to-transparent">
        <div class="flex items-center space-x-3 mb-2">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#CE9704] to-[#B8860B] flex items-center justify-center shadow-lg shadow-[#CE9704]/30">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold bg-gradient-to-r from-[#CE9704] to-[#FFD700] bg-clip-text text-transparent">Admin Panel</h2>
                <p class="text-xs text-gray-400 font-medium">Control Center</p>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-[#CE9704]/20">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#CE9704] to-[#B8860B] flex items-center justify-center text-xs font-bold shadow-md">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Navigation with Icons -->
    <nav class="mt-6 px-3 space-y-1">
        <a href="{{ route('admin.dashboard') }}" class="group flex items-center px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-[#CE9704] to-[#B8860B] shadow-lg shadow-[#CE9704]/30 text-white' : 'text-gray-300 hover:bg-[#2F2F2F]/50 hover:text-white' }}">
            <div class="w-10 h-10 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-white/20' : 'bg-[#CE9704]/10 group-hover:bg-[#CE9704]/20' }} flex items-center justify-center mr-3 transition-all duration-300">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-[#CE9704]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
            </div>
            <span class="font-semibold text-sm">Dashboard</span>
            @if(request()->routeIs('admin.dashboard'))
            <div class="ml-auto w-2 h-2 rounded-full bg-white animate-pulse"></div>
            @endif
        </a>
        
        <a href="{{ route('admin.products.index') }}" class="group flex items-center px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.products.*') ? 'bg-gradient-to-r from-[#CE9704] to-[#B8860B] shadow-lg shadow-[#CE9704]/30 text-white' : 'text-gray-300 hover:bg-[#2F2F2F]/50 hover:text-white' }}">
            <div class="w-10 h-10 rounded-lg {{ request()->routeIs('admin.products.*') ? 'bg-white/20' : 'bg-[#CE9704]/10 group-hover:bg-[#CE9704]/20' }} flex items-center justify-center mr-3 transition-all duration-300">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.products.*') ? 'text-white' : 'text-[#CE9704]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
            <span class="font-semibold text-sm">Products</span>
            @if(request()->routeIs('admin.products.*'))
            <div class="ml-auto w-2 h-2 rounded-full bg-white animate-pulse"></div>
            @endif
        </a>
        
        <a href="{{ route('admin.orders.index') }}" class="group flex items-center px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.orders.*') ? 'bg-gradient-to-r from-[#CE9704] to-[#B8860B] shadow-lg shadow-[#CE9704]/30 text-white' : 'text-gray-300 hover:bg-[#2F2F2F]/50 hover:text-white' }}">
            <div class="w-10 h-10 rounded-lg {{ request()->routeIs('admin.orders.*') ? 'bg-white/20' : 'bg-[#CE9704]/10 group-hover:bg-[#CE9704]/20' }} flex items-center justify-center mr-3 transition-all duration-300">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.orders.*') ? 'text-white' : 'text-[#CE9704]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </div>
            <span class="font-semibold text-sm">Orders</span>
            @if(request()->routeIs('admin.orders.*'))
            <div class="ml-auto w-2 h-2 rounded-full bg-white animate-pulse"></div>
            @endif
        </a>
        
        <a href="{{ route('admin.users.index') }}" class="group flex items-center px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.users.*') ? 'bg-gradient-to-r from-[#CE9704] to-[#B8860B] shadow-lg shadow-[#CE9704]/30 text-white' : 'text-gray-300 hover:bg-[#2F2F2F]/50 hover:text-white' }}">
            <div class="w-10 h-10 rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-white/20' : 'bg-[#CE9704]/10 group-hover:bg-[#CE9704]/20' }} flex items-center justify-center mr-3 transition-all duration-300">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-[#CE9704]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
            <span class="font-semibold text-sm">Users</span>
            @if(request()->routeIs('admin.users.*'))
            <div class="ml-auto w-2 h-2 rounded-full bg-white animate-pulse"></div>
            @endif
        </a>
        
        <a href="{{ route('admin.categories.index') }}" class="group flex items-center px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.categories.*') ? 'bg-gradient-to-r from-[#CE9704] to-[#B8860B] shadow-lg shadow-[#CE9704]/30 text-white' : 'text-gray-300 hover:bg-[#2F2F2F]/50 hover:text-white' }}">
            <div class="w-10 h-10 rounded-lg {{ request()->routeIs('admin.categories.*') ? 'bg-white/20' : 'bg-[#CE9704]/10 group-hover:bg-[#CE9704]/20' }} flex items-center justify-center mr-3 transition-all duration-300">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.categories.*') ? 'text-white' : 'text-[#CE9704]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
            </div>
            <span class="font-semibold text-sm">Categories</span>
            @if(request()->routeIs('admin.categories.*'))
            <div class="ml-auto w-2 h-2 rounded-full bg-white animate-pulse"></div>
            @endif
        </a>
        
        <a href="{{ route('admin.coupons.index') }}" class="group flex items-center px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.coupons.*') ? 'bg-gradient-to-r from-[#CE9704] to-[#B8860B] shadow-lg shadow-[#CE9704]/30 text-white' : 'text-gray-300 hover:bg-[#2F2F2F]/50 hover:text-white' }}">
            <div class="w-10 h-10 rounded-lg {{ request()->routeIs('admin.coupons.*') ? 'bg-white/20' : 'bg-[#CE9704]/10 group-hover:bg-[#CE9704]/20' }} flex items-center justify-center mr-3 transition-all duration-300">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.coupons.*') ? 'text-white' : 'text-[#CE9704]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <span class="font-semibold text-sm">Coupons</span>
            @if(request()->routeIs('admin.coupons.*'))
            <div class="ml-auto w-2 h-2 rounded-full bg-white animate-pulse"></div>
            @endif
        </a>
    </nav>
    
    <!-- Bottom Actions with Modern Design -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-[#CE9704]/20 bg-gradient-to-t from-[#1a1a1a] to-transparent">
        <a href="{{ route('home') }}" class="group flex items-center justify-center px-4 py-3 mb-2 rounded-xl bg-[#2F2F2F]/50 hover:bg-gradient-to-r hover:from-[#CE9704]/20 hover:to-[#B8860B]/20 border border-[#CE9704]/20 hover:border-[#CE9704]/40 transition-all duration-300">
            <svg class="w-4 h-4 text-gray-400 group-hover:text-[#CE9704] mr-2 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span class="text-sm font-medium text-gray-300 group-hover:text-[#CE9704] transition-colors">Back to Site</span>
        </a>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center px-4 py-3 rounded-xl bg-gradient-to-r from-red-600/20 to-red-700/20 hover:from-red-600/30 hover:to-red-700/30 border border-red-500/30 hover:border-red-500/50 transition-all duration-300 group">
                <svg class="w-4 h-4 text-red-400 group-hover:text-red-300 mr-2 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                <span class="text-sm font-medium text-red-400 group-hover:text-red-300 transition-colors">Logout</span>
            </button>
        </form>
    </div>
</div>

