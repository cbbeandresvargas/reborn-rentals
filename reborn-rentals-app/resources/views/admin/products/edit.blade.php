@extends('layouts.admin')

@section('title', 'Edit Product - Admin Panel')

@section('content')
<div class="ml-0 md:ml-72">
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
        <div class="px-6 py-4">
            <h1 class="text-2xl font-bold text-gray-800">Edit Product</h1>
        </div>
    </header>
    
    <main class="p-6">
        <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
            <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704]">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704]">{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Price *</label>
                        <input type="number" name="price" step="0.01" value="{{ old('price', $product->price) }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704]">
                        @error('price')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stock *</label>
                        <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" min="0" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704]">
                        <p class="text-xs text-gray-500 mt-1">Total available units for rent</p>
                        @error('stock')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select name="category_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704]">
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
                    <input type="number" name="odoo_product_id" value="{{ old('odoo_product_id', $product->odoo_product_id) }}" min="1"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704]"
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
                    <img src="{{ asset($product->image_url) }}" alt="{{ $product->name }}" class="w-32 h-32 object-contain border border-gray-300 rounded">
                </div>
                @endif

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Image</label>
                    <input type="file" name="image" accept="image/*"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704]">
                </div>

                <div class="mb-6 space-y-3">
                    <label class="flex items-center">
                        <input type="checkbox" name="active" value="1" {{ old('active', $product->active) ? 'checked' : '' }}
                            class="w-4 h-4 text-[#CE9704] border-gray-300 rounded focus:ring-[#CE9704]">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="hidden" value="1" {{ old('hidden', $product->hidden) ? 'checked' : '' }}
                            class="w-4 h-4 text-orange-500 border-gray-300 rounded focus:ring-orange-500">
                        <span class="ml-2 text-sm text-gray-700">Hide from customers (product will not be visible to buyers)</span>
                    </label>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="bg-[#CE9704] text-white px-6 py-2 rounded-lg hover:bg-[#B8860B] transition-colors">
                        Update Product
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>
</div>

@endsection

