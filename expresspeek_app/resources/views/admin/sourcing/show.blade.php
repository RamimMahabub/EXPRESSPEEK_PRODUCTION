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
<div class="mb-5 bg-emerald-900/30 border border-emerald-700/50 text-emerald-400 px-5 py-4 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div class="flex items-center gap-3">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @if(session('whatsapp_notify_link'))
        <a href="{{ session('whatsapp_notify_link') }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl transition-colors whitespace-nowrap">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            Notify Customer
        </a>
    @endif
</div>
@endif

{{-- ─── TOP BAR ─── --}}
<div class="flex items-center justify-between mb-6 flex-wrap gap-3">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.sourcing-requests.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-800 hover:bg-gray-700 text-sm text-slate-300 hover:text-white border border-gray-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to List
        </a>
        <a href="{{ route('admin.sourcing-requests.edit', $sourcingRequest) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-violet-600 hover:bg-violet-700 text-sm text-white border border-violet-500 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            Edit Request
        </a>
    </div>
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
                <div class="sm:col-span-2">
                    <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide mb-1">Delivery Address</p>
                    <p class="text-white font-semibold">
                        {{ $sourcingRequest->destination_address ?? 'Not provided' }}<br>
                        @if($sourcingRequest->destination_address2) {{ $sourcingRequest->destination_address2 }}<br> @endif
                        @if($sourcingRequest->destination_address3) {{ $sourcingRequest->destination_address3 }}<br> @endif
                    </p>
                    @if($sourcingRequest->destination_city || $sourcingRequest->destination_state || $sourcingRequest->destination_postal_code || $sourcingRequest->destination_country)
                    <p class="text-slate-300 text-sm mt-0.5">
                        {{ collect([$sourcingRequest->destination_city, $sourcingRequest->destination_state, $sourcingRequest->destination_postal_code])->filter()->join(', ') }}
                        <br>
                        {{ $sourcingRequest->destination_country }}
                        @if($sourcingRequest->destination_country_code)
                            <span class="text-slate-400 font-mono text-xs">({{ $sourcingRequest->destination_country_code }})</span>
                        @endif
                    </p>
                    @endif
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
                    <div x-data="{ showRejectModal: false }" class="bg-gray-800/50 border border-gray-700/50 rounded-xl p-5 relative overflow-hidden">
                        <div class="absolute top-0 right-0 bg-violet-600/20 text-violet-400 text-[10px] font-black uppercase pl-3 pr-2 py-1 rounded-bl-xl border-b border-l border-violet-500/20 flex items-center gap-2">
                            <span>Item #{{ $index + 1 }}</span>
                            @if($item->status === 'rejected')
                                <span class="bg-red-500 text-white px-2 py-0.5 rounded ml-2">REJECTED</span>
                            @endif
                            <div class="flex items-center gap-1 ml-2 border-l border-violet-500/30 pl-2">
                                @if($item->status !== 'rejected')
                                <button type="button" @click="showRejectModal = true" class="flex items-center gap-1.5 px-2 py-1 bg-orange-500/10 hover:bg-orange-500/20 text-orange-400 text-xs font-bold rounded-lg border border-orange-500/20 transition-colors">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                    Reject
                                </button>
                                @endif
                                <form action="{{ route('admin.sourcing-requests.items.destroy', [$sourcingRequest, $item]) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this item? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex items-center gap-1.5 px-2 py-1 bg-red-500/10 hover:bg-red-500/20 text-red-400 text-xs font-bold rounded-lg border border-red-500/20 transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-5">
                            {{-- Image --}}
                            <div class="flex-shrink-0 w-full sm:w-32 h-32 bg-gray-800 rounded-lg border border-gray-700 flex items-center justify-center overflow-hidden relative group">
                                @if($item->product_image)
                                    <a href="{{ asset('storage/' . $item->product_image) }}" target="_blank" class="block w-full h-full">
                                        <img src="{{ asset('storage/' . $item->product_image) }}" alt="Product image" class="w-full h-full object-cover group-hover:scale-105 transition-transform" onerror="this.onerror=null; this.src='{{ asset('images/placeholder.png') }}';">
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
                                
                                @if($item->status === 'rejected')
                                    <div class="mt-4 p-3 bg-red-500/10 border border-red-500/20 rounded-lg">
                                        <p class="text-xs text-red-400 font-semibold uppercase tracking-wide mb-1">Rejection Reason</p>
                                        <p class="text-red-300 text-sm whitespace-pre-wrap">{{ $item->rejection_reason }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Reject Modal --}}
                        <div x-show="showRejectModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                <div x-show="showRejectModal" x-transition.opacity class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" @click="showRejectModal = false" aria-hidden="true"></div>
                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                <div x-show="showRejectModal"
                                     x-transition:enter="ease-out duration-300"
                                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                     x-transition:leave="ease-in duration-200"
                                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                     @click.stop
                                     class="relative inline-block align-bottom bg-gray-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-800">
                                    <form action="{{ route('admin.sourcing-requests.items.reject', [$sourcingRequest, $item]) }}" method="POST">
                                        @csrf
                                        <div class="px-6 pt-6 pb-4">
                                            <div class="sm:flex sm:items-start">
                                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-500/20 sm:mx-0 sm:h-10 sm:w-10">
                                                    <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                    </svg>
                                                </div>
                                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                                    <h3 class="text-lg leading-6 font-medium text-white" id="modal-title">Reject Sourcing Item</h3>
                                                    <div class="mt-2 text-sm text-slate-300">
                                                        <p>Provide a reason for rejecting this item. The customer will be notified.</p>
                                                    </div>
                                                    <div class="mt-4">
                                                        <textarea name="rejection_reason" rows="3" required class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-red-500 transition-colors placeholder-gray-600 resize-none" placeholder="e.g. Item is out of stock, prohibited for shipping, etc."></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="px-6 py-4 bg-gray-800/50 sm:flex sm:flex-row-reverse border-t border-gray-800">
                                            <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Reject Item</button>
                                            <button type="button" @click="showRejectModal = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-600 shadow-sm px-4 py-2 bg-transparent text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                                        </div>
                                    </form>
                                </div>
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

                {{-- Tracking Info --}}
                <div class="mb-4">
                    <div class="mb-3">
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-wide">Internal Tracking</p>
                        <p class="font-mono text-lg text-violet-300">{{ $sourcingRequest->tracking_number }}</p>
                    </div>

                    <label class="block text-xs font-semibold text-amber-400 mb-1 flex items-center gap-1.5 mt-4">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        Carrier AWB Number <span class="text-slate-400 font-normal">(e.g. {{ $sourcingRequest->carrier_tracking_provider_name }} waybill)</span>
                    </label>
                    <div class="flex gap-2">
                        <select name="carrier_id" class="w-1/3 bg-gray-800 border border-gray-700 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-amber-400 transition-colors">
                            <option value="">Select carrier</option>
                            @foreach($carriers as $carrier)
                                <option value="{{ $carrier->id }}" @selected((string) old('carrier_id', $sourcingRequest->carrier_id) === (string) $carrier->id)>
                                    {{ \App\Services\QuoteService::getDisplayName($carrier->name) }}
                                </option>
                            @endforeach
                        </select>
                        <input type="text" name="carrier_tracking_number"
                               value="{{ old('carrier_tracking_number', $sourcingRequest->carrier_tracking_number) }}"
                               placeholder="AWB number (e.g. 1Z99...)"
                               class="flex-1 bg-gray-800 border border-amber-500/40 focus:border-amber-400 rounded-xl px-3 py-2 text-sm text-white focus:outline-none transition-colors placeholder-gray-600">
                    </div>
                    @if($sourcingRequest->carrier_tracking_number)
                        <div class="mt-2 flex items-center gap-2">
                            <a href="{{ $sourcingRequest->carrier_tracking_url }}" target="_blank"
                               class="flex items-center gap-1.5 text-xs bg-yellow-500 hover:bg-yellow-400 text-slate-100 font-bold rounded-xl px-3 py-1.5 transition-colors whitespace-nowrap">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                View on {{ $sourcingRequest->carrier_tracking_provider_name }}
                            </a>
                        </div>
                    @endif
                    <p class="mt-1.5 text-xs text-slate-500">
                        When set, searching this tracking number on ExpressPeek will open the carrier's live tracking page.
                    </p>
                </div>

                {{-- Customer Comments --}}
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Comments for Customer</label>
                    <textarea name="admin_notes" rows="4" placeholder="Write an update or comment visible to the customer..."
                        class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-violet-500 transition-colors placeholder-gray-600 resize-none">{{ old('admin_notes', $sourcingRequest->admin_notes) }}</textarea>
                </div>

                <button type="submit" class="w-full py-3 rounded-xl bg-violet-600 hover:bg-violet-700 text-white font-bold text-sm transition-colors">
                    Save Changes
                </button>
            </form>
        </div>

        {{-- Invoices --}}
        <div class="bg-gray-900 border border-blue-900/40 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Invoices <span class="bg-gray-800 text-slate-300 text-xs px-2 py-0.5 rounded-full ml-1">{{ $sourcingRequest->invoices->count() }}</span>
                </h3>
                <a href="{{ route('admin.sourcing-requests.invoice.create', $sourcingRequest) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-600/20 hover:bg-blue-600/30 text-blue-400 text-xs font-bold transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Create Invoice
                </a>
            </div>

            @if($sourcingRequest->invoices->count() > 0)
                <div class="space-y-3">
                    @foreach($sourcingRequest->invoices as $invoice)
                    <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <p class="font-mono text-sm text-white font-bold">{{ $invoice->invoice_number }}</p>
                                    @if($invoice->status === 'paid')
                                        <span class="text-[10px] font-bold uppercase tracking-wide bg-emerald-500/20 text-emerald-400 px-2 py-0.5 rounded-md border border-emerald-500/30">Paid</span>
                                    @elseif($invoice->status === 'cancelled')
                                        <span class="text-[10px] font-bold uppercase tracking-wide bg-red-500/20 text-red-400 px-2 py-0.5 rounded-md border border-red-500/30">Cancelled</span>
                                    @elseif($invoice->status === 'processing')
                                        <span class="text-[10px] font-bold uppercase tracking-wide bg-blue-500/20 text-blue-400 px-2 py-0.5 rounded-md border border-blue-500/30">Processing</span>
                                    @else
                                        <span class="text-[10px] font-bold uppercase tracking-wide bg-amber-500/20 text-amber-400 px-2 py-0.5 rounded-md border border-amber-500/30">Unpaid</span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-400">
                                    {{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }} • {{ $invoice->created_at->format('M d, Y') }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.sourcing-invoices.download', $invoice) }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-700 hover:bg-gray-600 text-slate-200 text-xs font-bold rounded-lg transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Download PDF
                                </a>
                                @if(in_array($invoice->status, ['unpaid', 'processing']))
                                <form action="{{ route('admin.sourcing-invoices.pay', $invoice) }}" method="POST" onsubmit="return confirm('Mark this invoice as paid?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-400 text-xs font-bold rounded-lg transition-colors border border-emerald-600/30">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Mark as Paid
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('admin.sourcing-invoices.destroy', $invoice) }}" method="POST" onsubmit="return confirm('Delete this invoice entirely?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 bg-red-900/30 hover:bg-red-900/50 text-red-400 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        @if($invoice->payment_method)
                        <div class="mt-4 pt-3 border-t border-gray-700/50">
                            <p class="text-xs text-slate-400 mb-2 font-semibold">Payment Details</p>
                            <div class="flex flex-wrap items-center gap-4 text-xs">
                                <div>
                                    <span class="text-slate-500 block mb-0.5">Method:</span>
                                    <span class="text-gray-200 uppercase font-bold">{{ $invoice->payment_method }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-500 block mb-0.5">Txn ID:</span>
                                    <span class="text-gray-200 font-mono font-bold">{{ $invoice->transaction_id }}</span>
                                </div>
                                @if($invoice->proof_of_payment_path)
                                <div>
                                    <span class="text-slate-500 block mb-0.5">Proof:</span>
                                    <a href="{{ asset('storage/' . $invoice->proof_of_payment_path) }}" target="_blank" class="text-violet-400 hover:text-violet-300 underline font-semibold flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        View Image
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4 bg-gray-800/50 rounded-xl border border-gray-700/50 border-dashed">
                    <p class="text-xs text-slate-400">No invoices generated yet.</p>
                </div>
            @endif
        </div>

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
