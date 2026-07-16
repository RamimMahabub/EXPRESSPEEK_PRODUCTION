@extends('layouts.dashboard')

@section('title', 'Shipment ' . $shipment->tracking_number)
@section('page-title', 'Shipment Details')
@section('page-subtitle', 'Current status: ' . $shipment->status_label)

@section('content')
<section class="max-w-7xl mx-auto px-6 py-6">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl md:text-4xl font-black text-white">{{ $shipment->tracking_number }}</h1>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('customer.shipments.waybill', $shipment) }}" target="_blank"
               class="inline-flex items-center justify-center px-5 py-3 rounded-xl neon-button text-sm font-semibold text-white transition-colors">
                Print Waybill
            </a>
            <a href="{{ route('customer.shipments.invoice', $shipment) }}" target="_blank"
               class="inline-flex items-center justify-center px-5 py-3 rounded-xl border border-violet-500/50 text-sm font-semibold text-violet-300 hover:bg-violet-900/20 transition-colors">
                Print Invoice
            </a>
            <a href="{{ route('customer.shipments.index') }}"
               class="inline-flex items-center justify-center px-5 py-3 rounded-xl border border-gray-700 text-sm font-semibold text-gray-300 hover:border-violet-500 hover:text-violet-400 hover:bg-violet-900/20 transition-colors">
                Back to Shipments
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 neon-card rounded-2xl p-6">
            <div class="flex items-start justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-xl font-bold text-white">Tracking Timeline</h2>
                    <p class="text-sm text-slate-400 mt-1">Latest updates for this shipment.</p>
                </div>
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-gray-800 text-gray-300">
                    {{ $shipment->status_label }}
                </span>
            </div>

            <div class="space-y-4">
                @forelse($shipment->trackingEvents as $event)
                    <div class="flex gap-4">
                        <div class="w-3.5 h-3.5 rounded-full mt-1.5 bg-violet-500 flex-shrink-0 shadow-[0_0_10px_rgba(139,92,246,0.6)]"></div>
                        <div class="flex-1 pb-4 border-b border-white/10 last:border-b-0 last:pb-0">
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mb-1">
                                <p class="text-sm font-semibold text-white">{{ ucfirst(str_replace('_', ' ', $event->status)) }}</p>
                                <p class="text-xs text-slate-400">{{ $event->occurred_at?->format('M d, Y h:i A') ?? $event->created_at?->format('M d, Y h:i A') }}</p>
                            </div>
                            <p class="text-sm text-slate-300">{{ $event->notes ?: 'No additional notes provided.' }}</p>
                            @if($event->location)
                                <p class="text-xs text-slate-500 mt-1">{{ $event->location }}</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-gray-700 p-8 text-center text-slate-400">
                        No tracking updates have been recorded for this shipment yet.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            <div class="neon-card rounded-2xl p-6">
                <h2 class="text-lg font-bold text-white mb-4">Shipment Summary</h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between gap-4 border-b border-white/5 pb-2">
                        <dt class="text-slate-400">Tracking Number</dt>
                        <dd class="font-mono font-semibold text-violet-300 text-right break-all">{{ $shipment->tracking_number }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-white/5 pb-2">
                        <dt class="text-slate-400">Receiver</dt>
                        <dd class="font-semibold text-white text-right">{{ $shipment->receiver_name }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-white/5 pb-2">
                        <dt class="text-slate-400">Destination</dt>
                        <dd class="font-semibold text-white text-right">{{ $shipment->receiver_city }}, {{ $shipment->receiver_country }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-white/5 pb-2">
                        <dt class="text-slate-400">Weight</dt>
                        <dd class="font-semibold text-white text-right">{{ $shipment->weight }} kg</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-white/5 pb-2">
                        <dt class="text-slate-400">Created</dt>
                        <dd class="font-semibold text-white text-right">{{ $shipment->created_at?->format('M d, Y') }}</dd>
                    </div>
                    @if($shipment->estimated_delivery)
                        <div class="flex justify-between gap-4 pb-2">
                            <dt class="text-slate-400">Estimated Delivery</dt>
                            <dd class="font-semibold text-emerald-400 text-right">{{ $shipment->estimated_delivery->format('M d, Y') }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <div class="neon-card rounded-2xl p-6">
                <h2 class="text-lg font-bold text-white mb-4">Carrier Tracking</h2>
                @if(!empty($shipment->carrier_tracking_number))
                    <p class="text-sm text-slate-400 mb-2">{{ $shipment->carrier_name ?: 'Carrier' }} reference</p>
                    <a href="{{ $shipment->carrier_tracking_url }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 text-sm font-semibold text-violet-400 hover:text-violet-300 transition-colors">
                        Open carrier tracking
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14"/>
                        </svg>
                    </a>
                    <p class="text-xs text-slate-400 mt-2 font-mono break-all bg-gray-900 rounded p-2">{{ $shipment->carrier_tracking_number }}</p>
                @else
                    <p class="text-sm text-slate-400 bg-gray-900 rounded p-3 border border-white/5 text-center">Carrier tracking has not been assigned yet.</p>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
