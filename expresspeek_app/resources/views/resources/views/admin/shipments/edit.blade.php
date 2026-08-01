@extends('layouts.dashboard')

@section('title', 'Edit Shipment')
@section('page-title', 'Edit Shipment')
@section('page-subtitle', 'Review and update full shipment details')

@section('content')

@php
    $statusOptions = \App\Models\Shipment::statuses();
    $itemsData = old('items', is_array($shipment->items) ? $shipment->items : []);
    $packagesData = old('packages', is_array($shipment->packages) ? $shipment->packages : []);

    if (!is_array($itemsData) || empty($itemsData)) {
        $itemsData = [['name' => '', 'quantity' => 1, 'value_per_item' => 0]];
    }

    if (!is_array($packagesData) || empty($packagesData)) {
        $packagesData = [['weight' => 0, 'quantity' => 1]];
    }
@endphp

<div class="max-w-6xl mx-auto space-y-6">
    <div class="neon-card rounded-2xl p-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs text-slate-400">Tracking</p>
                <p class="font-mono text-violet-300">{{ $shipment->tracking_number }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">AWB</p>
                <p class="font-mono text-slate-300">{{ $shipment->awb_number ?: '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">Invoice</p>
                <p class="font-mono text-slate-300">{{ $shipment->invoice_number ?: '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">Agent</p>
                <p class="text-sm text-gray-200">{{ $shipment->agent?->name ?: '—' }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.shipments.waybill', $shipment) }}" target="_blank"
                    class="text-xs neon-button text-white rounded-lg px-3 py-1.5 transition-colors">Waybill</a>
                <a href="{{ route('admin.shipments.invoice', $shipment) }}" target="_blank"
                    class="text-xs bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg px-3 py-1.5 transition-colors">Invoice</a>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.shipments.update', $shipment) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="neon-card rounded-2xl p-6">
            <h3 class="text-white font-semibold mb-4">Shipment Controls</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Status</label>
                    <select name="status" class="w-full bg-gray-800 border border-gray-700 rounded-xl px-3 py-2.5 text-sm text-white">
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $shipment->status) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Carrier</label>
                    <select name="carrier_id" class="w-full bg-gray-800 border border-gray-700 rounded-xl px-3 py-2.5 text-sm text-white">
                        <option value="">Select carrier</option>
                        @foreach($carriers as $carrier)
                            <option value="{{ $carrier->id }}" @selected((string) old('carrier_id', $shipment->carrier_id) === (string) $carrier->id)>
                                {{ $carrier->name }} ({{ $carrier->currency }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Total Packages</label>
                    <input type="number" min="1" name="total_packages" value="{{ old('total_packages', $shipment->total_packages) }}"
                        class="w-full bg-gray-800 border border-gray-700 rounded-xl px-3 py-2.5 text-sm text-white">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Total Weight (kg)</label>
                    <input type="number" min="0.01" step="0.01" name="total_weight" value="{{ old('total_weight', $shipment->total_weight) }}"
                        class="w-full bg-gray-800 border border-gray-700 rounded-xl px-3 py-2.5 text-sm text-white">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Shipment Type</label>
                    <select name="shipment_type" class="w-full bg-gray-800 border border-gray-700 rounded-xl px-3 py-2.5 text-sm text-white">
                        <option value="document" @selected(old('shipment_type', $shipment->shipment_type) === 'document')>Document</option>
                        <option value="non_document" @selected(old('shipment_type', $shipment->shipment_type) === 'non_document')>Non-document</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs text-slate-400 mb-1">Estimated Delivery</label>
                    <input type="date" name="estimated_delivery" value="{{ old('estimated_delivery', optional($shipment->estimated_delivery)->format('Y-m-d')) }}"
                        class="w-full bg-gray-800 border border-gray-700 rounded-xl px-3 py-2.5 text-sm text-white">
                </div>
                <div class="md:col-span-4">
                    <label class="block text-xs text-slate-400 mb-1">Notes</label>
                    <textarea name="notes" rows="3" class="w-full bg-gray-800 border border-gray-700 rounded-xl px-3 py-2.5 text-sm text-white">{{ old('notes', $shipment->notes) }}</textarea>
                </div>
                <div class="md:col-span-4">
                    <label class="block text-xs text-slate-400 mb-1">Document Description</label>
                    <textarea name="document_description" rows="2" class="w-full bg-gray-800 border border-gray-700 rounded-xl px-3 py-2.5 text-sm text-white">{{ old('document_description', $shipment->document_description) }}</textarea>
                </div>

                {{-- ── Carrier Tracking Number (Dynamic Carrier) ── --}}
                <div class="md:col-span-4">
                    <label class="block text-xs font-semibold text-amber-400 mb-1 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        Carrier Tracking # <span class="text-slate-400 font-normal">(e.g. {{ $shipment->carrier_tracking_provider_name }} waybill – assigned to this EP shipment)</span>
                    </label>
                    <div class="flex gap-2">
                        <input
                            id="carrier_tracking_number"
                            type="text"
                            name="carrier_tracking_number"
                            value="{{ old('carrier_tracking_number', $shipment->carrier_tracking_number) }}"
                            placeholder="Enter actual {{ $shipment->carrier_tracking_provider_name }} / carrier tracking number…"
                            class="flex-1 bg-gray-800 border border-amber-500/40 focus:border-amber-400 rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-amber-400/40 transition"
                        >
                        @if($shipment->carrier_tracking_number)
                        <a href="{{ $shipment->carrier_tracking_url }}"
                           target="_blank"
                           class="flex items-center gap-1.5 text-xs bg-yellow-500 hover:bg-yellow-400 text-slate-100 font-bold rounded-xl px-3 py-2 transition-colors whitespace-nowrap">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            View on {{ $shipment->carrier_tracking_provider_name }}
                        </a>
                        @endif
                    </div>
                    @if($shipment->carrier_tracking_number)
                    <p class="mt-1.5 text-xs text-amber-400/80">
                        ✓ Tracking agents who search <span class="font-mono font-bold">{{ $shipment->tracking_number }}</span>
                        will be redirected to {{ $shipment->carrier_tracking_provider_name }} tracking for <span class="font-mono font-bold">{{ $shipment->carrier_tracking_number }}</span>.
                    </p>
                    @else
                    <p class="mt-1.5 text-xs text-slate-500">
                        When set, searching <span class="font-mono text-violet-300">{{ $shipment->tracking_number }}</span> on ExpressPeak
                        will open the carrier's live tracking page.
                    </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="neon-card rounded-2xl p-6 space-y-3">
                <h3 class="text-white font-semibold mb-2">Sender Details</h3>
                @include('agent.shipments.partials.address-edit', ['prefix' => 'sender', 'source' => $shipment])
            </div>

            <div class="neon-card rounded-2xl p-6 space-y-3">
                <h3 class="text-white font-semibold mb-2">Receiver Details</h3>
                @include('agent.shipments.partials.address-edit', ['prefix' => 'receiver', 'source' => $shipment])
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="neon-card rounded-2xl p-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-white font-semibold">Items</h3>
                    <button type="button" onclick="addEditItemRow()" class="text-xs text-violet-300 border border-violet-500/40 rounded-lg px-3 py-1.5 hover:bg-violet-500/10">+ Add Item</button>
                </div>
                <div id="editItemsRows" class="space-y-3">
                    @foreach($itemsData as $index => $item)
                        <div class="edit-item-row grid grid-cols-12 gap-2 rounded-xl border border-white/10 bg-gray-900/30 p-3">
                            <div class="col-span-12 md:col-span-6">
                                <label class="mb-1 block text-[11px] font-medium text-slate-400">Item Name</label>
                                <input type="text" name="items[{{ $index }}][name]" value="{{ $item['name'] ?? '' }}" placeholder="Item name" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white">
                            </div>
                            <div class="col-span-12 sm:col-span-5 md:col-span-2">
                                <label class="mb-1 block text-[11px] font-medium text-slate-400">Quantity</label>
                                <input type="number" min="1" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] ?? 1 }}" placeholder="Qty" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white">
                            </div>
                            <div class="col-span-12 sm:col-span-5 md:col-span-3">
                                <label class="mb-1 block text-[11px] font-medium text-slate-400">Unit Value (USD)</label>
                                <input type="number" min="0" step="0.01" name="items[{{ $index }}][value_per_item]" value="{{ $item['value_per_item'] ?? 0 }}" placeholder="Value" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white">
                            </div>
                            <div class="col-span-12 sm:col-span-2 md:col-span-1 flex items-end">
                                <button type="button" onclick="removeEditRow(this, '.edit-item-row', addEditItemRow)" class="w-full rounded-lg border border-red-500/40 text-red-300 hover:bg-red-500/10">×</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="neon-card rounded-2xl p-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-white font-semibold">Packages</h3>
                    <button type="button" onclick="addEditPackageRow()" class="text-xs text-violet-300 border border-violet-500/40 rounded-lg px-3 py-1.5 hover:bg-violet-500/10">+ Add Package</button>
                </div>
                <div id="editPackagesRows" class="space-y-3">
                    @foreach($packagesData as $index => $package)
                        <div class="edit-package-row grid grid-cols-12 gap-2 rounded-xl border border-white/10 bg-gray-900/30 p-3">
                            <div class="col-span-12 sm:col-span-5">
                                <label class="mb-1 block text-[11px] font-medium text-slate-400">Weight (kg)</label>
                                <input type="number" min="0.01" step="0.01" name="packages[{{ $index }}][weight]" value="{{ $package['weight'] ?? 0 }}" placeholder="Weight (kg)" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white">
                            </div>
                            <div class="col-span-12 sm:col-span-6">
                                <label class="mb-1 block text-[11px] font-medium text-slate-400">Quantity</label>
                                <input type="number" min="1" name="packages[{{ $index }}][quantity]" value="{{ $package['quantity'] ?? 1 }}" placeholder="Qty" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white">
                            </div>
                            <div class="col-span-12 sm:col-span-1 flex items-end">
                                <button type="button" onclick="removeEditRow(this, '.edit-package-row', addEditPackageRow)" class="w-full rounded-lg border border-red-500/40 text-red-300 hover:bg-red-500/10">×</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="bg-red-900/30 border border-red-500/40 text-red-200 text-sm rounded-xl p-4">
                <p class="font-semibold mb-1">Please fix the highlighted issues:</p>
                <ul class="list-disc ml-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex items-center justify-between gap-3">
            <a href="{{ route('admin.shipments.index') }}" class="text-sm text-slate-400 hover:text-gray-200 border border-gray-700 rounded-xl px-4 py-2.5 transition-colors">Back</a>
            <button type="submit" class="text-sm font-semibold neon-button text-white rounded-xl px-6 py-2.5 transition-colors">Save Shipment</button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
function addEditItemRow() {
    const container = document.getElementById('editItemsRows');
    const index = container.querySelectorAll('.edit-item-row').length;
    const row = document.createElement('div');
    row.className = 'edit-item-row grid grid-cols-12 gap-2 rounded-xl border border-white/10 bg-gray-900/30 p-3';
    row.innerHTML = `
        <div class="col-span-12 md:col-span-6">
            <label class="mb-1 block text-[11px] font-medium text-slate-400">Item Name</label>
            <input type="text" name="items[${index}][name]" placeholder="Item name" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white">
        </div>
        <div class="col-span-12 sm:col-span-5 md:col-span-2">
            <label class="mb-1 block text-[11px] font-medium text-slate-400">Quantity</label>
            <input type="number" min="1" name="items[${index}][quantity]" value="1" placeholder="Qty" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white">
        </div>
        <div class="col-span-12 sm:col-span-5 md:col-span-3">
            <label class="mb-1 block text-[11px] font-medium text-slate-400">Unit Value (USD)</label>
            <input type="number" min="0" step="0.01" name="items[${index}][value_per_item]" value="0" placeholder="Value" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white">
        </div>
        <div class="col-span-12 sm:col-span-2 md:col-span-1 flex items-end">
            <button type="button" onclick="removeEditRow(this, '.edit-item-row', addEditItemRow)" class="w-full rounded-lg border border-red-500/40 text-red-300 hover:bg-red-500/10">×</button>
        </div>
    `;
    container.appendChild(row);
}

function addEditPackageRow() {
    const container = document.getElementById('editPackagesRows');
    const index = container.querySelectorAll('.edit-package-row').length;
    const row = document.createElement('div');
    row.className = 'edit-package-row grid grid-cols-12 gap-2 rounded-xl border border-white/10 bg-gray-900/30 p-3';
    row.innerHTML = `
        <div class="col-span-12 sm:col-span-5">
            <label class="mb-1 block text-[11px] font-medium text-slate-400">Weight (kg)</label>
            <input type="number" min="0.01" step="0.01" name="packages[${index}][weight]" value="0" placeholder="Weight (kg)" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white">
        </div>
        <div class="col-span-12 sm:col-span-6">
            <label class="mb-1 block text-[11px] font-medium text-slate-400">Quantity</label>
            <input type="number" min="1" name="packages[${index}][quantity]" value="1" placeholder="Qty" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white">
        </div>
        <div class="col-span-12 sm:col-span-1 flex items-end">
            <button type="button" onclick="removeEditRow(this, '.edit-package-row', addEditPackageRow)" class="w-full rounded-lg border border-red-500/40 text-red-300 hover:bg-red-500/10">×</button>
        </div>
    `;
    container.appendChild(row);
}

function removeEditRow(button, rowSelector, fallbackAddFn) {
    const container = button.closest('div[id$="Rows"]') || button.parentElement?.parentElement;
    const rows = container ? container.querySelectorAll(rowSelector) : [];
    if (rows.length <= 1) {
        fallbackAddFn && fallbackAddFn();
    }
    button.closest(rowSelector).remove();
    if (container && container.querySelectorAll(rowSelector).length === 0 && fallbackAddFn) {
        fallbackAddFn();
    }
}
</script>
@endpush
