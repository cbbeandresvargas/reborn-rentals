<!-- Sidebar Admin -->
<div class="fixed inset-y-0 left-0 w-64 bg-[#4A4A4A] text-white z-50 hidden md:block">
    <div class="p-6 border-b border-gray-600">
        <h2 class="text-2xl font-bold text-[#CE9704]">Admin Panel</h2>
        <p class="text-sm text-gray-300 mt-1">{{ Auth::user()->name }}</p>
    </div>
    
    <nav class="mt-6">
        <a href="{{ route('admin.dashboard') }}" class="block px-6 py-3 hover:bg-[#2F2F2F] transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-[#CE9704]' : '' }}">
            Dashboard
        </a>
        <a href="{{ route('admin.products.index') }}" class="block px-6 py-3 hover:bg-[#2F2F2F] transition-colors {{ request()->routeIs('admin.products.*') ? 'bg-[#CE9704]' : '' }}">
            Products
        </a>
        <a href="{{ route('admin.orders.index') }}" class="block px-6 py-3 hover:bg-[#2F2F2F] transition-colors {{ request()->routeIs('admin.orders.*') ? 'bg-[#CE9704]' : '' }}">
            Orders
        </a>
        <a href="{{ route('admin.users.index') }}" class="block px-6 py-3 hover:bg-[#2F2F2F] transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-[#CE9704]' : '' }}">
            Users
        </a>
        <a href="{{ route('admin.categories.index') }}" class="block px-6 py-3 hover:bg-[#2F2F2F] transition-colors {{ request()->routeIs('admin.categories.*') ? 'bg-[#CE9704]' : '' }}">
            Categories
        </a>
        <a href="{{ route('admin.coupons.index') }}" class="block px-6 py-3 hover:bg-[#2F2F2F] transition-colors {{ request()->routeIs('admin.coupons.*') ? 'bg-[#CE9704]' : '' }}">
            Coupons
        </a>
    </nav>
    
    <div class="absolute bottom-0 left-0 right-0 p-6 border-t border-gray-600">
        <a href="{{ route('home') }}" class="block px-6 py-3 hover:bg-[#2F2F2F] transition-colors text-sm">
            ← Back to Site
        </a>
        <form method="POST" action="{{ route('logout') }}" class="mt-2">
            @csrf
            <button type="submit" class="block w-full text-left px-6 py-3 hover:bg-[#2F2F2F] transition-colors text-sm">
                Logout
            </button>
        </form>
    </div>
</div>

