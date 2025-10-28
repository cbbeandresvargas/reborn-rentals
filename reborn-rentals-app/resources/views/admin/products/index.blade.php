@extends('layouts.app')

@section('title', 'Products - Admin Panel')

@section('content')
<div class="ml-0 md:ml-64">
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
        <div class="px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Products</h1>
            <a href="{{ route('admin.products.create') }}" class="bg-[#CE9704] text-white px-4 py-2 rounded-lg hover:bg-[#B8860B] transition-colors">
                + Add Product
            </a>
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
                    <tr>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <img src="{{ $product->image_url ? asset('storage/' . $product->image_url) : asset('Product1.png') }}" alt="{{ $product->name }}" class="w-12 h-12 object-contain mr-3">
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
                        <td class="px-6 py-4 text-sm font-medium">
                            <a href="{{ route('admin.products.edit', $product) }}" class="text-[#CE9704] hover:text-[#B8860B] mr-3">Edit</a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este producto?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                            </form>
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
@endsection

