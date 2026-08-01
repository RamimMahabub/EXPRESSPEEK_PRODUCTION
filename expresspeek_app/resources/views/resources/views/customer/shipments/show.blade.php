@extends('layouts.customer')

@section('title', 'Shipment ' . $shipment->tracking_number)

@section('content')
<section class="max-w-7xl mx-auto px-6 py-14">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-violet-600 mb-2">Shipment Details</p>
            <h1 class="text-3xl md:text-4xl font-black text-slate-100">{{ $shipment->tracking_number }}</h1>
            <p class="text-sm text-slate-400 mt-2">Current status: {{ $shipment->status_label }}</p>
        </div>

        <a href="{{ route('customer.shipments.index') }}"
           class="inline-flex items-center justify-center px-5 py-3 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 hover:border-violet-300 hover:text-violet-700 hover:bg-violet-50 transition-colors">
            Back to Shipments
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-2xl border border-white/10 shadow-sm p-6">
            <div class="flex items-start justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-100">Tracking Timeline</h2>
                    <p class="text-sm text-slate-400 mt-1">Latest updates for this shipment.</p>
                </div>
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                    {{ $shipment->status_label }}
                </span>
            </div>

            <div class="space-y-4">
                @forelse($shipment->trackingEvents as $event)
                    <div class="flex gap-4">
                        <div class="w-3.5 h-3.5 rounded-full mt-1.5 bg-violet-500 flex-shrink-0"></div>
                        <div class="flex-1 pb-4 border-b border-gray-50 last:border-b-0 last:pb-0">
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mb-1">
                                <p class="text-sm font-semibold text-slate-100">{{ ucfirst(str_replace('_', ' ', $event->status)) }}</p>
                                <p class="text-xs text-slate-400">{{ $event->occurred_at?->format('M d, Y h:i A') ?? $event->created_at?->format('M d, Y h:i A') }}</p>
                            </div>
                            <p class="text-sm text-slate-500">{{ $event->notes ?: 'No additional notes provided.' }}</p>
                            @if($event->location)
                                <p class="text-xs text-slate-400 mt-1">{{ $event->location }}</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-gray-200 p-8 text-center text-slate-400">
                        No tracking updates have been recorded for this shipment yet.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-white/10 shadow-sm p-6">
                <h2 class="text-lg font-bold text-slate-100 mb-4">Shipment Summary</h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-400">Tracking Number</dt>
                        <dd class="font-mono font-semibold text-slate-100 text-right break-all">{{ $shipment->tracking_number }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-400">Receiver</dt>
                        <dd class="font-semibold text-slate-100 text-right">{{ $shipment->receiver_name }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-400">Destination</dt>
                        <dd class="font-semibold text-slate-100 text-right">{{ $shipment->receiver_city }}, {{ $shipment->receiver_country }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-400">Weight</dt>
                        <dd class="font-semibold text-slate-100 text-right">{{ $shipment->weight }} kg</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-400">Created</dt>
                        <dd class="font-semibold text-slate-100 text-right">{{ $shipment->created_at?->format('M d, Y') }}</dd>
                    </div>
                    @if($shipment->estimated_delivery)
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-400">Estimated Delivery</dt>
                            <dd class="font-semibold text-slate-100 text-right">{{ $shipment->estimated_delivery->format('M d, Y') }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <div class="bg-white rounded-2xl border border-white/10 shadow-sm p-6">
                <h2 class="text-lg font-bold text-slate-100 mb-4">Carrier Tracking</h2>
                @if(!empty($shipment->carrier_tracking_number))
                    <p class="text-sm text-slate-400 mb-2">{{ $shipment->carrier_name ?: 'Carrier' }} reference</p>
                    <a href="{{ $shipment->carrier_tracking_url }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 text-sm font-semibold text-violet-700 hover:text-violet-800">
                        Open carrier tracking
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14"/>
                        </svg>
                    </a>
                    <p class="text-xs text-slate-400 mt-2 font-mono break-all">{{ $shipment->carrier_tracking_number }}</p>
                @else
                    <p class="text-sm text-slate-400">Carrier tracking has not been assigned yet.</p>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection