@extends('layouts.operations-dashboard')

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
    @if(session('success'))
        <div class="flex flex-wrap items-center gap-3 rounded-2xl border border-emerald-700/50 bg-emerald-900/30 px-5 py-4 text-emerald-400">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
            @if(session('label_ready'))
                <a href="{{ session('label_ready') }}" target="_blank" class="ml-auto inline-flex items-center gap-1.5 rounded-lg bg-emerald-500 px-3 py-2 text-xs font-extrabold text-white shadow transition hover:bg-emerald-400">
                    Print 75 × 100 mm label
                </a>
            @endif
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 rounded-2xl border border-red-700/50 bg-red-900/30 px-5 py-4 text-red-300">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.3 3.8 2.5 18a2 2 0 001.8 3h15.4a2 2 0 001.8-3L13.7 3.8a2 2 0 00-3.4 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="neon-card rounded-2xl p-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs text-slate-400">Tracking</p>
                <p class="font-mono text-violet-300">{{ $shipment->tracking_number }}</p>
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
                @if($shipment->carrier_tracking_number || $shipment->status === 'connected_to_carrier')
                    <a href="{{ route('admin.shipments.label', $shipment) }}"
                        onclick="openPrintWindow(this.href); return false;"
                        class="text-xs bg-fuchsia-600 hover:bg-fuchsia-500 text-white rounded-lg px-3 py-1.5 transition-colors">Print Label</a>
                @endif
                <a href="{{ route('admin.shipments.waybill', $shipment) }}"
                    onclick="openPrintWindow(this.href); return false;"
                    class="text-xs neon-button text-white rounded-lg px-3 py-1.5 transition-colors">Waybill</a>
                <a href="{{ route('admin.shipments.invoice', $shipment) }}"
                    onclick="openPrintWindow(this.href); return false;"
                    class="text-xs bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg px-3 py-1.5 transition-colors">{{ $shipment->shipment_purpose === 'Commercial' ? 'Commercial' : 'Proforma' }} Invoice</a>
                <a href="{{ route('admin.shipments.invoice.create', $shipment) }}"
                    class="text-xs bg-blue-600 hover:bg-blue-500 text-white rounded-lg px-3 py-1.5 transition-colors">Create Invoice</a>
            </div>
            <script>
            function openPrintWindow(url) {
                var w = 900, h = 700;
                var left = Math.round((screen.width - w) / 2);
                var top  = Math.round((screen.height - h) / 2);
                window.open(url, '_blank', 'width=' + w + ',height=' + h + ',left=' + left + ',top=' + top + ',menubar=no,toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes');
            }
            </script>
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
                        Carrier Tracking Number <span class="text-slate-400 font-normal">(assigned by {{ $shipment->carrier_tracking_provider_name }} to this EP shipment)</span>
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
                        When set, searching <span class="font-mono text-violet-300">{{ $shipment->tracking_number }}</span> on ExpressPeek
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
                            <div class="col-span-6 md:col-span-2">
                                <label class="mb-1 block text-[11px] font-medium text-slate-400">Actual weight (kg)</label>
                                <input type="number" min="0.01" step="0.01" name="packages[{{ $index }}][weight]" value="{{ $package['actual_weight'] ?? $package['weight'] ?? 0 }}" placeholder="Weight" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white">
                            </div>
                            <div class="col-span-6 md:col-span-2">
                                <label class="mb-1 block text-[11px] font-medium text-slate-400">Length (cm)</label>
                                <input type="number" min="0.01" step="0.01" name="packages[{{ $index }}][length]" value="{{ $package['length'] ?? '' }}" placeholder="Length" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white">
                            </div>
                            <div class="col-span-6 md:col-span-2">
                                <label class="mb-1 block text-[11px] font-medium text-slate-400">Width (cm)</label>
                                <input type="number" min="0.01" step="0.01" name="packages[{{ $index }}][width]" value="{{ $package['width'] ?? '' }}" placeholder="Width" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white">
                            </div>
                            <div class="col-span-6 md:col-span-2">
                                <label class="mb-1 block text-[11px] font-medium text-slate-400">Height (cm)</label>
                                <input type="number" min="0.01" step="0.01" name="packages[{{ $index }}][height]" value="{{ $package['height'] ?? '' }}" placeholder="Height" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white">
                            </div>
                            <div class="col-span-6 md:col-span-2">
                                <label class="mb-1 block text-[11px] font-medium text-slate-400">Quantity</label>
                                <input type="number" min="1" name="packages[{{ $index }}][quantity]" value="{{ $package['quantity'] ?? 1 }}" placeholder="Qty" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white">
                            </div>
                            <div class="col-span-6 md:col-span-2 flex items-end">
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

    <div class="rounded-2xl border border-blue-900/40 bg-gray-900 p-6">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <h3 class="flex items-center gap-2 text-sm font-bold text-white">
                <svg class="h-4 w-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Generated Invoices
                <span class="ml-1 rounded-full bg-gray-800 px-2 py-0.5 text-xs text-slate-300">{{ $shipment->invoices->count() }}</span>
            </h3>
            @if($invoiceSystemReady)
                <a href="{{ route('admin.shipments.invoice.create', $shipment) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600/20 px-3 py-1.5 text-xs font-bold text-blue-400 transition-colors hover:bg-blue-600/30">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Create Invoice
                </a>
            @endif
        </div>

        @if(!$invoiceSystemReady)
            <div class="rounded-xl border border-amber-500/30 bg-amber-500/10 p-4">
                <p class="text-sm font-bold text-amber-300">Invoice database update required</p>
                <p class="mt-1 text-xs leading-5 text-amber-200/80">Shipment editing remains available. Run the pending secure database update to enable generated invoices.</p>
                <a href="{{ route('admin.system-update') }}" class="mt-3 inline-flex rounded-lg bg-amber-500 px-3 py-2 text-xs font-bold text-gray-950 transition-colors hover:bg-amber-400">Open System Update</a>
            </div>
        @else
        @forelse($shipment->invoices as $invoice)
            <div class="mb-3 flex flex-wrap items-center justify-between gap-4 rounded-xl border border-gray-700 bg-gray-800 p-4 last:mb-0">
                <div>
                    <div class="mb-1 flex items-center gap-2">
                        <p class="font-mono text-sm font-bold text-white">{{ $invoice->invoice_number }}</p>
                        <span class="rounded-md border px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide
                            {{ $invoice->status === 'paid' ? 'border-emerald-500/30 bg-emerald-500/20 text-emerald-400' : ($invoice->status === 'cancelled' ? 'border-red-500/30 bg-red-500/20 text-red-400' : 'border-amber-500/30 bg-amber-500/20 text-amber-400') }}">
                            {{ $invoice->status_label }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-400">{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }} · {{ $invoice->created_at->format('M d, Y') }}</p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('admin.shipment-invoices.download', $invoice) }}" target="_blank" class="invoice-download-button" aria-label="Download invoice {{ $invoice->invoice_number }} as PDF">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Download PDF
                    </a>
                    @if($invoice->status === 'unpaid')
                        <form action="{{ route('admin.shipment-invoices.pay', $invoice) }}" method="POST" onsubmit="return confirm('Mark this invoice as paid?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-600/30 bg-emerald-600/20 px-3 py-1.5 text-xs font-bold text-emerald-400 transition-colors hover:bg-emerald-600/30">
                                Mark as Paid
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('admin.shipment-invoices.destroy', $invoice) }}" method="POST" onsubmit="return confirm('Delete this invoice entirely?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-lg bg-red-900/30 p-1.5 text-red-400 transition-colors hover:bg-red-900/50" aria-label="Delete invoice">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-gray-700 bg-gray-800/50 py-6 text-center">
                <p class="text-xs text-slate-400">No freight invoices generated yet.</p>
            </div>
        @endforelse
        @endif
    </div>
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
        <div class="col-span-6 md:col-span-2">
            <label class="mb-1 block text-[11px] font-medium text-slate-400">Actual weight (kg)</label>
            <input type="number" min="0.01" step="0.01" name="packages[${index}][weight]" placeholder="Weight" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white">
        </div>
        <div class="col-span-6 md:col-span-2">
            <label class="mb-1 block text-[11px] font-medium text-slate-400">Length (cm)</label>
            <input type="number" min="0.01" step="0.01" name="packages[${index}][length]" placeholder="Length" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white">
        </div>
        <div class="col-span-6 md:col-span-2">
            <label class="mb-1 block text-[11px] font-medium text-slate-400">Width (cm)</label>
            <input type="number" min="0.01" step="0.01" name="packages[${index}][width]" placeholder="Width" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white">
        </div>
        <div class="col-span-6 md:col-span-2">
            <label class="mb-1 block text-[11px] font-medium text-slate-400">Height (cm)</label>
            <input type="number" min="0.01" step="0.01" name="packages[${index}][height]" placeholder="Height" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white">
        </div>
        <div class="col-span-6 md:col-span-2">
            <label class="mb-1 block text-[11px] font-medium text-slate-400">Quantity</label>
            <input type="number" min="1" name="packages[${index}][quantity]" value="1" placeholder="Qty" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white">
        </div>
        <div class="col-span-6 md:col-span-2 flex items-end">
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
