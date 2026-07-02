@extends('layouts.dashboard')

@section('title', 'Agent Dashboard')
@section('page-title', 'Delivery Dashboard')
@section('page-subtitle', 'Your assigned shipments and delivery queue')

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-3 2xl:grid-cols-5 gap-4 mb-8">
    @foreach([
        ['label' => 'Total Assigned',     'value' => $stats['total'],            'color' => 'violet'],
        ['label' => 'Pending',            'value' => $stats['pending'],           'color' => 'yellow'],
        ['label' => 'In Transit',         'value' => $stats['in_transit'],        'color' => 'blue'],
        ['label' => 'Out for Delivery',   'value' => $stats['out_for_delivery'],  'color' => 'purple'],
        ['label' => 'Delivered Today',    'value' => $stats['delivered_today'],   'color' => 'emerald'],
    ] as $stat)
    <div class="neon-card rounded-2xl p-4 hover:border-{{ $stat['color'] }}-600/40 transition-colors">
        <p class="text-2xl font-bold text-white">{{ $stat['value'] }}</p>
        <p class="text-xs text-slate-400 mt-1">{{ $stat['label'] }}</p>
    </div>
    @endforeach
</div>

{{-- Shipment Queue --}}
<div class="neon-card rounded-2xl p-6">
    <div class="flex items-center justify-between mb-5">
        <h3 class="text-base font-semibold text-white">Delivery Queue</h3>
        <a href="{{ route('agent.shipments.index') }}"
           class="text-xs text-violet-400 hover:text-violet-300 transition-colors">View all →</a>
    </div>

    @if($shipments->isEmpty())
        <div class="flex flex-col items-center justify-center py-12 text-slate-500">
            <svg class="w-12 h-12 mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-sm">No assigned shipments</p>
        </div>
    @else
    <div class="overflow-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b border-white/10">
                    <th class="pb-3 text-xs font-medium text-slate-400">Tracking #</th>
                    <th class="pb-3 text-xs font-medium text-slate-400">Sender</th>
                    <th class="pb-3 text-xs font-medium text-slate-400">Recipient</th>
                    <th class="pb-3 text-xs font-medium text-slate-400">Destination</th>
                    <th class="pb-3 text-xs font-medium text-slate-400">Status</th>
                    <th class="pb-3 text-xs font-medium text-slate-400">ETA</th>
                    <th class="pb-3 text-xs font-medium text-slate-400 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @foreach($shipments->take(10) as $shipment)
                <tr class="hover:bg-gray-800/50 transition-colors">
                    <td class="py-3 font-mono text-violet-400 text-xs">{{ $shipment->tracking_number }}</td>
                    <td class="py-3 text-slate-300">{{ $shipment->sender->name ?? '—' }}</td>
                    <td class="py-3 text-slate-300">{{ $shipment->receiver_name }}</td>
                    <td class="py-3 text-slate-400 text-xs">{{ $shipment->receiver_city }}, {{ $shipment->receiver_country }}</td>
                    <td class="py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                            @if($shipment->status === 'delivered') bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shadow-[0_0_10px_rgba(16,185,129,0.2)]
                            @elseif($shipment->status === 'in_transit') bg-blue-500/10 text-blue-400 border border-blue-500/20 shadow-[0_0_10px_rgba(59,130,246,0.2)]
                            @elseif($shipment->status === 'out_for_delivery') bg-purple-500/10 text-purple-400 border border-purple-500/20 shadow-[0_0_10px_rgba(168,85,247,0.2)]
                            @elseif($shipment->status === 'pending') bg-amber-500/10 text-amber-400 border border-amber-500/20 shadow-[0_0_10px_rgba(245,158,11,0.2)]
                            @else bg-gray-800 text-slate-400
                            @endif">
                            {{ $shipment->status_label }}
                        </span>
                    </td>
                    <td class="py-3 text-slate-400 text-xs">
                        {{ $shipment->estimated_delivery?->format('M d, Y') ?? '—' }}
                    </td>
                    <td class="py-3 text-right">
                        <a href="{{ route('agent.shipments.edit', $shipment) }}"
                           class="inline-flex items-center gap-1.5 text-xs text-emerald-300 border border-emerald-600/40 hover:border-emerald-500 hover:text-emerald-200 rounded-lg px-2.5 py-1.5 transition-colors">
                            Edit
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@endsection
