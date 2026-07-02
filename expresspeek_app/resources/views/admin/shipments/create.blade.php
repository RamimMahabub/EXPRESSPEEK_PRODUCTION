@extends('layouts.dashboard')

@section('title', 'Create Shipment')
@section('page-title', 'Create Shipment')
@section('page-subtitle', 'Fill in the details to create a new shipment')

@php($guestMode = $guestMode ?? false)

@section('content')

<div id="shipmentWizard" class="max-w-5xl mx-auto">

    {{-- Progress Bar --}}
    <div class="mb-8">
        <div class="flex items-center justify-between relative">
            <div class="absolute top-5 left-0 right-0 h-0.5 bg-gray-800 z-0"></div>
            <div class="absolute top-5 left-0 h-0.5 bg-violet-600 z-0 transition-all duration-500" id="progressLine" style="width:0%"></div>
            @foreach([
                ['icon'=>'📍','label'=>'From / To'],
                ['icon'=>'📦','label'=>'Shipment Type'],
                ['icon'=>'⚖️','label'=>'Packages'],
                ['icon'=>'🚚','label'=>'Carrier'],
                ['icon'=>'🧾','label'=>'Review & Print'],
            ] as $i => $step)
            <div class="step-indicator relative z-10 flex flex-col items-center gap-2" data-step="{{ $i+1 }}">
                <div class="step-circle w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold border-2 transition-all duration-300
                    {{ $i===0 ? 'bg-violet-600 border-violet-600 text-white' : 'bg-gray-900 border-gray-700 text-slate-400' }}">
                    <span class="step-number">{{ $i+1 }}</span>
                    <svg class="step-check w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
                <span class="text-xs font-medium text-slate-400 step-label {{ $i===0 ? '!text-violet-400' : '' }} whitespace-nowrap">{{ $step['label'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Step Panels --}}
    <form id="createShipmentForm" novalidate>
        @csrf

        {{-- ============================================================ --}}
        {{-- STEP 1: FROM / TO --}}
        {{-- ============================================================ --}}
        <div class="wizard-step active" data-step="1">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 relative">

                {{-- FROM Column --}}
                <div class="neon-card rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-base font-semibold text-white flex items-center gap-2">
                            <span class="w-6 h-6 bg-violet-600 rounded-full flex items-center justify-center text-xs">F</span>
                            From (Sender)
                        </h3>
                    </div>
                    @include('admin.partials.contact-form', ['prefix' => 'sender', 'showAddressBookPicker' => ! $guestMode])
                </div>

                {{-- Switch Button --}}
                <button type="button" id="switchBtn" onclick="switchContacts()"
                    class="hidden lg:flex absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 z-10
                    w-10 h-10 bg-yellow-500 hover:bg-yellow-400 rounded-full items-center justify-center shadow-lg transition-all hover:scale-110">
                    <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </button>
                <button type="button" onclick="switchContacts()"
                    class="lg:hidden mx-auto flex items-center gap-2 bg-yellow-500 hover:bg-yellow-400 text-black font-semibold text-xs rounded-full px-4 py-2 transition-all">
                    ⇅ Switch Sender / Receiver
                </button>

                {{-- TO Column --}}
                <div class="neon-card rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-base font-semibold text-white flex items-center gap-2">
                            <span class="w-6 h-6 bg-emerald-600 rounded-full flex items-center justify-center text-xs">T</span>
                            To (Receiver)
                        </h3>
                    </div>
                    @include('admin.partials.contact-form', ['prefix' => 'receiver', 'showAddressBookPicker' => ! $guestMode])
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- STEP 2: SHIPMENT TYPE --}}
        {{-- ============================================================ --}}
        <div class="wizard-step hidden" data-step="2">
            <div class="neon-card rounded-2xl p-6">
                <h3 class="text-base font-semibold text-white mb-6">What are you shipping?</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <label class="shipment-type-card block cursor-pointer" for="type_document" data-shipment-type-card="document">
                        <input type="radio" id="type_document" name="shipment_type" value="document" class="sr-only" onchange="onShipmentTypeChange()">
                        <div class="rounded-2xl p-6 transition-all duration-300" style="border: 2px solid #e2e8f0; cursor:pointer;" data-shipment-type-panel="document">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
                                <div style="font-size:2.5rem; line-height:1;">📄</div>
                                <div style="width:22px; height:22px; border-radius:50%; border:2px solid #94a3b8; display:flex; align-items:center; justify-content:center; transition:all 0.2s; flex-shrink:0;" data-shipment-type-badge="document">
                                    <svg style="width:12px; height:12px; color:white; opacity:0; transition:opacity 0.2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                            </div>
                            <h4 style="font-weight:600; font-size:1.1rem; margin-bottom:4px;" data-shipment-type-title="document">Document</h4>
                            <p style="font-size:0.875rem; color:#94a3b8; margin:0;">Letters, contracts, certificates, papers</p>
                            <p style="font-size:0.75rem; color:#a78bfa; margin-top:10px; display:none;" data-shipment-type-note="document">This shipment will be created as a document.</p>
                        </div>
                    </label>
                    <label class="shipment-type-card block cursor-pointer" for="type_non_document" data-shipment-type-card="non_document">
                        <input type="radio" id="type_non_document" name="shipment_type" value="non_document" class="sr-only" onchange="onShipmentTypeChange()" checked>
                        <div class="rounded-2xl p-6 transition-all duration-300" style="border: 2px solid #e2e8f0; cursor:pointer;" data-shipment-type-panel="non_document">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
                                <div style="font-size:2.5rem; line-height:1;">📦</div>
                                <div style="width:22px; height:22px; border-radius:50%; border:2px solid #94a3b8; display:flex; align-items:center; justify-content:center; transition:all 0.2s; flex-shrink:0;" data-shipment-type-badge="non_document">
                                    <svg style="width:12px; height:12px; color:white; opacity:0; transition:opacity 0.2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                            </div>
                            <h4 style="font-weight:600; font-size:1.1rem; margin-bottom:4px;" data-shipment-type-title="non_document">Non-Document</h4>
                            <p style="font-size:0.875rem; color:#94a3b8; margin:0;">Goods, merchandise, gifts, samples</p>
                            <p style="font-size:0.75rem; color:#a78bfa; margin-top:10px; display:none;" data-shipment-type-note="non_document">This shipment will be created as a non-document parcel.</p>
                        </div>
                    </label>
                </div>

                {{-- Document description (optional) --}}
                <div id="docDescSection" class="hidden mb-6">
                    <label class="block text-sm font-medium text-slate-400 mb-2">Document Description <span class="text-slate-500">(optional)</span></label>
                    <textarea name="document_description" rows="3" placeholder="E.g. Commercial Invoice, Legal Agreement..."
                        class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500 transition-colors"></textarea>
                </div>

                {{-- Non-document items --}}
                <div id="itemsSection" class="">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-sm font-semibold text-white">Item Details</h4>
                    </div>
                    <div id="itemsList" class="space-y-3">
                        {{-- First item row --}}
                    </div>
                    <button type="button" onclick="addItem()"
                        class="mt-4 flex items-center gap-2 text-sm text-violet-400 hover:text-violet-300 border border-violet-600/40 hover:border-violet-500 rounded-xl px-4 py-2.5 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Another Item
                    </button>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- STEP 3: PACKAGES --}}
        {{-- ============================================================ --}}
        <div class="wizard-step hidden" data-step="3">
            <div class="neon-card rounded-2xl p-6">
                <h3 class="text-base font-semibold text-white mb-6">Package Details</h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm mb-4">
                        <thead>
                            <tr class="text-left border-b border-white/10">
                                <th class="pb-3 text-xs font-medium text-slate-400 w-8">#</th>
                                <th class="pb-3 text-xs font-medium text-slate-400">Weight (kg)</th>
                                <th class="pb-3 text-xs font-medium text-slate-400">Quantity</th>
                                <th class="pb-3 text-xs font-medium text-slate-400 w-10"></th>
                            </tr>
                        </thead>
                        <tbody id="packagesList" class="divide-y divide-gray-800/50">
                            {{-- rows added by JS --}}
                        </tbody>
                    </table>
                </div>

                <button type="button" onclick="addPackage()"
                    class="flex items-center gap-2 text-sm text-violet-400 hover:text-violet-300 border border-violet-600/40 hover:border-violet-500 rounded-xl px-4 py-2.5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Another Package
                </button>

                {{-- Totals --}}
                <div class="mt-6 pt-5 border-t border-white/10 grid grid-cols-2 gap-4">
                    <div class="bg-gray-800/60 rounded-xl p-4 text-center">
                        <p class="text-xs text-slate-400 mb-1">Total Packages</p>
                        <p class="text-2xl font-bold text-white" id="totalPackagesDisplay">0</p>
                    </div>
                    <div class="bg-gray-800/60 rounded-xl p-4 text-center">
                        <p class="text-xs text-slate-400 mb-1">Total Weight</p>
                        <p class="text-2xl font-bold text-white"><span id="totalWeightDisplay">0.00</span> <span class="text-sm font-normal text-slate-400">kg</span></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- STEP 4: SELECT CARRIER --}}
        {{-- ============================================================ --}}
        <div class="wizard-step hidden" data-step="4">
            <div class="neon-card rounded-2xl p-6">
                <h3 class="text-base font-semibold text-white mb-2">Select Service Provider</h3>
                <p class="text-sm text-slate-400 mb-6">Available carriers for destination: <span id="destinationLabel" class="text-violet-300 font-medium"></span></p>

                <div id="carriersLoading" class="flex items-center justify-center py-12 text-slate-500">
                    <svg class="animate-spin w-6 h-6 mr-2 text-violet-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Loading carriers...
                </div>

                <div id="carriersList" class="grid grid-cols-1 sm:grid-cols-2 gap-4 hidden">
                    {{-- populated by JS --}}
                </div>

                <div id="carriersEmpty" class="hidden text-center py-12 text-slate-500">
                    <p class="text-sm">No carriers available for this destination.</p>
                </div>

                <input type="hidden" name="carrier_id" id="selectedCarrierId" value="{{ request('carrier_id') }}">
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- STEP 5: REVIEW & PRINT --}}
        {{-- ============================================================ --}}
        <div class="wizard-step hidden" data-step="5">
            <div class="neon-card rounded-2xl p-6">
                <div id="reviewLoading" class="flex items-center justify-center py-12 text-slate-500">
                    <svg class="animate-spin w-6 h-6 mr-2 text-violet-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Creating shipment...
                </div>

                <div id="reviewContent" class="hidden">
                    {{-- Success Banner --}}
                    <div class="flex items-center gap-3 bg-emerald-900/30 border border-emerald-500/40 text-emerald-300 px-5 py-4 rounded-xl mb-6">
                        <svg class="w-6 h-6 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="font-semibold text-sm">Shipment Created Successfully!</p>
                            <p class="text-xs text-emerald-400 mt-0.5">Tracking: <span id="reviewTracking" class="font-mono font-bold"></span> | AWB: <span id="reviewAwb" class="font-mono font-bold"></span></p>
                        </div>
                    </div>

                    {{-- Summary Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="bg-gray-800/50 rounded-xl p-4">
                            <p class="text-xs text-slate-400 mb-3 font-medium uppercase tracking-wider">Sender</p>
                            <div id="reviewSender" class="text-sm text-slate-300 space-y-1"></div>
                        </div>
                        <div class="bg-gray-800/50 rounded-xl p-4">
                            <p class="text-xs text-slate-400 mb-3 font-medium uppercase tracking-wider">Receiver</p>
                            <div id="reviewReceiver" class="text-sm text-slate-300 space-y-1"></div>
                        </div>
                        <div class="bg-gray-800/50 rounded-xl p-4">
                            <p class="text-xs text-slate-400 mb-3 font-medium uppercase tracking-wider">Shipment</p>
                            <div id="reviewShipment" class="text-sm text-slate-300 space-y-1"></div>
                        </div>
                        <div class="bg-gray-800/50 rounded-xl p-4">
                            <p class="text-xs text-slate-400 mb-3 font-medium uppercase tracking-wider">Packages</p>
                            <div id="reviewPackages" class="text-sm text-slate-300 space-y-1"></div>
                        </div>
                    </div>

                    {{-- Print Buttons --}}
                    <div class="flex flex-wrap gap-3 mb-6">
                        <a id="waybillBtn" href="#" target="_blank"
                            class="flex items-center gap-2 neon-button text-white font-semibold text-sm rounded-xl px-5 py-3 transition-all hover:scale-105">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Print Waybill
                        </a>
                        <a id="invoiceBtn" href="#" target="_blank"
                            class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-sm rounded-xl px-5 py-3 transition-all hover:scale-105">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Print Invoice
                        </a>
                    </div>

                    <div class="flex gap-3">
                        <button type="button" onclick="resetWizard()"
                            class="flex items-center gap-2 text-sm text-violet-400 hover:text-violet-300 border border-violet-600/40 hover:border-violet-500 rounded-xl px-4 py-2.5 transition-all">
                            + Create Another Shipment
                        </button>
                        <a href="{{ route('agent.dashboard') }}"
                            class="flex items-center gap-2 text-sm text-slate-400 hover:text-gray-200 border border-gray-700 hover:border-gray-600 rounded-xl px-4 py-2.5 transition-all">
                            ← Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <div class="flex items-center justify-between mt-6" id="wizardNav">
            <button type="button" id="prevBtn" onclick="prevStep()"
                class="hidden flex items-center gap-2 text-sm text-slate-400 hover:text-gray-200 border border-gray-700 hover:border-gray-600 rounded-xl px-5 py-2.5 transition-all">
                ← Previous
            </button>
            <div class="flex-1"></div>
            <button type="button" id="nextBtn" onclick="nextStep()"
                class="flex items-center gap-2 neon-button text-white font-semibold text-sm rounded-xl px-6 py-2.5 transition-all hover:scale-105">
                Next Step →
            </button>
        </div>
    </form>
</div>

<div id="addressBookPickerModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/40 backdrop-blur-sm px-4 py-6">
    <div class="w-full max-w-3xl rounded-3xl border border-slate-200 bg-white shadow-2xl shadow-slate-200/50">
        <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-6 py-5">
            <div>
                <h3 class="text-lg font-semibold text-slate-900">Select From Address Book</h3>
                <p class="mt-1 text-sm text-slate-500">Choose a saved contact to auto-fill sender or receiver details.</p>
            </div>
            <button type="button" onclick="closeAddressBookPicker()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
            <input id="addressBookPickerSearch" type="text" placeholder="Search name, company, city, country..."
                oninput="filterAddressBookPicker(this.value)"
                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition-all focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20">
        </div>

        <div class="max-h-[65vh] overflow-y-auto px-6 py-5">
            <div id="addressBookPickerList" class="grid grid-cols-1 gap-3 sm:grid-cols-2"></div>
            <div id="addressBookPickerEmpty" class="hidden rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-6 py-12 text-center text-sm text-slate-500">
                No saved addresses found.
            </div>
            <div id="addressBookPickerLoading" class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-6 py-12 text-center text-sm text-slate-500">
                Loading saved addresses...
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ============================================================
// STATE
// ============================================================
let currentStep = 1;
const totalSteps = 5;
let createdShipment = null;
let addressBookEntries = [];
let activeAddressBookPrefix = 'sender';
let addressBookPickerInitialized = false;

// Helper: always get fresh CSRF token from meta tag
function getCSRF() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        || document.querySelector('[name="_token"]')?.value
        || '';
}

function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#39;');
}

