@extends('layouts.admin')

@section('title', 'Products - Admin Panel')

@section('content')
<div class="ml-0 md:ml-72 bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-gradient-to-r from-white via-gray-50 to-white shadow-lg border-b-2 border-[#CE9704]/20 sticky top-0 z-40 backdrop-blur-sm">
        <div class="px-4 sm:px-6 lg:px-8 py-4 sm:py-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-[#CE9704] to-[#B8860B] bg-clip-text text-transparent">Product Management</h1>
                    <p class="text-sm text-gray-600 mt-1">Manage your product catalog</p>
                </div>
                <button onclick="openCreateProductModal()" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-[#CE9704] to-[#B8860B] text-white rounded-lg hover:shadow-lg transition-all duration-200 font-semibold text-sm shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Product
                </button>
            </div>
        </div>
    </header>
    
    <main class="p-4 sm:p-6 lg:p-8">
        @if(session('success'))
        <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-lg shadow-sm">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
        @endif

        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 mb-6 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-white px-4 sm:px-6 lg:px-8 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#CE9704]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filter Products
                    </h2>
                    @if(request('category_id'))
                    <a href="{{ route('admin.products.index') }}" class="text-sm text-red-600 hover:text-red-700 font-medium flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Clear Filter
                    </a>
                    @endif
                </div>
                <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Filter by Category</label>
                        <select name="category_id" 
                                onchange="this.form.submit()"
                                class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704] transition-all bg-white font-medium">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <!-- Products Table -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-white px-4 sm:px-6 lg:px-8 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-800">Products Catalog</h2>
                    <div class="flex items-center gap-3">
                        @if(request('category_id'))
                        <span class="text-xs text-gray-600 bg-[#CE9704]/10 text-[#CE9704] px-3 py-1 rounded-full font-semibold border border-[#CE9704]/20">
                            Filtered: {{ $categories->firstWhere('id', request('category_id'))->name ?? 'Unknown' }}
                        </span>
                        @endif
                    <span class="text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full font-medium">
                        {{ $products->total() }} {{ $products->total() === 1 ? 'Product' : 'Products' }}
                    </span>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-4 sm:px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b-2 border-[#CE9704]/30">Product</th>
                            <th class="px-4 sm:px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b-2 border-[#CE9704]/30 hidden md:table-cell">Category</th>
                            <th class="px-4 sm:px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b-2 border-[#CE9704]/30">Price</th>
                            <th class="px-4 sm:px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b-2 border-[#CE9704]/30">Status</th>
                            <th class="px-4 sm:px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b-2 border-[#CE9704]/30">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($products as $product)
                        <tr class="hover:bg-gradient-to-r hover:from-[#CE9704]/5 hover:to-transparent transition-all duration-150 group">
                            <td class="px-4 sm:px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-14 w-14 sm:h-16 sm:w-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg p-2 border border-gray-200 shadow-sm group-hover:shadow-md transition-shadow">
                                        <img src="{{ $product->image_url ? asset($product->image_url) : asset('Product1.png') }}" alt="{{ $product->name }}" class="w-full h-full object-contain">
                                    </div>
                                    <div class="ml-4">
                                        <div class="font-bold text-gray-900 text-sm sm:text-base">{{ $product->name }}</div>
                                        <div class="text-xs text-gray-500 mt-1">ID: #{{ $product->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 sm:px-6 py-4 text-sm text-gray-700 font-medium hidden md:table-cell">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-gray-100 text-gray-700 text-xs font-semibold">
                                    {{ $product->category->name ?? 'Uncategorized' }}
                                </span>
                            </td>
                            <td class="px-4 sm:px-6 py-4">
                                <span class="text-base sm:text-lg font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                            </td>
                            <td class="px-4 sm:px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold {{ $product->active ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                                        <span class="w-2 h-2 mr-1.5 rounded-full {{ $product->active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                        {{ $product->active ? 'Active' : 'Inactive' }}
                                    </span>
                                    @if($product->hidden)
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-800 border border-orange-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                        </svg>
                                        Hidden
                                    </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 sm:px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.products.show', $product) }}" class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-[#CE9704] to-[#B8860B] text-white rounded-lg hover:shadow-lg transition-all duration-200 text-xs sm:text-sm font-semibold">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                        View
                                </a>
                                    <button type="button" 
                                            onclick="openDeleteProductModal('{{ $product->id }}', '{{ addslashes($product->name) }}')"
                                            class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-xs sm:text-sm font-semibold">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 sm:px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </div>
                                    <p class="text-gray-600 font-semibold text-lg mb-1">No products found</p>
                                    <p class="text-sm text-gray-500">Get started by creating your first product</p>
                                    <button onclick="openCreateProductModal()" class="mt-4 inline-flex items-center px-4 py-2 bg-gradient-to-r from-[#CE9704] to-[#B8860B] text-white rounded-lg hover:shadow-lg transition-all duration-200 text-sm font-semibold">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Create First Product
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
        @if($products->hasPages())
        <div class="mt-6 flex justify-center">
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-2">
                {{ $products->links() }}
            </div>
        </div>
        @endif
    </main>
</div>


<!-- Create Product Modal -->
<div id="create-product-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" style="display: none; pointer-events: none;">
    <div class="bg-white rounded-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border-2 border-[#CE9704]/20">
        <!-- Header -->
        <div class="bg-gradient-to-r from-[#CE9704] to-[#B8860B] p-6 sticky top-0 z-10">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-white">Create New Product</h2>
                    <p class="text-sm text-white/90 mt-1">Add a new product to your catalog</p>
                </div>
                <button onclick="closeCreateProductModal()" class="text-white hover:bg-white/20 rounded-full p-2 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Form -->
        <form action="{{ route('admin.products.store') }}" method="POST" id="create-product-form" enctype="multipart/form-data" class="p-6 sm:p-8">
            @csrf

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    Product Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="product-name" value="{{ old('name') }}" required
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704] transition-all {{ $errors->has('name') ? 'border-red-500' : '' }}"
                    placeholder="Enter product name">
                <div id="name-error" class="text-red-600 text-sm mt-1 font-medium {{ $errors->has('name') ? '' : 'hidden' }}">
                    @if($errors->has('name'))
                        {{ $errors->first('name') }}
                    @endif
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                <textarea name="description" id="product-description" rows="4"
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704] transition-all resize-none"
                    placeholder="Enter product description">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Price <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold">$</span>
                        <input type="number" name="price" id="product-price" step="0.01" value="{{ old('price') }}" required
                            class="w-full pl-8 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704] transition-all {{ $errors->has('price') ? 'border-red-500' : '' }}"
                            placeholder="0.00">
                    </div>
                    <div id="price-error" class="text-red-600 text-sm mt-1 font-medium {{ $errors->has('price') ? '' : 'hidden' }}">
                        @if($errors->has('price'))
                            {{ $errors->first('price') }}
                        @endif
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Stock <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="stock" id="product-stock" value="{{ old('stock', 0) }}" min="0" required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704] transition-all {{ $errors->has('stock') ? 'border-red-500' : '' }}"
                        placeholder="0">
                    <p class="text-xs text-gray-500 mt-1">Total available units for rent</p>
                    <div id="stock-error" class="text-red-600 text-sm mt-1 font-medium {{ $errors->has('stock') ? '' : 'hidden' }}">
                        @if($errors->has('stock'))
                            {{ $errors->first('stock') }}
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Category</label>
                    <select name="category_id" id="product-category"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704] transition-all bg-white">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Odoo Product ID
                        <span class="text-xs text-gray-500 font-normal">(Required for Odoo integration)</span>
                    </label>
                    <input type="number" name="odoo_product_id" id="product-odoo-id" value="{{ old('odoo_product_id') }}" min="1"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704] transition-all {{ $errors->has('odoo_product_id') ? 'border-red-500' : '' }}"
                        placeholder="Enter Odoo product ID">
                    <p class="text-xs text-gray-500 mt-1">The ID of the corresponding product in Odoo</p>
                    <div id="odoo-product-id-error" class="text-red-600 text-sm mt-1 font-medium {{ $errors->has('odoo_product_id') ? '' : 'hidden' }}">
                        @if($errors->has('odoo_product_id'))
                            {{ $errors->first('odoo_product_id') }}
                        @endif
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-2">Product Image</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-[#CE9704] transition-colors">
                    <input type="file" name="image" id="product-image" accept="image/*"
                        class="hidden">
                    <label for="product-image" class="cursor-pointer">
                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-sm text-gray-600 font-medium">Click to upload image</p>
                        <p class="text-xs text-gray-500 mt-1">PNG, JPG up to 2MB</p>
                    </label>
                </div>
                <div id="image-error" class="text-red-600 text-sm mt-1 font-medium {{ $errors->has('image') ? '' : 'hidden' }}">
                    @if($errors->has('image'))
                        {{ $errors->first('image') }}
                    @endif
                </div>
                <!-- Image Preview -->
                <div id="image-preview-container" class="mt-4 hidden">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Preview:</label>
                    <div class="relative inline-block border-2 border-[#CE9704] rounded-lg p-2 bg-gray-50">
                        <img id="image-preview" src="" alt="Preview" class="max-w-xs max-h-64 rounded-lg object-contain">
                        <button type="button" onclick="clearImagePreview()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1.5 hover:bg-red-600 transition-colors shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mb-8 space-y-3">
                <label class="flex items-center p-4 bg-gray-50 rounded-lg border-2 border-gray-200 hover:border-[#CE9704] transition-colors cursor-pointer">
                    <input type="checkbox" name="active" id="product-active" value="1" {{ old('active') ? 'checked' : 'checked' }}
                        class="w-5 h-5 text-[#CE9704] border-gray-300 rounded focus:ring-[#CE9704]">
                    <span class="ml-3 text-sm font-semibold text-gray-700">Product is active and visible to customers</span>
                </label>
                <label class="flex items-center p-4 bg-orange-50 rounded-lg border-2 border-orange-200 hover:border-orange-400 transition-colors cursor-pointer">
                    <input type="checkbox" name="hidden" id="product-hidden" value="1" {{ old('hidden') ? 'checked' : '' }}
                        class="w-5 h-5 text-orange-500 border-gray-300 rounded focus:ring-orange-500">
                    <span class="ml-3 text-sm font-semibold text-gray-700">Hide from customers (product will not be visible to buyers)</span>
                </label>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end pt-6 border-t border-gray-200">
                <button type="button" onclick="closeCreateProductModal()" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-semibold">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-[#CE9704] to-[#B8860B] text-white rounded-lg hover:shadow-lg transition-all duration-200 font-bold">
                    Create Product
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
        // Reset stock to default
        const stockInput = document.getElementById('product-stock');
        if (stockInput) {
            stockInput.value = '0';
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
    const uploadArea = document.querySelector('label[for="product-image"]')?.closest('.border-dashed');
    
    if (previewContainer) {
        previewContainer.classList.add('hidden');
    }
    if (preview) {
        preview.src = '';
    }
    if (fileInput) {
        fileInput.value = '';
    }
    if (uploadArea) {
        uploadArea.classList.remove('hidden');
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
    const imageLabel = document.querySelector('label[for="product-image"]');
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
                    // Hide upload area when image is selected
                    if (imageLabel && imageLabel.closest('.border-dashed')) {
                        imageLabel.closest('.border-dashed').classList.add('hidden');
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

<!-- Modal de Confirmación de Eliminación -->
<div id="delete-product-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm hidden">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all">
        <div class="p-6">
            <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Delete Product</h3>
            <p class="text-gray-600 text-center mb-6" id="delete-product-message"></p>
            <form id="delete-product-form" method="POST" class="space-y-3">
                @csrf
                @method('DELETE')
                <div class="flex gap-3">
                    <button type="button" onclick="closeDeleteProductModal()" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2.5 rounded-lg hover:bg-gray-300 transition-all font-semibold">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 bg-red-600 text-white px-4 py-2.5 rounded-lg hover:bg-red-700 transition-all font-semibold">
                        Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Funciones para modal de eliminación de producto
function openDeleteProductModal(productId, productName) {
    const modal = document.getElementById('delete-product-modal');
    if (modal) {
        document.getElementById('delete-product-message').textContent = 
            `Are you sure you want to delete the product "${productName}"?\n\nThis action cannot be undone and will permanently remove the product from your catalog.`;
        const form = document.getElementById('delete-product-form');
        form.action = `/admin/products/${productId}`;
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
    }
}

function closeDeleteProductModal() {
    const modal = document.getElementById('delete-product-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
    }
}

// Cerrar modal al hacer click fuera
document.addEventListener('DOMContentLoaded', function() {
    const deleteProductModal = document.getElementById('delete-product-modal');
    
    if (deleteProductModal) {
        deleteProductModal.addEventListener('click', function(e) {
            if (e.target === deleteProductModal) {
                closeDeleteProductModal();
            }
        });
    }
    
    // Cerrar con tecla ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteProductModal();
        }
    });
    
    // Abrir modal automáticamente si hay errores de validación
    @if($errors->any() && old('_token'))
        openCreateProductModal();
    @endif
});
</script>
@endsection
