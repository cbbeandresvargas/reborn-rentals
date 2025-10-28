@extends('layouts.app')

@section('title', 'Create Coupon - Admin Panel')

@section('content')
<div class="ml-0 md:ml-64">
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
        <div class="px-6 py-4">
            <h1 class="text-2xl font-bold text-gray-800">Create Coupon</h1>
        </div>
    </header>
    
    <main class="p-6">
        <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
            <form action="{{ route('admin.coupons.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Code *</label>
                    <input type="text" name="code" value="{{ old('code') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704]">
                    @error('code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Discount Type *</label>
                        <select name="discount_type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704]">
                            <option value="percentage" {{ old('discount_type') === 'percentage' ? 'selected' : '' }}>Percentage</option>
                            <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Discount Value *</label>
                        <input type="number" name="discount_value" step="0.01" value="{{ old('discount_value') }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704]">
                        @error('discount_value')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Max Uses</label>
                        <input type="number" name="max_uses" value="{{ old('max_uses') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704]">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Min Order Total</label>
                        <input type="number" name="min_order_total" step="0.01" value="{{ old('min_order_total') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704]">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Starts At</label>
                        <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704]">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Expires At</label>
                        <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704]">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}
                            class="w-4 h-4 text-[#CE9704] border-gray-300 rounded focus:ring-[#CE9704]">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="bg-[#CE9704] text-white px-6 py-2 rounded-lg hover:bg-[#B8860B] transition-colors">
                        Create Coupon
                    </button>
                    <a href="{{ route('admin.coupons.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>
</div>

@include('admin.sidebar')
@endsection

