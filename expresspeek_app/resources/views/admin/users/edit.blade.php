@extends('layouts.dashboard')

@section('title', 'Edit User')
@section('page-title', 'Edit User')
@section('page-subtitle', 'Update user details and roles')

@section('content')

<div class="max-w-2xl">
    <div class="neon-card rounded-2xl p-6">
        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Full Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                    class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition-colors @error('name') border-red-500 @enderror">
                @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Email Address</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                    class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition-colors @error('email') border-red-500 @enderror">
                @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Role</label>
                <select name="role" required
                    class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition-colors @error('role') border-red-500 @enderror">
                    <option value="">Select a role...</option>
                    <option value="admin"    {{ old('role', $user->hasRole('admin') ? 'admin' : '') === 'admin'    ? 'selected' : '' }}>Admin</option>
                    <option value="customer" {{ old('role', $user->hasRole('customer') ? 'customer' : '') === 'customer' ? 'selected' : '' }}>Customer</option>
                    <option value="agent"    {{ old('role', $user->hasRole('agent') ? 'agent' : '') === 'agent'    ? 'selected' : '' }}>Agent</option>
                </select>
                @error('role') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                    class="px-6 py-2.5 rounded-xl neon-button text-white text-sm font-medium transition-colors">
                    Update User
                </button>
                <a href="{{ route('admin.users.index') }}"
                   class="px-6 py-2.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-slate-300 text-sm font-medium transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
