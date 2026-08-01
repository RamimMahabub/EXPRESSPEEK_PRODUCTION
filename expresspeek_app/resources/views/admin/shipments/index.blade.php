@extends('layouts.operations-dashboard')

@section('title', 'Shipments')
@section('page-title', 'Shipments')
@section('page-subtitle', 'All shipments across the platform')

@section('content')

<div x-data="shipmentAnalytics()" class="shipment-workspace ship-shell flex flex-col gap-6">

    <div class="ship-hero order-1 relative overflow-hidden rounded-[1.75rem] bg-slate-950 px-6 py-7 text-white shadow-[0_24px_70px_-28px_rgba(15,23,42,0.8)] sm:px-8">
        <div class="pointer-events-none absolute -right-16 -top-24 h-64 w-64 rounded-full bg-indigo-500/30 blur-3xl"></div>
        <div class="pointer-events-none absolute bottom-0 left-1/3 h-36 w-72 rounded-full bg-cyan-400/10 blur-3xl"></div>
        <div class="relative flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
        <div class="max-w-2xl">
            <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-indigo-300">Operations intelligence</p>
            <h2 class="mt-2 text-2xl font-extrabold tracking-tight sm:text-3xl">Shipment control center</h2>
            <p class="mt-2 text-sm leading-6 text-slate-300">Search, manage, and understand every shipment from one focused workspace.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 self-start sm:self-auto">
            <a href="{{ route('admin.shipments.create') }}" class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-xs font-bold text-indigo-700 shadow-lg shadow-slate-950/20 transition hover:-translate-y-0.5 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-white/70 focus:ring-offset-2 focus:ring-offset-indigo-950">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                Create shipment
            </a>
            <a href="{{ route('volumetric-weight-calculator') }}" class="inline-flex items-center gap-2 rounded-full border border-indigo-300/30 bg-indigo-400/15 px-4 py-2 text-xs font-semibold text-indigo-100 transition hover:bg-indigo-400/25">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19V5h16v14H4Z"/><path d="M8 5v4M12 5v2M16 5v4"/></svg>
                Weight calculator
            </a>
            <div class="ship-live-pill inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-4 py-2 text-xs font-semibold text-slate-100 shadow-inner backdrop-blur">
                <span class="relative flex h-2.5 w-2.5"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-50"></span><span class="relative h-2.5 w-2.5 rounded-full bg-emerald-400"></span></span>
                <span x-text="recordTypeLabel()"></span>
            </div>
        </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="workspace-filters ship-filter-card premium-panel order-1 p-4 sm:p-5">
        <div class="mb-4 flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h3 class="text-sm font-semibold text-slate-900">Filter this workspace</h3>
                <p class="mt-0.5 text-xs text-slate-500">Filters update both the overview and records.</p>
            </div>
            <span x-show="activeFilterCount()" x-text="activeFilterCount() + ' active'" class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700"></span>
        </div>
        <div class="ship-filter-grid flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[140px] relative">
            <label class="block text-xs font-medium text-gray-500 mb-1.5">Start Date</label>
            <div class="relative">
                <input type="text" placeholder="DD/MM/YYYY" x-ref="startDate" x-init="flatpickr($el, { altInput: true, altFormat: 'd/m/Y', dateFormat: 'Y-m-d', defaultDate: filters.start_date || null, onChange: (s, d) => { filters.start_date = d; } })" class="w-full bg-white border border-gray-300 rounded-lg text-sm text-gray-900 px-3 py-2 pr-8 focus:ring-violet-500 focus:border-violet-500">
                <button type="button" @click="$refs.startDate._flatpickr.clear(); filters.start_date='';" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500">&times;</button>
            </div>
        </div>
        <div class="flex-1 min-w-[140px] relative">
            <label class="block text-xs font-medium text-gray-500 mb-1.5">End Date</label>
            <div class="relative">
                <input type="text" placeholder="DD/MM/YYYY" x-ref="endDate" x-init="flatpickr($el, { altInput: true, altFormat: 'd/m/Y', dateFormat: 'Y-m-d', defaultDate: filters.end_date || null, onChange: (s, d) => { filters.end_date = d; } })" class="w-full bg-white border border-gray-300 rounded-lg text-sm text-gray-900 px-3 py-2 pr-8 focus:ring-violet-500 focus:border-violet-500">
                <button type="button" @click="$refs.endDate._flatpickr.clear(); filters.end_date='';" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500">&times;</button>
            </div>
        </div>
        <div class="flex-1 min-w-[140px]">
            <label class="block text-xs font-medium text-gray-500 mb-1.5">Record Type</label>
            <select x-model="filters.record_type" @change="filters.statuses = []; filters.agents = []; applyFilters()" class="h-11 w-full border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-800 focus:border-indigo-500 focus:ring-indigo-100">
                <option value="all">All Types</option>
                <option value="shipment">Standard Shipments</option>
                <option value="sourcing">Sourcing Requests</option>
            </select>
        </div>
        {{-- Carrier Dropdown --}}
        <div class="flex-1 min-w-[160px]" x-data="{ open: false }">
            <label class="block text-xs font-medium text-gray-500 mb-1.5">Carrier</label>
            <div class="relative w-full">
                <button @click="open = !open" @click.outside="open = false" type="button" class="flex h-11 w-full items-center justify-between border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:border-indigo-300 focus:border-indigo-500 focus:ring-indigo-100">
                    <span x-text="filters.carriers.length ? filters.carriers.length + ' selected' : 'All Carriers'" class="truncate"></span>
                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" style="display: none;" class="absolute z-20 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto py-1">
                    @foreach($carriers as $carrier)
                    <label class="flex items-center px-3 py-2 hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" value="{{ $carrier->id }}" x-model="filters.carriers" class="rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                        <span class="ml-2 text-sm text-gray-700 truncate">{{ $carrier->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Agent Dropdown --}}
        <div class="flex-1 min-w-[160px]" x-data="{ open: false }" x-show="filters.record_type !== 'sourcing'">
            <label class="block text-xs font-medium text-gray-500 mb-1.5">Agent</label>
            <div class="relative w-full">
                <button @click="open = !open" @click.outside="open = false" type="button" class="flex h-11 w-full items-center justify-between border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:border-indigo-300 focus:border-indigo-500 focus:ring-indigo-100">
                    <span x-text="filters.agents.length ? filters.agents.length + ' selected' : 'All Agents'" class="truncate"></span>
                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" style="display: none;" class="absolute z-20 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto py-1">
                    @foreach($agents as $agent)
                    <label class="flex items-center px-3 py-2 hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" value="{{ $agent->id }}" x-model="filters.agents" class="rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                        <span class="ml-2 text-sm text-gray-700 truncate">{{ $agent->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Status Dropdown --}}
        <div class="flex-1 min-w-[160px]" x-data="{ open: false }">
            <label class="block text-xs font-medium text-gray-500 mb-1.5">Status</label>
            <div class="relative w-full">
                <button @click="open = !open" @click.outside="open = false" type="button" class="flex h-11 w-full items-center justify-between border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:border-indigo-300 focus:border-indigo-500 focus:ring-indigo-100">
                    <span x-text="filters.statuses.length ? filters.statuses.length + ' selected' : 'All Statuses'" class="truncate"></span>
                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" style="display: none;" class="absolute z-20 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto py-1">
                    @foreach($statuses as $key => $label)
                    <label class="flex items-center px-3 py-2 hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" value="{{ $key }}" x-model="filters.statuses" class="rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                        <span class="ml-2 text-sm text-gray-700 truncate">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex flex-1 items-center justify-end gap-2 min-w-[205px]">
            <button @click="applyFilters()" class="ship-filter-apply flex h-11 items-center gap-2 border border-indigo-600 bg-gradient-to-r from-indigo-600 to-violet-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-indigo-100 transition hover:-translate-y-0.5 hover:shadow-indigo-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Apply
            </button>
            <button @click="resetFilters()" class="ship-filter-reset flex h-11 items-center gap-2 border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 shadow-sm transition hover:border-slate-300 hover:bg-slate-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Reset
            </button>
        </div>
        </div>
    </div>

