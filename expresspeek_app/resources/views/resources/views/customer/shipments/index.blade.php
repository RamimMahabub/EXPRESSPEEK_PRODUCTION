@extends('layouts.customer')

@section('title', 'My Shipments')

@section('content')
<section class="max-w-7xl mx-auto px-6 py-14">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-violet-600 mb-2">Customer Portal</p>
            <h1 class="text-3xl md:text-4xl font-black text-slate-100">My Shipments</h1>
            <p class="text-sm text-slate-400 mt-2">Track every shipment linked to your account in one place.</p>
        </div>

        <a href="{{ route('customer.dashboard') }}"
           class="inline-flex items-center justify-center px-5 py-3 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 hover:border-violet-300 hover:text-violet-700 hover:bg-violet-50 transition-colors">
            Back to Dashboard
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-white/10 shadow-sm overflow-hidden">
        @forelse($shipments as $shipment)
            <a href="{{ route('customer.shipments.show', $shipment) }}" class="flex items-center gap-5 px-6 py-5 border-b border-gray-50 hover:bg-gray-50 transition-colors last:border-b-0 group">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0
                    {{ $shipment->status === 'delivered' ? 'bg-emerald-100 text-emerald-600' : ($shipment->status === 'in_transit' ? 'bg-blue-100 text-blue-600' : ($shipment->status === 'out_for_delivery' ? 'bg-purple-100 text-purple-600' : 'bg-amber-100 text-amber-600')) }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($shipment->status === 'delivered')
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
                        <p class="text-sm font-bold text-slate-100 truncate">{{ $shipment->receiver_name }}</p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold flex-shrink-0
                            {{ $shipment->status === 'delivered' ? 'bg-emerald-100 text-emerald-700' : ($shipment->status === 'in_transit' ? 'bg-blue-100 text-blue-700' : ($shipment->status === 'out_for_delivery' ? 'bg-purple-100 text-purple-700' : 'bg-amber-100 text-amber-700')) }}">
                            {{ $shipment->status_label }}
                        </span>
                    </div>

                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-400">
                        <span class="font-mono text-violet-600 font-semibold">{{ $shipment->tracking_number }}</span>
                        <span>To {{ $shipment->receiver_city }}, {{ $shipment->receiver_country }}</span>
                        <span>{{ $shipment->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                <div class="hidden md:block text-right flex-shrink-0">
                    <p class="text-sm font-semibold text-gray-700">{{ $shipment->weight }} kg</p>
                    @if($shipment->estimated_delivery)
                        <p class="text-xs text-slate-400 mt-0.5">ETA {{ $shipment->estimated_delivery->format('M d, Y') }}</p>
                    @endif
                </div>

                <svg class="w-4 h-4 text-slate-300 group-hover:text-violet-500 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        @empty
            <div class="px-6 py-16 text-center">
                <div class="mx-auto w-16 h-16 rounded-2xl bg-violet-50 flex items-center justify-center mb-4 text-violet-600">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-slate-100">No shipments yet</h2>
                <p class="text-sm text-slate-400 mt-2">Your account does not have any shipments attached yet.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $shipments->links() }}
    </div>
</section>
@endsection