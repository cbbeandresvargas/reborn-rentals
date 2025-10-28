@extends('layouts.app')

@section('title', 'Create User - Admin Panel')

@section('content')
<div class="ml-0 md:ml-64">
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
        <div class="px-6 py-4">
            <h1 class="text-2xl font-bold text-gray-800">Create User</h1>
        </div>
    </header>
    
    <main class="p-6">
        <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704]">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704]">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704]">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                    <input type="text" name="username" value="{{ old('username') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704]">
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                        <input type="password" name="password" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704]">
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password *</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704]">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role *</label>
                    <select name="role" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704]">
                        <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704]">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                    <input type="text" name="address" value="{{ old('address') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#CE9704]">
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="bg-[#CE9704] text-white px-6 py-2 rounded-lg hover:bg-[#B8860B] transition-colors">
                        Create User
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>
</div>

@include('admin.sidebar')
@endsection