function getAddressBookEntries() {
    if (addressBookEntries.length) {
        return Promise.resolve(addressBookEntries);
    }

    return fetch('{{ route("admin.address-book.index", [], false) }}', {
        credentials: 'same-origin',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(response => {
        if (!response.ok) throw new Error('HTTP ' + response.status);
        return response.json();
    })
    .then(entries => {
        addressBookEntries = Array.isArray(entries) ? entries : [];
        return addressBookEntries;
    });
}

function openAddressBookPicker(prefix) {
    activeAddressBookPrefix = prefix;
    const modal = document.getElementById('addressBookPickerModal');
    const loading = document.getElementById('addressBookPickerLoading');
    const empty = document.getElementById('addressBookPickerEmpty');
    const list = document.getElementById('addressBookPickerList');

    if (!modal || !loading || !empty || !list) return;

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    loading.classList.remove('hidden');
    empty.classList.add('hidden');
    list.innerHTML = '';
    document.getElementById('addressBookPickerSearch').value = '';

    getAddressBookEntries()
        .then(entries => renderAddressBookPicker(entries))
        .catch(error => {
            console.error('Address book picker load error:', error);
            list.innerHTML = '';
            empty.textContent = 'Failed to load saved addresses. Please try again.';
            empty.classList.remove('hidden');
        })
        .finally(() => {
            loading.classList.add('hidden');
        });
}

function closeAddressBookPicker() {
    const modal = document.getElementById('addressBookPickerModal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function filterAddressBookPicker(query) {
    const term = query.toLowerCase().trim();
    const filtered = addressBookEntries.filter(entry =>
        (entry.name || '').toLowerCase().includes(term) ||
        (entry.company || '').toLowerCase().includes(term) ||
        (entry.city || '').toLowerCase().includes(term) ||
        (entry.country_name || '').toLowerCase().includes(term)
    );

    renderAddressBookPicker(filtered);
}

function renderAddressBookPicker(entries) {
    const list = document.getElementById('addressBookPickerList');
    const empty = document.getElementById('addressBookPickerEmpty');

    if (!list || !empty) return;

    if (!entries.length) {
        list.innerHTML = '';
        empty.classList.remove('hidden');
        return;
    }

    empty.classList.add('hidden');
    list.innerHTML = entries.map(entry => `
        <button type="button" onclick="useAddressBookEntry(${entry.id})"
            class="group rounded-2xl border border-slate-200 bg-white p-4 text-left transition-all hover:border-violet-300 hover:shadow-md hover:shadow-violet-100/50 focus:outline-none focus:ring-2 focus:ring-violet-500/20">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold text-slate-900 transition-colors group-hover:text-violet-700">${escapeHtml(entry.name || 'Unnamed')}</p>
                    ${entry.company ? `<p class="mt-0.5 truncate text-xs text-slate-500">${escapeHtml(entry.company)}</p>` : ''}
                    <p class="mt-1 flex items-center gap-1 text-xs text-slate-500">
                        <svg class="h-3 w-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.242-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        ${escapeHtml(entry.city || '')}${entry.country_name ? `, ${escapeHtml(entry.country_name)}` : ''}
                    </p>
                </div>
                <span class="shrink-0 rounded-full border border-violet-200 bg-violet-50 px-2.5 py-1 text-[10px] font-semibold text-violet-600 transition-all group-hover:bg-violet-600 group-hover:text-white">Use</span>
            </div>
        </button>
    `).join('');
}

function useAddressBookEntry(id) {
    const entry = addressBookEntries.find(item => String(item.id) === String(id));
    if (!entry) return;

    const prefix = activeAddressBookPrefix;
    const setValue = (field, value) => {
        const element = document.querySelector(`[name="${prefix}_${field}"]`);
        if (!element) return;
        element.value = value ?? '';
        element.dispatchEvent(new Event('input', { bubbles: true }));
        element.dispatchEvent(new Event('change', { bubbles: true }));
    };

    setValue('name', entry.name);
    setValue('company', entry.company);
    setValue('country', entry.country_name || '');
    setValue('country_code', entry.country_code || '');
    setValue('address', entry.address);
    setValue('address2', entry.address2);
    setValue('address3', entry.address3);
    setValue('postal_code', entry.postal_code);
    setValue('city', entry.city);
    setValue('state', entry.state);
    setValue('email', entry.email);
    setValue('phone_type', entry.phone_type || 'Office');
    setValue('phone_code', entry.phone_code);
    setValue('phone', entry.phone);

    const businessToggle = document.querySelector(`[name="${prefix}_is_business"]`);
    if (businessToggle) {
        businessToggle.checked = !!entry.is_business;
        businessToggle.dispatchEvent(new Event('change', { bubbles: true }));
    }

    const modal = document.getElementById('addressBookPickerModal');
    if (modal) {
        closeAddressBookPicker();
    }

    showToast(`Loaded ${entry.name || 'address'} into ${prefix === 'sender' ? 'sender' : 'receiver'}`);
}

// ============================================================
// WIZARD NAVIGATION
// ============================================================
function showStep(step) {
    document.querySelectorAll('.wizard-step').forEach(el => el.classList.add('hidden'));
    document.querySelector(`.wizard-step[data-step="${step}"]`).classList.remove('hidden');

    // Update indicators
    document.querySelectorAll('.step-indicator').forEach((el, idx) => {
        const circle = el.querySelector('.step-circle');
        const num = el.querySelector('.step-number');
        const check = el.querySelector('.step-check');
        const label = el.querySelector('.step-label');
        const s = idx + 1;

        circle.className = circle.className.replace(/bg-\w+-\d+|border-\w+-\d+|text-\w+-\d+/g, '').trim();
        if (s < step) {
            circle.classList.add('bg-violet-600', 'border-violet-600', 'text-white');
            num.classList.add('hidden'); check.classList.remove('hidden');
            label.style.color = '#a78bfa';
        } else if (s === step) {
            circle.classList.add('bg-violet-600', 'border-violet-600', 'text-white');
            num.classList.remove('hidden'); check.classList.add('hidden');
            label.style.color = '#a78bfa';
        } else {
            circle.classList.add('bg-gray-900', 'border-gray-700', 'text-slate-400');
            num.classList.remove('hidden'); check.classList.add('hidden');
            label.style.color = '';
        }
    });

    // Progress line
    const pct = step > 1 ? ((step - 1) / (totalSteps - 1)) * 100 : 0;
    document.getElementById('progressLine').style.width = pct + '%';

    // Prev/Next buttons
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const wizNav  = document.getElementById('wizardNav');

    if (step === 1) { prevBtn.classList.add('hidden'); } else { prevBtn.classList.remove('hidden'); }

    if (step === totalSteps) {
        wizNav.classList.add('hidden');
    } else {
        wizNav.classList.remove('hidden');
        nextBtn.textContent = step === 4 ? 'Create Shipment →' : 'Next Step →';
    }
}

function nextStep() {
    if (!validateStep(currentStep)) return;

    if (currentStep === 4) {
        // Submit form
        submitShipment();
        return;
    }

    if (currentStep === 3) {
        // Load carriers before showing step 4
        loadCarriers();
    }

    currentStep++;
    showStep(currentStep);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function prevStep() {
    if (currentStep > 1) {
        currentStep--;
        showStep(currentStep);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function resetWizard() {
    currentStep = 1;
    document.getElementById('createShipmentForm').reset();
    createdShipment = null;
    // Reset items & packages
    document.getElementById('itemsList').innerHTML = '';
    document.getElementById('packagesList').innerHTML = '';
    addItem();
    addPackage();
    showStep(1);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ============================================================
// STEP VALIDATION
// ============================================================
function validateStep(step) {
    if (step === 1) {
        const required = ['sender_name','sender_country','sender_address','sender_city',
                          'receiver_name','receiver_country','receiver_address','receiver_city'];
        for (const f of required) {
            const el = document.querySelector(`[name="${f}"]`);
            if (!el || !el.value.trim()) {
                showFieldError(el, `${f.replace(/_/g,' ')} is required`);
                el && el.focus();
                return false;
            }
        }
    }
    if (step === 2) {
        const type = document.querySelector('[name="shipment_type"]:checked');
        if (!type) { alert('Please select a shipment type.'); return false; }
        if (type.value === 'non_document') {
            const rows = document.querySelectorAll('.item-row');
            for (const row of rows) {
                const name = row.querySelector('[data-field="name"]');
                if (!name || !name.value.trim()) {
                    name && name.focus();
                    showFieldError(name, 'Item name is required');
                    return false;
                }
            }
        }
    }
    if (step === 3) {
        const rows = document.querySelectorAll('.package-row');
        if (rows.length === 0) { alert('Add at least one package.'); return false; }
        for (const row of rows) {
            const w = row.querySelector('[data-field="weight"]');
            if (!w || parseFloat(w.value) <= 0) {
                w && w.focus();
                showFieldError(w, 'Weight required');
                return false;
            }
        }
    }
    if (step === 4) {
        const carrier = document.getElementById('selectedCarrierId').value;
        if (!carrier) { alert('Please select a carrier.'); return false; }
    }
    return true;
}

function showFieldError(el, msg) {
    if (!el) return;
    el.classList.add('border-red-500');
    let err = el.parentNode.querySelector('.field-error');
    if (!err) {
        err = document.createElement('p');
        err.className = 'field-error text-xs text-red-400 mt-1';
        el.parentNode.appendChild(err);
    }
    err.textContent = msg;
    setTimeout(() => { el.classList.remove('border-red-500'); err.remove(); }, 3000);
}

// ============================================================
// SHIPMENT TYPE TOGGLE
// ============================================================
function onShipmentTypeChange() {
    const val = document.querySelector('[name="shipment_type"]:checked')?.value;
    document.getElementById('docDescSection').classList.toggle('hidden', val !== 'document');
    document.getElementById('itemsSection').classList.toggle('hidden', val !== 'non_document');
    updateShipmentTypeSelectionStyles();
}

function updateShipmentTypeSelectionStyles() {
    const selected = document.querySelector('[name="shipment_type"]:checked')?.value || '';

    document.querySelectorAll('[data-shipment-type-card]').forEach(card => {
        const type = card.dataset.shipmentTypeCard;
        const badge = card.querySelector(`[data-shipment-type-badge="${type}"]`);
        const note = card.querySelector(`[data-shipment-type-note="${type}"]`);
        const panel = card.querySelector(`[data-shipment-type-panel="${type}"]`);
        const title = card.querySelector(`[data-shipment-type-title="${type}"]`);
        const isSelected = selected === type;

        card.setAttribute('aria-pressed', String(isSelected));

        // Badge circle
        if (badge) {
            if (isSelected) {
                badge.style.borderColor = '#7c3aed';
                badge.style.backgroundColor = '#7c3aed';
                badge.querySelector('svg').style.opacity = '1';
            } else {
                badge.style.borderColor = '#94a3b8';
                badge.style.backgroundColor = 'transparent';
                badge.querySelector('svg').style.opacity = '0';
            }
        }

        // Note text
        if (note) note.style.display = isSelected ? 'block' : 'none';

        // Card panel
        if (panel) {
            if (isSelected) {
                panel.style.borderColor = '#7c3aed';
                panel.style.boxShadow = '0 0 0 3px rgba(124,58,237,0.12), 0 8px 24px rgba(124,58,237,0.12)';
                panel.style.background = 'linear-gradient(135deg, rgba(124,58,237,0.05) 0%, rgba(139,92,246,0.08) 100%)';
                panel.style.transform = 'translateY(-2px)';
                if (title) title.style.color = '#7c3aed';
            } else {
                panel.style.borderColor = '#e2e8f0';
                panel.style.boxShadow = '';
                panel.style.background = '';
                panel.style.transform = '';
                if (title) title.style.color = '';
            }
        }
    });
}

// ============================================================
// ITEMS (Step 2 - Non Document)
// ============================================================
let itemCount = 0;

function addItem(data = {}) {
    itemCount++;
    const idx = itemCount;
    const row = document.createElement('div');
    row.className = 'item-row bg-gray-800/50 border border-gray-700/50 rounded-xl p-4';
    row.dataset.idx = idx;
    row.innerHTML = `
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Item ${idx}</span>
            <div class="flex gap-2">
                <button type="button" onclick="copyItem(${idx})" class="text-xs text-blue-400 hover:text-blue-300 border border-blue-600/30 rounded-lg px-2.5 py-1 transition-all">Copy</button>
                <button type="button" onclick="removeItem(this)" class="text-xs text-red-400 hover:text-red-300 border border-red-600/30 rounded-lg px-2.5 py-1 transition-all">Remove</button>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-1">
                <label class="block text-xs text-slate-400 mb-1">Item Name *</label>
                <input type="text" name="items[${idx}][name]" data-field="name" value="${data.name||''}"
                    placeholder="e.g. Cotton Shirt"
                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500">
            </div>
            <div>
                <label class="block text-xs text-slate-400 mb-1">Quantity *</label>
                <input type="number" name="items[${idx}][quantity]" data-field="quantity" value="${data.quantity||1}" min="1"
                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-violet-500">
            </div>
            <div>
                <label class="block text-xs text-slate-400 mb-1">Value per Item (USD) *</label>
                <input type="number" name="items[${idx}][value_per_item]" data-field="value_per_item" value="${data.value_per_item||''}" min="0" step="0.01"
                    placeholder="0.00"
                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500">
            </div>
        </div>`;
    document.getElementById('itemsList').appendChild(row);
}

function removeItem(btn) {
    const rows = document.querySelectorAll('.item-row');
    if (rows.length <= 1) { alert('At least one item is required.'); return; }
    btn.closest('.item-row').remove();
}

function copyItem(idx) {
    const row = document.querySelector(`.item-row[data-idx="${idx}"]`);
    if (!row) return;
    const data = {
        name: row.querySelector('[data-field="name"]').value,
        quantity: row.querySelector('[data-field="quantity"]').value,
        value_per_item: row.querySelector('[data-field="value_per_item"]').value,
    };
    addItem(data);
}

// ============================================================
// PACKAGES (Step 3)
// ============================================================
let packageCount = 0;

function addPackage(data = {}) {
    packageCount++;
    const idx = packageCount;
    const tbody = document.getElementById('packagesList');
    const tr = document.createElement('tr');
    tr.className = 'package-row';
    tr.dataset.idx = idx;
    tr.innerHTML = `
        <td class="py-3 text-slate-400 text-xs pr-2">${idx}</td>
        <td class="py-3 pr-3">
            <input type="number" name="packages[${idx}][weight]" data-field="weight"
                value="${data.weight||''}" min="0.01" step="0.01" placeholder="0.00"
                oninput="updatePackageTotals()"
                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500">
        </td>
        <td class="py-3 pr-3">
            <input type="number" name="packages[${idx}][quantity]" data-field="quantity"
                value="${data.quantity||1}" min="1"
                oninput="updatePackageTotals()"
                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-violet-500">
        </td>
        <td class="py-3">
            <button type="button" onclick="removePackage(this)"
                class="w-7 h-7 rounded-lg bg-red-900/30 hover:bg-red-900/60 text-red-400 flex items-center justify-center transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </td>`;
    tbody.appendChild(tr);
    updatePackageTotals();
}

function removePackage(btn) {
    const rows = document.querySelectorAll('.package-row');
    if (rows.length <= 1) { alert('At least one package is required.'); return; }
    btn.closest('.package-row').remove();
    updatePackageTotals();
}

function updatePackageTotals() {
    let totalPkgs = 0, totalWt = 0;
    document.querySelectorAll('.package-row').forEach(row => {
        const w = parseFloat(row.querySelector('[data-field="weight"]').value) || 0;
        const q = parseInt(row.querySelector('[data-field="quantity"]').value) || 0;
        totalPkgs += q;
        totalWt   += w * q;
    });
    document.getElementById('totalPackagesDisplay').textContent = totalPkgs;
    document.getElementById('totalWeightDisplay').textContent   = totalWt.toFixed(2);
}

// ============================================================
// CARRIERS (Step 4)
// ============================================================
let selectedCarrierId = '';

function hydrateWizardFromQuery() {
    const params = new URLSearchParams(window.location.search);
    const carrierId = params.get('carrier_id');
    const country = params.get('country');
    const shipmentType = params.get('type');
    const weight = params.get('weight');

    if (carrierId) {
        selectedCarrierId = String(carrierId);
        const carrierInput = document.getElementById('selectedCarrierId');
        if (carrierInput) {
            carrierInput.value = selectedCarrierId;
        }
    }

    if (country) {
        const receiverCountry = document.querySelector('[name="receiver_country"]');
        if (receiverCountry && !receiverCountry.value) {
            receiverCountry.value = country;
            receiverCountry.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }

    if (shipmentType) {
        const typeInput = document.querySelector(`[name="shipment_type"][value="${shipmentType}"]`);
        if (typeInput) {
            typeInput.checked = true;
        }
    }

    if (weight) {
        const firstWeight = document.querySelector('.package-row [data-field="weight"]');
        if (firstWeight && !firstWeight.value) {
            firstWeight.value = weight;
            updatePackageTotals();
        }
    }
}

function loadCarriers() {
    const country = document.querySelector('[name="receiver_country"]')?.value || '';
    const code    = document.querySelector('[name="receiver_country_code"]')?.value || '';
    const shipmentType = document.querySelector('[name="shipment_type"]:checked')?.value || 'non_document';
    const totalWeight = parseFloat(document.getElementById('totalWeightDisplay')?.textContent || '0') || 0;
    selectedCarrierId = document.getElementById('selectedCarrierId')?.value || '';
    document.getElementById('destinationLabel').textContent = country;

    const loading  = document.getElementById('carriersLoading');
    const list     = document.getElementById('carriersList');
    const empty    = document.getElementById('carriersEmpty');
    loading.classList.remove('hidden');
    list.classList.add('hidden');
    empty.classList.add('hidden');

    fetch('{{ route("admin.carriers.available", [], false) }}?' + new URLSearchParams({country_code: (code || country), country: country, weight: (totalWeight || 1), type: shipmentType}).toString(), {
        credentials: 'same-origin',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
    })
    .then(carriers => {
        loading.classList.add('hidden');
        if (!carriers.length) { empty.classList.remove('hidden'); return; }

        list.innerHTML = '';
        carriers.forEach(c => {
            const card = document.createElement('label');
            card.className = 'carrier-card block cursor-pointer';
            card.dataset.carrierCard = c.id;
            card.setAttribute('aria-pressed', String(String(selectedCarrierId) === String(c.id)));
            card.innerHTML = `
                <input type="radio" name="_carrier_select" value="${c.id}" class="sr-only" ${String(selectedCarrierId) === String(c.id) ? 'checked' : ''} onchange="selectCarrier(${c.id})">
                <div class="rounded-2xl p-5 transition-all duration-300" style="${String(selectedCarrierId) === String(c.id) ? 'border: 2px solid #7c3aed; box-shadow: 0 0 0 3px rgba(124,58,237,0.12), 0 8px 24px rgba(124,58,237,0.12); background: linear-gradient(135deg, rgba(124,58,237,0.05) 0%, rgba(139,92,246,0.08) 100%); transform: translateY(-2px);' : 'border: 2px solid #e2e8f0; background: #ffffff; cursor: pointer;'}" data-carrier-panel="${c.id}">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 12px;">
                        <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
                            <div style="width: 20px; height: 20px; flex-shrink: 0; border-radius: 50%; border: 2px solid ${String(selectedCarrierId) === String(c.id) ? '#7c3aed' : '#94a3b8'}; background-color: ${String(selectedCarrierId) === String(c.id) ? '#7c3aed' : 'transparent'}; display: flex; align-items: center; justify-content: center; transition: all 0.2s;" data-carrier-selected-badge="${c.id}">
                                <svg style="width: 10px; height: 10px; color: white; opacity: ${String(selectedCarrierId) === String(c.id) ? '1' : '0'}; transition: opacity 0.2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div style="min-width: 0;">
                            <h4 style="font-weight: 600; font-size: 0.875rem; color: ${String(selectedCarrierId) === String(c.id) ? '#7c3aed' : '#334155'}; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" data-carrier-title="${c.id}">${c.name}</h4>
                            <p style="font-size: 0.6875rem; color: #a78bfa; margin-top: 4px; display: ${String(selectedCarrierId) === String(c.id) ? 'block' : 'none'};" data-carrier-selected-text="${c.id}">This carrier is selected</p>
                        </div>
                        <span style="flex-shrink: 0; font-size: 0.75rem; border-radius: 0.5rem; padding: 4px 8px; font-weight: 500; ${String(selectedCarrierId) === String(c.id) ? 'background-color: rgba(139,92,246,0.15); color: #8b5cf6;' : 'background-color: #f1f5f9; color: #64748b'};" data-carrier-currency="${c.id}">${c.currency}</span>
                    </div>
                    <p style="font-size: 0.75rem; color: #94a3b8; margin: 0;">Estimated rate: ${Number(c.price).toFixed(2)} ${c.currency}</p>
                </div>`;
            list.appendChild(card);
        });
        updateCarrierSelectionStyles();
        list.classList.remove('hidden');
    })
    .catch(err => { console.error('Carrier load error:', err); loading.classList.add('hidden'); empty.classList.remove('hidden'); });
}

function selectCarrier(id) {
    selectedCarrierId = String(id);
    document.getElementById('selectedCarrierId').value = selectedCarrierId;
    updateCarrierSelectionStyles();
    showToast('Selected ' + (document.querySelector(`.carrier-card[data-carrier-card="${id}"] h4`)?.textContent || 'carrier'));
}

function updateCarrierSelectionStyles() {
    document.querySelectorAll('[data-carrier-card]').forEach(card => {
        const id = String(card.dataset.carrierCard);
        const isSelected = String(selectedCarrierId) === id;
        const badge = card.querySelector(`[data-carrier-selected-badge="${id}"]`);
        const selectedText = card.querySelector(`[data-carrier-selected-text="${id}"]`);
        const panel = card.querySelector(`[data-carrier-panel="${id}"]`);
        const title = card.querySelector(`[data-carrier-title="${id}"]`);
        const currencyBadge = card.querySelector(`[data-carrier-currency="${id}"]`);

        card.setAttribute('aria-pressed', String(isSelected));
        
        // Badge Circle
        if (badge) {
            if (isSelected) {
                badge.style.borderColor = '#7c3aed';
                badge.style.backgroundColor = '#7c3aed';
                badge.querySelector('svg').style.opacity = '1';
            } else {
                badge.style.borderColor = '#94a3b8';
                badge.style.backgroundColor = 'transparent';
                badge.querySelector('svg').style.opacity = '0';
            }
        }
        
        // Selected Note
        if (selectedText) selectedText.style.display = isSelected ? 'block' : 'none';
        
        // Card Panel
        if (panel) {
            if (isSelected) {
                panel.style.borderColor = '#7c3aed';
                panel.style.boxShadow = '0 0 0 3px rgba(124,58,237,0.12), 0 8px 24px rgba(124,58,237,0.12)';
                panel.style.background = 'linear-gradient(135deg, rgba(124,58,237,0.05) 0%, rgba(139,92,246,0.08) 100%)';
                panel.style.transform = 'translateY(-2px)';
                if (title) title.style.color = '#7c3aed';
                if (currencyBadge) {
                    currencyBadge.style.backgroundColor = 'rgba(139,92,246,0.15)';
                    currencyBadge.style.color = '#8b5cf6';
                }
            } else {
                panel.style.borderColor = '#e2e8f0';
                panel.style.boxShadow = '';
                panel.style.background = '#ffffff';
                panel.style.transform = '';
                if (title) title.style.color = '#334155';
                if (currencyBadge) {
                    currencyBadge.style.backgroundColor = '#f1f5f9';
                    currencyBadge.style.color = '#64748b';
                }
            }
        }
    });
}

// ============================================================
// SUBMIT
// ============================================================
function submitShipment() {
    const form = document.getElementById('createShipmentForm');
    const fd   = new FormData(form);

    // Add receiver_country_code from hidden field
    const rcc = document.querySelector('[name="receiver_country_code"]')?.value;
    if (rcc) fd.set('receiver_country_code', rcc);

    currentStep = 5;
    showStep(5);
    document.getElementById('reviewLoading').classList.remove('hidden');
    document.getElementById('reviewContent').classList.add('hidden');

    fetch('{{ route("admin.shipments.store", [], false) }}', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'X-CSRF-TOKEN': getCSRF(),
            'Accept': 'application/json'
        },
        body: fd,
    })
    .then(r => {
        if (!r.ok) return r.json().then(e => Promise.reject(e));
        return r.json();
    })
    .then(data => {
        document.getElementById('reviewLoading').classList.add('hidden');
        if (data.success) {
            createdShipment = data.shipment;
            document.getElementById('reviewContent').classList.remove('hidden');
            document.getElementById('reviewTracking').textContent = data.shipment.tracking_number;
            document.getElementById('reviewAwb').textContent      = data.shipment.awb_number;
            const waybillBtn = document.getElementById('waybillBtn');
            const invoiceBtn = document.getElementById('invoiceBtn');
            if (data.waybill_url) {
                waybillBtn.href = data.waybill_url;
                waybillBtn.classList.remove('hidden');
            } else {
                waybillBtn.classList.add('hidden');
            }
            if (data.invoice_url) {
                invoiceBtn.href = data.invoice_url;
                invoiceBtn.classList.remove('hidden');
            } else {
                invoiceBtn.classList.add('hidden');
            }
            populateReview(data.shipment, fd);
        } else {
            alert('Error: ' + (data.message || 'Unknown error'));
            currentStep = 4; showStep(4);
        }
    })
    .catch(err => {
        document.getElementById('reviewLoading').classList.add('hidden');
        const msg = err?.message || JSON.stringify(err) || 'Unknown error';
        alert('Error creating shipment: ' + msg);
        currentStep = 4; showStep(4);
    });
}

function populateReview(shipment, fd) {
    const g = (k) => shipment[k] || fd.get(k) || '—';

    document.getElementById('reviewSender').innerHTML = `
        <p class="font-medium text-white">${g('sender_name')}</p>
        ${g('sender_company') !== '—' ? `<p class="text-slate-400">${g('sender_company')}</p>` : ''}
        <p>${g('sender_address')}</p>
        <p>${g('sender_city')}, ${g('sender_country')}</p>
        <p class="text-slate-400">${g('sender_phone')}</p>`;

    document.getElementById('reviewReceiver').innerHTML = `
        <p class="font-medium text-white">${g('receiver_name')}</p>
        ${g('receiver_company') !== '—' ? `<p class="text-slate-400">${g('receiver_company')}</p>` : ''}
        <p>${g('receiver_address')}</p>
        <p>${g('receiver_city')}, ${g('receiver_country')}</p>
        <p class="text-slate-400">${g('receiver_phone')}</p>`;

    document.getElementById('reviewShipment').innerHTML = `
        <p>Type: <span class="text-white">${(g('shipment_type')||'').replace('_',' ')}</span></p>
        <p>Carrier: <span class="text-white">${g('carrier_name')}</span></p>
        <p>Status: <span class="text-yellow-400">Pending</span></p>`;

    document.getElementById('reviewPackages').innerHTML = `
        <p>Total Packages: <span class="text-white">${g('total_packages')}</span></p>
        <p>Total Weight: <span class="text-white">${g('total_weight')} kg</span></p>`;
}

// ============================================================
// SWITCH CONTACTS
// ============================================================
function switchContacts() {
    const getVal = (name) => document.querySelector(`[name="${name}"]`)?.value || '';
    const setVal = (name, val) => { const el = document.querySelector(`[name="${name}"]`); if (el) el.value = val; };

    const fields = ['name','company','country','country_code','address','address2','address3',
                    'postal_code','city','state','email','phone_type','phone_code','phone','is_business'];

    const sData = {}, rData = {};
    fields.forEach(f => {
        sData[f] = getVal(`sender_${f}`);
        rData[f] = getVal(`receiver_${f}`);
    });
    fields.forEach(f => {
        setVal(`sender_${f}`, rData[f]);
        setVal(`receiver_${f}`, sData[f]);
    });
}

// ============================================================
// SAVE TO ADDRESS BOOK
// ============================================================
function saveToAddressBook(prefix) {
    const g = (f) => document.querySelector(`[name="${prefix}_${f}"]`)?.value || '';
    const saveButton = document.querySelector(`[data-save-address-book-button="${prefix}"]`);
    const status = document.getElementById(`${prefix}_save_status`);
    const data = {
        name: g('name'), company: g('company'), country_code: g('country_code'),
        country_name: g('country'), address: g('address'), address2: g('address2'),
        address3: g('address3'), postal_code: g('postal_code'), city: g('city'),
        state: g('state'), email: g('email'), phone_type: g('phone_type'),
        phone_code: g('phone_code'), phone: g('phone'),
        is_business: document.querySelector(`[name="${prefix}_is_business"]`)?.checked ? 1 : 0,
    };
    if (!data.name || !data.city) { alert('Name and City are required to save.'); return; }

    setAddressBookSaveState(prefix, 'Saving address...', true, 'text-slate-400');

    fetch('{{ route("admin.address-book.store", [], false) }}', {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCSRF(), 'Accept': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(async response => {
        const payload = await response.json().catch(() => ({}));

        if (!response.ok) {
            throw payload;
        }

        return payload;
    })
    .then(payload => {
        if (payload.success) {
            if (payload.duplicate) {
                showToast('Address already saved');
                setAddressBookSaveState(prefix, 'This address is already in your book.', false, 'text-amber-400');
                return;
            }

            showToast('Address saved to book ✓');
            setAddressBookSaveState(prefix, 'Saved successfully.', false, 'text-emerald-400');
        }
    })
    .catch(err => {
        console.error('Save address error:', err);
        setAddressBookSaveState(prefix, err?.message || 'Failed to save address', false, 'text-red-400');
        showToast(err?.message || 'Failed to save address');
    })
    .finally(() => {
        if (saveButton) {
            saveButton.disabled = false;
            const label = saveButton.querySelector('span');
            if (label) label.textContent = 'Save to Address Book';
        }
        if (status && !status.textContent) {
            status.classList.add('hidden');
        }
    });
}

function setAddressBookSaveState(prefix, message, busy, colorClass) {
    const saveButton = document.querySelector(`[data-save-address-book-button="${prefix}"]`);
    const label = saveButton?.querySelector('span');
    const status = document.getElementById(`${prefix}_save_status`);

    if (saveButton) {
        saveButton.disabled = busy;
        if (label) {
            label.textContent = busy ? 'Saving...' : 'Save to Address Book';
        }
    }

    if (status) {
        status.textContent = message;
        status.className = `text-xs leading-5 ${colorClass || 'text-slate-400'}`;
        status.classList.remove('hidden');
    }
}

function showToast(msg) {
    const t = document.createElement('div');
    t.className = 'fixed bottom-6 right-6 z-50 bg-emerald-700 text-white text-sm font-medium py-3 px-5 rounded-xl shadow-xl animate-fade-in';
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 3000);
}

// ============================================================
// INIT
// ============================================================
document.addEventListener('DOMContentLoaded', () => {
    showStep(1);
    addItem();
    addPackage();
    hydrateWizardFromQuery();
    // Initial type state
    const checked = document.querySelector('[name="shipment_type"]:checked');
    if (checked) onShipmentTypeChange();
});

document.getElementById('addressBookPickerModal')?.addEventListener('click', (event) => {
    if (event.target.id === 'addressBookPickerModal') {
        closeAddressBookPicker();
    }
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        closeAddressBookPicker();
    }
});
</script>
@endpush
