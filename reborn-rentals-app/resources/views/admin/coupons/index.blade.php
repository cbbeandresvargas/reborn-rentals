@extends('layouts.admin')

@section('title', 'Coupons - Admin Panel')

@section('content')
<div class="ml-0 md:ml-72 bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-gradient-to-r from-white to-gray-50 shadow-lg border-b border-gray-200 sticky top-0 z-40 backdrop-blur-sm">
        <div class="px-4 sm:px-6 lg:px-8 py-4 sm:py-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-[#CE9704] rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">Coupons</h1>
                        <p class="text-sm text-gray-500 mt-1">Manage discount coupons and promotions</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="hidden sm:flex items-center gap-2 px-4 py-2 bg-white rounded-lg shadow-sm border border-gray-200">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm font-semibold text-gray-700">{{ $coupons->total() ?? 0 }}</span>
                        <span class="text-xs text-gray-500">Total Coupons</span>
                    </div>
                    <button onclick="openCreateCouponModal()" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-[#CE9704] to-[#B8860B] text-white rounded-lg hover:shadow-lg transition-all duration-200 font-semibold text-sm shadow-md">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Coupon
            </button>
                </div>
            </div>
        </div>
    </header>
    
    <main class="p-4 sm:p-6 lg:p-8">
        @if(session('success'))
        <div id="success-message" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm" style="display: flex;">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all">
                <div class="p-6">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full">
                        <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Success</h3>
                    <p class="text-gray-600 text-center mb-6">{{ session('success') }}</p>
                    <button onclick="closeSuccessModal()" class="w-full bg-gradient-to-r from-[#CE9704] to-[#B8860B] text-white px-4 py-2.5 rounded-lg hover:from-[#B8860B] hover:to-[#CE9704] transition-all font-semibold">
                        OK
                    </button>
                </div>
            </div>
        </div>
        @endif

        @if($errors->any())
        <div id="error-message" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm" style="display: flex;">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all">
                <div class="p-6">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full">
                        <svg class="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Validation Errors</h3>
                    <ul class="text-gray-600 text-center mb-6 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
                    <button onclick="closeErrorModal()" class="w-full bg-red-600 text-white px-4 py-2.5 rounded-lg hover:bg-red-700 transition-all font-semibold">
                        OK
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- Coupons Table -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-white px-4 sm:px-6 lg:px-8 py-4 border-b border-gray-200">
                <h2 class="text-lg sm:text-xl font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#CE9704]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Coupons Catalog
                </h2>
            </div>
            
            <div class="overflow-x-auto">
            <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                    <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                    </svg>
                                    Code
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Discount
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Valid Period
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Status
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                    </svg>
                                    Actions
                                </div>
                            </th>
                    </tr>
                </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($coupons as $coupon)
                        <tr class="hover:bg-gradient-to-r hover:from-gray-50 hover:to-white transition-all duration-200 group cursor-pointer" onclick="window.location.href='{{ route('admin.coupons.show', $coupon) }}'">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-[#CE9704]/10 rounded-lg group-hover:bg-[#CE9704]/20 transition-colors">
                                        <svg class="w-5 h-5 text-[#CE9704]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 text-base">{{ $coupon->code }}</div>
                            @if($coupon->min_order_total)
                                        <div class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Min: ${{ number_format($coupon->min_order_total, 2) }}
                                        </div>
                            @endif
                                    </div>
                                </div>
                        </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-2">
                                    <span class="px-3 py-1.5 bg-green-100 text-green-800 rounded-full text-sm font-bold">
                                @if($coupon->discount_type === 'percentage')
                                    {{ $coupon->discount_value }}%
                                @else
                                    ${{ number_format($coupon->discount_value, 2) }}
                                @endif
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ $coupon->discount_type === 'percentage' ? 'off' : 'discount' }}
                                    </span>
                            </div>
                        </td>
                            <td class="px-6 py-5">
                                <div class="text-sm">
                                    <div class="font-medium text-gray-900 flex items-center gap-1 mb-1">
                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ $coupon->starts_at?->format('M d, Y') ?? 'No start' }}
                                    </div>
                                    <div class="text-gray-500 flex items-center gap-1">
                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $coupon->expires_at?->format('M d, Y') ?? 'No expiry' }}
                                    </div>
                                </div>
                        </td>
                            <td class="px-6 py-5" onclick="event.stopPropagation();">
                                <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST" class="inline" id="toggle-form-{{ $coupon->id }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="code" value="{{ $coupon->code }}">
                                    <input type="hidden" name="discount_type" value="{{ $coupon->discount_type }}">
                                    <input type="hidden" name="discount_value" value="{{ $coupon->discount_value }}">
                                    <input type="hidden" name="max_uses" value="{{ $coupon->max_uses }}">
                                    <input type="hidden" name="min_order_total" value="{{ $coupon->min_order_total }}">
                                    <input type="hidden" name="starts_at" value="{{ $coupon->starts_at ? $coupon->starts_at->format('Y-m-d\TH:i') : '' }}">
                                    <input type="hidden" name="expires_at" value="{{ $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '' }}">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ $coupon->is_active ? 'checked' : '' }}
                                            onchange="this.form.submit()">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#CE9704] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#CE9704]"></div>
                                        <span class="ml-3 text-sm font-medium {{ $coupon->is_active ? 'text-green-600' : 'text-gray-500' }}" id="status-label-{{ $coupon->id }}">
                                {{ $coupon->is_active ? 'Active' : 'Inactive' }}
                            </span>
                                    </label>
                                </form>
                        </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-2 text-sm font-medium text-gray-600 group-hover:text-[#CE9704] transition-colors">
                                    <span>View details</span>
                                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="p-4 bg-gray-100 rounded-full mb-4">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-lg font-semibold text-gray-700 mb-1">No coupons found</p>
                                    <p class="text-sm text-gray-500">Create your first coupon to get started.</p>
                                    <button onclick="openCreateCouponModal()" class="mt-4 inline-flex items-center px-4 py-2 bg-gradient-to-r from-[#CE9704] to-[#B8860B] text-white rounded-lg hover:shadow-lg transition-all duration-200 text-sm font-semibold">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Create First Coupon
                                    </button>
                                </div>
                            </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($coupons->hasPages())
        <div class="mt-6 flex justify-center">
            <div class="bg-white rounded-lg shadow-md border border-gray-200 px-4 py-3">
            {{ $coupons->links() }}
            </div>
        </div>
        @endif
    </main>
</div>

<!-- Create Coupon Modal -->
<div id="create-coupon-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" style="display: none; pointer-events: none;">
    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border-2 border-[#CE9704]/20">
        <!-- Header -->
        <div class="bg-gradient-to-r from-[#CE9704] to-[#B8860B] p-6 sticky top-0 z-10">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-white">Create New Coupon</h2>
                    <p class="text-sm text-white/90 mt-1">Add a new discount coupon to your catalog</p>
                </div>
                <button onclick="closeCreateCouponModal()" class="text-white hover:bg-white/20 rounded-full p-2 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Form -->
        <form action="{{ route('admin.coupons.store') }}" method="POST" id="create-coupon-form" class="p-6 sm:p-8">
            @csrf

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    Code <span class="text-red-500">*</span>
                </label>
                <input type="text" name="code" id="coupon-code" value="{{ old('code') }}" required
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704] transition-all {{ $errors->has('code') ? 'border-red-500' : '' }}"
                    placeholder="Enter coupon code">
                <div id="code-error" class="text-red-600 text-sm mt-1.5 font-medium {{ $errors->has('code') ? '' : 'hidden' }}">
                    @if($errors->has('code'))
                        {{ $errors->first('code') }}
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Discount Type <span class="text-red-500">*</span></label>
                    <select name="discount_type" id="coupon-discount-type" required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704] transition-all bg-white font-medium">
                        <option value="percentage" {{ old('discount_type') === 'percentage' ? 'selected' : '' }}>Percentage</option>
                        <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Discount Value <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold" id="discount-symbol">{{ old('discount_type') === 'fixed' ? '$' : '' }}</span>
                    <input type="number" name="discount_value" id="coupon-discount-value" step="0.01" value="{{ old('discount_value') }}" required
                            class="w-full {{ old('discount_type') === 'fixed' ? 'pl-8' : 'pl-4' }} pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704] transition-all {{ $errors->has('discount_value') ? 'border-red-500' : '' }}"
                            placeholder="0.00">
                    </div>
                    <div id="discount-value-error" class="text-red-600 text-sm mt-1.5 font-medium {{ $errors->has('discount_value') ? '' : 'hidden' }}">
                        @if($errors->has('discount_value'))
                            {{ $errors->first('discount_value') }}
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Max Uses</label>
                    <input type="number" name="max_uses" id="coupon-max-uses" value="{{ old('max_uses') }}"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704] transition-all"
                        placeholder="Unlimited">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Min Order Total</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold">$</span>
                    <input type="number" name="min_order_total" id="coupon-min-order" step="0.01" value="{{ old('min_order_total') }}"
                            class="w-full pl-8 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704] transition-all"
                            placeholder="0.00">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Starts At</label>
                    <input type="datetime-local" name="starts_at" id="coupon-starts-at" value="{{ old('starts_at') }}"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704] transition-all">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Expires At</label>
                    <input type="datetime-local" name="expires_at" id="coupon-expires-at" value="{{ old('expires_at') }}"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704] transition-all">
                </div>
            </div>

            <div class="mb-8">
                <label class="flex items-center p-4 bg-gray-50 rounded-lg border-2 border-gray-200 hover:border-[#CE9704] transition-colors cursor-pointer">
                    <input type="checkbox" name="is_active" id="coupon-is-active" value="1" {{ old('is_active') ? 'checked' : 'checked' }}
                        class="w-5 h-5 text-[#CE9704] border-gray-300 rounded focus:ring-[#CE9704]">
                    <span class="ml-3 text-sm font-semibold text-gray-700">Coupon is active and available for use</span>
                </label>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end pt-6 border-t border-gray-200">
                <button type="button" onclick="closeCreateCouponModal()" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-semibold">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-[#CE9704] to-[#B8860B] text-white rounded-lg hover:shadow-lg transition-all duration-200 font-bold">
                    Create Coupon
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Funciones para modales de éxito/error
function closeSuccessModal() {
    const modal = document.getElementById('success-message');
    if (modal) {
        modal.style.display = 'none';
    }
}

