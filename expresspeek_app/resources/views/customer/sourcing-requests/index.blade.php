@extends('layouts.operations-dashboard')

@section('title', 'Sourcing Requests')
@section('page-title', 'Sourcing Requests')
@section('page-subtitle', 'Track the status of your shop-from-BD sourcing requests.')

@section('content')
<div class="ap-shell">
    <section class="ap-page-head">
        <div class="ap-head-copy"><span class="ap-eyebrow">Shop from Bangladesh</span><h1>Your sourcing request journey</h1><p>Follow each product request from review through payment, sourcing, and final shipment.</p></div>
        <div class="ap-head-actions"><a href="{{ route('customer.sourcing-requests.create', ['new' => 1]) }}" class="ap-button light">New sourcing request</a></div>
    </section>

    <div class="space-y-5">
        @forelse($sourcingRequests as $request)
            @php
                $firstItem = $request->items->first();
                $hasInvoices = $request->invoices->count() > 0;
                $unpaidInvoices = $request->invoices->where('status', 'unpaid');
                $processingInvoices = $request->invoices->where('status', 'processing');
                $paidInvoices = $request->invoices->where('status', 'paid');
                $firstInvoice = $request->invoices->first();
                $firstUnpaid = $unpaidInvoices->first();
                $hasUnpaid = $unpaidInvoices->count() > 0;
                $hasProcessing = $processingInvoices->count() > 0;
                $allPaid = $hasInvoices && $paidInvoices->count() === $request->invoices->count();

                $colorMap = [
                    'blue'    => 'bg-blue-50 text-blue-700 border-blue-200',
                    'indigo'  => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                    'purple'  => 'bg-purple-50 text-purple-700 border-purple-200',
                    'amber'   => 'bg-amber-50 text-amber-700 border-amber-200',
                    'orange'  => 'bg-orange-50 text-orange-700 border-orange-200',
                    'cyan'    => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                    'emerald' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'red'     => 'bg-red-50 text-red-700 border-red-200',
                    'gray'    => 'bg-slate-50 text-slate-600 border-slate-200',
                ];
                $statusCss = $colorMap[$request->status_color] ?? $colorMap['gray'];

                // Card border accent
                $cardBorder = $hasUnpaid
                    ? 'border-orange-400 bg-orange-50/20 shadow-orange-100'
                    : ($allPaid ? 'border-emerald-400 bg-emerald-50/20 shadow-emerald-100' : 'border-slate-200 bg-white');
            @endphp

            <div class="rounded-2xl border-2 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden {{ $cardBorder }}">

                {{-- Payment Due Banner (prominent, full-width) --}}
                @if($hasUnpaid)
                    <div class="flex items-center justify-between gap-4 px-5 py-3 bg-gradient-to-r from-orange-500 to-amber-500">
                        <div class="flex items-center gap-2.5 text-white">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                            <span class="text-sm font-bold">Invoice ready — payment required to proceed</span>
                        </div>
                        <a href="{{ route('customer.sourcing-invoices.show', $firstUnpaid) }}" class="flex-shrink-0 inline-flex items-center gap-1.5 px-4 py-1.5 bg-white text-orange-600 font-bold text-sm rounded-lg hover:bg-orange-50 transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            Pay Now
                        </a>
                    </div>
                @elseif($hasProcessing)
                    <div class="flex items-center gap-2.5 px-5 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-500 text-white">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm font-semibold">Payment submitted — we're verifying your payment</span>
                    </div>
                @endif

                {{-- Main Card Body --}}
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5 p-5">

                    {{-- Thumbnail --}}
                    <div class="w-16 h-16 rounded-xl flex items-center justify-center flex-shrink-0 bg-white border border-slate-100 shadow-sm overflow-hidden relative">
                        @if($firstItem && $firstItem->product_image)
                            <img src="{{ asset('storage/' . $firstItem->product_image) }}" alt="Product" class="w-full h-full object-cover">
                        @else
                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        @endif
                        @if($request->items->count() > 1)
                            <div class="absolute bottom-0 right-0 bg-violet-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-tl-lg">
                                +{{ $request->items->count() - 1 }}
                            </div>
                        @endif
                    </div>

                    {{-- Details --}}
                    <div class="flex-1 min-w-0 w-full">

                        {{-- Top row: ref + status + actions --}}
                        <div class="flex flex-wrap items-center justify-between gap-3 mb-2">
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <p class="text-base font-bold text-slate-800">{{ $request->reference_number }}</p>
                                <span class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-extrabold uppercase tracking-wider border shadow-sm {{ $statusCss }}">
                                    {{ $request->status_label }}
                                </span>
                                {{-- Payment status inline badge --}}
                                @if($hasUnpaid)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider border bg-orange-50 text-orange-700 border-orange-300">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                        Payment Due
                                    </span>
                                @elseif($hasProcessing)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider border bg-blue-50 text-blue-700 border-blue-200">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Processing
                                    </span>
                                @elseif($allPaid)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider border bg-emerald-50 text-emerald-700 border-emerald-200">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Paid
                                    </span>
                                @endif
                            </div>

                            {{-- Action buttons --}}
                            <div class="flex items-center gap-2 flex-wrap">
                                @if($request->status === 'draft')
                                    <a href="{{ route('customer.sourcing-requests.edit', $request) }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-violet-700 bg-violet-50 hover:bg-violet-100 rounded-xl transition-colors border border-violet-200">
                                        Resume Draft
                                    </a>
                                    <form action="{{ route('customer.sourcing-requests.destroy', $request) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this draft?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition-colors border border-red-200" title="Remove Draft">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                @else
                                    @if($request->items->where('status', 'rejected')->isNotEmpty())
                                        <a href="{{ route('customer.sourcing-requests.edit', $request) }}?return_to={{ urlencode(url()->current()) }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-violet-700 bg-violet-50 hover:bg-violet-100 rounded-xl transition-colors border border-violet-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            Edit Items
                                        </a>
                                    @endif

                                    {{-- Pay Now — highest priority CTA --}}
                                    @if($hasUnpaid)
                                        <a href="{{ route('customer.sourcing-invoices.show', $firstUnpaid) }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 rounded-xl transition-all shadow-md shadow-orange-200 hover:shadow-orange-300 hover:-translate-y-0.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                            Pay Now
                                        </a>
                                    @endif

                                    {{-- View Invoice --}}
                                    @if($hasInvoices)
                                        <a href="{{ route('customer.sourcing-invoices.show', $firstInvoice) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 rounded-xl transition-colors border border-amber-300">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            View Invoice
                                        </a>
                                    @endif

                                    {{-- View Details --}}
                                    <a href="{{ route('customer.sourcing-requests.show', $request) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 rounded-xl transition-colors border border-slate-200 shadow-sm">
                                        View Details
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Product description --}}
                        <div class="text-sm text-slate-500 mb-3 truncate">
                            {{ $firstItem ? $firstItem->product_description : 'No description provided' }}
                        </div>

                        {{-- Bottom meta row --}}
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-slate-500">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                To {{ $request->destination_country }}
                            </span>

                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ $request->created_at->format('M d, Y') }}
                            </span>

                            @if($hasInvoices)
                                <span class="inline-flex items-center gap-1 font-medium text-amber-600">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    {{ $request->invoices->count() }} Invoice(s)
                                </span>
                            @endif

                            @if($request->status !== 'draft' && ($request->tracking_number || $request->carrier_tracking_number))
                                <span class="inline-flex items-center gap-1.5 font-medium text-violet-600 bg-violet-50 px-2 py-0.5 rounded border border-violet-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    Tracking Added
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-6 py-16 text-center">
                <div class="mx-auto w-16 h-16 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center mb-4 text-slate-400">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-slate-800">No sourcing requests</h2>
                <p class="text-sm text-slate-500 mt-2">You haven't made any Shop From BD requests yet.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $sourcingRequests->links() }}
    </div>
</div>
@endsection
