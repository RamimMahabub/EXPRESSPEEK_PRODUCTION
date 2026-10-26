@extends('layouts.operations-dashboard')

@section('title', 'My Shipments')
@section('page-title', 'My Shipments')
@section('page-subtitle', 'Track every shipment linked to your account in one place.')

@section('content')
<div class="ap-shell">
    <section class="ap-page-head">
        <div class="ap-head-copy"><span class="ap-eyebrow">Personal shipment hub</span><h1>Every delivery, one clear view</h1><p>Follow shipment progress, check destinations, and open full tracking details whenever you need them.</p></div>
        <div class="ap-head-actions"><a href="{{ route('volumetric-weight-calculator') }}" class="ap-button ghost">Weight calculator</a><a href="{{ route('customer.shipments.create') }}" class="ap-button light">Create shipment</a></div>
    </section>

    <div class="neon-card rounded-2xl overflow-hidden">
        @forelse($shipments as $shipment)
            <div class="relative flex items-center border-b border-white/5 hover:bg-white/5 transition-colors last:border-b-0 group">
                <a href="{{ $shipment->status === 'draft' ? '#' : route('customer.shipments.show', $shipment) }}" class="flex-1 flex items-center gap-5 px-6 py-5 cursor-pointer">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0
                    {{ in_array($shipment->status, ['connected_to_carrier', 'delivered'], true) ? 'bg-emerald-500/10 text-emerald-400' : ($shipment->status === 'in_transit' ? 'bg-blue-500/10 text-blue-400' : ($shipment->status === 'out_for_delivery' ? 'bg-purple-500/10 text-purple-400' : 'bg-amber-500/10 text-amber-400')) }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($shipment->status === 'draft')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        @elseif(in_array($shipment->status, ['connected_to_carrier', 'delivered'], true))
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        @elseif(in_array($shipment->status, ['in_transit', 'out_for_delivery']))
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        @endif
                    </svg>
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 mb-1">
                        <p class="text-sm font-bold text-white truncate">{{ $shipment->receiver_name }}</p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold flex-shrink-0 border
                            {{ $shipment->status === 'draft' ? 'bg-slate-500/10 text-slate-400 border-slate-500/20' : (in_array($shipment->status, ['connected_to_carrier', 'delivered'], true) ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : ($shipment->status === 'in_transit' ? 'bg-blue-500/10 text-blue-400 border-blue-500/20' : ($shipment->status === 'out_for_delivery' ? 'bg-purple-500/10 text-purple-400 border-purple-500/20' : 'bg-amber-500/10 text-amber-400 border-amber-500/20'))) }}">
                            {{ $shipment->status_label }}
                        </span>
                    </div>

                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-400">
                        <span class="font-mono text-violet-400 font-semibold">{{ $shipment->tracking_number }}</span>
                        <span>To {{ $shipment->receiver_city }}, {{ $shipment->receiver_country }}</span>
                        <span>{{ $shipment->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                @if($shipment->estimated_delivery)
                    <div class="hidden md:block text-right flex-shrink-0">
                        <p class="text-xs text-slate-400 mt-0.5">ETA {{ $shipment->estimated_delivery->format('M d, Y') }}</p>
                    </div>
                @endif
                <svg class="w-4 h-4 text-slate-400 group-hover:text-violet-400 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                </a>
                
                @if($shipment->status === 'draft')
                <div class="pr-6">
                    <form action="{{ route('customer.shipments.destroy', $shipment) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this draft?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="flex items-center justify-center h-8 w-8 rounded-lg bg-red-900/30 text-red-400 hover:bg-red-900/60 transition-colors" title="Remove Draft">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
                @endif
            </div>
        @empty
            <div class="px-6 py-16 text-center">
                <div class="mx-auto w-16 h-16 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center mb-4 text-slate-400">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-white">No shipments yet</h2>
                <p class="text-sm text-slate-400 mt-2">Your account does not have any shipments attached yet.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $shipments->links() }}
    </div>
</div>
@endsection