<div class="ship-record-card premium-panel order-2 overflow-hidden p-4 sm:p-6">
    <div class="ship-record-head mb-5 flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
        <div>
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 text-white shadow-lg shadow-indigo-200">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m4.5 7.5 7.5 4 7.5-4M12 11.5V21"/></svg>
                </span>
                <div>
                    <h3 class="text-lg font-bold tracking-tight text-slate-900">Shipment records</h3>
                    <p class="mt-0.5 text-xs text-slate-500">Live search and inline operations</p>
                </div>
            </div>
        </div>
        <div class="flex w-full flex-col gap-2 sm:flex-row sm:items-center xl:w-auto">
            <div class="ship-search-box relative min-w-0 flex-1 sm:w-[25rem] sm:flex-none">
                <svg class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7" stroke-width="2"/><path d="m20 20-4-4" stroke-linecap="round" stroke-width="2"/></svg>
                <input x-ref="recordsSearch" x-model="filters.search" @input.debounce.450ms="autoSearch()" @keydown.enter.prevent="autoSearch(true)" type="text" autocomplete="off" placeholder="Search sender or tracking number" class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50/80 pl-11 pr-12 text-sm font-medium text-slate-800 shadow-inner outline-none transition placeholder:text-slate-400 hover:bg-white focus:border-indigo-400 focus:bg-white focus:ring-4 focus:ring-indigo-100">
                <span x-show="isSearching" class="absolute right-4 top-1/2 h-4 w-4 -translate-y-1/2 animate-spin rounded-full border-2 border-indigo-200 border-t-indigo-600"></span>
                <button x-show="filters.search && !isSearching" @click="filters.search = ''; autoSearch(true)" type="button" aria-label="Clear search" class="absolute right-3 top-1/2 flex h-7 w-7 -translate-y-1/2 items-center justify-center rounded-full text-lg text-slate-400 transition hover:bg-slate-200 hover:text-slate-700">&times;</button>
            </div>
            <span id="shipment-result-count" class="ship-result-count self-center whitespace-nowrap rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-600 shadow-sm">{{ $shipments->count() }} of {{ $shipments->total() }}</span>
        </div>
    </div>

    <div id="shipment-results">
    @if($shipments->isEmpty())
        <div class="flex flex-col items-center justify-center py-14 text-gray-500">
            <svg class="w-12 h-12 mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <p class="text-sm">No shipments found.</p>
        </div>
    @else
        <div class="shipment-table-wrap overflow-auto rounded-2xl border border-slate-200/80">
            <table class="shipment-table w-full min-w-[1180px] text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-gradient-to-r from-slate-50 to-indigo-50/40 text-left">
                        <th class="pb-3 text-xs font-medium text-gray-500">Tracking #</th>
                        <th class="pb-3 text-xs font-medium text-gray-500">Sender</th>
                        <th class="pb-3 text-xs font-medium text-gray-500">Receiver</th>
                        <th class="pb-3 text-xs font-medium text-gray-500">Destination</th>
                        <th class="pb-3 text-xs font-medium text-gray-500">Carrier</th>
                        <th class="pb-3 text-xs font-medium text-gray-500">Carrier Tracking</th>
                        <th class="pb-3 text-xs font-medium text-gray-500">Status</th>
                        <th class="pb-3 text-xs font-medium text-gray-500">Created</th>
                        <th class="pb-3 text-xs font-medium text-gray-500 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($shipments as $shipment)
                    @php($senderDisplay = $shipment->is_sourcing ? ($shipment->customer_name ?: 'Unknown') : ($shipment->sender_name ?: 'Unknown'))
                    <tr x-data="inlineEditor({{ $shipment->id }}, '{{ $shipment->status }}', '{{ $shipment->carrier_tracking_number }}', '{{ $shipment->carrier_id }}', {{ $shipment->is_sourcing ? 'true' : 'false' }})" class="shipment-row transition-all">
                        <td class="py-4 font-mono text-indigo-600 text-xs">
                            <span class="block font-bold tracking-wide">{{ $shipment->tracking_number ?: '—' }}</span>
                            <span class="mt-1.5 inline-flex rounded-full border px-2 py-0.5 font-sans text-[9px] font-bold uppercase tracking-wide {{ $shipment->is_sourcing ? 'border-fuchsia-100 bg-fuchsia-50 text-fuchsia-700' : 'border-indigo-100 bg-indigo-50 text-indigo-700' }}">{{ $shipment->is_sourcing ? 'Sourcing' : 'Shipment' }}</span>
                        </td>
                        <td class="py-4 text-slate-700">
                            <div class="ship-sender flex items-center gap-3">
                                <span class="ship-avatar flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-slate-100 to-indigo-100 text-xs font-extrabold text-indigo-700 ring-1 ring-inset ring-white">{{ strtoupper(mb_substr($senderDisplay, 0, 1)) }}</span>
                                <span class="font-semibold text-slate-800">{{ $senderDisplay }}</span>
                            </div>
                        </td>
                        <td class="py-4 font-medium text-slate-700">{{ $shipment->receiver_name ?: '—' }}</td>
                        <td class="py-4 text-xs text-slate-500">
                            <div class="flex items-center gap-2"><svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5" stroke-width="1.8"/></svg><span>{{ $shipment->receiver_city ?: '—' }}{{ $shipment->receiver_country ? ', ' . $shipment->receiver_country : '' }}</span></div>
                        </td>
                        <td class="py-4">
                            <select x-model="carrierId" class="ship-inline-select w-full max-w-[145px] cursor-pointer rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-indigo-300 focus:border-indigo-500 focus:ring-indigo-100">
                                <option value="">Select Carrier</option>
                                @foreach($carriers as $carrier)
                                <option value="{{ $carrier->id }}">{{ $carrier->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="py-4">
                            <input type="text" x-model="carrierTracking" class="ship-inline-input w-36 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-700 shadow-sm transition placeholder:text-slate-400 hover:border-indigo-300 focus:border-indigo-500 focus:ring-indigo-100" placeholder="Add tracking">
                        </td>
                        <td class="py-3">
                            <select x-model="status" class="ship-status-select cursor-pointer rounded-full border-0 px-3 py-2 text-xs font-bold shadow-sm ring-1 ring-inset ring-black/5 transition focus:ring-2 focus:ring-indigo-400" :class="{
                                'bg-emerald-100 text-emerald-700': ['connected_to_carrier','delivered','completed'].includes(status),
                                'bg-blue-100 text-blue-700': status === 'in_transit',
                                'bg-sky-100 text-sky-700': ['picked_up','shipped'].includes(status),
                                'bg-purple-100 text-purple-700': status === 'out_for_delivery',
                                'bg-amber-100 text-amber-700': ['pending','new','reviewing','contacted','payment_pending','sourcing'].includes(status),
                                'bg-rose-100 text-rose-700': ['failed','returned','cancelled'].includes(status),
                                'bg-gray-100 text-gray-700': !['connected_to_carrier','delivered','completed','in_transit','picked_up','shipped','out_for_delivery','pending','new','reviewing','contacted','payment_pending','sourcing','failed','returned','cancelled'].includes(status)
                            }">
                                @foreach($shipment->is_sourcing ? \App\Models\SourcingRequest::statuses() : \App\Models\Shipment::statuses() as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="py-4 text-xs text-slate-500">
                            @if($shipment->created_at)
                                <span class="block font-semibold text-slate-700">{{ $shipment->created_at->format('M d, Y') }}</span>
                                <span class="mt-0.5 block text-[10px] text-slate-400">{{ $shipment->created_at->format('H:i') }}</span>
                            @else
                                —
                            @endif
                        </td>
                        <td class="py-3 text-right">
                            <div x-show="!isDirty" class="flex items-center justify-end gap-2">
                                @if($shipment->is_sourcing)
                                    <a href="{{ route('admin.sourcing-requests.show', $shipment) }}"
                                       class="ship-action-edit inline-flex items-center gap-1.5 rounded-xl border border-indigo-100 bg-indigo-50 px-3 py-2 text-xs font-bold text-indigo-700 transition hover:border-indigo-200 hover:bg-indigo-100">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.sourcing-requests.destroy', $shipment) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this sourcing request?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ship-action-delete inline-flex items-center gap-1.5 rounded-xl border border-rose-100 bg-white px-3 py-2 text-xs font-bold text-rose-600 transition hover:border-rose-200 hover:bg-rose-50">
                                            Delete
                                        </button>
                                    </form>
                                @else
                                    <a x-show="carrierTracking || status === 'connected_to_carrier'" style="display:none;" :href="`/admin/shipments/${id}/label`" target="_blank"
                                       class="inline-flex items-center gap-1.5 rounded-xl border border-fuchsia-100 bg-fuchsia-50 px-3 py-2 text-xs font-bold text-fuchsia-700 transition hover:border-fuchsia-200 hover:bg-fuchsia-100"
                                       onclick="this.className='inline-flex items-center gap-1.5 rounded-xl border border-emerald-100 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 transition hover:border-emerald-200 hover:bg-emerald-100'">
                                        Label
                                    </a>
                                    <a href="{{ route('admin.shipments.edit', $shipment) }}"
                                       class="ship-action-edit inline-flex items-center gap-1.5 rounded-xl border border-indigo-100 bg-indigo-50 px-3 py-2 text-xs font-bold text-indigo-700 transition hover:border-indigo-200 hover:bg-indigo-100">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.shipments.destroy', $shipment) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this shipment?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ship-action-delete inline-flex items-center gap-1.5 rounded-xl border border-rose-100 bg-white px-3 py-2 text-xs font-bold text-rose-600 transition hover:border-rose-200 hover:bg-rose-50">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                            <div x-show="isDirty" style="display:none;" class="flex items-center justify-end gap-1.5">
                                <button @click="save()" class="inline-flex items-center justify-center min-w-[50px] gap-1 text-xs text-white bg-violet-600 hover:bg-violet-700 border border-violet-600 rounded-lg px-2.5 py-1.5 shadow-sm transition-colors" :disabled="isSavingStatus">
                                    <span x-show="!isSavingStatus">Save</span>
                                    <span x-show="isSavingStatus" style="display:none;">
                                        <svg class="animate-spin w-3 h-3 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    </span>
                                </button>
                                <button @click="discard()" class="inline-flex items-center gap-1 text-xs text-gray-700 bg-gray-100 hover:bg-gray-200 border border-gray-200 rounded-lg px-2.5 py-1.5 transition-colors" :disabled="isSavingStatus">
                                    Discard
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-5">
            {{ $shipments->links() }}
        </div>
    @endif
    </div>
</div>

    <div class="analytics-area order-3 space-y-6">
        <div x-show="analyticsError" style="display:none;" class="flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
            <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>
            <div><span class="font-semibold">Analytics could not be loaded.</span> <span x-text="analyticsError"></span></div>
        </div>

        <div class="grid grid-cols-2 gap-3 lg:grid-cols-3 2xl:grid-cols-6">
            <template x-for="metric in metricCards()" :key="metric.key">
                <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="absolute inset-x-0 top-0 h-1" :class="metric.bar"></div>
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500" x-text="metric.label"></p>
                            <div x-show="isLoading" class="mt-3 h-8 w-16 animate-pulse rounded bg-slate-100"></div>
                            <p x-show="!isLoading" class="mt-2 text-2xl font-bold tracking-tight text-slate-900" x-text="metric.value"></p>
                        </div>
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl" :class="metric.iconBg">
                            <span class="h-2.5 w-2.5 rounded-full" :class="metric.dot"></span>
                        </div>
                    </div>
                    <p class="mt-2 truncate text-xs text-slate-400" x-text="metric.hint"></p>
                </div>
            </template>
        </div>

        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Performance overview</h3>
                <p class="mt-1 text-sm text-slate-500">Every chart uses the same filters shown above.</p>
            </div>
            <span x-show="lastUpdated" class="text-xs text-slate-400">Updated <span x-text="lastUpdated"></span></span>
        </div>

        {{-- Charts --}}
        <div class="grid grid-cols-1 gap-5 xl:grid-cols-12">
            <div class="relative rounded-2xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-8">
                <div class="mb-5 flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900">Shipment volume</h3>
                        <p class="mt-0.5 text-xs text-slate-500">New records created over time</p>
                    </div>
                    <select x-model="filters.grouping" @change="applyFilters()" class="rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-700 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>
                <div class="relative h-64 w-full">
                    <template x-if="isLoading"><div class="absolute inset-0 z-10 flex animate-pulse items-center justify-center rounded-xl bg-slate-50"><span class="text-sm text-slate-400">Loading analytics...</span></div></template>
                    <div x-show="!isLoading && chartErrors.volume" class="absolute inset-0 z-10 flex items-center justify-center text-sm text-rose-500">Volume chart could not be displayed</div>
                    <div x-show="!isLoading && !chartErrors.volume && !hasChartData('volumeTrend')" class="absolute inset-0 z-10 flex items-center justify-center text-sm text-slate-400">No volume data for these filters</div>
                    <canvas id="volumeChart" class="h-full w-full"></canvas>
                </div>
            </div>

            <div class="relative rounded-2xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-4">
                <div class="mb-5">
                    <h3 class="text-sm font-semibold text-slate-900">Lifecycle mix</h3>
                    <p class="mt-0.5 text-xs text-slate-500">Current status of filtered records</p>
                </div>
                <div class="relative h-64 w-full">
                    <template x-if="isLoading"><div class="absolute inset-0 z-10 flex animate-pulse items-center justify-center rounded-xl bg-slate-50"><span class="text-sm text-slate-400">Loading analytics...</span></div></template>
                    <div x-show="!isLoading && chartErrors.status" class="absolute inset-0 z-10 flex items-center justify-center text-sm text-rose-500">Lifecycle chart could not be displayed</div>
                    <div x-show="!isLoading && !chartErrors.status && !hasChartData('statusDistribution')" class="absolute inset-0 z-10 flex items-center justify-center text-sm text-slate-400">No status data for these filters</div>
                    <canvas id="statusChart" class="h-full w-full"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
            <div class="relative rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-900">Carrier workload</h3>
                <p class="mt-0.5 text-xs text-slate-500">Volume assigned to each carrier</p>
                <div class="relative mt-5 min-h-60 w-full">
                    <template x-if="isLoading"><div class="absolute inset-0 z-10 animate-pulse rounded-xl bg-slate-50"></div></template>
                    <div x-show="!isLoading && !hasChartData('carrierVolume')" class="absolute inset-0 z-10 flex items-center justify-center text-sm text-slate-400">No carrier data</div>
                    <div x-show="!isLoading && hasChartData('carrierVolume')" class="space-y-4">
                        <template x-for="item in rankedItems('carrierVolume')" :key="item.label">
                            <div>
                                <div class="mb-1.5 flex items-center justify-between gap-3 text-xs">
                                    <span class="truncate font-medium text-slate-600" x-text="item.label"></span>
                                    <span class="font-bold text-slate-900" x-text="formatNumber(item.value)"></span>
                                </div>
                                <div class="h-2 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-indigo-500" :style="`width: ${item.percent}%`"></div></div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            <div class="relative rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-900">Top destinations</h3>
                <p class="mt-0.5 text-xs text-slate-500">Most active delivery markets</p>
                <div class="relative mt-5 min-h-60 w-full">
                    <template x-if="isLoading"><div class="absolute inset-0 z-10 animate-pulse rounded-xl bg-slate-50"></div></template>
                    <div x-show="!isLoading && !hasChartData('topDestinations')" class="absolute inset-0 z-10 flex items-center justify-center text-sm text-slate-400">No destination data</div>
                    <div x-show="!isLoading && hasChartData('topDestinations')" class="space-y-4">
                        <template x-for="item in rankedItems('topDestinations')" :key="item.label">
                            <div>
                                <div class="mb-1.5 flex items-center justify-between gap-3 text-xs">
                                    <span class="truncate font-medium text-slate-600" x-text="item.label"></span>
                                    <span class="font-bold text-slate-900" x-text="formatNumber(item.value)"></span>
                                </div>
                                <div class="h-2 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-fuchsia-500" :style="`width: ${item.percent}%`"></div></div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            <div class="relative rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-900">Team workload</h3>
                <p class="mt-0.5 text-xs text-slate-500">Records handled by agent or desk</p>
                <div class="relative mt-5 min-h-60 w-full">
                    <template x-if="isLoading"><div class="absolute inset-0 z-10 animate-pulse rounded-xl bg-slate-50"></div></template>
                    <div x-show="!isLoading && !hasChartData('agentPerformance')" class="absolute inset-0 z-10 flex items-center justify-center text-sm text-slate-400">No team data</div>
                    <div x-show="!isLoading && hasChartData('agentPerformance')" class="space-y-4">
                        <template x-for="item in rankedItems('agentPerformance')" :key="item.label">
                            <div>
                                <div class="mb-1.5 flex items-center justify-between gap-3 text-xs">
                                    <span class="truncate font-medium text-slate-600" x-text="item.label"></span>
                                    <span class="font-bold text-slate-900" x-text="formatNumber(item.value)"></span>
                                </div>
                                <div class="h-2 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-cyan-500" :style="`width: ${item.percent}%`"></div></div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-slate-900">Lifecycle trend</h3>
            <p class="mt-0.5 text-xs text-slate-500">Pending, in-transit, delivered, and exception volume over time</p>
            <div class="relative mt-4 h-72 w-full">
                <template x-if="isLoading"><div class="absolute inset-0 z-10 flex animate-pulse items-center justify-center rounded-xl bg-slate-50"><span class="text-sm text-slate-400">Loading analytics...</span></div></template>
                <div x-show="!isLoading && chartErrors.outcome" class="absolute inset-0 z-10 flex items-center justify-center text-sm text-rose-500">Lifecycle trend could not be displayed</div>
                <div x-show="!isLoading && !chartErrors.outcome && !hasChartData('outcomeTrend')" class="absolute inset-0 z-10 flex items-center justify-center text-sm text-slate-400">No lifecycle data for these filters</div>
                <canvas id="outcomeChart" class="h-full w-full"></canvas>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .premium-panel {
        border: 1px solid rgba(226, 232, 240, .8);
        border-radius: 1.5rem;
        background: linear-gradient(145deg, rgba(255,255,255,.98), rgba(248,250,252,.94));
        box-shadow: 0 22px 55px -34px rgba(15, 23, 42, .38), 0 1px 2px rgba(15, 23, 42, .04);
    }
    .ship-shell { gap: 24px !important; }
    .ship-hero {
        position: relative !important;
        min-height: 150px;
        overflow: hidden !important;
        padding: 30px 34px !important;
        border: 1px solid rgba(129,140,248,.24);
        border-radius: 26px !important;
        color: #fff !important;
        background: radial-gradient(circle at 82% 16%, rgba(99,102,241,.46), transparent 30%), linear-gradient(120deg,#0b1220 0%,#111c38 54%,#21184f 100%) !important;
        box-shadow: 0 28px 65px -34px rgba(15,23,42,.88) !important;
    }
    .ship-hero > div:last-child { position:relative; z-index:2; display:flex; align-items:flex-end; justify-content:space-between; gap:28px; }
    .ship-hero h2 { margin:7px 0 0; color:#fff !important; font-size:30px !important; line-height:1.15; font-weight:850 !important; letter-spacing:-.04em; }
    .ship-hero h2 + p { margin:10px 0 0; color:#b9c4da !important; font-size:14px; }
    .ship-hero p:first-child { margin:0; color:#a5b4fc !important; font-size:10px; font-weight:800; letter-spacing:.2em; }
    .ship-live-pill { display:inline-flex !important; align-items:center; gap:9px; flex:0 0 auto; padding:9px 14px !important; border:1px solid rgba(255,255,255,.14) !important; border-radius:999px !important; color:#eef2ff !important; background:rgba(255,255,255,.09) !important; backdrop-filter:blur(12px); }
    .ship-filter-card, .ship-record-card { padding:24px !important; border-radius:24px !important; background:#fff !important; box-shadow:0 18px 45px -30px rgba(15,23,42,.45) !important; }
    .ship-filter-card > div:first-child { display:flex; align-items:center; justify-content:space-between; padding-bottom:14px; margin-bottom:18px; border-bottom:1px solid #edf1f7; }
    .ship-filter-card > div:first-child h3 { margin:0; color:#111827; font-size:14px; font-weight:800; }
    .ship-filter-card > div:first-child p { margin:3px 0 0; color:#94a3b8; font-size:11px; }
    .ship-filter-grid { display:grid !important; grid-template-columns:repeat(6,minmax(135px,1fr)) auto; align-items:end !important; gap:12px !important; }
    .ship-filter-grid > div { min-width:0 !important; }
    .workspace-filters label { color: #64748b; font-size: .68rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }
    .workspace-filters input[type="text"], .workspace-filters select, .workspace-filters button { border-radius: .8rem; }
    .workspace-filters input[type="text"], .workspace-filters select { min-height: 2.65rem; border-color: #e2e8f0; background-color: rgba(248,250,252,.72); }
    .workspace-filters input[type="text"]:focus, .workspace-filters select:focus { border-color: #818cf8; background:#fff; box-shadow: 0 0 0 4px rgba(99,102,241,.09); }
    .ship-filter-apply, .ship-filter-reset { height:43px !important; display:inline-flex !important; align-items:center; justify-content:center; border-radius:12px !important; white-space:nowrap; }
    .ship-filter-apply { border:0 !important; color:#fff !important; background:linear-gradient(135deg,#4f46e5,#7c3aed) !important; box-shadow:0 10px 24px -12px rgba(79,70,229,.8) !important; }
    .ship-filter-reset { color:#475569 !important; border:1px solid #e2e8f0 !important; background:#fff !important; }
    .ship-record-head { display:flex !important; align-items:center !important; justify-content:space-between !important; gap:24px; margin-bottom:20px !important; }
    .ship-record-head > div:last-child { display:flex !important; flex-direction:row !important; align-items:center !important; gap:10px !important; width:auto !important; }
    .ship-search-box { position:relative !important; width:430px !important; flex:0 0 430px !important; }
    .ship-search-box > svg { position:absolute !important; z-index:2; left:16px !important; top:50% !important; width:18px !important; height:18px !important; transform:translateY(-50%) !important; color:#6366f1 !important; }
    .ship-search-box > input { display:block !important; width:100% !important; height:46px !important; padding:0 46px 0 46px !important; border:1px solid #dbe3ef !important; border-radius:14px !important; color:#172033 !important; background:#f8fafc !important; box-shadow:inset 0 1px 2px rgba(15,23,42,.03) !important; outline:0 !important; }
    .ship-search-box > input:focus { border-color:#818cf8 !important; background:#fff !important; box-shadow:0 0 0 4px rgba(99,102,241,.1) !important; }
    .ship-search-box > button, .ship-search-box > span { position:absolute !important; z-index:3; right:12px !important; top:50% !important; transform:translateY(-50%) !important; }
    .ship-result-count { display:inline-flex !important; align-items:center; height:34px; padding:0 12px !important; border:1px solid #e2e8f0 !important; border-radius:999px !important; color:#475569 !important; background:#fff !important; font-size:11px !important; font-weight:800 !important; }
    .shipment-table-wrap { border:1px solid #dbe3ef !important; border-radius:16px !important; box-shadow:0 16px 38px -30px rgba(15,23,42,.5); }
    .shipment-table { width:100%; border-collapse:separate; border-spacing:0; }
    .shipment-table thead tr { background:#131c2e !important; }
    .shipment-table th { padding: 14px 12px; color: #cbd5e1 !important; font-size: .66rem; font-weight: 800; letter-spacing: .07em; text-transform: uppercase; white-space: nowrap; }
    .shipment-table td { padding-left: .75rem; padding-right: .75rem; vertical-align: middle; }
    .shipment-table tbody tr + tr td { border-top: 1px solid #edf1f6; }
    .shipment-table tbody tr:nth-child(even) { background:#f9fbfd; }
    .shipment-row:hover { background:#f1f4ff !important; box-shadow:inset 3px 0 0 #6366f1; }
    .ship-sender { display:flex !important; align-items:center; gap:10px; }
    .ship-avatar { width:34px !important; height:34px !important; display:grid !important; place-items:center; flex:0 0 34px; border-radius:10px !important; color:#4338ca !important; background:#eef2ff !important; font-size:11px; font-weight:800; }
    .ship-inline-select, .ship-inline-input { height:38px !important; border:1px solid #dbe3ef !important; border-radius:10px !important; color:#334155 !important; background:#fff !important; box-shadow:0 2px 7px rgba(15,23,42,.035) !important; }
    .ship-status-select { min-width:112px; height:36px; border-radius:999px !important; font-weight:750 !important; }
    .ship-action-edit, .ship-action-delete { display:inline-flex !important; align-items:center; justify-content:center; height:36px; padding:0 13px !important; border-radius:10px !important; font-weight:750 !important; }
    .ship-action-edit { color:#4338ca !important; border:1px solid #c7d2fe !important; background:#eef2ff !important; }
    .ship-action-delete { color:#e11d48 !important; border:1px solid #fecdd3 !important; background:#fff1f2 !important; }
    .analytics-area > .grid > .relative,
    .analytics-area > .relative,
    .analytics-area > .grid > template + div {
        border-color: rgba(226,232,240,.8);
        border-radius: 1.5rem;
        box-shadow: 0 20px 48px -34px rgba(15,23,42,.42), 0 1px 2px rgba(15,23,42,.04);
    }
    .analytics-area canvas { filter: saturate(.92); }
    @media (max-width: 1380px) {
        .ship-filter-grid { grid-template-columns:repeat(3,minmax(160px,1fr)); }
        .ship-filter-grid > div:last-child { justify-content:flex-start !important; }
    }
    @media (max-width: 900px) {
        .ship-hero > div:last-child, .ship-record-head { align-items:flex-start !important; flex-direction:column !important; }
        .ship-record-head > div:last-child { width:100% !important; }
        .ship-search-box { width:100% !important; flex:1 1 auto !important; }
        .ship-filter-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
    }
    @media (max-width: 640px) {
        .premium-panel { border-radius: 1.15rem; }
        .shipment-workspace { gap: 1rem; }
        .ship-hero { padding:24px 20px !important; border-radius:18px !important; }
        .ship-hero h2 { font-size:24px !important; }
        .ship-filter-card, .ship-record-card { padding:16px !important; border-radius:18px !important; }
        .ship-filter-grid { grid-template-columns:1fr; }
        .ship-record-head > div:last-child { align-items:stretch !important; flex-direction:column !important; }
        .ship-result-count { align-self:flex-start !important; }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('inlineEditor', (id, initialStatus, initialCarrierTracking, initialCarrierId, isSourcing) => ({
    id: id,
    status: initialStatus,
    carrierTracking: initialCarrierTracking,
    carrierId: String(initialCarrierId || ''),
    isSourcing: isSourcing,
    originalStatus: initialStatus,
    originalTracking: initialCarrierTracking,
    originalCarrierId: String(initialCarrierId || ''),
    isSavingStatus: false,
    
    get isDirty() {
        return this.status !== this.originalStatus || this.carrierTracking !== this.originalTracking || this.carrierId !== this.originalCarrierId;
    },

    discard() {
        this.status = this.originalStatus;
        this.carrierTracking = this.originalTracking;
        this.carrierId = this.originalCarrierId;
    },
    
    async save() {
        if (!this.isDirty) return;
        this.isSavingStatus = true;
        
        try {
            const payload = {
                status: this.status,
                carrier_tracking_number: this.carrierTracking,
                carrier_id: this.carrierId || null
            };
            
            const endpoint = this.isSourcing 
                ? `/admin/sourcing-requests/${this.id}/inline`
                : `/admin/shipments/${this.id}/inline`;
                
            const response = await fetch(endpoint, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            });
            
            if(!response.ok) throw new Error('Network response was not ok');
            const data = await response.json();
            
            if(data.success) {
                this.status = data.shipment.status || this.status;
                this.carrierTracking = data.shipment.carrier_tracking_number ?? '';
                
                this.originalStatus = this.status;
                this.originalTracking = this.carrierTracking;
                this.originalCarrierId = this.carrierId;
            }
        } catch(e) {
            console.error("Failed to save inline update", e);
            alert("Failed to save update. Please try again.");
        } finally {
            this.isSavingStatus = false;
        }
    }
}));

    Alpine.data('shipmentAnalytics', () => ({
    showAnalytics: false,
    isLoading: true,
    analyticsError: '',
    chartErrors: {},
    isSearching: false,
    submittedSearch: '',
    searchController: null,
    lastUpdated: '',
    analyticsData: {},
    filters: {
        start_date: '',
        end_date: '',
        carriers: [],
        agents: [],
        statuses: [],
        search: '',
        grouping: 'daily',
        record_type: '{{ $recordType ?? "shipment" }}'
    },
    summary: { total: 0, pending: 0, inTransit: 0, delivered: 0, returned: 0, successRate: 0 },
    charts: {},
    
    init() {
        const params = new URLSearchParams(window.location.search);
        this.filters.start_date = params.get('start_date') || '';
        this.filters.end_date = params.get('end_date') || '';
        this.filters.grouping = params.get('grouping') || 'daily';
        this.filters.record_type = params.get('record_type') || this.filters.record_type;
        this.filters.search = params.get('search') || '';
        this.submittedSearch = this.filters.search.trim();
        
        const carrierParams = params.getAll('carriers[]');
        if(carrierParams.length) this.filters.carriers = carrierParams;
        
        const agentParams = params.getAll('agents[]');
        if(agentParams.length) this.filters.agents = agentParams;
        
        const statusParams = params.getAll('statuses[]');
        if(statusParams.length) this.filters.statuses = statusParams;

        if(this.submittedSearch) {
            this.$nextTick(() => {
                const input = this.$refs.recordsSearch;
                if(input) {
                    input.focus();
                    input.setSelectionRange(input.value.length, input.value.length);
                }
            });
        }
        
        this.fetchData();
    },

    async autoSearch(force = false) {
        const search = this.filters.search.trim();
        if(!force && search === this.submittedSearch) return;

        this.isSearching = true;
        if(this.searchController) this.searchController.abort();
        const requestController = new AbortController();
        this.searchController = requestController;
        const params = this.buildFilterParams();
        const url = `{{ route('admin.shipments.index') }}?${params.toString()}`;

        try {
            const response = await fetch(url, {
                headers: {
                    'Accept': 'text/html',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                signal: requestController.signal
            });
            if(!response.ok) throw new Error(`Search request failed: ${response.status}`);

            const html = await response.text();
            const nextPage = new DOMParser().parseFromString(html, 'text/html');
            const nextResults = nextPage.getElementById('shipment-results');
            const currentResults = document.getElementById('shipment-results');
            const nextCount = nextPage.getElementById('shipment-result-count');
            const currentCount = document.getElementById('shipment-result-count');

            if(!nextResults || !currentResults) throw new Error('Search results were not found in the response.');

            if(window.Alpine?.destroyTree) window.Alpine.destroyTree(currentResults);
            currentResults.innerHTML = nextResults.innerHTML;
            if(window.Alpine?.initTree) window.Alpine.initTree(currentResults);
            if(nextCount && currentCount) currentCount.textContent = nextCount.textContent;

            this.submittedSearch = search;
            window.history.replaceState({}, '', url);
        } catch(error) {
            if(error.name !== 'AbortError') {
                console.error('Live shipment search failed', error);
                window.location.href = url;
            }
        } finally {
            if(this.searchController === requestController) {
                this.searchController = null;
                this.isSearching = false;
            }
        }
    },

    buildFilterParams() {
        const params = new URLSearchParams();
        if(this.filters.start_date) params.append('start_date', this.filters.start_date);
        if(this.filters.end_date) params.append('end_date', this.filters.end_date);
        if(this.filters.grouping) params.append('grouping', this.filters.grouping);
        if(this.filters.record_type) params.append('record_type', this.filters.record_type);
        if(this.filters.search.trim()) params.append('search', this.filters.search.trim());
        
        this.filters.carriers.forEach(c => params.append('carriers[]', c));
        this.filters.agents.forEach(a => params.append('agents[]', a));
        this.filters.statuses.forEach(s => params.append('statuses[]', s));

        return params;
    },

    applyFilters() {
        const params = this.buildFilterParams();
        window.location.href = `{{ route('admin.shipments.index') }}?${params.toString()}`;
    },
    
    resetFilters() {
        window.location.href = `{{ route('admin.shipments.index') }}`;
    },
    
    formatNumber(num) {
        return new Intl.NumberFormat('en-US').format(num || 0);
    },

    recordTypeLabel() {
        return {
            all: 'All record types',
            shipment: 'Standard shipments',
            sourcing: 'Sourcing requests'
        }[this.filters.record_type] || 'Standard shipments';
    },

    activeFilterCount() {
        return [
            this.filters.start_date,
            this.filters.end_date,
            ...this.filters.carriers,
            ...this.filters.agents,
            ...this.filters.statuses
        ].filter(Boolean).length;
    },

    hasChartData(key) {
        const value = this.analyticsData[key];
        return value && Object.keys(value).length > 0;
    },

    rankedItems(key) {
        const entries = Object.entries(this.analyticsData[key] || {})
            .map(([label, value]) => ({ label, value: Number(value) || 0 }))
            .sort((a, b) => b.value - a.value)
            .slice(0, 6);
        const maximum = Math.max(...entries.map(item => item.value), 1);

        return entries.map(item => ({
            ...item,
            percent: Math.max((item.value / maximum) * 100, 3)
        }));
    },

    metricCards() {
        return [
            { key: 'total', label: 'Total records', value: this.formatNumber(this.summary.total), hint: this.recordTypeLabel(), bar: 'bg-indigo-500', iconBg: 'bg-indigo-50', dot: 'bg-indigo-500' },
            { key: 'pending', label: 'Pending', value: this.formatNumber(this.summary.pending), hint: 'Awaiting progress', bar: 'bg-amber-400', iconBg: 'bg-amber-50', dot: 'bg-amber-500' },
            { key: 'transit', label: 'In transit', value: this.formatNumber(this.summary.inTransit), hint: 'Moving to destination', bar: 'bg-sky-500', iconBg: 'bg-sky-50', dot: 'bg-sky-500' },
            { key: 'delivered', label: 'Delivered', value: this.formatNumber(this.summary.delivered), hint: 'Completed successfully', bar: 'bg-emerald-500', iconBg: 'bg-emerald-50', dot: 'bg-emerald-500' },
            { key: 'exceptions', label: 'Exceptions', value: this.formatNumber(this.summary.returned), hint: 'Failed, returned or cancelled', bar: 'bg-rose-500', iconBg: 'bg-rose-50', dot: 'bg-rose-500' },
            { key: 'success', label: 'Success rate', value: `${this.summary.successRate || 0}%`, hint: 'Delivered / completed outcomes', bar: 'bg-teal-500', iconBg: 'bg-teal-50', dot: 'bg-teal-500' }
        ];
    },
    
    async fetchData() {
        this.isLoading = true;
        this.analyticsError = '';
        try {
            const params = new URLSearchParams();
            if(this.filters.start_date) params.append('start_date', this.filters.start_date);
            if(this.filters.end_date) params.append('end_date', this.filters.end_date);
            if(this.filters.grouping) params.append('grouping', this.filters.grouping);
            if(this.filters.record_type) params.append('record_type', this.filters.record_type);
            
            this.filters.carriers.forEach(c => params.append('carriers[]', c));
            this.filters.agents.forEach(a => params.append('agents[]', a));
            this.filters.statuses.forEach(s => params.append('statuses[]', s));
            
            const response = await fetch(`{{ route('admin.dashboard.analytics') }}?${params.toString()}`, {
                headers: { 'Accept': 'application/json' }
            });
            
            if(!response.ok) throw new Error('Analytics fetch failed: ' + response.status);
            const data = await response.json();
            this.analyticsData = data;
            this.summary = { ...this.summary, ...(data.summary || {}) };
            this.lastUpdated = new Intl.DateTimeFormat('en', { hour: 'numeric', minute: '2-digit' }).format(new Date());
            
            this.$nextTick(() => {
                this.updateCharts(data);
            });
        } catch(e) {
            console.error("Error fetching analytics", e);
            this.analyticsError = 'Please refresh the page or adjust the filters and try again.';
        } finally {
            this.isLoading = false;
        }
    },

    updateCharts(data) {
        if (!window.Chart) {
            this.analyticsError = 'The chart library did not load. Please refresh the page.';
            this.chartErrors = { volume: true, status: true, outcome: true };
            return;
        }

        this.safeInitChart('volume', () => this.initVolumeChart(data.volumeTrend || {}));
        this.safeInitChart('status', () => this.initStatusChart(data.statusDistribution || {}));
        this.safeInitChart('outcome', () => this.initOutcomeChart(data.outcomeTrend || {}));
    },

    safeInitChart(name, renderer) {
        try {
            renderer();
            delete this.chartErrors[name];
        } catch (error) {
            console.error(`Failed to render ${name} chart`, error);
            this.chartErrors[name] = true;
        }
    },
    
    getCommonOptions() {
        return {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            color: '#64748b',
            plugins: {
                legend: { labels: { color: '#64748b', font: { family: 'inherit' } } },
                tooltip: {
                    backgroundColor: '#ffffff',
                    titleColor: '#1e293b',
                    bodyColor: '#475569',
                    borderColor: '#e2e8f0',
                    borderWidth: 1,
                    padding: 10,
                    displayColors: true,
                    boxPadding: 4,
                }
            },
            scales: {
                y: { grid: { color: '#f1f5f9' }, ticks: { color: '#94a3b8', precision: 0 } },
                x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
            }
        };
    },

    initVolumeChart(dataObj) {
        const ctx = document.getElementById('volumeChart');
        if(!ctx || !window.Chart) {
            this.analyticsError = 'The chart library did not load. Please refresh the page.';
            return;
        }
        if(this.charts.volume) this.charts.volume.destroy();
        
        const options = this.getCommonOptions();
        options.plugins.legend.display = false;
        
        this.charts.volume = new window.Chart(ctx, {
            type: 'line',
            data: {
                labels: Object.keys(dataObj),
                datasets: [{
                    label: 'Shipments',
                    data: Object.values(dataObj).map(value => Number(value) || 0),
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: '#8b5cf6',
                }]
            },
            options: options
        });
    },
    
    initStatusChart(dataObj) {
        const ctx = document.getElementById('statusChart');
        if(!ctx || !window.Chart) return;
        if(this.charts.status) this.charts.status.destroy();
        
        const colorMap = {
            'pending': '#f59e0b',
            'in_transit': '#0ea5e9',
            'out_for_delivery': '#a855f7',
            'delivered': '#10b981',
            'failed': '#ef4444',
            'returned': '#f97316',
        };
        
        const labels = Object.keys(dataObj);
        const colors = labels.map(l => colorMap[l] || '#cbd5e1');
        
        this.charts.status = new window.Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels.map(l => l.charAt(0).toUpperCase() + l.slice(1).replaceAll('_', ' ')),
                datasets: [{
                    data: Object.values(dataObj).map(value => Number(value) || 0),
                    backgroundColor: colors,
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                cutout: '70%',
                plugins: {
                    legend: { position: 'bottom', labels: { color: '#64748b', padding: 14, usePointStyle: true, pointStyle: 'circle' } },
                    tooltip: this.getCommonOptions().plugins.tooltip
                }
            }
        });
    },
    
    initOutcomeChart(dataObj) {
        const ctx = document.getElementById('outcomeChart');
        if(!ctx || !window.Chart) return;
        if(this.charts.outcome) this.charts.outcome.destroy();
        
        const labels = Object.keys(dataObj);
        const delivered = labels.map(date => dataObj[date]['delivered'] || 0);
        const inTransit = labels.map(date => dataObj[date]['in_transit'] || 0);
        const returned = labels.map(date => dataObj[date]['returned'] || 0);
        const pending = labels.map(date => dataObj[date]['pending'] || 0);
        
        const options = this.getCommonOptions();
        options.scales.x.stacked = true;
        options.scales.y.stacked = true;
        options.interaction = { mode: 'index', intersect: false };
        
        this.charts.outcome = new window.Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    { label: 'Pending', data: pending, backgroundColor: '#f59e0b', borderRadius: 3 },
                    { label: 'In transit', data: inTransit, backgroundColor: '#0ea5e9', borderRadius: 3 },
                    { label: 'Delivered', data: delivered, backgroundColor: '#10b981', borderRadius: 3 },
                    { label: 'Exceptions', data: returned, backgroundColor: '#f43f5e', borderRadius: 3 }
                ]
            },
            options: options
        });
    }
}));
});
</script>
@endpush
