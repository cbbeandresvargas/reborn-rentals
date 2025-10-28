@extends('layouts.app')

@section('title', 'Coupons - Admin Panel')

@section('content')
<div class="ml-0 md:ml-64">
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
        <div class="px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Coupons</h1>
            <a href="{{ route('admin.coupons.create') }}" class="bg-[#CE9704] text-white px-4 py-2 rounded-lg hover:bg-[#B8860B] transition-colors">
                + Add Coupon
            </a>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Discount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valid Period</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($coupons as $coupon)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $coupon->code }}</div>
                            @if($coupon->min_order_total)
                            <div class="text-xs text-gray-500">Min: ${{ number_format($coupon->min_order_total, 2) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold">
                                @if($coupon->discount_type === 'percentage')
                                    {{ $coupon->discount_value }}%
                                @else
                                    ${{ number_format($coupon->discount_value, 2) }}
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <div>{{ $coupon->starts_at?->format('M d, Y') ?? 'No start' }}</div>
                            <div>{{ $coupon->expires_at?->format('M d, Y') ?? 'No expiry' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $coupon->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $coupon->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-[#CE9704] hover:text-[#B8860B] mr-3">Edit</a>
                            <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este cupón?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No coupons found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $coupons->links() }}
        </div>
    </main>
</div>

@include('admin.sidebar')
@endsection

