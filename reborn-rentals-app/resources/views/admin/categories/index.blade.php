@extends('layouts.app')

@section('title', 'Categories - Admin Panel')

@section('content')
<div class="ml-0 md:ml-64">
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
        <div class="px-6 py-4">
            <h1 class="text-2xl font-bold text-gray-800">Categories</h1>
        </div>
    </header>
    
    <main class="p-6">
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Formulario -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold mb-4">Add Category</h2>
                    <form action="{{ route('admin.categories.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704]">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704]">{{ old('description') }}</textarea>
                        </div>
                        <button type="submit" class="w-full bg-[#CE9704] text-white px-4 py-2 rounded-lg hover:bg-[#B8860B] transition-colors">
                            Create Category
                        </button>
                    </form>
                </div>
            </div>

            <!-- Lista -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Products</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($categories as $category)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $category->name }}</div>
                                    @if($category->description)
                                    <div class="text-sm text-gray-500">{{ Str::limit($category->description, 50) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $category->products_count ?? 0 }}</td>
                                <td class="px-6 py-4 text-sm font-medium">
                                    <button onclick="editCategory({{ $category->id }}, '{{ $category->name }}', '{{ addslashes($category->description ?? '') }}')" 
                                        class="text-[#CE9704] hover:text-[#B8860B] mr-3">Edit</button>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar esta categoría?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-gray-500">No categories found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4">
            {{ $categories->links() }}
        </div>
    </main>
</div>

<!-- Modal para editar -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full">
        <h2 class="text-xl font-bold mb-4">Edit Category</h2>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                <input type="text" name="name" id="editName" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704]">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" id="editDescription" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704]"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="bg-[#CE9704] text-white px-4 py-2 rounded-lg hover:bg-[#B8860B] transition-colors">
                    Update
                </button>
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" 
                    class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function editCategory(id, name, description) {
    document.getElementById('editForm').action = '/admin/categories/' + id;
    document.getElementById('editName').value = name;
    document.getElementById('editDescription').value = description;
    document.getElementById('editModal').classList.remove('hidden');
}
</script>

@include('admin.sidebar')
@endsection

