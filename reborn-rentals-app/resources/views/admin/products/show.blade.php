@extends('layouts.admin')

@section('title', $product->name . ' - Product Details')

@section('content')
<div class="ml-0 md:ml-72">
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
        <div class="px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.products.index') }}" class="text-[#CE9704] hover:text-[#B8860B] transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-800">Product Details</h1>
            </div>
            <div class="flex gap-3">
                <button onclick="openEditProductModal()" class="bg-[#CE9704] text-white px-4 py-2 rounded-lg hover:bg-[#B8860B] transition-colors">
                    Edit Product
                </button>
                <button onclick="openDeleteProductModal()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                    Delete Product
                </button>
            </div>
        </div>
    </header>
    
    <main class="p-6">
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Product Image -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Product Image</h2>
                <div class="flex justify-center">
                    <img src="{{ $product->image_url ? asset($product->image_url) : asset('Product1.png') }}" 
                         alt="{{ $product->name }}" 
                         class="max-w-full h-auto max-h-96 object-contain rounded-lg border border-gray-300" />
                </div>
            </div>

            <!-- Product Information -->
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold mb-4">Product Information</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Name</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $product->name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">ID</label>
                            <p class="text-gray-900">#{{ $product->id }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Price</label>
                            <p class="text-2xl font-bold text-[#CE9704]">${{ number_format($product->price, 2) }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Stock</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $product->stock ?? 0 }} units</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Category</label>
                            <span class="inline-block bg-gray-100 px-3 py-1 rounded-full text-sm">
                                {{ $product->category->name ?? 'N/A' }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Odoo Product ID</label>
                            @if($product->odoo_product_id)
                                <p class="text-gray-900">
                                    <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">
                                        {{ $product->odoo_product_id }}
                                    </span>
                                </p>
                                <p class="text-xs text-gray-500 mt-1">Product is mapped to Odoo</p>
                            @else
                                <p class="text-gray-900">
                                    <span class="inline-block bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-semibold">
                                        Not Mapped
                                    </span>
                                </p>
                                <p class="text-xs text-red-500 mt-1">⚠️ This product needs to be mapped to Odoo before orders can be synced</p>
                            @endif
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                            <div class="flex flex-col gap-2">
                                <span class="px-3 py-1 text-sm rounded-full {{ $product->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $product->active ? 'Active' : 'Inactive' }}
                                </span>
                                @if($product->hidden)
                                <span class="px-3 py-1 text-sm rounded-full bg-orange-100 text-orange-800 inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                    </svg>
                                    Hidden from customers
                                </span>
                                @endif
                            </div>
                        </div>

                        @if($product->description)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Description</label>
                            <p class="text-gray-700">{{ $product->description }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                @if($product->orderItems->count() > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold mb-4">Order History</h2>
                    <p class="text-gray-600">This product has been ordered {{ $product->orderItems->count() }} time(s).</p>
                </div>
                @endif
            </div>
        </div>
    </main>
</div>


<!-- Edit Product Modal -->
<div id="edit-product-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none; pointer-events: none;">
    <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">
        <!-- Header -->
        <div class="p-6 border-b border-gray-200 sticky top-0 bg-white z-10">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-800">Edit Product</h2>
                <button onclick="closeEditProductModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Form -->
        <form action="{{ route('admin.products.update', $product) }}" method="POST" id="edit-product-form" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                <input type="text" name="name" id="edit-product-name" value="{{ old('name', $product->name) }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704]">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" id="edit-product-description" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704]">{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Price *</label>
                    <input type="number" name="price" id="edit-product-price" step="0.01" value="{{ old('price', $product->price) }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704]">
                    @error('price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stock *</label>
                    <input type="number" name="stock" id="edit-product-stock" value="{{ old('stock', $product->stock ?? 0) }}" min="0" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704]">
                    <p class="text-xs text-gray-500 mt-1">Total available units for rent</p>
                    @error('stock')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select name="category_id" id="edit-product-category"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704]">
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ (old('category_id', $product->category_id) == $category->id) ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Odoo Product ID
                    <span class="text-xs text-gray-500">(Required for Odoo integration)</span>
                </label>
                <input type="number" name="odoo_product_id" id="edit-product-odoo-product-id" value="{{ old('odoo_product_id', $product->odoo_product_id) }}" min="1"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704]"
                    placeholder="Enter Odoo product ID">
                <p class="text-xs text-gray-500 mt-1">
                    The ID of the corresponding product in Odoo. This is required to sync orders to Odoo.
                </p>
                @error('odoo_product_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            @if($product->image_url)
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Image</label>
                <div class="relative inline-block">
                    <img src="{{ asset($product->image_url) }}" alt="Current Image" class="max-w-xs max-h-64 rounded-lg border border-gray-300 shadow-sm object-contain">
                </div>
            </div>
            @endif

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">New Image</label>
                <input type="file" name="image" id="edit-product-image" accept="image/*"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704]">
                @error('image')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <!-- Image Preview -->
                <div id="edit-image-preview-container" class="mt-4 hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Preview:</label>
                    <div class="relative inline-block">
                        <img id="edit-image-preview" src="" alt="Preview" class="max-w-xs max-h-64 rounded-lg border border-gray-300 shadow-sm object-contain">
                        <button type="button" onclick="clearEditImagePreview()" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mb-6 space-y-3">
                <label class="flex items-center">
                    <input type="checkbox" name="active" id="edit-product-active" value="1" {{ old('active', $product->active) ? 'checked' : '' }}
                        class="w-4 h-4 text-[#CE9704] border-gray-300 rounded focus:ring-[#CE9704]">
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="hidden" id="edit-product-hidden" value="1" {{ old('hidden', $product->hidden) ? 'checked' : '' }}
                        class="w-4 h-4 text-orange-500 border-gray-300 rounded focus:ring-orange-500">
                    <span class="ml-2 text-sm text-gray-700">Hide from customers (product will not be visible to buyers)</span>
                </label>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-[#CE9704] text-white px-6 py-2 rounded-lg hover:bg-[#B8860B] transition-colors">
                    Update Product
                </button>
                <button type="button" onclick="closeEditProductModal()" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Product Confirmation Modal -->
<div id="delete-product-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none; pointer-events: none;">
    <div class="bg-white rounded-lg max-w-md w-full shadow-2xl">
        <!-- Header -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-800">Delete Product</h2>
                <button onclick="closeDeleteProductModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Content -->
        <div class="p-6">
            <p class="text-gray-700 mb-4">Are you sure you want to delete the product <strong>{{ $product->name }}</strong>?</p>
            <p class="text-sm text-gray-500 mb-6">This action cannot be undone.</p>
            
            <!-- Form -->
            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" id="delete-product-form" class="flex gap-3">
                @csrf
                @method('DELETE')
                <button type="submit" class="flex-1 bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-colors">
                    Delete
                </button>
                <button type="button" onclick="closeDeleteProductModal()" class="flex-1 bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function openEditProductModal() {
    const modal = document.getElementById('edit-product-modal');
    if (modal) {
        modal.style.display = 'flex';
        modal.style.pointerEvents = 'auto';
    }
}

function closeEditProductModal() {
    const modal = document.getElementById('edit-product-modal');
    if (modal) {
        modal.style.display = 'none';
        modal.style.pointerEvents = 'none';
        clearEditImagePreview();
    }
}

function clearEditImagePreview() {
    const previewContainer = document.getElementById('edit-image-preview-container');
    const preview = document.getElementById('edit-image-preview');
    const fileInput = document.getElementById('edit-product-image');
    
    if (previewContainer) previewContainer.classList.add('hidden');
    if (preview) preview.src = '';
    if (fileInput) fileInput.value = '';
}

function openDeleteProductModal() {
    const modal = document.getElementById('delete-product-modal');
    if (modal) {
        modal.style.display = 'flex';
        modal.style.pointerEvents = 'auto';
    }
}

function closeDeleteProductModal() {
    const modal = document.getElementById('delete-product-modal');
    if (modal) {
        modal.style.display = 'none';
        modal.style.pointerEvents = 'none';
    }
}

// Close modals when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    // Edit modal
    const editModal = document.getElementById('edit-product-modal');
    if (editModal) {
        editModal.addEventListener('click', function(e) {
            if (e.target === editModal) {
                closeEditProductModal();
            }
        });
    }

    // Delete modal
    const deleteModal = document.getElementById('delete-product-modal');
    if (deleteModal) {
        deleteModal.addEventListener('click', function(e) {
            if (e.target === deleteModal) {
                closeDeleteProductModal();
            }
        });
    }

    // Handle edit image preview
    const editImageInput = document.getElementById('edit-product-image');
    if (editImageInput) {
        editImageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file type
                if (!file.type.startsWith('image/')) {
                    alert('Please select a valid image file.');
                    clearEditImagePreview();
                    return;
                }

                // Validate file size (2MB max)
                if (file.size > 2 * 1024 * 1024) {
                    alert('The image is too large. Please select an image smaller than 2MB.');
                    clearEditImagePreview();
                    return;
                }

                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('edit-image-preview');
                    const previewContainer = document.getElementById('edit-image-preview-container');
                    
                    if (preview) {
                        preview.src = e.target.result;
                    }
                    if (previewContainer) {
                        previewContainer.classList.remove('hidden');
                    }
                };
                reader.readAsDataURL(file);
            } else {
                clearEditImagePreview();
            }
        });
    }
});
</script>
@endsection