function closeErrorModal() {
    const modal = document.getElementById('error-message');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Auto-cerrar modales después de 5 segundos
document.addEventListener('DOMContentLoaded', function() {
    const successModal = document.getElementById('success-message');
    const errorModal = document.getElementById('error-message');
    
    if (successModal) {
        setTimeout(() => {
            closeSuccessModal();
        }, 5000);
    }
    
    if (errorModal) {
        setTimeout(() => {
            closeErrorModal();
        }, 5000);
    }
    
    // Cerrar al hacer click fuera del modal
    if (successModal) {
        successModal.addEventListener('click', function(e) {
            if (e.target === successModal) {
                closeSuccessModal();
            }
        });
    }
    
    if (errorModal) {
        errorModal.addEventListener('click', function(e) {
            if (e.target === errorModal) {
                closeErrorModal();
            }
        });
    }
    
    // Cerrar con tecla ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeSuccessModal();
            closeErrorModal();
        }
    });
});

function openCreateCouponModal() {
    const modal = document.getElementById('create-coupon-modal');
    if (modal) {
        modal.style.display = 'flex';
        modal.style.pointerEvents = 'auto';
        // Reset form
        document.getElementById('create-coupon-form').reset();
        // Clear error messages
        document.querySelectorAll('[id$="-error"]').forEach(el => {
            el.classList.add('hidden');
            el.textContent = '';
        });
        // Remove error borders
        document.querySelectorAll('#create-coupon-form input, #create-coupon-form select').forEach(el => {
            el.classList.remove('border-red-500');
        });
        // Reset discount symbol
        updateDiscountSymbol();
    }
}

