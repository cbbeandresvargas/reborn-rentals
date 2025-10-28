@extends('layouts.app')

@section('title', 'User Details - Admin Panel')

@section('content')
<div class="ml-0 md:ml-64">
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
        <div class="px-6 py-4">
            <h1 class="text-2xl font-bold text-gray-800">User Details</h1>
        </div>
    </header>
    
    <main class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold mb-4">User Information</h2>
                    <div class="space-y-3">
                        <div><strong>Name:</strong> {{ $user->name }} {{ $user->last_name }}</div>
                        <div><strong>Email:</strong> {{ $user->email }}</div>
                        <div><strong>Username:</strong> {{ $user->username ?? 'N/A' }}</div>
                        <div><strong>Phone:</strong> {{ $user->phone_number ?? 'N/A' }}</div>
                        <div><strong>Address:</strong> {{ $user->address ?? 'N/A' }}</div>
                        <div><strong>Role:</strong> <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm">{{ ucfirst($user->role) }}</span></div>
                        <div><strong>Registered:</strong> {{ $user->created_at->format('M d, Y') }}</div>
                    </div>
                </div>

                @if($user->orders->count() > 0)
                <div class="bg-white rounded-lg shadow p-6 mt-6">
                    <h2 class="text-xl font-bold mb-4">Order History</h2>
                    <div class="space-y-3">
                        @foreach($user->orders->take(5) as $order)
                        <div class="flex justify-between items-center border-b pb-2">
                            <div>
                                <p class="font-semibold">Order #{{ $order->id }}</p>
                                <p class="text-sm text-gray-500">{{ $order->ordered_at->format('M d, Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold">${{ number_format($order->total_amount, 2) }}</p>
                                <span class="px-2 py-1 text-xs rounded {{ $order->status ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $order->status ? 'Completed' : 'Pending' }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <div>
                <div class="bg-white rounded-lg shadow p-6">
                    <a href="{{ route('admin.users.edit', $user) }}" class="block w-full bg-[#CE9704] text-white px-4 py-2 rounded-lg hover:bg-[#B8860B] transition-colors text-center mb-3">
                        Edit User
                    </a>
                    @if($user->id !== auth()->id())
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este usuario?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                            Delete User
                        </button>
                    </form>
                    @endif
                </div>

                <div class="bg-white rounded-lg shadow p-6 mt-6">
                    <h3 class="font-semibold mb-3">Statistics</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>Total Orders:</span>
                            <span class="font-semibold">{{ $user->orders->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Total Spent:</span>
                            <span class="font-semibold">${{ number_format($user->orders->sum('total_amount'), 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('admin.users.index') }}" class="text-[#CE9704] hover:underline">← Back to Users</a>
        </div>
    </main>
</div>

@include('admin.sidebar')
@endsection

