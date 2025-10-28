@extends('layouts.app')

@section('title', 'Create Product - Admin Panel')

@section('content')
<div class="ml-0 md:ml-64">
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
        <div class="px-6 py-4">
            <h1 class="text-2xl font-bold text-gray-800">Create Product</h1>
        </div>
    </header>
    
    <main class="p-6">
        <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-transparent">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704]">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Price *</label>
                        <input type="number" name="price" step="0.01" value="{{ old('price') }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704]">
                        @error('price')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select name="category_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704]">
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
                    <input type="file" name="image" accept="image/*"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704]">
                    @error('image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="active" value="1" {{ old('active') ? 'checked' : '' }}
                            class="w-4 h-4 text-[#CE9704] border-gray-300 rounded focus:ring-[#CE9704]">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="bg-[#CE9704] text-white px-6 py-2 rounded-lg hover:bg-[#B8860B] transition-colors">
                        Create Product
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>
</div>

@include('admin.sidebar')
@endsection

