@extends('layouts.operations-dashboard')

@section('title', 'Shipment ' . $shipment->tracking_number)
@section('page-title', 'Shipment Details')
@section('page-subtitle', 'Current status: ' . $shipment->status_label)

@section('content')
<div class="ap-shell">
    <section class="ap-page-head">
        <div class="ap-head-copy"><span class="ap-eyebrow">Shipment detail</span><h1>{{ $shipment->tracking_number }}</h1><p>Review the latest movement, delivery details, and printable shipment documents.</p></div>
        <div class="ap-head-actions">
            <a href="{{ route('customer.shipments.waybill', $shipment) }}" target="_blank"
               class="ap-button light">
                Print Waybill
            </a>
            @if($shipment->shipment_type !== 'document')
            <a href="{{ route('customer.shipments.invoice', $shipment) }}" target="_blank" class="ap-button light">{{ $shipment->shipment_purpose === 'Commercial' ? 'Commercial' : 'Proforma' }} Invoice</a>
            @endif
            <a href="{{ route('customer.shipments.index') }}"
               class="ap-button ghost">
                Back
            </a>
        </div>
    </section>

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
                                <p class="text-sm font-semibold text-white">{{ \App\Models\Shipment::statuses()[$event->status] ?? ucwords(str_replace('_', ' ', $event->status)) }}</p>
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

            @if($shipment->invoices->isNotEmpty())
                <div class="neon-card rounded-2xl p-6">
                    <h2 class="mb-4 flex items-center gap-2 text-lg font-bold text-white">
                        Generated Receipts
                        <span class="rounded-full bg-gray-800 px-2 py-0.5 text-xs text-slate-300">{{ $shipment->invoices->count() }}</span>
                    </h2>
                    <div class="space-y-3">
                        @foreach($shipment->invoices as $invoice)
                            <div class="rounded-xl border border-gray-700 bg-gray-800/80 p-4">
                                <div class="mb-2 flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-mono text-xs font-bold text-slate-200">{{ $invoice->invoice_number }}</p>
                                        <p class="mt-1 text-xs text-slate-400">{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}</p>
                                    </div>
                                    <span class="rounded border px-2 py-0.5 text-[10px] font-bold uppercase
                                        {{ $invoice->status === 'paid' ? 'border-emerald-500/20 bg-emerald-500/10 text-emerald-400' : ($invoice->status === 'cancelled' ? 'border-red-500/20 bg-red-500/10 text-red-400' : 'border-amber-500/20 bg-amber-500/10 text-amber-400') }}">
                                        {{ $invoice->status_label }}
                                    </span>
                                </div>
                                <a href="{{ route('customer.shipment-invoices.download', $invoice) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-violet-400 transition-colors hover:text-violet-300">
                                    Download PDF
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
