@extends('layouts.dashboard')

@section('title', 'Sourcing Requests')
@section('page-title', 'Sourcing Requests')
@section('page-subtitle', 'Track the status of your shop-from-BD sourcing requests.')

@section('content')
<section class="max-w-7xl mx-auto px-6 py-6">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-violet-600 mb-2">Customer Portal</p>
            <h1 class="text-3xl md:text-4xl font-black text-slate-100">Sourcing Requests</h1>
            <p class="text-sm text-slate-400 mt-2">Track the status of your shop-from-BD sourcing requests.</p>
        </div>

        <a href="{{ route('sourcing.create') }}"
           class="inline-flex items-center justify-center px-5 py-3 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 hover:border-violet-300 hover:text-violet-700 hover:bg-violet-50 transition-colors">
            New Sourcing Request
        </a>
    </div>

    <div class="neon-card rounded-2xl overflow-hidden">
        @forelse($sourcingRequests as $request)
            @php $firstItem = $request->items->first(); @endphp
            <div class="flex items-center gap-5 px-6 py-5 border-b border-white/5 hover:bg-white/5 transition-colors last:border-b-0 group">
                
                {{-- Product Image Thumbnail --}}
                <div class="w-16 h-16 rounded-xl flex items-center justify-center flex-shrink-0 bg-slate-800 border border-slate-700 overflow-hidden relative">
                    @if($firstItem && $firstItem->product_image)
                        <img src="{{ asset('storage/' . $firstItem->product_image) }}" alt="Product" class="w-full h-full object-cover">
                    @else
                        <svg class="w-6 h-6 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    @endif
                    @if($request->items->count() > 1)
                        <div class="absolute bottom-0 right-0 bg-violet-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-tl-lg">
                            +{{ $request->items->count() - 1 }}
                        </div>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 mb-1">
                        <p class="text-sm font-bold text-white truncate">{{ $request->reference_number }}</p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold flex-shrink-0 border
                            {{ $request->status === 'completed' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : ($request->status === 'processing' ? 'bg-blue-500/10 text-blue-400 border-blue-500/20' : ($request->status === 'cancelled' ? 'bg-red-500/10 text-red-400 border-red-500/20' : 'bg-amber-500/10 text-amber-400 border-amber-500/20')) }}">
                            {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                        </span>
                    </div>

                    <div class="text-sm text-slate-200 mb-1 truncate">
                        {{ $firstItem ? $firstItem->product_description : 'No description provided' }}
                    </div>

                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-400">
                        @if($firstItem && $firstItem->product_link)
                            <a href="{{ $firstItem->product_link }}" target="_blank" class="text-violet-400 hover:underline truncate max-w-[200px]">{{ $firstItem->product_link }}</a>
                        @endif
                        @if($request->tracking_number)
                            <span class="font-mono text-violet-400 font-semibold flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                Tracking: {{ $request->tracking_number }}
                            </span>
                        @endif
                        @if($request->awb_number)
                            <span class="font-mono text-amber-400 font-semibold flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                {{ $request->carrier_tracking_provider_name }} AWB: {{ $request->awb_number }}
                            </span>
                        @endif
                        <span>To {{ $request->destination_country }}</span>
                        <span>{{ $request->created_at->format('M d, Y') }}</span>
                    </div>
                </div>

            </div>

            {{-- Invoices Section --}}
            @if($request->invoices->count() > 0)
            <div class="bg-black/20 border-t border-white/5 px-6 py-4">
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-500 mb-3 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Invoices
                </p>
                <div class="flex flex-wrap gap-3">
                    @foreach($request->invoices as $invoice)
                        <div class="inline-flex items-center gap-3 bg-gray-800/80 border border-gray-700/80 rounded-xl px-4 py-2">
                            <div>
                                <p class="text-xs font-mono font-bold text-slate-300">{{ $invoice->invoice_number }}</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}</p>
                            </div>
                            
                            @if($invoice->status === 'paid')
                                <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-2 py-0.5 rounded text-[10px] font-bold uppercase">Paid</span>
                            @elseif($invoice->status === 'cancelled')
                                <span class="bg-red-500/10 text-red-400 border border-red-500/20 px-2 py-0.5 rounded text-[10px] font-bold uppercase">Cancelled</span>
                            @else
                                <span class="bg-amber-500/10 text-amber-400 border border-amber-500/20 px-2 py-0.5 rounded text-[10px] font-bold uppercase">Unpaid</span>
                            @endif

                            <div class="h-6 w-px bg-gray-700 mx-1"></div>

                            <a href="{{ route('customer.sourcing-invoices.download', $invoice) }}" target="_blank"
                               class="text-violet-400 hover:text-violet-300 bg-violet-400/10 hover:bg-violet-400/20 p-1.5 rounded-lg transition-colors" title="Download Invoice">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        @empty
            <div class="px-6 py-16 text-center">
                <div class="mx-auto w-16 h-16 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center mb-4 text-slate-400">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-white">No sourcing requests</h2>
                <p class="text-sm text-slate-400 mt-2">You haven't made any sourcing requests yet.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $sourcingRequests->links() }}
    </div>
</section>
@endsection
