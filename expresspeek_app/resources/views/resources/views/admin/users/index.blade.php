@extends('layouts.dashboard')

@section('title', 'Manage Users')
@section('page-title', 'Users')
@section('page-subtitle', 'Manage platform users and roles')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-2">
        @foreach(['all', 'admin', 'customer', 'agent'] as $r)
        <a href="{{ route('admin.users.index', ['role' => $r]) }}"
           class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors
                  {{ $role === $r ? 'bg-violet-600 text-white' : 'bg-gray-800 text-slate-400 hover:text-white' }}">
            {{ ucfirst($r) }}
        </a>
        @endforeach
    </div>
    <a href="{{ route('admin.users.create') }}"
       class="flex items-center gap-2 px-4 py-2 rounded-xl neon-button text-white text-sm font-medium transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add User
    </a>
</div>

<div class="neon-card rounded-2xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="border-b border-white/10 bg-gray-800/50">
            <tr class="text-left">
                <th class="px-6 py-3.5 text-xs font-medium text-slate-400">User</th>
                <th class="px-6 py-3.5 text-xs font-medium text-slate-400">Email</th>
                <th class="px-6 py-3.5 text-xs font-medium text-slate-400">Role</th>
                <th class="px-6 py-3.5 text-xs font-medium text-slate-400">Joined</th>
                <th class="px-6 py-3.5 text-xs font-medium text-slate-400">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-800">
            @forelse($users as $user)
            <tr class="hover:bg-gray-800/40 transition-colors">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-blue-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <span class="font-medium text-gray-200">{{ $user->name }}</span>
                    </div>
                </td>
                <td class="px-6 py-4 text-slate-400">{{ $user->email }}</td>
                <td class="px-6 py-4">
                    @foreach($user->roles as $r)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                        @if($r->name === 'admin') bg-violet-900/40 text-violet-400
                        @elseif($r->name === 'agent') bg-blue-500/10 text-blue-400 border border-blue-500/20 shadow-[0_0_10px_rgba(59,130,246,0.2)]
                        @else bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shadow-[0_0_10px_rgba(16,185,129,0.2)]
                        @endif">
                        {{ ucfirst($r->name) }}
                    </span>
                    @endforeach
                </td>
                <td class="px-6 py-4 text-slate-400 text-xs">{{ $user->created_at->format('M d, Y') }}</td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.users.edit', $user) }}"
                           class="px-2.5 py-1 rounded-lg bg-gray-800 hover:bg-gray-700 text-slate-300 text-xs transition-colors">
                            Edit
                        </a>
                        @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                              onsubmit="return confirm('Delete {{ $user->name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="px-2.5 py-1 rounded-lg bg-red-900/30 hover:bg-red-900/60 text-red-400 text-xs transition-colors">
                                Delete
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-slate-500 text-sm">No users found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($users instanceof \Illuminate\Pagination\LengthAwarePaginator && $users->hasPages())
    <div class="px-6 py-4 border-t border-white/10">
        {{ $users->links() }}
    </div>
    @endif
</div>

@endsection
