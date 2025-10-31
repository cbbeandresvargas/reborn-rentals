@extends('layouts.app')

@section('title', 'Products - Admin Panel')

@section('content')
<div class="ml-0 md:ml-64">
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
        <div class="px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Products</h1>
            <button onclick="openCreateProductModal()" class="bg-[#CE9704] text-white px-4 py-2 rounded-lg hover:bg-[#B8860B] transition-colors">
                + Add Product
            </button>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-50 transition-colors cursor-pointer" onclick="window.location.href='{{ route('admin.products.show', $product) }}'">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <img src="{{ $product->image_url ? asset($product->image_url) : asset('Product1.png') }}" alt="{{ $product->name }}" class="w-12 h-12 object-contain mr-3">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $product->name }}</div>
                                    <div class="text-sm text-gray-500">ID: {{ $product->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $product->category->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">${{ number_format($product->price, 2) }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $product->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $product->active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-500">
                            Click to view details →
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No products found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </main>
</div>

@include('admin.sidebar')

<!-- Create Product Modal -->
<div id="create-product-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none; pointer-events: none;">
    <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">
        <!-- Header -->
        <div class="p-6 border-b border-gray-200 sticky top-0 bg-white z-10">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-800">Create Product</h2>
                <button onclick="closeCreateProductModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Form -->
        <form action="{{ route('admin.products.store') }}" method="POST" id="create-product-form" enctype="multipart/form-data" class="p-6">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                <input type="text" name="name" id="product-name" value="{{ old('name') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704] {{ $errors->has('name') ? 'border-red-500' : '' }}">
                <div id="name-error" class="text-red-500 text-sm mt-1 {{ $errors->has('name') ? '' : 'hidden' }}">
                    @if($errors->has('name'))
                        {{ $errors->first('name') }}
                    @endif
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" id="product-description" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704]">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Price *</label>
                    <input type="number" name="price" id="product-price" step="0.01" value="{{ old('price') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704] {{ $errors->has('price') ? 'border-red-500' : '' }}">
                    <div id="price-error" class="text-red-500 text-sm mt-1 {{ $errors->has('price') ? '' : 'hidden' }}">
                        @if($errors->has('price'))
                            {{ $errors->first('price') }}
                        @endif
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select name="category_id" id="product-category"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704]">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Image</label>
                <input type="file" name="image" id="product-image" accept="image/*"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704]">
                <div id="image-error" class="text-red-500 text-sm mt-1 {{ $errors->has('image') ? '' : 'hidden' }}">
                    @if($errors->has('image'))
                        {{ $errors->first('image') }}
                    @endif
                </div>
                <!-- Image Preview -->
                <div id="image-preview-container" class="mt-4 hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Preview:</label>
                    <div class="relative inline-block">
                        <img id="image-preview" src="" alt="Preview" class="max-w-xs max-h-64 rounded-lg border border-gray-300 shadow-sm object-contain">
                        <button type="button" onclick="clearImagePreview()" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="active" id="product-active" value="1" {{ old('active') ? 'checked' : 'checked' }}
                        class="w-4 h-4 text-[#CE9704] border-gray-300 rounded focus:ring-[#CE9704]">
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                </label>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-[#CE9704] text-white px-6 py-2 rounded-lg hover:bg-[#B8860B] transition-colors">
                    Create Product
                </button>
                <button type="button" onclick="closeCreateProductModal()" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openCreateProductModal() {
    const modal = document.getElementById('create-product-modal');
    if (modal) {
        modal.style.display = 'flex';
        modal.style.pointerEvents = 'auto';
        // Reset form
        const form = document.getElementById('create-product-form');
        if (form) {
            form.reset();
        }
        // Clear image preview
        clearImagePreview();
        // Clear error messages
        document.querySelectorAll('[id$="-error"]').forEach(el => {
            el.classList.add('hidden');
            el.textContent = '';
        });
        // Remove error borders
        const formInputs = document.querySelectorAll('#create-product-form input, #create-product-form select, #create-product-form textarea');
        formInputs.forEach(el => {
            el.classList.remove('border-red-500');
        });
    }
}

function closeCreateProductModal() {
    const modal = document.getElementById('create-product-modal');
    if (modal) {
        modal.style.display = 'none';
        modal.style.pointerEvents = 'none';
        // Reset form
        const form = document.getElementById('create-product-form');
        if (form) {
            form.reset();
        }
        // Clear image preview
        clearImagePreview();
        // Clear error messages
        document.querySelectorAll('[id$="-error"]').forEach(el => {
            el.classList.add('hidden');
            el.textContent = '';
        });
        // Remove error borders
        const formInputs = document.querySelectorAll('#create-product-form input, #create-product-form select, #create-product-form textarea');
        formInputs.forEach(el => {
            el.classList.remove('border-red-500');
        });
    }
}

function clearImagePreview() {
    const previewContainer = document.getElementById('image-preview-container');
    const preview = document.getElementById('image-preview');
    const fileInput = document.getElementById('product-image');
    
    if (previewContainer) {
        previewContainer.classList.add('hidden');
    }
    if (preview) {
        preview.src = '';
    }
    if (fileInput) {
        fileInput.value = '';
    }
}

// Close modal when clicking outside and handle image preview
document.addEventListener('DOMContentLoaded', function() {
    // Create modal
    const createModal = document.getElementById('create-product-modal');
    if (createModal) {
        createModal.addEventListener('click', function(e) {
            if (e.target === createModal) {
                closeCreateProductModal();
            }
        });
    }

    // Handle create image preview
    const imageInput = document.getElementById('product-image');
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file type
                if (!file.type.startsWith('image/')) {
                    alert('Please select a valid image file.');
                    clearImagePreview();
                    return;
                }

                // Validate file size (2MB max)
                if (file.size > 2 * 1024 * 1024) {
                    alert('The image is too large. Please select an image smaller than 2MB.');
                    clearImagePreview();
                    return;
                }

                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('image-preview');
                    const previewContainer = document.getElementById('image-preview-container');
                    
                    if (preview) {
                        preview.src = e.target.result;
                    }
                    if (previewContainer) {
                        previewContainer.classList.remove('hidden');
                    }
                };
                reader.readAsDataURL(file);
            } else {
                clearImagePreview();
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
                    alert('Por favor, selecciona un archivo de imagen válido.');
                    clearEditImagePreview();
                    return;
                }

                // Validate file size (2MB max)
                if (file.size > 2 * 1024 * 1024) {
                    alert('La imagen es demasiado grande. Por favor, selecciona una imagen menor a 2MB.');
                    clearEditImagePreview();
                    return;
                }

                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('edit-image-preview');
                    const previewContainer = document.getElementById('edit-image-preview-container');
                    const currentImageContainer = document.getElementById('edit-current-image-container');
                    
                    if (preview) {
                        preview.src = e.target.result;
                    }
                    if (previewContainer) {
                        previewContainer.classList.remove('hidden');
                    }
                    // Hide current image when new preview is shown
                    if (currentImageContainer) {
                        currentImageContainer.classList.add('hidden');
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
