@extends('layouts.app')

@section('title', 'Orders - Admin Panel')

@section('content')
<div class="ml-0 md:ml-64">
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
        <div class="px-6 py-4">
            <h1 class="text-2xl font-bold text-gray-800">Orders</h1>
        </div>
    </header>
    
    <main class="p-6">
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($orders as $order)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">#{{ $order->id }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $order->user->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">${{ number_format($order->total_amount, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $order->ordered_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $order->status ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $order->status ? 'Completed' : 'Pending' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-[#CE9704] hover:text-[#B8860B]">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No orders found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </main>
</div>

@include('admin.sidebar')
@endsection

