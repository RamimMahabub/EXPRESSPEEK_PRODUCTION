@extends('layouts.dashboard')

@section('title', 'Edit Shipment')
@section('page-title', 'Edit Shipment')
@section('page-subtitle', 'View and update all shipment information')

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
            <div class="flex gap-2">
                @if(\Illuminate\Support\Facades\Route::has('agent.shipments.waybill'))
                    <a href="{{ route('agent.shipments.waybill', $shipment) }}" target="_blank"
                        class="text-xs neon-button text-white rounded-lg px-3 py-1.5 transition-colors">Waybill</a>
                @endif
                <a href="{{ route('agent.shipments.invoice', $shipment) }}" target="_blank"
                    class="text-xs bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg px-3 py-1.5 transition-colors">Invoice</a>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('agent.shipments.update', $shipment) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <input type="hidden" name="status" value="{{ old('status', $shipment->status) }}">
        <input type="hidden" name="carrier_id" value="{{ old('carrier_id', $shipment->carrier_id) }}">
        <input type="hidden" name="total_packages" value="{{ old('total_packages', $shipment->total_packages) }}">
        <input type="hidden" name="total_weight" value="{{ old('total_weight', $shipment->total_weight) }}">
        <input type="hidden" name="shipment_type" value="{{ old('shipment_type', $shipment->shipment_type) }}">
        <input type="hidden" name="estimated_delivery" value="{{ old('estimated_delivery', optional($shipment->estimated_delivery)->format('Y-m-d')) }}">
        <input type="hidden" name="notes" value="{{ old('notes', $shipment->notes) }}">
        <input type="hidden" name="document_description" value="{{ old('document_description', $shipment->document_description) }}">

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
                @error('items')<p class="text-xs text-red-400 mt-2">{{ $message }}</p>@enderror
                @error('items.*.name')<p class="text-xs text-red-400 mt-2">{{ $message }}</p>@enderror
                @error('items.*.quantity')<p class="text-xs text-red-400 mt-2">{{ $message }}</p>@enderror
                @error('items.*.value_per_item')<p class="text-xs text-red-400 mt-2">{{ $message }}</p>@enderror
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
                @error('packages')<p class="text-xs text-red-400 mt-2">{{ $message }}</p>@enderror
                @error('packages.*.weight')<p class="text-xs text-red-400 mt-2">{{ $message }}</p>@enderror
                @error('packages.*.quantity')<p class="text-xs text-red-400 mt-2">{{ $message }}</p>@enderror
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
            <a href="{{ route('agent.dashboard') }}" class="text-sm text-slate-400 hover:text-gray-200 border border-gray-700 rounded-xl px-4 py-2.5 transition-colors">Back</a>
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
