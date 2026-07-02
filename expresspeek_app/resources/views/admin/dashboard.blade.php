@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Platform overview & analytics')

@section('content')

<div class="flex justify-end mb-5">
    <a
        href="{{ route('home') }}"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-800 hover:bg-gray-700 text-sm text-gray-200 hover:text-white border border-gray-700 transition-colors"
    >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Home
    </a>
</div>

{{-- Stats Grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-5 gap-5 mb-8">

    {{-- Total Shipments --}}
    <div class="neon-card rounded-2xl p-5 hover:border-violet-600/40 transition-colors">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-violet-500/10 border border-violet-500/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-slate-500 bg-gray-800 px-2 py-1 rounded-full">All Time</span>
        </div>
        <p class="text-3xl font-bold text-white">{{ number_format($shipmentStats['total']) }}</p>
        <p class="text-sm text-slate-400 mt-1">Total Shipments</p>
    </div>

    {{-- Pending --}}
    <div class="neon-card rounded-2xl p-5 hover:border-yellow-600/40 transition-colors">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-yellow-500/10 border border-yellow-500/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-yellow-600 bg-yellow-900/20 px-2 py-1 rounded-full">Awaiting</span>
        </div>
        <p class="text-3xl font-bold text-white">{{ number_format($shipmentStats['pending']) }}</p>
        <p class="text-sm text-slate-400 mt-1">Pending Pickup</p>
    </div>

    {{-- In Transit --}}
    <div class="neon-card rounded-2xl p-5 hover:border-blue-600/40 transition-colors">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-blue-600 bg-blue-900/20 px-2 py-1 rounded-full">Active</span>
        </div>
        <p class="text-3xl font-bold text-white">{{ number_format($shipmentStats['in_transit']) }}</p>
        <p class="text-sm text-slate-400 mt-1">In Transit</p>
    </div>

    {{-- Delivered --}}
    <div class="neon-card rounded-2xl p-5 hover:border-emerald-600/40 transition-colors">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-emerald-600 bg-emerald-900/20 px-2 py-1 rounded-full">Done</span>
        </div>
        <p class="text-3xl font-bold text-white">{{ number_format($shipmentStats['delivered']) }}</p>
        <p class="text-sm text-slate-400 mt-1">Delivered</p>
    </div>

    {{-- Sourcing Requests --}}
    <a href="{{ route('admin.sourcing-requests.index') }}" class="neon-card rounded-2xl p-5 hover:border-amber-600/40 transition-colors block">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center">
                <span class="text-lg">🛒</span>
            </div>
            @if($newSourcingCount > 0)
            <span class="text-xs font-bold text-amber-400 bg-amber-900/30 border border-amber-700/50 px-2 py-1 rounded-full animate-pulse">{{ $newSourcingCount }} New</span>
            @else
            <span class="text-xs font-medium text-slate-500 bg-gray-800 px-2 py-1 rounded-full">All</span>
            @endif
        </div>
        <p class="text-3xl font-bold text-white">{{ number_format($totalSourcingCount) }}</p>
        <p class="text-sm text-slate-400 mt-1">Sourcing Requests</p>
    </a>
</div>

{{-- User Stats + Recent Shipments --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- Users Overview --}}
    <div class="neon-card rounded-2xl p-6">
        <h3 class="text-base font-semibold text-white mb-5">User Overview</h3>
        <div class="space-y-4">
            @foreach([
                ['label' => 'Admins',    'count' => $userStats['admins'],    'color' => 'violet'],
                ['label' => 'Customers', 'count' => $userStats['customers'], 'color' => 'blue'],
                ['label' => 'Agents',    'count' => $userStats['agents'],    'color' => 'emerald'],
            ] as $item)
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-{{ $item['color'] }}-500"></span>
                    <span class="text-sm text-slate-400">{{ $item['label'] }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-32 h-1.5 bg-gray-800 rounded-full overflow-hidden">
                        @php $pct = $userStats['total'] > 0 ? ($item['count'] / $userStats['total'] * 100) : 0; @endphp
                        <div class="h-full rounded-full bg-{{ $item['color'] }}-500" style="width: {{ $pct }}%"></div>
                    </div>
                    <span class="text-sm font-semibold text-white w-6 text-right">{{ $item['count'] }}</span>
                </div>
            </div>
            @endforeach
            <div class="pt-3 border-t border-white/10 flex justify-between items-center">
                <span class="text-sm text-slate-400">Total Users</span>
                <span class="text-sm font-bold text-white">{{ $userStats['total'] }}</span>
            </div>
        </div>
        <a href="{{ route('admin.users.index') }}"
           class="mt-5 flex items-center justify-center gap-2 w-full py-2.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-sm text-slate-300 hover:text-white transition-colors">
            Manage Users
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>

    {{-- Recent Shipments --}}
    <div class="xl:col-span-2 neon-card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-semibold text-white">Recent Shipments</h3>
            <a href="{{ route('admin.shipments.index') }}"
               class="text-xs text-violet-400 hover:text-violet-300 transition-colors">View all →</a>
        </div>

        @if($recentShipments->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 text-slate-500">
                <svg class="w-12 h-12 mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <p class="text-sm">No shipments yet</p>
            </div>
        @else
        <div class="overflow-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-white/10">
                        <th class="pb-3 text-xs font-medium text-slate-400">Tracking #</th>
                        <th class="pb-3 text-xs font-medium text-slate-400">Sender</th>
                        <th class="pb-3 text-xs font-medium text-slate-400">Destination</th>
                        <th class="pb-3 text-xs font-medium text-slate-400">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @foreach($recentShipments as $shipment)
                    <tr class="hover:bg-gray-800/50 transition-colors">
                        <td class="py-3 font-mono text-violet-400 text-xs">{{ $shipment->tracking_number }}</td>
                        <td class="py-3 text-slate-300">{{ $shipment->sender->name ?? '—' }}</td>
                        <td class="py-3 text-slate-400">{{ $shipment->receiver_city }}, {{ $shipment->receiver_country }}</td>
                        <td class="py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                @if($shipment->status === 'delivered') bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shadow-[0_0_10px_rgba(16,185,129,0.2)]
                                @elseif($shipment->status === 'in_transit') bg-blue-500/10 text-blue-400 border border-blue-500/20 shadow-[0_0_10px_rgba(59,130,246,0.2)]
                                @elseif($shipment->status === 'pending') bg-amber-500/10 text-amber-400 border border-amber-500/20 shadow-[0_0_10px_rgba(245,158,11,0.2)]
                                @else bg-gray-800 text-slate-400
                                @endif">
                                {{ $shipment->status_label }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

@endsection
