@extends('layouts.admin')

@section('title', $category->name . ' - Category Details')

@section('content')
<div class="ml-0 md:ml-72">
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40 px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.categories.index') }}" class="text-[#CE9704] hover:text-[#B8860B] transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Category Details</h1>
        </div>
        <div class="flex gap-3">
            <button onclick="openEditCategoryModal()" class="bg-[#CE9704] text-white px-4 py-2 rounded-lg hover:bg-[#B8860B] transition-colors">
                Edit Category
            </button>
            @if($category->products->count() === 0)
            <button onclick="openDeleteCategoryModal()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                Delete Category
            </button>
            @endif
        </div>
    </header>
    
    <main class="p-6">
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
        @endif

        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Category Information -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold mb-4">Category Information</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Name</label>
                            <p class="text-2xl font-bold text-[#CE9704]">{{ $category->name }}</p>
                        </div>

                        @if($category->description)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Description</label>
                            <p class="text-gray-900">{{ $category->description }}</p>
                        </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Total Products</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $category->products->count() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Products List -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold mb-4">Products in this Category</h2>
                    @if($category->products->count() > 0)
                    <div class="space-y-3">
                        @foreach($category->products as $product)
                        <a href="{{ route('admin.products.show', $product) }}" class="flex items-center gap-4 p-3 rounded-lg hover:bg-gray-50 transition-colors {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                            <img src="{{ $product->image_url ? asset($product->image_url) : asset('Product1.png') }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-16 h-16 object-contain rounded">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">{{ $product->name }}</h3>
                                <p class="text-sm text-gray-500">{{ Str::limit($product->description, 60) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-lg">${{ number_format($product->price, 2) }}</p>
                                <span class="px-2 py-1 text-xs rounded-full {{ $product->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $product->active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <p class="text-gray-500 text-center py-8">No products in this category yet.</p>
                    @endif
                </div>
            </div>

            <!-- Statistics -->
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold mb-4">Statistics</h2>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Products:</span>
                            <span class="text-xl font-bold text-[#CE9704]">{{ $category->products->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Active Products:</span>
                            <span class="text-xl font-bold text-[#CE9704]">{{ $category->products->where('active', true)->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Inactive Products:</span>
                            <span class="text-xl font-bold text-gray-600">{{ $category->products->where('active', false)->count() }}</span>
                        </div>
                    </div>
                </div>

                @if($category->products->count() > 0)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-sm text-yellow-800">
                        <strong>Note:</strong> This category cannot be deleted because it has {{ $category->products->count() }} associated product(s).
                    </p>
                </div>
                @endif
            </div>
        </div>
    </main>
</div>


<!-- Edit Category Modal -->
<div id="edit-category-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none; pointer-events: none;">
    <div class="bg-white rounded-lg max-w-md w-full shadow-2xl">
        <div class="p-6 border-b border-gray-200 sticky top-0 bg-white z-10 flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">Edit Category</h2>
            <button onclick="closeEditCategoryModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" id="edit-category-form" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                <input type="text" name="name" id="edit-category-name" value="{{ old('name', $category->name) }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-transparent">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" id="edit-category-description" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-transparent">{{ old('description', $category->description) }}</textarea>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" class="bg-[#CE9704] text-white px-6 py-2 rounded-lg hover:bg-[#B8860B] transition-colors">
                    Update Category
                </button>
                <button type="button" onclick="closeEditCategoryModal()" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Category Confirmation Modal -->
@if($category->products->count() === 0)
<div id="delete-category-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none; pointer-events: none;">
    <div class="bg-white rounded-lg max-w-md w-full shadow-2xl">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">Delete Category</h2>
            <button onclick="closeDeleteCategoryModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <div class="p-6">
            <p class="text-gray-700 mb-4">Are you sure you want to delete the category <strong>{{ $category->name }}</strong>?</p>
            <p class="text-sm text-gray-500 mb-6">This action cannot be undone.</p>
            
            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="flex gap-3">
                @csrf
                @method('DELETE')
                <button type="submit" class="flex-1 bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-colors">
                    Delete
                </button>
                <button type="button" onclick="closeDeleteCategoryModal()" class="flex-1 bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
            </form>
        </div>
    </div>
</div>
@endif

<script>
function openEditCategoryModal() {
    const modal = document.getElementById('edit-category-modal');
    if (modal) {
        modal.style.display = 'flex';
        modal.style.pointerEvents = 'auto';
    }
}

function closeEditCategoryModal() {
    const modal = document.getElementById('edit-category-modal');
    if (modal) {
        modal.style.display = 'none';
        modal.style.pointerEvents = 'none';
    }
}

function openDeleteCategoryModal() {
    const modal = document.getElementById('delete-category-modal');
    if (modal) {
        modal.style.display = 'flex';
        modal.style.pointerEvents = 'auto';
    }
}

function closeDeleteCategoryModal() {
    const modal = document.getElementById('delete-category-modal');
    if (modal) {
        modal.style.display = 'none';
        modal.style.pointerEvents = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const editModal = document.getElementById('edit-category-modal');
    if (editModal) {
        editModal.addEventListener('click', function(e) {
            if (e.target === editModal) {
                closeEditCategoryModal();
            }
        });
    }

    const deleteModal = document.getElementById('delete-category-modal');
    if (deleteModal) {
        deleteModal.addEventListener('click', function(e) {
            if (e.target === deleteModal) {
                closeDeleteCategoryModal();
            }
        });
    }
});
</script>
@endsection