function closeCreateCouponModal() {
    const modal = document.getElementById('create-coupon-modal');
    if (modal) {
        modal.style.display = 'none';
        modal.style.pointerEvents = 'none';
        // Reset form
        document.getElementById('create-coupon-form').reset();
        // Clear error messages
        document.querySelectorAll('[id$="-error"]').forEach(el => {
            el.classList.add('hidden');
            el.textContent = '';
        });
        // Remove error borders
        document.querySelectorAll('#create-coupon-form input, #create-coupon-form select').forEach(el => {
            el.classList.remove('border-red-500');
        });
    }
}

function updateDiscountSymbol() {
    const discountType = document.getElementById('coupon-discount-type')?.value;
    const discountSymbol = document.getElementById('discount-symbol');
    const discountInput = document.getElementById('coupon-discount-value');
    
    if (discountSymbol && discountInput) {
        if (discountType === 'fixed') {
            discountSymbol.textContent = '$';
            discountInput.classList.add('pl-8');
            discountInput.classList.remove('pl-4');
        } else {
            discountSymbol.textContent = '';
            discountInput.classList.remove('pl-8');
            discountInput.classList.add('pl-4');
        }
    }
}

// Close modal when clicking outside and handle form
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('create-coupon-modal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeCreateCouponModal();
            }
        });
    }

    // Update discount symbol when type changes
    const discountTypeSelect = document.getElementById('coupon-discount-type');
    if (discountTypeSelect) {
        discountTypeSelect.addEventListener('change', updateDiscountSymbol);
    }

    // Handle form submission with validation
    const form = document.getElementById('create-coupon-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Clear previous errors
            document.querySelectorAll('[id$="-error"]').forEach(el => {
                el.classList.add('hidden');
                el.textContent = '';
            });

            // Basic client-side validation
            const code = document.getElementById('coupon-code').value.trim();
            const discountValue = document.getElementById('coupon-discount-value').value;

            if (!code) {
                e.preventDefault();
                const errorEl = document.getElementById('code-error');
                const inputEl = document.getElementById('coupon-code');
                errorEl.textContent = 'The code is required';
                errorEl.classList.remove('hidden');
                inputEl.classList.add('border-red-500');
                return false;
            }

            if (!discountValue || parseFloat(discountValue) <= 0) {
                e.preventDefault();
                const errorEl = document.getElementById('discount-value-error');
                const inputEl = document.getElementById('coupon-discount-value');
                errorEl.textContent = 'The discount value must be greater than 0';
                errorEl.classList.remove('hidden');
                inputEl.classList.add('border-red-500');
                return false;
            }
        });
    }

    // Open modal automatically if there are validation errors
    @if($errors->any() && old('_token'))
        openCreateCouponModal();
    @endif

    // Update status labels when toggles change
    document.querySelectorAll('input[name="is_active"]').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const formId = this.closest('form').id;
            const couponId = formId.replace('toggle-form-', '');
            const label = document.getElementById('status-label-' + couponId);
            
            if (label) {
                if (this.checked) {
                    label.textContent = 'Active';
                    label.classList.remove('text-gray-500');
                    label.classList.add('text-green-600');
                } else {
                    label.textContent = 'Inactive';
                    label.classList.remove('text-green-600');
                    label.classList.add('text-gray-500');
                }
            }
        });
    });
});
</script>
@endsection
