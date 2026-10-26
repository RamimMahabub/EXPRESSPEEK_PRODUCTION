@extends('layouts.operations-dashboard')

@section('title', 'Request ' . $sourcingRequest->reference_number)
@section('page-title', 'Request ' . $sourcingRequest->reference_number)
@section('page-subtitle', 'Details and progress of your sourcing request.')

@section('content')
<div class="ap-shell">
    <section class="ap-page-head">
        <div class="ap-head-copy">
            <a href="{{ route('customer.sourcing-requests.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700 mb-2 inline-flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Requests
            </a>
            <h1>{{ $sourcingRequest->reference_number }}</h1>
            <p>Created on {{ $sourcingRequest->created_at->format('M d, Y') }}</p>
        </div>
        <div class="ap-head-actions">
            @php
                $colorMap = [
                    'blue' => 'bg-blue-50 text-blue-600 border-blue-200',
                    'indigo' => 'bg-indigo-50 text-indigo-600 border-indigo-200',
                    'purple' => 'bg-purple-50 text-purple-600 border-purple-200',
                    'amber' => 'bg-amber-50 text-amber-600 border-amber-200',
                    'orange' => 'bg-orange-50 text-orange-600 border-orange-200',
                    'cyan' => 'bg-cyan-50 text-cyan-600 border-cyan-200',
                    'emerald' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                    'red' => 'bg-red-50 text-red-600 border-red-200',
                    'gray' => 'bg-slate-50 text-slate-600 border-slate-200',
                ];
                $statusCss = $colorMap[$sourcingRequest->status_color] ?? $colorMap['gray'];
            @endphp
            <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold uppercase tracking-wider border {{ $statusCss }}">
                {{ $sourcingRequest->status_label }}
            </span>
        </div>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Items --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <h2 class="text-base font-bold text-slate-800">Requested Items ({{ $sourcingRequest->items->count() }})</h2>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach($sourcingRequest->items as $item)
                        <div class="flex gap-5 p-6">
                            <div class="w-20 h-20 rounded-xl flex items-center justify-center flex-shrink-0 bg-slate-50 border border-slate-100 overflow-hidden relative">
                                @if($item->product_image)
                                    <img src="{{ asset('storage/' . $item->product_image) }}" alt="Product" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-slate-700 leading-relaxed">{{ $item->product_description }}</p>
                                @if($item->product_link)
                                    <a href="{{ $item->product_link }}" target="_blank" class="text-sm font-semibold text-violet-600 hover:underline inline-flex items-center gap-1 mt-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                        View Product Link
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Tracking --}}
            @if($sourcingRequest->tracking_number || $sourcingRequest->carrier_tracking_number)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <h2 class="text-base font-bold text-slate-800">Tracking Information</h2>
                </div>
                <div class="p-6 space-y-4">
                    @if($sourcingRequest->tracking_number)
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Internal Tracking</p>
                            <div class="flex items-center gap-3">
                                <p class="text-sm font-mono font-bold text-violet-700">{{ $sourcingRequest->tracking_number }}</p>
                                <a href="{{ route('customer.track', ['tracking' => $sourcingRequest->tracking_number]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-violet-600 hover:bg-violet-700 rounded-lg transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Track Now
                                </a>
                            </div>
                        </div>
                    @endif
                    @if($sourcingRequest->carrier_tracking_number)
                        <div class="pt-2">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Carrier Tracking</p>
                            <a href="{{ $sourcingRequest->carrier_tracking_url }}" target="_blank" class="inline-flex items-center gap-2 text-sm font-mono font-bold text-orange-700 hover:text-orange-800 hover:underline">
                                {{ $sourcingRequest->carrier_tracking_provider_name }}: {{ $sourcingRequest->carrier_tracking_number }}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Admin Comments --}}
            @if($sourcingRequest->admin_notes)
            <div class="relative overflow-hidden rounded-2xl border border-violet-200 bg-gradient-to-br from-violet-50 via-white to-indigo-50 shadow-sm">
                <div class="absolute inset-y-0 left-0 w-1 bg-violet-500"></div>
                <div class="p-6 pl-7">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-violet-100 text-violet-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 10h8M8 14h5m7-2a8 8 0 01-8 8 8.5 8.5 0 01-3.6-.8L4 20l.8-3.4A8 8 0 1112 20z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold uppercase tracking-wider text-violet-600">Update from ExpressPeek</p>
                            <p class="mt-2 whitespace-pre-line text-sm leading-7 text-slate-700">{{ $sourcingRequest->admin_notes }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="space-y-6">
            {{-- Delivery Info --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <h2 class="text-base font-bold text-slate-800">Delivery Details</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Destination</p>
                        <p class="text-sm font-medium text-slate-800">{{ $sourcingRequest->destination_country }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Address</p>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            {{ $sourcingRequest->destination_address }}<br>
                            @if($sourcingRequest->destination_address2) {{ $sourcingRequest->destination_address2 }}<br> @endif
                            @if($sourcingRequest->destination_address3) {{ $sourcingRequest->destination_address3 }}<br> @endif
                        {{ $sourcingRequest->destination_city }}, {{ $sourcingRequest->destination_state }}<br>
                        {{ $sourcingRequest->destination_postal_code }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Contact</p>
                        <p class="text-sm text-slate-600">{{ $sourcingRequest->customer_name }}</p>
                        <p class="text-sm text-slate-600 font-mono mt-0.5">{{ $sourcingRequest->whatsapp_country_code }} {{ $sourcingRequest->whatsapp_number }}</p>
                    </div>
                </div>
            </div>

            {{-- Invoices --}}
            @if($sourcingRequest->invoices->count() > 0)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <h2 class="text-base font-bold text-slate-800">Invoices</h2>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach($sourcingRequest->invoices as $invoice)
                        <div class="p-5">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <p class="text-sm font-bold text-slate-800 uppercase tracking-wide">
                                        {{ collect($invoice->items)->pluck('description')->implode(', ') }}
                                    </p>
                                    <p class="text-xs font-mono font-medium text-slate-500 mt-1">Invoice: {{ $invoice->invoice_number }} &bull; {{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}</p>
                                </div>
                                @if($invoice->status === 'paid')
                                    <span class="bg-emerald-50 text-emerald-600 border border-emerald-200 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider">Paid</span>
                                @elseif($invoice->status === 'cancelled')
                                    <span class="bg-red-50 text-red-600 border border-red-200 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider">Cancelled</span>
                                @elseif($invoice->status === 'processing')
                                    <span class="bg-blue-50 text-blue-600 border border-blue-200 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider">Processing</span>
                                @else
                                    <span class="bg-amber-50 text-amber-600 border border-amber-200 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider">Unpaid</span>
                                @endif
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('customer.sourcing-invoices.download', $invoice) }}" target="_blank" class="flex-1 flex items-center justify-center py-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 transition-colors gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Download
                                </a>
                                @if($invoice->status === 'unpaid')
                                <a href="{{ route('customer.sourcing-invoices.show', $invoice) }}" class="flex-1 flex items-center justify-center py-2 bg-violet-600 hover:bg-violet-700 rounded-lg text-sm font-semibold text-white transition-colors gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    Pay Now
                                </a>
                                @else
                                <a href="{{ route('customer.sourcing-invoices.show', $invoice) }}" class="flex-1 flex items-center justify-center py-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 transition-colors gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    View
                                </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
