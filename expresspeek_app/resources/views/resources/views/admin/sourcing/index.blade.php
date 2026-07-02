@extends('layouts.dashboard')

@section('title', 'Sourcing Requests')
@section('page-title', 'Sourcing Requests')
@section('page-subtitle', 'Manage "Shop from Bangladesh" customer orders')

@section('content')

{{-- ─── ALERTS ─── --}}
@if(session('success'))
<div class="mb-5 bg-emerald-900/30 border border-emerald-700/50 text-emerald-400 px-5 py-4 rounded-2xl flex items-center gap-3">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('success') }}
</div>
@endif

{{-- ─── HEADER STATS ─── --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-7">
    @php
        $statusColors = [
            'new'             => 'blue',
            'reviewing'       => 'indigo',
            'contacted'       => 'purple',
            'payment_pending' => 'amber',
            'sourcing'        => 'orange',
            'shipped'         => 'cyan',
            'completed'       => 'emerald',
            'cancelled'       => 'red',
        ];
        $counts = \App\Models\SourcingRequest::selectRaw('status, count(*) as cnt')->groupBy('status')->pluck('cnt', 'status');
        $totalAll = $counts->sum();
    @endphp
    <div class="neon-card rounded-2xl p-4 col-span-2 sm:col-span-1">
        <p class="text-3xl font-black text-white">{{ $totalAll }}</p>
        <p class="text-xs text-slate-400 mt-0.5 font-medium">Total Requests</p>
    </div>
    <div class="bg-gray-900 border border-blue-800/40 rounded-2xl p-4">
        <p class="text-3xl font-black text-blue-400">{{ $newCount }}</p>
        <p class="text-xs text-slate-400 mt-0.5 font-medium">New</p>
    </div>
    <div class="bg-gray-900 border border-amber-800/40 rounded-2xl p-4">
        <p class="text-3xl font-black text-amber-400">{{ $counts['payment_pending'] ?? 0 }}</p>
        <p class="text-xs text-slate-400 mt-0.5 font-medium">Awaiting Payment</p>
    </div>
    <div class="bg-gray-900 border border-emerald-800/40 rounded-2xl p-4">
        <p class="text-3xl font-black text-emerald-400">{{ $counts['completed'] ?? 0 }}</p>
        <p class="text-xs text-slate-400 mt-0.5 font-medium">Completed</p>
    </div>
</div>

{{-- ─── FILTERS ─── --}}
<form method="GET" action="{{ route('admin.sourcing-requests.index') }}" class="flex flex-wrap gap-3 mb-6">
    <div class="relative flex-1 min-w-[200px]">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, reference, WhatsApp..."
            class="w-full pl-9 pr-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:border-violet-500 transition-colors">
    </div>
    <select name="status" class="bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-gray-200 focus:outline-none focus:border-violet-500 transition-colors">
        <option value="">All Statuses</option>
        @foreach($statuses as $key => $label)
            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    <button type="submit" class="px-5 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold transition-colors">
        Filter
    </button>
    @if(request('search') || request('status'))
    <a href="{{ route('admin.sourcing-requests.index') }}" class="px-5 py-2.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-slate-300 text-sm font-semibold transition-colors border border-gray-700">
        Clear
    </a>
    @endif
</form>

{{-- ─── TABLE ─── --}}
<div class="neon-card rounded-2xl overflow-hidden">
    @if($sourcingRequests->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-slate-500">
            <svg class="w-16 h-16 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            <p class="text-sm font-medium">No sourcing requests found.</p>
            @if(request('search') || request('status'))
                <a href="{{ route('admin.sourcing-requests.index') }}" class="mt-3 text-violet-400 hover:text-violet-300 text-sm font-semibold">Clear filters</a>
            @endif
        </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-white/10 text-left">
                    <th class="px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Reference</th>
                    <th class="px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Customer</th>
                    <th class="px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Destination</th>
                    <th class="px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Product</th>
                    <th class="px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</th>
                    <th class="px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Date</th>
                    <th class="px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @foreach($sourcingRequests as $req)
                @php
                    $color = match($req->status) {
                        'new'             => 'blue',
                        'reviewing'       => 'indigo',
                        'contacted'       => 'purple',
                        'payment_pending' => 'amber',
                        'sourcing'        => 'orange',
                        'shipped'         => 'cyan',
                        'completed'       => 'emerald',
                        'cancelled'       => 'red',
                        default           => 'gray',
                    };
                @endphp
                <tr class="hover:bg-gray-800/50 transition-colors group">
                    <td class="px-5 py-4">
                        <span class="font-mono text-violet-400 font-semibold text-xs">{{ $req->reference_number }}</span>
                        @if($req->status === 'new')
                            <span class="ml-1.5 w-2 h-2 rounded-full bg-blue-400 inline-block animate-pulse" title="New"></span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <p class="font-semibold text-white text-sm">{{ $req->customer_name }}</p>
                        <a href="{{ $req->whatsapp_link }}" target="_blank" class="text-xs text-emerald-400 hover:text-emerald-300 flex items-center gap-1 mt-0.5 transition-colors">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            {{ $req->whatsapp_country_code }}{{ $req->whatsapp_number }}
                        </a>
                    </td>
                    <td class="px-5 py-4 text-slate-300 text-sm">
                        {{ $req->destination_country }}
                    </td>
                    <td class="px-5 py-4 max-w-xs">
                        <p class="text-slate-300 text-sm line-clamp-2 leading-relaxed">{{ $req->product_description }}</p>
                        @if($req->product_image)
                            <span class="text-xs text-violet-400 mt-0.5 inline-block">📷 Has image</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                            bg-{{ $color }}-900/40 text-{{ $color }}-400 border border-{{ $color }}-800/50">
                            {{ $req->status_label }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-slate-400 text-xs whitespace-nowrap">
                        {{ $req->created_at->format('M d, Y') }}<br>
                        <span class="text-slate-500">{{ $req->created_at->diffForHumans() }}</span>
                    </td>
                    <td class="px-5 py-4">
                        <a href="{{ route('admin.sourcing-requests.show', $req) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-violet-600/10 hover:bg-violet-600/20 text-violet-400 hover:text-violet-300 text-xs font-semibold transition-colors">
                            View
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($sourcingRequests->hasPages())
    <div class="px-5 py-4 border-t border-white/10">
        {{ $sourcingRequests->links() }}
    </div>
    @endif
    @endif
</div>

@endsection
