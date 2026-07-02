@extends('layouts.customer')

@section('title', 'Create Shipment')

@section('content')
<section class="max-w-7xl mx-auto px-6 py-14">
    @if(session('success'))
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-8 flex flex-col gap-4 rounded-3xl border border-white/10 bg-white p-6 shadow-sm md:flex-row md:items-center md:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-violet-600">Shipping Quote Engine</p>
            <h1 class="mt-2 text-3xl font-black text-slate-100">Create Shipment</h1>
            <p class="mt-2 text-sm text-slate-400">Complete the shipment details and continue without login, or sign in to attach it to your account.</p>
        </div>

        <div class="flex flex-wrap gap-3">
            @guest
                <a href="{{ route('login', ['next' => request()->fullUrl()]) }}" class="rounded-xl border border-violet-200 px-4 py-2.5 text-sm font-semibold text-violet-700 hover:bg-violet-50">Log in</a>
                <a href="{{ route('register', ['next' => request()->fullUrl()]) }}" class="rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-violet-700">Create Account</a>
            @endguest
            <a href="#shipment-form" class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">Continue as guest</a>
        </div>
    </div>

    @auth
        <div class="mb-6 rounded-2xl border border-blue-200 bg-blue-50 px-5 py-4 text-sm text-blue-800">
            You are creating this shipment as <span class="font-semibold">{{ auth()->user()->name }}</span>.
        </div>
    @endauth

    <form id="shipment-form" action="{{ route('shipment.store') }}" method="POST" class="space-y-8">
        @csrf

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-3xl border border-white/10 bg-white p-6 shadow-sm">
                <div class="mb-5 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-bold text-slate-100">Sender</h2>
                        <p class="text-sm text-slate-400">The customer name is saved on the shipment.</p>
                    </div>
                    <span class="rounded-full bg-violet-50 px-3 py-1 text-xs font-semibold text-violet-700">From</span>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">Full Name</label>
                        <input type="text" name="sender_name" value="{{ old('sender_name', $senderName) }}" @auth readonly @endauth class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-slate-100 outline-none transition focus:border-violet-400 focus:ring-2 focus:ring-violet-100 {{ auth()->check() ? 'bg-gray-50' : 'bg-white' }}" placeholder="Sender name">
                    </div>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">Country</label>
                            <input type="text" name="sender_country" value="{{ old('sender_country') }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-violet-400 focus:ring-2 focus:ring-violet-100" placeholder="Bangladesh">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">City</label>
                            <input type="text" name="sender_city" value="{{ old('sender_city') }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-violet-400 focus:ring-2 focus:ring-violet-100" placeholder="Dhaka">
                        </div>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">Address</label>
                        <input type="text" name="sender_address" value="{{ old('sender_address') }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-violet-400 focus:ring-2 focus:ring-violet-100" placeholder="Street address">
                    </div>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">Email</label>
                            <input type="email" name="sender_email" value="{{ old('sender_email') }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-violet-400 focus:ring-2 focus:ring-violet-100" placeholder="sender@example.com">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">Phone</label>
                            <input type="text" name="sender_phone" value="{{ old('sender_phone') }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-violet-400 focus:ring-2 focus:ring-violet-100" placeholder="01XXXXXXXXX">
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white p-6 shadow-sm">
                <div class="mb-5 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-bold text-slate-100">Receiver</h2>
                        <p class="text-sm text-slate-400">Destination details for the shipment.</p>
                    </div>
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">To</span>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">Full Name</label>
                        <input type="text" name="receiver_name" value="{{ old('receiver_name') }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-violet-400 focus:ring-2 focus:ring-violet-100" placeholder="Receiver name">
                    </div>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">Country</label>
                            <input type="text" name="receiver_country" value="{{ old('receiver_country', $destinationCountry) }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-violet-400 focus:ring-2 focus:ring-violet-100" placeholder="United States">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">City</label>
                            <input type="text" name="receiver_city" value="{{ old('receiver_city') }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-violet-400 focus:ring-2 focus:ring-violet-100" placeholder="New York">
                        </div>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">Address</label>
                        <input type="text" name="receiver_address" value="{{ old('receiver_address') }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-violet-400 focus:ring-2 focus:ring-violet-100" placeholder="Street address">
                    </div>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">Email</label>
                            <input type="email" name="receiver_email" value="{{ old('receiver_email') }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-violet-400 focus:ring-2 focus:ring-violet-100" placeholder="receiver@example.com">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">Phone</label>
                            <input type="text" name="receiver_phone" value="{{ old('receiver_phone') }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-violet-400 focus:ring-2 focus:ring-violet-100" placeholder="01XXXXXXXXX">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-3xl border border-white/10 bg-white p-6 shadow-sm">
                <h2 class="text-xl font-bold text-slate-100">Shipment Details</h2>
                <p class="mt-2 text-sm text-slate-400">Choose the shipment type and enter package information.</p>

                <div class="mt-5 space-y-4">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">Shipment Type</label>
                        <select id="shipment_type" name="shipment_type" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-violet-400 focus:ring-2 focus:ring-violet-100">
                            <option value="document" @selected(old('shipment_type', $shipmentType) === 'document')>Document</option>
                            <option value="non_document" @selected(old('shipment_type', $shipmentType) !== 'document')>Non-document</option>
                        </select>
                    </div>

                    <div id="document-description-wrap" class="hidden">
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">Document Description</label>
                        <textarea name="document_description" rows="3" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-violet-400 focus:ring-2 focus:ring-violet-100" placeholder="Commercial documents, contract, etc.">{{ old('document_description') }}</textarea>
                    </div>

                    <div id="items-wrap" class="space-y-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">Item Name</label>
                            <input type="text" name="items[0][name]" value="{{ old('items.0.name') }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-violet-400 focus:ring-2 focus:ring-violet-100" placeholder="Item name">
                        </div>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">Quantity</label>
                                <input type="number" min="1" name="items[0][quantity]" value="{{ old('items.0.quantity', 1) }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-violet-400 focus:ring-2 focus:ring-violet-100">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">Value per Item</label>
                                <input type="number" min="0" step="0.01" name="items[0][value_per_item]" value="{{ old('items.0.value_per_item', 0) }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-violet-400 focus:ring-2 focus:ring-violet-100">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">Package Weight (kg)</label>
                            <input type="number" min="0.01" step="0.5" name="packages[0][weight]" value="{{ old('packages.0.weight', $weight) }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-violet-400 focus:ring-2 focus:ring-violet-100">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">Quantity</label>
                            <input type="number" min="1" name="packages[0][quantity]" value="{{ old('packages.0.quantity', 1) }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-violet-400 focus:ring-2 focus:ring-violet-100">
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white p-6 shadow-sm">
                <div class="mb-5">
                    <h2 class="text-xl font-bold text-slate-100">Carrier</h2>
                    <p class="mt-2 text-sm text-slate-400">Choose the carrier from your quote results.</p>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    @if(!empty($quotes['options']))
                        @foreach($quotes['options'] as $option)
                            <label class="cursor-pointer rounded-2xl border p-4 transition-colors {{ (int) ($option['carrier_id'] ?? 0) === (int) $selectedCarrierId ? 'border-violet-400 bg-violet-50' : 'border-gray-200 hover:border-violet-300 hover:bg-violet-50/50' }}">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <input type="radio" name="carrier_id" value="{{ $option['carrier_id'] }}" @checked((int) ($option['carrier_id'] ?? 0) === (int) $selectedCarrierId) class="text-violet-600 focus:ring-violet-500">
                                            <span class="text-sm font-semibold text-slate-100">{{ $option['carrier'] }}</span>
                                            @if(($option['carrier_id'] ?? 0) === ($quotes['recommended']['carrier_id'] ?? null))
                                                <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold uppercase text-amber-700">Recommended</span>
                                            @endif
                                        </div>
                                        <p class="mt-2 text-xs text-slate-400">Rate: {{ number_format((float) $option['total_price'], 2) }} {{ $option['currency'] }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-black text-slate-100">{{ number_format((float) $option['total_price'], 2) }}</p>
                                        <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">{{ $option['currency'] }}</p>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    @elseif($selectedCarrier)
                        <label class="rounded-2xl border border-violet-300 bg-violet-50 p-4">
                            <div class="flex items-center gap-2">
                                <input type="radio" name="carrier_id" value="{{ $selectedCarrier->id }}" checked class="text-violet-600 focus:ring-violet-500">
                                <span class="text-sm font-semibold text-slate-100">{{ $selectedCarrier->name }}</span>
                                <span class="rounded-full bg-violet-100 px-2 py-0.5 text-[10px] font-bold uppercase text-violet-700">Selected</span>
                            </div>
                        </label>
                    @else
                        <div class="rounded-2xl border border-dashed border-gray-200 p-5 text-sm text-slate-400">
                            No carrier selected yet. Go back to the quote engine and pick a carrier.
                        </div>
                    @endif
                </div>

                <input type="hidden" name="sender_country_code" value="{{ old('sender_country_code') }}">
                <input type="hidden" name="receiver_country_code" value="{{ old('receiver_country_code', $destinationCountry) }}">

                <div class="mt-6 flex items-center justify-between gap-4 rounded-2xl bg-gray-50 px-5 py-4 text-sm text-slate-500">
                    <span>Guest checkout is allowed.</span>
                    <span class="font-semibold text-slate-100">Logged-in users are linked automatically.</span>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="rounded-2xl bg-gradient-to-r from-violet-600 to-blue-700 px-8 py-4 text-sm font-bold text-white shadow-lg shadow-violet-500/20 hover:opacity-95">
                Create Shipment
            </button>
        </div>
    </form>
</section>

@push('scripts')
<script>
    const shipmentTypeSelect = document.getElementById('shipment_type');
    const itemsWrap = document.getElementById('items-wrap');
    const documentDescriptionWrap = document.getElementById('document-description-wrap');

    function syncShipmentTypeFields() {
        const documentMode = shipmentTypeSelect.value === 'document';
        itemsWrap.classList.toggle('hidden', documentMode);
        documentDescriptionWrap.classList.toggle('hidden', !documentMode);
    }

    shipmentTypeSelect.addEventListener('change', syncShipmentTypeFields);
    syncShipmentTypeFields();
</script>
@endpush
@endsection