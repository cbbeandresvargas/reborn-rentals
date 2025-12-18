@extends('layouts.admin')

@section('title', 'Categories - Admin Panel')

@section('content')
<div class="ml-0 md:ml-72 bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-gradient-to-r from-white to-gray-50 shadow-lg border-b border-gray-200 sticky top-0 z-40 backdrop-blur-sm">
        <div class="px-4 sm:px-6 lg:px-8 py-4 sm:py-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-[#CE9704] rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">Categories</h1>
                        <p class="text-sm text-gray-500 mt-1">Manage product categories</p>
                    </div>
                </div>
                <div class="hidden sm:flex items-center gap-2 px-4 py-2 bg-white rounded-lg shadow-sm border border-gray-200">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    <span class="text-sm font-semibold text-gray-700">{{ $categories->total() ?? 0 }}</span>
                    <span class="text-xs text-gray-500">Total Categories</span>
                </div>
            </div>
        </div>
    </header>
    
    <main class="p-4 sm:p-6 lg:p-8">
        @if(session('success'))
        <div id="success-message" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm" style="display: flex;">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all">
                <div class="p-6">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full">
                        <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Success</h3>
                    <p class="text-gray-600 text-center mb-6">{{ session('success') }}</p>
                    <button onclick="closeSuccessModal()" class="w-full bg-gradient-to-r from-[#CE9704] to-[#B8860B] text-white px-4 py-2.5 rounded-lg hover:from-[#B8860B] hover:to-[#CE9704] transition-all font-semibold">
                        OK
                    </button>
                </div>
            </div>
        </div>
        @endif
        @if(session('error'))
        <div id="error-message" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm" style="display: flex;">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all">
                <div class="p-6">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full">
                        <svg class="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Error</h3>
                    <p class="text-gray-600 text-center mb-6">{{ session('error') }}</p>
                    <button onclick="closeErrorModal()" class="w-full bg-red-600 text-white px-4 py-2.5 rounded-lg hover:bg-red-700 transition-all font-semibold">
                        OK
                    </button>
                </div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Formulario de Creación -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#CE9704]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add New Category
                        </h2>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
                        @csrf
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Name <span class="text-red-500">*</span>
                                </label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704] transition-all outline-none">
                            @error('name')
                                    <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                            @enderror
                        </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                            <textarea name="description" rows="3"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704] focus:border-[#CE9704] transition-all outline-none resize-none">{{ old('description') }}</textarea>
                        </div>
                            <button type="submit" class="w-full bg-gradient-to-r from-[#CE9704] to-[#B8860B] text-white px-4 py-2.5 rounded-lg hover:from-[#B8860B] hover:to-[#CE9704] transition-all duration-300 font-semibold shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                <div class="flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                            Create Category
                                </div>
                        </button>
                    </form>
                    </div>
                </div>
            </div>

            <!-- Lista de Categorías -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                    <table class="w-full">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                            <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                            </svg>
                                            Category Name
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                            </svg>
                                            Products
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                            </svg>
                                            Actions
                                        </div>
                                    </th>
                            </tr>
                        </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($categories as $category)
                                <tr class="hover:bg-gradient-to-r hover:from-gray-50 hover:to-white transition-all duration-200 group">
                                    <td class="px-6 py-5 cursor-pointer" onclick="window.location.href='{{ route('admin.categories.show', $category) }}'">
                                        <div class="flex items-center gap-3">
                                            <div class="p-2 bg-[#CE9704]/10 rounded-lg group-hover:bg-[#CE9704]/20 transition-colors">
                                                <svg class="w-5 h-5 text-[#CE9704]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-900 text-base">{{ $category->name }}</div>
                                    @if($category->description)
                                                <div class="text-sm text-gray-500 mt-1">{{ Str::limit($category->description, 60) }}</div>
                                    @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 cursor-pointer" onclick="window.location.href='{{ route('admin.categories.show', $category) }}'">
                                        <div class="flex items-center gap-2">
                                            <span class="px-3 py-1.5 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">
                                                {{ $category->products_count ?? 0 }}
                                            </span>
                                            <span class="text-xs text-gray-500">product(s)</span>
                                        </div>
                                </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.categories.show', $category) }}" 
                                               class="px-3 py-1.5 bg-[#CE9704] text-white rounded-lg hover:bg-[#B8860B] transition-colors text-sm font-medium flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                View
                                            </a>
                                            <button type="button" 
                                                    class="px-3 py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-medium flex items-center gap-1.5"
                                                    onclick="event.stopPropagation(); openDeleteModal('{{ $category->id }}', '{{ addslashes($category->name) }}', {{ $category->products_count ?? 0 }})">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Delete
                                            </button>
                                        </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                    <td colspan="3" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="p-4 bg-gray-100 rounded-full mb-4">
                                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                </svg>
                                            </div>
                                            <p class="text-lg font-semibold text-gray-700 mb-1">No categories found</p>
                                            <p class="text-sm text-gray-500">Create your first category to get started.</p>
                                        </div>
                                    </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if($categories->hasPages())
        <div class="mt-6 flex justify-center">
            <div class="bg-white rounded-lg shadow-md border border-gray-200 px-4 py-3">
            {{ $categories->links() }}
            </div>
        </div>
        @endif
    </main>
</div>

<!-- Modal de Confirmación de Eliminación -->
<div id="delete-confirm-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm hidden">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all">
        <div class="p-6">
            <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Delete Category</h3>
            <p class="text-gray-600 text-center mb-6" id="delete-confirm-message"></p>
            <form id="delete-form" method="POST" class="space-y-3">
                @csrf
                @method('DELETE')
                <div class="flex gap-3">
                    <button type="button" onclick="closeDeleteModal()" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2.5 rounded-lg hover:bg-gray-300 transition-all font-semibold">
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

<!-- Modal de Error (Productos Asociados) -->
<div id="delete-error-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm hidden">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all">
        <div class="p-6">
            <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-yellow-100 rounded-full">
                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Cannot Delete Category</h3>
            <p class="text-gray-600 text-center mb-6" id="delete-error-message"></p>
            <button onclick="closeDeleteErrorModal()" class="w-full bg-gradient-to-r from-[#CE9704] to-[#B8860B] text-white px-4 py-2.5 rounded-lg hover:from-[#B8860B] hover:to-[#CE9704] transition-all font-semibold">
                OK
            </button>
        </div>
    </div>
</div>

<script>
// Funciones para modales de éxito/error de sesión
function closeSuccessModal() {
    const modal = document.getElementById('success-message');
    if (modal) {
        modal.style.display = 'none';
    }
}

function closeErrorModal() {
    const modal = document.getElementById('error-message');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Auto-cerrar modales de sesión después de 5 segundos
document.addEventListener('DOMContentLoaded', function() {
    const successModal = document.getElementById('success-message');
    const errorModal = document.getElementById('error-message');
    
    if (successModal) {
        setTimeout(() => {
            closeSuccessModal();
        }, 5000);
    }
    
    if (errorModal) {
        setTimeout(() => {
            closeErrorModal();
        }, 5000);
    }
    
    // Cerrar al hacer click fuera del modal
    if (successModal) {
        successModal.addEventListener('click', function(e) {
            if (e.target === successModal) {
                closeSuccessModal();
            }
        });
    }
    
    if (errorModal) {
        errorModal.addEventListener('click', function(e) {
            if (e.target === errorModal) {
                closeErrorModal();
            }
        });
    }
});

// Funciones para modal de eliminación
function openDeleteModal(categoryId, categoryName, productsCount) {
    const confirmModal = document.getElementById('delete-confirm-modal');
    const errorModal = document.getElementById('delete-error-modal');
    
    if (productsCount > 0) {
        // Mostrar modal de error
        document.getElementById('delete-error-message').textContent = 
            `Cannot delete category "${categoryName}" because it has ${productsCount} associated product(s). Please remove all products from this category first.`;
        errorModal.classList.remove('hidden');
        errorModal.style.display = 'flex';
    } else {
        // Mostrar modal de confirmación
        document.getElementById('delete-confirm-message').textContent = 
            `Are you sure you want to delete the category "${categoryName}"?\n\nThis action cannot be undone.`;
        const form = document.getElementById('delete-form');
        form.action = `/admin/categories/${categoryId}`;
        confirmModal.classList.remove('hidden');
        confirmModal.style.display = 'flex';
    }
}

function closeDeleteModal() {
    const modal = document.getElementById('delete-confirm-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
    }
}

function closeDeleteErrorModal() {
    const modal = document.getElementById('delete-error-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
    }
}

// Cerrar modales al hacer click fuera
document.addEventListener('DOMContentLoaded', function() {
    const deleteConfirmModal = document.getElementById('delete-confirm-modal');
    const deleteErrorModal = document.getElementById('delete-error-modal');
    
    if (deleteConfirmModal) {
        deleteConfirmModal.addEventListener('click', function(e) {
            if (e.target === deleteConfirmModal) {
                closeDeleteModal();
            }
        });
    }
    
    if (deleteErrorModal) {
        deleteErrorModal.addEventListener('click', function(e) {
            if (e.target === deleteErrorModal) {
                closeDeleteErrorModal();
            }
        });
    }
    
    // Cerrar con tecla ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteModal();
            closeDeleteErrorModal();
            closeSuccessModal();
            closeErrorModal();
        }
    });
});
</script>
@endsection
