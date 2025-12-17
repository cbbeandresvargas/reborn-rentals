@extends('layouts.admin')

@section('title', $coupon->code . ' - Coupon Details')

@section('content')
<div class="ml-0 md:ml-72">
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40 px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.coupons.index') }}" class="text-[#CE9704] hover:text-[#B8860B] transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Coupon Details</h1>
        </div>
        <div class="flex gap-3">
            <button onclick="openEditCouponModal()" class="bg-[#CE9704] text-white px-4 py-2 rounded-lg hover:bg-[#B8860B] transition-colors">
                Edit Coupon
            </button>
            <button onclick="openDeleteCouponModal()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                Delete Coupon
            </button>
        </div>
    </header>
    
    <main class="p-6">
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Coupon Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Coupon Information</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Code</label>
                        <p class="text-2xl font-bold text-[#CE9704]">{{ $coupon->code }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Discount</label>
                        <p class="text-xl font-semibold text-gray-900">
                            @if($coupon->discount_type === 'percentage')
                                {{ $coupon->discount_value }}%
                            @else
                                ${{ number_format($coupon->discount_value, 2) }}
                            @endif
                            <span class="text-sm text-gray-500 ml-2">({{ $coupon->discount_type === 'percentage' ? 'Percentage' : 'Fixed Amount' }})</span>
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                        <span class="px-3 py-1 text-sm rounded-full {{ $coupon->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $coupon->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    @if($coupon->max_uses)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Max Uses</label>
                        <p class="text-gray-900">{{ $coupon->max_uses }}</p>
                    </div>
                    @endif

                    @if($coupon->min_order_total)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Min Order Total</label>
                        <p class="text-gray-900">${{ number_format($coupon->min_order_total, 2) }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Validity Period -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Validity Period</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Starts At</label>
                        <p class="text-gray-900">{{ $coupon->starts_at ? $coupon->starts_at->format('M d, Y H:i') : 'No start date' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Expires At</label>
                        <p class="text-gray-900">{{ $coupon->expires_at ? $coupon->expires_at->format('M d, Y H:i') : 'No expiration' }}</p>
                    </div>

                    @if($coupon->starts_at && $coupon->expires_at)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Duration</label>
                        <p class="text-gray-900">{{ $coupon->starts_at->diffForHumans($coupon->expires_at) }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </main>
</div>


<!-- Edit Coupon Modal -->
<div id="edit-coupon-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none; pointer-events: none;">
    <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">
        <div class="p-6 border-b border-gray-200 sticky top-0 bg-white z-10 flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800">Edit Coupon</h2>
            <button onclick="closeEditCouponModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST" id="edit-coupon-form" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Code *</label>
                <input type="text" name="code" id="edit-coupon-code" value="{{ old('code', $coupon->code) }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704]">
                @error('code')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Discount Type *</label>
                    <select name="discount_type" id="edit-coupon-discount-type" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704]">
                        <option value="percentage" {{ old('discount_type', $coupon->discount_type) === 'percentage' ? 'selected' : '' }}>Percentage</option>
                        <option value="fixed" {{ old('discount_type', $coupon->discount_type) === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Discount Value *</label>
                    <input type="number" name="discount_value" id="edit-coupon-discount-value" step="0.01" value="{{ old('discount_value', $coupon->discount_value) }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704]">
                    @error('discount_value')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Max Uses</label>
                    <input type="number" name="max_uses" id="edit-coupon-max-uses" value="{{ old('max_uses', $coupon->max_uses) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Min Order Total</label>
                    <input type="number" name="min_order_total" id="edit-coupon-min-order" step="0.01" value="{{ old('min_order_total', $coupon->min_order_total) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704]">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Starts At</label>
                    <input type="datetime-local" name="starts_at" id="edit-coupon-starts-at" value="{{ old('starts_at', $coupon->starts_at?->format('Y-m-d\TH:i')) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Expires At</label>
                    <input type="datetime-local" name="expires_at" id="edit-coupon-expires-at" value="{{ old('expires_at', $coupon->expires_at?->format('Y-m-d\TH:i')) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704]">
                </div>
            </div>

            <div>
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" id="edit-coupon-is-active" value="1" {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}
                        class="w-4 h-4 text-[#CE9704] border-gray-300 rounded focus:ring-[#CE9704]">
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                </label>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" class="bg-[#CE9704] text-white px-6 py-2 rounded-lg hover:bg-[#B8860B] transition-colors">
                    Update Coupon
                </button>
                <button type="button" onclick="closeEditCouponModal()" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Coupon Confirmation Modal -->
<div id="delete-coupon-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none; pointer-events: none;">
    <div class="bg-white rounded-lg max-w-md w-full shadow-2xl">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">Delete Coupon</h2>
            <button onclick="closeDeleteCouponModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <div class="p-6">
            <p class="text-gray-700 mb-4">Are you sure you want to delete the coupon <strong>{{ $coupon->code }}</strong>?</p>
            <p class="text-sm text-gray-500 mb-6">This action cannot be undone.</p>
            
            <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="flex gap-3">
                @csrf
                @method('DELETE')
                <button type="submit" class="flex-1 bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-colors">
                    Delete
                </button>
                <button type="button" onclick="closeDeleteCouponModal()" class="flex-1 bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function openEditCouponModal() {
    const modal = document.getElementById('edit-coupon-modal');
    if (modal) {
        modal.style.display = 'flex';
        modal.style.pointerEvents = 'auto';
    }
}

function closeEditCouponModal() {
    const modal = document.getElementById('edit-coupon-modal');
    if (modal) {
        modal.style.display = 'none';
        modal.style.pointerEvents = 'none';
    }
}

function openDeleteCouponModal() {
    const modal = document.getElementById('delete-coupon-modal');
    if (modal) {
        modal.style.display = 'flex';
        modal.style.pointerEvents = 'auto';
    }
}

function closeDeleteCouponModal() {
    const modal = document.getElementById('delete-coupon-modal');
    if (modal) {
        modal.style.display = 'none';
        modal.style.pointerEvents = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const editModal = document.getElementById('edit-coupon-modal');
    if (editModal) {
        editModal.addEventListener('click', function(e) {
            if (e.target === editModal) {
                closeEditCouponModal();
            }
        });
    }

    const deleteModal = document.getElementById('delete-coupon-modal');
    if (deleteModal) {
        deleteModal.addEventListener('click', function(e) {
            if (e.target === deleteModal) {
                closeDeleteCouponModal();
            }
        });
    }
});
</script>
@endsection

