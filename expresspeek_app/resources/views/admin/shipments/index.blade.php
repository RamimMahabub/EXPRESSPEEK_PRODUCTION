@extends('layouts.dashboard')

@section('title', 'Shipments')
@section('page-title', 'Shipments')
@section('page-subtitle', 'All shipments across the platform')

@section('content')

<div x-data="shipmentAnalytics()" x-init="init()">

    <div class="flex justify-between items-center mb-5">
        <h3 class="text-lg font-semibold text-gray-900">Shipment Filters & Analytics</h3>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex flex-wrap gap-4 items-end mb-8">
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
            <select x-model="filters.record_type" class="w-full bg-white border border-gray-300 rounded-lg text-sm text-gray-900 px-3 py-2 focus:ring-violet-500 focus:border-violet-500 h-[38px]">
                <option value="all">All Types</option>
                <option value="shipment">Standard Shipments</option>
                <option value="sourcing">Sourcing Requests</option>
            </select>
        </div>
        {{-- Carrier Dropdown --}}
        <div class="flex-1 min-w-[160px]" x-data="{ open: false }">
            <label class="block text-xs font-medium text-gray-500 mb-1.5">Carrier</label>
            <div class="relative w-full">
                <button @click="open = !open" @click.outside="open = false" type="button" class="w-full bg-white border border-gray-300 rounded-lg text-sm text-gray-700 px-3 py-2 flex justify-between items-center focus:ring-violet-500 focus:border-violet-500 h-[38px]">
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
        <div class="flex-1 min-w-[160px]" x-data="{ open: false }">
            <label class="block text-xs font-medium text-gray-500 mb-1.5">Agent</label>
            <div class="relative w-full">
                <button @click="open = !open" @click.outside="open = false" type="button" class="w-full bg-white border border-gray-300 rounded-lg text-sm text-gray-700 px-3 py-2 flex justify-between items-center focus:ring-violet-500 focus:border-violet-500 h-[38px]">
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
                <button @click="open = !open" @click.outside="open = false" type="button" class="w-full bg-white border border-gray-300 rounded-lg text-sm text-gray-700 px-3 py-2 flex justify-between items-center focus:ring-violet-500 focus:border-violet-500 h-[38px]">
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

        <div class="flex items-center gap-2">
            <button @click="applyFilters()" class="px-5 py-2.5 bg-violet-600 hover:bg-violet-700 text-white text-sm font-medium rounded-lg transition-colors border border-violet-600 flex items-center gap-2 h-[38px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Apply
            </button>
            <button @click="resetFilters()" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors border border-gray-300 flex items-center gap-2 h-[38px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Reset
            </button>
        </div>
    </div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
    <div class="flex items-center justify-between mb-5">
        <h3 class="text-base font-semibold text-gray-900">Shipment Records</h3>
        <span class="text-xs text-gray-500">Showing {{ $shipments->count() }} of {{ $shipments->total() }}</span>
    </div>

    @if($shipments->isEmpty())
        <div class="flex flex-col items-center justify-center py-14 text-gray-500">
            <svg class="w-12 h-12 mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <p class="text-sm">No shipments found.</p>
        </div>
    @else
        <div class="overflow-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-gray-100">
                        <th class="pb-3 text-xs font-medium text-gray-500">Tracking #</th>
                        <th class="pb-3 text-xs font-medium text-gray-500">AWB</th>
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
                    <tr x-data="inlineEditor({{ $shipment->id }}, '{{ $shipment->status }}', '{{ $shipment->carrier_tracking_number }}', '{{ $shipment->carrier_id }}', {{ $shipment->is_sourcing ? 'true' : 'false' }})" class="hover:bg-gray-50 transition-colors">
                        <td class="py-3 font-mono text-violet-600 text-xs">{{ $shipment->tracking_number ?: '—' }}</td>
                        <td class="py-3 font-mono text-gray-500 text-xs">{{ $shipment->awb_number ?: '—' }}</td>
                        <td class="py-3 text-gray-700">{{ $shipment->is_sourcing ? ($shipment->customer_name ?: '—') : ($shipment->sender_name ?: '—') }}</td>
                        <td class="py-3 text-gray-700">{{ $shipment->receiver_name ?: '—' }}</td>
                        <td class="py-3 text-gray-500 text-xs">{{ $shipment->receiver_city ?: '—' }}{{ $shipment->receiver_country ? ', ' . $shipment->receiver_country : '' }}</td>
                        <td class="py-3">
                            <select x-model="carrierId" class="text-xs font-medium px-2 py-1.5 w-full max-w-[130px] rounded border border-transparent hover:border-gray-300 focus:border-violet-500 focus:ring-violet-500 bg-transparent focus:bg-white cursor-pointer transition-colors">
                                <option value="">Select Carrier</option>
                                @foreach($carriers as $carrier)
                                <option value="{{ $carrier->id }}">{{ $carrier->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="py-3">
                            <input type="text" x-model="carrierTracking" class="w-32 text-xs px-2 py-1.5 border border-transparent hover:border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded bg-transparent focus:bg-white transition-colors" placeholder="Add tracking">
                        </td>
                        <td class="py-3">
                            <select x-model="status" class="text-xs font-medium px-2 py-1 rounded-full border-0 focus:ring-2 focus:ring-violet-500 cursor-pointer transition-colors shadow-sm" :class="{
                                'bg-emerald-100 text-emerald-700': status === 'delivered',
                                'bg-blue-100 text-blue-700': status === 'in_transit',
                                'bg-purple-100 text-purple-700': status === 'out_for_delivery',
                                'bg-amber-100 text-amber-700': status === 'pending',
                                'bg-gray-100 text-gray-700': !['delivered','in_transit','out_for_delivery','pending'].includes(status)
                            }">
                                @foreach(\App\Models\Shipment::statuses() as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="py-3 text-gray-500 text-xs">{{ $shipment->created_at?->format('M d, Y H:i') ?: '—' }}</td>
                        <td class="py-3 text-right">
                            <div x-show="!isDirty" class="flex items-center justify-end gap-2">
                                @if($shipment->is_sourcing)
                                    <a href="{{ route('admin.sourcing-requests.show', $shipment) }}"
                                       class="inline-flex items-center gap-1.5 text-xs text-emerald-600 border border-emerald-200 hover:border-emerald-300 bg-emerald-50 hover:bg-emerald-100 rounded-lg px-2.5 py-1.5 transition-colors">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.sourcing-requests.destroy', $shipment) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this sourcing request?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1.5 text-xs text-red-600 border border-red-200 hover:border-red-300 bg-red-50 hover:bg-red-100 rounded-lg px-2.5 py-1.5 transition-colors">
                                            Delete
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.shipments.edit', $shipment) }}"
                                       class="inline-flex items-center gap-1.5 text-xs text-emerald-600 border border-emerald-200 hover:border-emerald-300 bg-emerald-50 hover:bg-emerald-100 rounded-lg px-2.5 py-1.5 transition-colors">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.shipments.destroy', $shipment) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this shipment?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1.5 text-xs text-red-600 border border-red-200 hover:border-red-300 bg-red-50 hover:bg-red-100 rounded-lg px-2.5 py-1.5 transition-colors">
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

    <div class="space-y-6">
        {{-- Dynamic Stats Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-5 gap-5">
            {{-- Total Shipments --}}
            <div class="rounded-xl shadow-lg border border-violet-200/50 p-5 relative overflow-hidden bg-gradient-to-br from-violet-500 to-indigo-600 text-white transform hover:-translate-y-1 transition-all duration-300">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-white opacity-10 rounded-full blur-xl"></div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center shadow-inner">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                </div>
                <div x-show="isLoading" class="h-9 w-24 bg-white/20 rounded-lg animate-pulse mb-1 mt-1 backdrop-blur-md"></div>
                <p x-show="!isLoading" class="text-3xl font-bold" x-text="formatNumber(summary.total)"></p>
                <p class="text-sm text-violet-100 mt-1 font-medium tracking-wide">Total Shipments</p>
            </div>

            {{-- Pending --}}
            <div class="rounded-xl shadow-lg border border-amber-200/50 p-5 relative overflow-hidden bg-gradient-to-br from-amber-400 to-orange-500 text-white transform hover:-translate-y-1 transition-all duration-300">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-white opacity-10 rounded-full blur-xl"></div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center shadow-inner">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div x-show="isLoading" class="h-9 w-24 bg-white/20 rounded-lg animate-pulse mb-1 mt-1 backdrop-blur-md"></div>
                <p x-show="!isLoading" class="text-3xl font-bold" x-text="formatNumber(summary.pending)"></p>
                <p class="text-sm text-amber-50 mt-1 font-medium tracking-wide">Pending</p>
            </div>

            {{-- Delivered --}}
            <div class="rounded-xl shadow-lg border border-emerald-200/50 p-5 relative overflow-hidden bg-gradient-to-br from-emerald-400 to-teal-500 text-white transform hover:-translate-y-1 transition-all duration-300">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-white opacity-10 rounded-full blur-xl"></div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center shadow-inner">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div x-show="isLoading" class="h-9 w-24 bg-white/20 rounded-lg animate-pulse mb-1 mt-1 backdrop-blur-md"></div>
                <p x-show="!isLoading" class="text-3xl font-bold" x-text="formatNumber(summary.delivered)"></p>
                <p class="text-sm text-emerald-50 mt-1 font-medium tracking-wide">Delivered</p>
            </div>

            {{-- Returned --}}
            <div class="rounded-xl shadow-lg border border-rose-200/50 p-5 relative overflow-hidden bg-gradient-to-br from-rose-400 to-red-500 text-white transform hover:-translate-y-1 transition-all duration-300">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-white opacity-10 rounded-full blur-xl"></div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center shadow-inner">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/></svg>
                    </div>
                </div>
                <div x-show="isLoading" class="h-9 w-24 bg-white/20 rounded-lg animate-pulse mb-1 mt-1 backdrop-blur-md"></div>
                <p x-show="!isLoading" class="text-3xl font-bold" x-text="formatNumber(summary.returned)"></p>
                <p class="text-sm text-rose-50 mt-1 font-medium tracking-wide">Returned</p>
            </div>

            {{-- Success Rate --}}
            <div class="rounded-xl shadow-lg border border-blue-200/50 p-5 relative overflow-hidden bg-gradient-to-br from-blue-400 to-cyan-500 text-white transform hover:-translate-y-1 transition-all duration-300">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-white opacity-10 rounded-full blur-xl"></div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center shadow-inner">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                </div>
                <div x-show="isLoading" class="h-9 w-24 bg-white/20 rounded-lg animate-pulse mb-1 mt-1 backdrop-blur-md"></div>
                <p x-show="!isLoading" class="text-3xl font-bold"><span x-text="summary.successRate"></span>%</p>
                <p class="text-sm text-blue-50 mt-1 font-medium tracking-wide">Delivery Success Rate</p>
            </div>
        </div>

        {{-- Charts --}}
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 relative">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-900">Shipment Volume Trend</h3>
                    <select x-model="filters.grouping" @change="applyFilters()" class="bg-white border border-gray-300 rounded-lg text-xs text-gray-700 px-2 py-1 focus:ring-violet-500 focus:border-violet-500">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>
                <div class="relative h-72 w-full">
                    <template x-if="isLoading"><div class="absolute inset-0 bg-gray-50 animate-pulse rounded-lg flex items-center justify-center"><span class="text-sm text-gray-400">Loading...</span></div></template>
                    <canvas id="volumeChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 relative">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Status Distribution</h3>
                <div class="relative h-72 w-full flex items-center justify-center">
                    <template x-if="isLoading"><div class="absolute inset-0 bg-gray-50 animate-pulse rounded-lg flex items-center justify-center"><span class="text-sm text-gray-400">Loading...</span></div></template>
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 relative">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Carrier Volume</h3>
                <div class="relative h-64 w-full">
                    <template x-if="isLoading"><div class="absolute inset-0 bg-gray-50 animate-pulse rounded-lg flex items-center justify-center"><span class="text-sm text-gray-400">Loading...</span></div></template>
                    <canvas id="carrierChart"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 relative">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Top Destinations</h3>
                <div class="relative h-64 w-full">
                    <template x-if="isLoading"><div class="absolute inset-0 bg-gray-50 animate-pulse rounded-lg flex items-center justify-center"><span class="text-sm text-gray-400">Loading...</span></div></template>
                    <canvas id="destinationChart"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 relative">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Agent Performance</h3>
                <div class="relative h-64 w-full">
                    <template x-if="isLoading"><div class="absolute inset-0 bg-gray-50 animate-pulse rounded-lg flex items-center justify-center"><span class="text-sm text-gray-400">Loading...</span></div></template>
                    <canvas id="agentChart"></canvas>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 relative">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Delivery Outcome Trend</h3>
            <div class="relative h-80 w-full">
                <template x-if="isLoading"><div class="absolute inset-0 bg-gray-50 animate-pulse rounded-lg flex items-center justify-center"><span class="text-sm text-gray-400">Loading...</span></div></template>
                <canvas id="outcomeChart"></canvas>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
                this.carrierTracking = data.shipment.carrier_tracking_number || this.carrierTracking;
                
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
    filters: {
        start_date: '',
        end_date: '',
        carriers: [],
        agents: [],
        statuses: [],
        grouping: 'daily',
        record_type: '{{ $recordType ?? "shipment" }}'
    },
    summary: { total: 0, pending: 0, delivered: 0, returned: 0, successRate: 0 },
    charts: {},
    
    init() {
        const params = new URLSearchParams(window.location.search);
        this.filters.start_date = params.get('start_date') || '';
        this.filters.end_date = params.get('end_date') || '';
        this.filters.grouping = params.get('grouping') || 'daily';
        
        const carrierParams = params.getAll('carriers[]');
        if(carrierParams.length) this.filters.carriers = carrierParams;
        
        const agentParams = params.getAll('agents[]');
        if(agentParams.length) this.filters.agents = agentParams;
        
        const statusParams = params.getAll('statuses[]');
        if(statusParams.length) this.filters.statuses = statusParams;
        
        this.fetchData();
    },
    
    applyFilters() {
        const params = new URLSearchParams();
        if(this.filters.start_date) params.append('start_date', this.filters.start_date);
        if(this.filters.end_date) params.append('end_date', this.filters.end_date);
        if(this.filters.grouping) params.append('grouping', this.filters.grouping);
        if(this.filters.record_type) params.append('record_type', this.filters.record_type);
        
        this.filters.carriers.forEach(c => params.append('carriers[]', c));
        this.filters.agents.forEach(a => params.append('agents[]', a));
        this.filters.statuses.forEach(s => params.append('statuses[]', s));
        
        window.location.href = `{{ route('admin.shipments.index') }}?${params.toString()}`;
    },
    
    resetFilters() {
        window.location.href = `{{ route('admin.shipments.index') }}`;
    },
    
    formatNumber(num) {
        return new Intl.NumberFormat('en-US').format(num || 0);
    },
    
    async fetchData() {
        this.isLoading = true;
        try {
            const params = new URLSearchParams();
            if(this.filters.start_date) params.append('start_date', this.filters.start_date);
            if(this.filters.end_date) params.append('end_date', this.filters.end_date);
            if(this.filters.grouping) params.append('grouping', this.filters.grouping);
            
            this.filters.carriers.forEach(c => params.append('carriers[]', c));
            this.filters.agents.forEach(a => params.append('agents[]', a));
            this.filters.statuses.forEach(s => params.append('statuses[]', s));
            
            const response = await fetch(`{{ route('admin.dashboard.analytics') }}?${params.toString()}`, {
                headers: { 'Accept': 'application/json' }
            });
            
            if(!response.ok) throw new Error('Analytics fetch failed: ' + response.status);
            const data = await response.json();
            this.summary = data.summary;
            
            this.$nextTick(() => {
                this.updateCharts(data);
            });
        } catch(e) {
            console.error("Error fetching analytics", e);
        } finally {
            this.isLoading = false;
        }
    },

    updateCharts(data) {
        this.initVolumeChart(data.volumeTrend || {});
        this.initStatusChart(data.statusDistribution || {});
        this.initCarrierChart(data.carrierVolume || {});
        this.initDestinationChart(data.topDestinations || {});
        this.initAgentChart(data.agentPerformance || {});
        this.initOutcomeChart(data.outcomeTrend || {});
    },
    
    getCommonOptions() {
        return {
            responsive: true,
            maintainAspectRatio: false,
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
        if(!ctx || !window.Chart) return;
        if(this.charts.volume) this.charts.volume.destroy();
        
        const options = this.getCommonOptions();
        options.plugins.legend.display = false;
        
        this.charts.volume = new window.Chart(ctx, {
            type: 'line',
            data: {
                labels: Object.keys(dataObj),
                datasets: [{
                    label: 'Shipments',
                    data: Object.values(dataObj),
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
            'picked_up': '#3b82f6',
            'in_transit': '#6366f1',
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
                labels: labels.map(l => l.charAt(0).toUpperCase() + l.slice(1).replace('_', ' ')),
                datasets: [{
                    data: Object.values(dataObj),
                    backgroundColor: colors,
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { position: 'right', labels: { color: '#64748b', padding: 20 } },
                    tooltip: this.getCommonOptions().plugins.tooltip
                }
            }
        });
    },
    
    initCarrierChart(dataObj) {
        const ctx = document.getElementById('carrierChart');
        if(!ctx || !window.Chart) return;
        if(this.charts.carrier) this.charts.carrier.destroy();
        
        const options = this.getCommonOptions();
        options.indexAxis = 'y';
        options.plugins.legend.display = false;
        
        this.charts.carrier = new window.Chart(ctx, {
            type: 'bar',
            data: {
                labels: Object.keys(dataObj),
                datasets: [{
                    label: 'Shipments',
                    data: Object.values(dataObj),
                    backgroundColor: '#3b82f6',
                    borderRadius: 4,
                }]
            },
            options: options
        });
    },
    
    initDestinationChart(dataObj) {
        const ctx = document.getElementById('destinationChart');
        if(!ctx || !window.Chart) return;
        if(this.charts.destination) this.charts.destination.destroy();
        
        const options = this.getCommonOptions();
        options.plugins.legend.display = false;
        
        this.charts.destination = new window.Chart(ctx, {
            type: 'bar',
            data: {
                labels: Object.keys(dataObj),
                datasets: [{
                    label: 'Shipments',
                    data: Object.values(dataObj),
                    backgroundColor: '#ec4899',
                    borderRadius: 4,
                }]
            },
            options: options
        });
    },
    
    initAgentChart(dataObj) {
        const ctx = document.getElementById('agentChart');
        if(!ctx || !window.Chart) return;
        if(this.charts.agent) this.charts.agent.destroy();
        
        const options = this.getCommonOptions();
        options.plugins.legend.display = false;
        
        this.charts.agent = new window.Chart(ctx, {
            type: 'bar',
            data: {
                labels: Object.keys(dataObj),
                datasets: [{
                    label: 'Shipments Handled',
                    data: Object.values(dataObj),
                    backgroundColor: '#06b6d4',
                    borderRadius: 4,
                }]
            },
            options: options
        });
    },
    
    initOutcomeChart(dataObj) {
        const ctx = document.getElementById('outcomeChart');
        if(!ctx || !window.Chart) return;
        if(this.charts.outcome) this.charts.outcome.destroy();
        
        const labels = Object.keys(dataObj);
        const delivered = labels.map(date => dataObj[date]['delivered'] || 0);
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
                    { label: 'Delivered', data: delivered, backgroundColor: '#10b981', borderRadius: { topLeft: 4, topRight: 4, bottomLeft: 4, bottomRight: 4 } },
                    { label: 'Returned', data: returned, backgroundColor: '#ef4444', borderRadius: { topLeft: 4, topRight: 4, bottomLeft: 4, bottomRight: 4 } },
                    { label: 'Pending', data: pending, backgroundColor: '#f59e0b', borderRadius: { topLeft: 4, topRight: 4, bottomLeft: 4, bottomRight: 4 } }
                ]
            },
            options: options
        });
    }
}));
});
</script>
@endpush
