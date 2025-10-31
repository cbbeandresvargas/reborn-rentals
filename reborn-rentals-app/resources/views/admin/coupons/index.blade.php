@extends('layouts.app')

@section('title', 'Coupons - Admin Panel')

@section('content')
<div class="ml-0 md:ml-64">
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
        <div class="px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Coupons</h1>
            <button onclick="openCreateCouponModal()" class="bg-[#CE9704] text-white px-4 py-2 rounded-lg hover:bg-[#B8860B] transition-colors">
                + Add Coupon
            </button>
        </div>
    </header>
    
    <main class="p-6">
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" id="success-message">
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
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
                    <tr class="hover:bg-gray-50 transition-colors cursor-pointer" onclick="window.location.href='{{ route('admin.coupons.show', $coupon) }}'">
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
                        <td class="px-6 py-4 text-sm font-medium text-gray-500">
                            Click to view details →
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

<!-- Create Coupon Modal -->
<div id="create-coupon-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">
        <!-- Header -->
        <div class="p-6 border-b border-gray-200 sticky top-0 bg-white z-10">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-800">Create Coupon</h2>
                <button onclick="closeCreateCouponModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Form -->
        <form action="{{ route('admin.coupons.store') }}" method="POST" id="create-coupon-form" class="p-6">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Code *</label>
                <input type="text" name="code" id="coupon-code" value="{{ old('code') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704] {{ $errors->has('code') ? 'border-red-500' : '' }}">
                <div id="code-error" class="text-red-500 text-sm mt-1 {{ $errors->has('code') ? '' : 'hidden' }}">
                    @if($errors->has('code'))
                        {{ $errors->first('code') }}
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Discount Type *</label>
                    <select name="discount_type" id="coupon-discount-type" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704]">
                        <option value="percentage" {{ old('discount_type') === 'percentage' ? 'selected' : '' }}>Percentage</option>
                        <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Discount Value *</label>
                    <input type="number" name="discount_value" id="coupon-discount-value" step="0.01" value="{{ old('discount_value') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704] {{ $errors->has('discount_value') ? 'border-red-500' : '' }}">
                    <div id="discount-value-error" class="text-red-500 text-sm mt-1 {{ $errors->has('discount_value') ? '' : 'hidden' }}">
                        @if($errors->has('discount_value'))
                            {{ $errors->first('discount_value') }}
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Max Uses</label>
                    <input type="number" name="max_uses" id="coupon-max-uses" value="{{ old('max_uses') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Min Order Total</label>
                    <input type="number" name="min_order_total" id="coupon-min-order" step="0.01" value="{{ old('min_order_total') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704]">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Starts At</label>
                    <input type="datetime-local" name="starts_at" id="coupon-starts-at" value="{{ old('starts_at') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Expires At</label>
                    <input type="datetime-local" name="expires_at" id="coupon-expires-at" value="{{ old('expires_at') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704]">
                </div>
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" id="coupon-is-active" value="1" {{ old('is_active') ? 'checked' : 'checked' }}
                        class="w-4 h-4 text-[#CE9704] border-gray-300 rounded focus:ring-[#CE9704]">
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                </label>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-[#CE9704] text-white px-6 py-2 rounded-lg hover:bg-[#B8860B] transition-colors">
                    Create Coupon
                </button>
                <button type="button" onclick="closeCreateCouponModal()" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>


<script>

function openCreateCouponModal() {
    const modal = document.getElementById('create-coupon-modal');
    if (modal) {
        modal.style.display = 'flex';
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

function closeCreateCouponModal() {
    const modal = document.getElementById('create-coupon-modal');
    if (modal) {
        modal.style.display = 'none';
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

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('create-coupon-modal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeCreateCouponModal();
            }
        });
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
                errorEl.textContent = 'El código es requerido';
                errorEl.classList.remove('hidden');
                inputEl.classList.add('border-red-500');
                return false;
            }

            if (!discountValue || parseFloat(discountValue) <= 0) {
                e.preventDefault();
                const errorEl = document.getElementById('discount-value-error');
                const inputEl = document.getElementById('coupon-discount-value');
                errorEl.textContent = 'El valor de descuento debe ser mayor a 0';
                errorEl.classList.remove('hidden');
                inputEl.classList.add('border-red-500');
                return false;
            }
        });
    }
});

// Hide success message after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const successMsg = document.getElementById('success-message');
    if (successMsg) {
        setTimeout(() => {
            successMsg.style.display = 'none';
        }, 5000);
    }

    // Open modal automatically if there are validation errors
    @if($errors->any() && old('_token'))
        openCreateCouponModal();
    @endif
});

</script>
@endsection

