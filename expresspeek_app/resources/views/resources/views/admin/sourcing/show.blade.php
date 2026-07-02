@extends('layouts.dashboard')

@section('title', 'Sourcing Request ' . $sourcingRequest->reference_number)
@section('page-title', $sourcingRequest->reference_number)
@section('page-subtitle', 'Sourcing request details & management')

@section('content')

@php
    $color = match($sourcingRequest->status) {
        'new'             => 'blue',
        'reviewing'       => 'indigo',
        'contacted'       => 'purple',
        'payment_pending' => 'amber',
        'sourcing'        => 'orange',
        'shipped'         => 'cyan',
        'completed'       => 'emerald',
        'cancelled'       => 'red',
        default           => 'gray',
    };
@endphp

{{-- ─── ALERTS ─── --}}
@if(session('success'))
<div class="mb-5 bg-emerald-900/30 border border-emerald-700/50 text-emerald-400 px-5 py-4 rounded-2xl flex items-center gap-3">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('success') }}
</div>
@endif

{{-- ─── TOP BAR ─── --}}
<div class="flex items-center justify-between mb-6 flex-wrap gap-3">
    <a href="{{ route('admin.sourcing-requests.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-800 hover:bg-gray-700 text-sm text-slate-300 hover:text-white border border-gray-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to List
    </a>
    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-bold
        bg-{{ $color }}-900/40 text-{{ $color }}-400 border border-{{ $color }}-800/50">
        {{ $sourcingRequest->status_label }}
    </span>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- ─── LEFT: Customer & Product Info ─── --}}
    <div class="xl:col-span-2 space-y-5">

        {{-- Customer Info --}}
        <div class="neon-card rounded-2xl p-6">
            <h3 class="text-sm font-bold text-white mb-5 flex items-center gap-2">
                <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Customer Information
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide mb-1">Full Name</p>
                    <p class="text-white font-semibold">{{ $sourcingRequest->customer_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide mb-1">WhatsApp</p>
                    <a href="{{ $sourcingRequest->whatsapp_link }}" target="_blank"
                       class="inline-flex items-center gap-2 bg-emerald-900/30 border border-emerald-700/50 text-emerald-400 hover:text-emerald-300 px-4 py-2.5 rounded-xl text-sm font-bold transition-colors">
                        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        {{ $sourcingRequest->whatsapp_country_code }}{{ $sourcingRequest->whatsapp_number }}
                        <span class="text-xs text-emerald-500 font-normal">→ Chat</span>
                    </a>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide mb-1">Destination Country</p>
                    <p class="text-white font-semibold">
                        {{ $sourcingRequest->destination_country }}
                        @if($sourcingRequest->destination_country_code)
                            <span class="text-slate-400 font-mono text-xs">({{ $sourcingRequest->destination_country_code }})</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide mb-1">Submitted</p>
                    <p class="text-white">{{ $sourcingRequest->created_at->format('M d, Y \a\t h:i A') }}</p>
                    <p class="text-xs text-slate-400">{{ $sourcingRequest->created_at->diffForHumans() }}</p>
                </div>
                @if($sourcingRequest->user)
                <div class="sm:col-span-2">
                    <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide mb-1">Linked Account</p>
                    <p class="text-slate-300 text-sm">{{ $sourcingRequest->user->name }} — {{ $sourcingRequest->user->email }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Product Info --}}
        <div class="neon-card rounded-2xl p-6">
            <h3 class="text-sm font-bold text-white mb-5 flex items-center gap-2">
                <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                Requested Products <span class="bg-gray-800 text-slate-300 text-xs px-2 py-0.5 rounded-full ml-1">{{ $sourcingRequest->items->count() }}</span>
            </h3>

            @if($sourcingRequest->items->count() > 0)
                <div class="space-y-4">
                    @foreach($sourcingRequest->items as $index => $item)
                    <div class="bg-gray-800/50 border border-gray-700/50 rounded-xl p-5 relative overflow-hidden">
                        <div class="absolute top-0 right-0 bg-violet-600/20 text-violet-400 text-[10px] font-black uppercase px-3 py-1 rounded-bl-xl border-b border-l border-violet-500/20">
                            Item #{{ $index + 1 }}
                        </div>

                        <div class="flex flex-col sm:flex-row gap-5">
                            {{-- Image --}}
                            <div class="flex-shrink-0 w-full sm:w-32 h-32 bg-gray-800 rounded-lg border border-gray-700 flex items-center justify-center overflow-hidden relative group">
                                @if($item->product_image)
                                    <a href="{{ Storage::url($item->product_image) }}" target="_blank" class="block w-full h-full">
                                        <img src="{{ Storage::url($item->product_image) }}" alt="Product image" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                                    </a>
                                @else
                                    <div class="flex flex-col items-center justify-center text-slate-500">
                                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span class="text-[10px] uppercase font-bold tracking-widest">No Image</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Details --}}
                            <div class="flex-1">
                                <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide mb-1.5">Description</p>
                                <p class="text-gray-200 text-sm leading-relaxed whitespace-pre-wrap mb-4">{{ $item->product_description }}</p>

                                @if($item->product_link)
                                    <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide mb-1.5">Link</p>
                                    <a href="{{ $item->product_link }}" target="_blank" class="inline-flex items-center gap-1.5 text-violet-400 hover:text-violet-300 text-sm font-medium transition-colors break-all bg-violet-400/10 px-3 py-1.5 rounded-lg border border-violet-400/20">
                                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        {{ str($item->product_link)->limit(60) }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6 text-slate-400 text-sm">
                    No products associated with this request.
                </div>
            @endif
        </div>

        {{-- Linked Shipment --}}
        @if($sourcingRequest->shipment)
        <div class="bg-gray-900 border border-violet-800/30 rounded-2xl p-6">
            <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Linked Shipment
            </h3>
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-mono text-violet-400 font-bold">{{ $sourcingRequest->shipment->tracking_number }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $sourcingRequest->shipment->status_label }}</p>
                </div>
                <a href="{{ route('admin.shipments.edit', $sourcingRequest->shipment) }}"
                   class="px-4 py-2 rounded-xl bg-violet-600/20 hover:bg-violet-600/30 text-violet-400 text-sm font-semibold transition-colors">
                    View Shipment →
                </a>
            </div>
        </div>
        @endif
    </div>

    {{-- ─── RIGHT: Admin Actions ─── --}}
    <div class="space-y-5">

        {{-- Update Status & Notes --}}
        <div class="neon-card rounded-2xl p-6">
            <h3 class="text-sm font-bold text-white mb-5 flex items-center gap-2">
                <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Update Request
            </h3>

            <form action="{{ route('admin.sourcing-requests.update', $sourcingRequest) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                {{-- Status --}}
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Status</label>
                    <select name="status" class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-violet-500 transition-colors">
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ $sourcingRequest->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Quoted Price --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Quoted Price</label>
                        <input type="number" name="quoted_price" step="0.01" min="0"
                               value="{{ old('quoted_price', $sourcingRequest->quoted_price) }}"
                               placeholder="0.00"
                               class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-violet-500 transition-colors placeholder-gray-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Currency</label>
                        <select name="quoted_currency" class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-violet-500 transition-colors">
                            @foreach(['BDT','USD','GBP','EUR','AUD','AED','SGD','CAD'] as $cur)
                                <option value="{{ $cur }}" {{ ($sourcingRequest->quoted_currency ?? 'BDT') === $cur ? 'selected' : '' }}>{{ $cur }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Admin Notes --}}
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Admin Notes (Internal)</label>
                    <textarea name="admin_notes" rows="4" placeholder="Notes visible only to admins..."
                        class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-violet-500 transition-colors placeholder-gray-600 resize-none">{{ old('admin_notes', $sourcingRequest->admin_notes) }}</textarea>
                </div>

                <button type="submit" class="w-full py-3 rounded-xl bg-violet-600 hover:bg-violet-700 text-white font-bold text-sm transition-colors">
                    Save Changes
                </button>
            </form>
        </div>

        {{-- Quick Info --}}
        @if($sourcingRequest->quoted_price)
        <div class="bg-amber-900/20 border border-amber-800/40 rounded-2xl p-5">
            <p class="text-xs font-bold text-amber-500 uppercase tracking-wide mb-1">Quoted to Customer</p>
            <p class="text-2xl font-black text-amber-400">
                {{ number_format($sourcingRequest->quoted_price, 2) }}
                <span class="text-sm font-semibold">{{ $sourcingRequest->quoted_currency }}</span>
            </p>
        </div>
        @endif

        {{-- Danger Zone --}}
        @if($sourcingRequest->status !== 'cancelled' && $sourcingRequest->status !== 'completed')
        <div class="bg-gray-900 border border-red-900/40 rounded-2xl p-5">
            <h4 class="text-xs font-bold text-red-500 uppercase tracking-wide mb-3">Danger Zone</h4>
            <form action="{{ route('admin.sourcing-requests.destroy', $sourcingRequest) }}" method="POST"
                  onsubmit="return confirm('Cancel this request? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full py-2.5 rounded-xl bg-red-900/30 hover:bg-red-900/50 text-red-400 text-sm font-bold transition-colors border border-red-800/50">
                    Cancel This Request
                </button>
            </form>
        </div>
        @endif
    </div>
</div>

@endsection
