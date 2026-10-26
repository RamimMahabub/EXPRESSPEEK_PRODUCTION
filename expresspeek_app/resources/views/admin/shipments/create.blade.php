@extends('layouts.operations-dashboard')

@section('title', 'Create Shipment')
@section('page-title', 'Create Shipment')
@section('page-subtitle', 'Fill in the details to create a new shipment')

@php($guestMode = $guestMode ?? false)

@section('content')

<div id="shipmentWizard" class="premium-wizard ap-shell max-w-5xl mx-auto">

    <section class="ap-page-head wizard-hero">
        <div class="ap-head-copy">
            <span class="ap-eyebrow">Guided shipment builder</span>
            <h1>Create a shipment with confidence</h1>
            <p>Move from contact details to carrier selection in five focused steps. Your shipment is only created after review.</p>
        </div>
        <div class="wizard-secure"><svg viewBox="0 0 24 24"><path d="M7 10V7a5 5 0 0 1 10 0v3"/><rect x="4" y="10" width="16" height="11" rx="3"/><path d="M12 14v3"/></svg><span><strong>Secure workflow</strong><small>Details saved only on submit</small></span></div>
    </section>

    {{-- Progress Bar --}}
    <div class="wizard-progress-card mb-8">
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
            <div class="step-indicator relative z-10 flex flex-col items-center gap-2 cursor-pointer transition-transform hover:scale-105" data-step="{{ $i+1 }}" onclick="goToStep({{ $i+1 }})">
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

                <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-6">
                    <label class="shipment-type-card block cursor-pointer" for="type_document" data-shipment-type-card="document">
                        <input type="radio" id="type_document" name="shipment_type" value="document" class="sr-only" onchange="onShipmentTypeChange()">
                        <div class="rounded-2xl p-4 sm:p-6 transition-all duration-300" style="border: 2px solid #e2e8f0; cursor:pointer;" data-shipment-type-panel="document">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
                                <div style="font-size:2rem; line-height:1;" class="sm:text-[2.5rem]">📄</div>
                                <div style="width:22px; height:22px; border-radius:50%; border:2px solid #94a3b8; display:flex; align-items:center; justify-content:center; transition:all 0.2s; flex-shrink:0;" data-shipment-type-badge="document">
                                    <svg style="width:12px; height:12px; color:white; opacity:0; transition:opacity 0.2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                            </div>
                            <h4 style="font-weight:600; font-size:1rem; margin-bottom:4px;" class="sm:text-[1.1rem]" data-shipment-type-title="document">Document</h4>
                            <p style="font-size:0.75rem; color:#94a3b8; margin:0;" class="sm:text-[0.875rem]">Letters, contracts, certificates</p>
                            <p style="font-size:0.7rem; color:#a78bfa; margin-top:10px; display:none;" data-shipment-type-note="document">Created as a document.</p>
                        </div>
                    </label>
                    <label class="shipment-type-card block cursor-pointer" for="type_non_document" data-shipment-type-card="non_document">
                        <input type="radio" id="type_non_document" name="shipment_type" value="non_document" class="sr-only" onchange="onShipmentTypeChange()" checked>
                        <div class="rounded-2xl p-4 sm:p-6 transition-all duration-300" style="border: 2px solid #e2e8f0; cursor:pointer;" data-shipment-type-panel="non_document">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
                                <div style="font-size:2rem; line-height:1;" class="sm:text-[2.5rem]">📦</div>
                                <div style="width:22px; height:22px; border-radius:50%; border:2px solid #94a3b8; display:flex; align-items:center; justify-content:center; transition:all 0.2s; flex-shrink:0;" data-shipment-type-badge="non_document">
                                    <svg style="width:12px; height:12px; color:white; opacity:0; transition:opacity 0.2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                            </div>
                            <h4 style="font-weight:600; font-size:1rem; margin-bottom:4px;" class="sm:text-[1.1rem]" data-shipment-type-title="non_document">Non-Document</h4>
                            <p style="font-size:0.75rem; color:#94a3b8; margin:0;" class="sm:text-[0.875rem]">Goods, merchandise, gifts</p>
                            <p style="font-size:0.7rem; color:#a78bfa; margin-top:10px; display:none;" data-shipment-type-note="non_document">Created as a parcel.</p>
                        </div>
                    </label>
                </div>

                {{-- Document description (required) --}}
                <div id="docDescSection" class="hidden mb-6">
                    <label class="block text-sm font-medium text-slate-400 mb-2">Document Description <span class="text-red-400">*</span></label>
                    <textarea name="document_description" rows="3" placeholder="E.g. Documents, Commercial Invoice, Legal Agreement..."
                        class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500 transition-colors"></textarea>
                </div>

                {{-- Non-document items --}}
                <div id="itemsSection" class="">
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-400 mb-2">
                            What is the purpose of your shipment? 
                            <span class="inline-block text-violet-400 ml-1 cursor-help" title="Select the main purpose of your shipment for customs declarations">
                                <svg class="w-4 h-4 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                            </span>
                        </label>
                        <select name="shipment_purpose" required class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-violet-500 transition-colors appearance-none" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394A3B8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;">
                            <option value="" disabled selected>Select purpose...</option>
                            <option value="Gift">Gift</option>
                            <option value="Commercial">Commercial</option>
                            <option value="Personal, Not for Resale">Personal, Not for Resale</option>
                            <option value="Sample">Sample</option>
                            <option value="Return for Repair">Return for Repair</option>
                            <option value="Return after Repair">Return after Repair</option>
                            <option value="Return to Seller">Return to Seller</option>
                        </select>
                    </div>

                    <div class="mb-6" x-data="{ showRemarks: false }">
                        <label class="flex items-center gap-2 cursor-pointer mb-2">
                            <input type="checkbox" x-model="showRemarks" class="rounded border-gray-700 bg-gray-900 text-violet-600 focus:ring-violet-500">
                            <span class="text-sm font-medium text-slate-400">Add Remarks <span class="text-slate-500">(optional)</span></span>
                        </label>
                        <div x-show="showRemarks" x-transition.opacity>
                            <textarea name="remarks" rows="2" placeholder="e.g. SAMPLE OF NO COMMERCIAL VALUE, THIS VALUE CUSTOMS PURPOSE ONLY."
                                class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500 transition-colors"></textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-sm font-semibold text-white">Item Details</h4>
                    </div>
                    <div id="itemsListContainer" x-data="{
                        items: [],
                        editingId: null,
                        init() {
                            this.addItem(); // add first item
                        },
                        addItem() {
                            const id = Date.now().toString();
                            this.items.push({ id, name: '', quantity: '', value: '' });
                            this.editingId = id;
                        },
                        editItem(id) {
                            this.editingId = id;
                        },
                        removeItem(id) {
                            if (this.items.length > 1) {
                                this.items = this.items.filter(i => i.id !== id);
                            }
                        },
                        copyItem(item) {
                            const id = Date.now().toString();
                            this.items.push({ id, name: item.name, quantity: item.quantity, value: item.value });
                        },
                        closeItem() {
                            this.editingId = null;
                        }
                    }">
                        <div class="space-y-3">
                            <template x-for="(item, index) in items" :key="item.id">
                                <div class="item-row rounded-xl border border-white/10 bg-gray-900/30 overflow-hidden transition-all shadow-sm">
                                    
                                    <!-- SUMMARY VIEW (Collapsed) -->
                                    <div x-show="editingId !== item.id" class="p-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                        <div class="flex-1 flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 text-sm text-slate-300">
                                            <div class="flex items-start gap-2 min-w-0">
                                                <span class="font-bold text-slate-500 whitespace-nowrap" x-text="(index + 1) + '.'"></span>
                                                <span class="font-semibold text-white truncate" x-text="item.name || 'Unnamed Item'"></span>
                                            </div>
                                            <div class="hidden sm:block w-px h-4 bg-gray-700"></div>
                                            <div class="flex items-center gap-4 text-slate-400">
                                                <span>Qty: <span class="font-mono text-white" x-text="item.quantity"></span></span>
                                                <span>Value: <span class="font-mono text-white" x-text="item.value"></span> <span class="text-xs">USD</span></span>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center gap-2 self-start md:self-auto shrink-0">
                                            <button type="button" @click="editItem(item.id)" class="text-xs font-semibold text-violet-400 hover:text-violet-300 bg-violet-400/10 hover:bg-violet-400/20 px-3 py-1.5 rounded-lg transition-colors">Edit</button>
                                            <button type="button" @click="copyItem(item)" class="text-xs font-semibold text-blue-400 hover:text-blue-300 bg-blue-400/10 hover:bg-blue-400/20 px-3 py-1.5 rounded-lg transition-colors">Copy</button>
                                            <button type="button" x-show="items.length > 1" @click="removeItem(item.id)" class="text-xs font-semibold text-red-400 hover:text-red-300 bg-red-400/10 hover:bg-red-400/20 px-3 py-1.5 rounded-lg transition-colors">Remove</button>
                                        </div>
                                    </div>

                                    <!-- EDIT VIEW (Expanded) -->
                                    <div x-show="editingId === item.id" x-transition.opacity class="p-5 bg-gray-800/50 border-l-2 border-violet-500">
                                        <div class="flex items-center justify-between mb-4">
                                            <h4 class="text-sm font-bold text-white flex items-center gap-2">
                                                <span class="w-6 h-6 rounded-full bg-violet-600/30 text-violet-400 flex items-center justify-center text-xs" x-text="index + 1"></span>
                                                Item Description
                                            </h4>
                                        </div>

                                        <div class="grid grid-cols-12 gap-4">
                                            <div class="col-span-12 md:col-span-6">
                                                <label class="mb-1.5 block text-[11px] font-semibold text-slate-400 uppercase tracking-wider">What is the item?</label>
                                                <input type="text" data-field="name" :name="'items['+index+'][name]'" x-model="item.name" placeholder="E.g. RICE SKIN CARE CREAM GEL" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition-all">
                                            </div>
                                            <div class="col-span-12 sm:col-span-6 md:col-span-3">
                                                <label class="mb-1.5 block text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Quantity</label>
                                                <input type="number" data-field="quantity" min="1" :name="'items['+index+'][quantity]'" x-model="item.quantity" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition-all">
                                            </div>
                                            <div class="col-span-12 sm:col-span-6 md:col-span-3">
                                                <label class="mb-1.5 block text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Value (Per Item) USD</label>
                                                <input type="number" data-field="value_per_item" min="0" step="0.01" :name="'items['+index+'][value_per_item]'" x-model="item.value" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition-all">
                                            </div>
                                        </div>

                                        <div class="mt-5 flex items-center justify-between border-t border-gray-700 pt-4">
                                            <button type="button" x-show="items.length > 1" @click="removeItem(item.id)" class="text-xs font-semibold text-red-400 hover:text-red-300 flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Remove Item
                                            </button>
                                            <div x-show="items.length <= 1"></div>

                                            <button type="button" @click="closeItem()" class="text-sm font-bold text-white bg-violet-600 hover:bg-violet-500 px-5 py-2 rounded-xl transition-colors shadow-lg shadow-violet-500/20">
                                                Save Item Details
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- HIDDEN INPUTS FOR FORM SUBMISSION WHEN COLLAPSED -->
                                    <div x-show="editingId !== item.id">
                                        <input type="hidden" data-field="name-hidden" :name="'items['+index+'][name]'" :value="item.name">
                                        <input type="hidden" data-field="quantity-hidden" :name="'items['+index+'][quantity]'" :value="item.quantity">
                                        <input type="hidden" data-field="value_per_item-hidden" :name="'items['+index+'][value_per_item]'" :value="item.value">
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="mt-4 flex justify-end border-t border-white/10 pt-4">
                            <button type="button" @click="addItem()" class="flex items-center gap-2 text-sm font-bold text-violet-300 hover:text-violet-200 bg-violet-500/10 hover:bg-violet-500/20 border border-violet-500/30 rounded-xl px-5 py-2.5 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Add Another Item
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- STEP 3: PACKAGES --}}
        {{-- ============================================================ --}}
        <div class="wizard-step hidden" data-step="3">
            <div class="neon-card rounded-2xl p-6">
                <h3 class="text-base font-semibold text-white mb-2">Package Details</h3>
                <div class="mb-6 flex items-start gap-3 rounded-xl border border-violet-500/20 bg-violet-500/10 px-4 py-3 text-xs leading-5 text-slate-300">
                    <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-violet-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg>
                    <p><strong class="text-white">Actual weight is required.</strong> Dimensions are optional. If all three dimensions are entered, volumetric weight is calculated using Length (cm) x Width (cm) x Height (cm) / 5000, and the larger weight is charged.</p>
                </div>

                <div id="packagesList" class="mb-4 space-y-4">
                    {{-- package cards added by JS --}}
                </div>

                <button type="button" onclick="addPackage()"
                    class="flex items-center gap-2 text-sm text-violet-400 hover:text-violet-300 border border-violet-600/40 hover:border-violet-500 rounded-xl px-4 py-2.5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Another Package
                </button>

                {{-- Totals --}}
                <div class="mt-6 pt-5 border-t border-white/10 grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-gray-800/60 rounded-xl p-4 text-center">
                        <p class="text-xs text-slate-400 mb-1">Total Packages</p>
                        <p class="text-2xl font-bold text-white" id="totalPackagesDisplay">0</p>
                    </div>
                    <div class="bg-gray-800/60 rounded-xl p-4 text-center">
                        <p class="text-xs text-slate-400 mb-1">Volumetric Weight</p>
                        <p class="text-2xl font-bold text-white"><span id="totalVolumetricDisplay">0.00</span> <span class="text-sm font-normal text-slate-400">kg</span></p>
                    </div>
                    <div class="bg-gray-800/60 rounded-xl p-4 text-center">
                        <p class="text-xs text-slate-400 mb-1">Chargeable Weight</p>
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
                            <p class="text-xs text-emerald-400 mt-0.5">Tracking: <span id="reviewTracking" class="font-mono font-bold"></span></p>
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
                        <a href="{{ route(auth()->user()->dashboardRouteName()) }}"
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

@push('styles')
<style>
    .premium-wizard{width:min(1240px,100%);max-width:none!important;margin-inline:auto}
    .wizard-hero{min-height:128px}
    .wizard-secure{position:relative;z-index:2;display:flex;align-items:center;gap:11px;padding:13px 15px;border:1px solid rgba(255,255,255,.2);border-radius:15px;background:rgba(255,255,255,.1);backdrop-filter:blur(10px)}.wizard-secure svg{width:24px;height:24px;fill:none;stroke:#70e6bd;stroke-width:1.8}.wizard-secure strong,.wizard-secure small{display:block}.wizard-secure strong{color:#fff;font-size:10px}.wizard-secure small{margin-top:3px;color:#b9c7e3;font-size:8px}
    .wizard-progress-card{position:relative;padding:21px 30px 17px;border:1px solid #e2e8f2;border-radius:18px;background:#fff;box-shadow:0 10px 28px rgba(24,43,84,.06)}
    .premium-wizard .step-circle{width:40px!important;height:40px!important;border-width:0!important;color:#8491a8!important;background:#eef2f7!important;box-shadow:0 0 0 5px #fff,0 0 0 6px #e5eaf2}
    .premium-wizard .step-circle.bg-violet-600{color:#fff!important;background:linear-gradient(145deg,#8252f6,#5a2de0)!important;box-shadow:0 0 0 5px #fff,0 0 0 6px #cfbff9,0 7px 17px rgba(91,45,218,.25)}
    .premium-wizard .step-circle.bg-emerald-600{color:#fff!important;background:linear-gradient(145deg,#35cea0,#0d9f71)!important}
    .premium-wizard .step-label{color:#77859e!important;font-size:10px!important;font-weight:750!important}.premium-wizard .step-label[style*="a78bfa"]{color:#6839dc!important}
    .premium-wizard #progressLine{height:3px!important;background:linear-gradient(90deg,#7040e9,#2e76f0)!important}.premium-wizard .wizard-progress-card .bg-gray-800{height:3px!important;background:#edf0f5!important}
    .premium-wizard .neon-card{border:1px solid #e2e8f2!important;border-radius:21px!important;background:#fff!important;box-shadow:0 13px 34px rgba(24,43,84,.07)!important}
    .premium-wizard .neon-card>h3,.premium-wizard .neon-card h3,.premium-wizard .neon-card h4{color:#17213c!important}
    .premium-wizard label,.premium-wizard .text-slate-400{color:#63728d!important}
    .premium-wizard input:not([type="checkbox"]):not([type="radio"]):not([type="hidden"]),.premium-wizard select,.premium-wizard textarea{color:#18233f!important;border-color:#dce4ef!important;background:#fff!important;box-shadow:0 2px 6px rgba(25,43,82,.025)!important}
    .premium-wizard input::placeholder,.premium-wizard textarea::placeholder{color:#a2aec1!important}
    .premium-wizard input:focus,.premium-wizard select:focus,.premium-wizard textarea:focus{border-color:#7b55ed!important;box-shadow:0 0 0 4px rgba(105,62,231,.09)!important;outline:0!important}
    .premium-wizard [id$="_country_dropdown"]{border:1px solid #dce4ef!important;background:#fff!important;box-shadow:0 16px 36px rgba(17,34,75,.14)!important}.premium-wizard .country-option{color:#42516d!important}.premium-wizard .country-option:hover{background:#f3f0ff!important}
    .premium-wizard [data-save-address-book-button]{border-color:#afe8d2!important;color:#087b58!important;background:linear-gradient(135deg,#eafaf4,#def7ed)!important;font-weight:800!important}
    .premium-wizard [onclick^="openAddressBookPicker"]{border-color:#ddd2fb!important;color:#6538db!important;background:#f4f0ff!important;font-weight:800!important}
    .premium-wizard [data-shipment-type-panel]{background:linear-gradient(145deg,#fff,#fafbff)!important}.premium-wizard [data-shipment-type-panel][style*="124, 58, 237"]{background:#f8f5ff!important}
    .premium-wizard .bg-gray-800\/60,.premium-wizard .bg-gray-800\/50{background:#f6f8fb!important}.premium-wizard .border-white\/10{border-color:#e7ebf2!important}.premium-wizard .divide-gray-800\/50>*+*,.premium-wizard .divide-gray-800>*+*{border-color:#e8edf4!important}
    .premium-wizard #wizardNav{position:sticky;bottom:16px;z-index:20;margin:22px 0 0;padding:11px;border:1px solid rgba(218,225,238,.9);border-radius:16px;background:rgba(255,255,255,.92);box-shadow:0 14px 35px rgba(20,37,75,.13);backdrop-filter:blur(14px)}
    .premium-wizard #nextBtn{min-height:43px;border-radius:12px;background:linear-gradient(135deg,#7d4df3,#5a2cdd)!important;box-shadow:0 9px 20px rgba(92,45,218,.25)!important}.premium-wizard #prevBtn{min-height:43px;color:#50607c!important;border-color:#dce3ee!important;background:#fff!important}
    .premium-wizard #switchBtn{background:linear-gradient(145deg,#ffcc55,#f4a900)!important;box-shadow:0 10px 24px rgba(218,155,0,.28)!important}
    @media(max-width:767px){.wizard-progress-card{padding:18px 9px 15px}.premium-wizard .step-circle{width:34px!important;height:34px!important}.premium-wizard .step-label{max-width:58px;text-align:center;white-space:normal!important}.wizard-secure{display:none}}
</style>
@endpush

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
    let cCode = entry.country_code || '';
    if (!cCode && entry.country_name && typeof window !== 'undefined' && window.COUNTRIES) {
        const found = window.COUNTRIES.find(c => c.name.toLowerCase() === entry.country_name.toLowerCase().trim());
        if (found) cCode = found.code;
    }
    setValue('country_code', cCode);
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
function goToStep(targetStep) {
    if (targetStep === currentStep) return;
    
    if (targetStep > currentStep) {
        for (let s = currentStep; s < targetStep; s++) {
            if (!validateStep(s)) return;
        }
    }
    
    if (targetStep >= 4 && currentStep < 4) {
        loadCarriers();
    }
    
    currentStep = targetStep;
    showStep(currentStep);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

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
    // document.getElementById('itemsList').innerHTML = ''; // handled by alpine
    document.getElementById('packagesList').innerHTML = '';
    packageCount = 0; // reset counter
    addPackage();
    showStep(1);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ============================================================
// STEP VALIDATION
// ============================================================
function validateStep(step) {
    if (step === 1) {
        const required = ['sender_name','sender_country','sender_address','sender_city','sender_phone',
                          'receiver_name','receiver_country','receiver_address','receiver_city','receiver_phone'];
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
        if (type.value === 'document') {
            const docDesc = document.querySelector('[name="document_description"]');
            if (!docDesc || !docDesc.value.trim()) {
                docDesc && docDesc.focus();
                showFieldError(docDesc, 'Document description is required');
                return false;
            }
        }
        if (type.value === 'non_document') {
            const purpose = document.querySelector('[name="shipment_purpose"]');
            if (!purpose || !purpose.value.trim()) {
                purpose && purpose.focus();
                showFieldError(purpose, 'Shipment purpose is required');
                return false;
            }
            const rows = document.querySelectorAll('.item-row');
            for (const row of rows) {
                const name = row.querySelector('[data-field="name"]');
                if (!name || !name.value.trim()) {
                    name && name.focus();
                    showFieldError(name, 'Item name is required');
                    return false;
                }
                const quantity = row.querySelector('[data-field="quantity"]');
                if (!quantity || !quantity.value.trim() || quantity.value <= 0) {
                    quantity && quantity.focus();
                    showFieldError(quantity, 'Quantity is required');
                    return false;
                }
                const value = row.querySelector('[data-field="value_per_item"]');
                if (!value || !value.value.trim() || value.value <= 0) {
                    value && value.focus();
                    showFieldError(value, 'Value is required');
                    return false;
                }
            }
        }
    }
    if (step === 3) {
        const rows = document.querySelectorAll('.package-row');
        if (rows.length === 0) { alert('Add at least one package.'); return false; }
        for (const row of rows) {
            const weight = row.querySelector('[data-field="weight"]');
            const dimensionInputs = ['length', 'width', 'height'].map(field => row.querySelector(`[data-field="${field}"]`));
            const actualWeight = parseFloat(weight?.value) || 0;
            const dimensionValues = dimensionInputs.map(input => parseFloat(input?.value) || 0);
            const hasAnyDimension = dimensionValues.some(value => value > 0);
            const hasAllDimensions = dimensionValues.every(value => value > 0);

            if (actualWeight <= 0) {
                weight?.focus();
                showFieldError(weight, 'Actual weight is required');
                return false;
            }

            if (hasAnyDimension && !hasAllDimensions) {
                const missingDimension = dimensionInputs.find((input, index) => dimensionValues[index] <= 0);
                missingDimension?.focus();
                showFieldError(missingDimension, 'Complete all three dimensions');
                return false;
            }

            const quantity = row.querySelector('[data-field="quantity"]');
            if (!quantity || parseInt(quantity.value, 10) <= 0) {
                quantity?.focus();
                showFieldError(quantity, 'Quantity must be at least 1');
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
// Handled by Alpine.js component in the view

// ============================================================
// PACKAGES (Step 3)
// ============================================================
let packageCount = 0;

function addPackage(data = {}) {
    packageCount++;
    const idx = packageCount;
    const container = document.getElementById('packagesList');
    const packageCard = document.createElement('div');
    packageCard.className = 'package-row overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm';
    packageCard.dataset.idx = idx;
    const initialWeight = data.actual_weight ?? data.weight ?? '';
    packageCard.innerHTML = `
        <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-4 py-3">
            <div class="flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-violet-100 text-xs font-bold text-violet-700">${idx}</span>
                <span class="text-sm font-semibold text-slate-900">Package ${idx}</span>
            </div>
            <button type="button" onclick="removePackage(this)"
                aria-label="Remove package ${idx}"
                class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-900/30 text-red-400 transition-all hover:bg-red-900/60">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="grid grid-cols-1 gap-4 p-4 md:grid-cols-12">
            <label class="block md:col-span-5">
                <span class="mb-2 flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-slate-400">
                    Actual weight (kg)
                    <span class="rounded-full bg-rose-50 px-2 py-0.5 text-[10px] font-semibold normal-case tracking-normal text-rose-600">Required</span>
                </span>
                <input type="number" name="packages[${idx}][weight]" data-field="weight"
                    value="${initialWeight}" min="0.01" step="0.01" placeholder="0.00" required
                    oninput="updatePackageTotals()"
                    class="w-full rounded-xl border border-gray-700 bg-gray-800 px-3 py-3 text-sm text-white placeholder-gray-600 focus:border-violet-500 focus:outline-none">
            </label>
            <label class="block md:col-span-2">
                <span class="mb-2 block text-xs font-medium uppercase tracking-wide text-slate-400">Quantity</span>
                <input type="number" name="packages[${idx}][quantity]" data-field="quantity"
                    value="${data.quantity||1}" min="1" required
                    oninput="updatePackageTotals()"
                    class="w-full rounded-xl border border-gray-700 bg-gray-800 px-3 py-3 text-sm text-white focus:border-violet-500 focus:outline-none">
            </label>
            <div class="md:col-span-5">
                <span class="mb-2 block text-xs font-medium uppercase tracking-wide text-slate-400">Chargeable weight</span>
                <div class="rounded-xl border border-violet-200 bg-violet-50 px-4 py-2.5">
                    <strong class="block text-base text-violet-700" data-field="chargeable">0.00 kg</strong>
                    <span class="mt-0.5 block text-[11px] text-slate-400" data-field="weight-source">Actual weight required</span>
                </div>
            </div>
        </div>

        <div class="border-t border-slate-200 bg-slate-50/80 p-4">
            <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <p class="text-sm font-semibold text-slate-800">Volumetric option <span class="font-normal text-slate-500">(optional)</span></p>
                    <p class="mt-0.5 text-[11px] text-slate-500">Add all three dimensions only when you want volumetric weight calculated.</p>
                </div>
                <span class="rounded-lg border border-cyan-200 bg-cyan-50 px-3 py-1.5 text-[11px] font-medium text-cyan-700">L × W × H ÷ 5000</span>
            </div>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <label class="block">
                    <span class="mb-1.5 block text-xs text-slate-400">Length (cm)</span>
                    <input type="number" name="packages[${idx}][length]" data-field="length"
                        value="${data.length||''}" min="0.01" step="0.01" placeholder="Optional"
                        oninput="updatePackageTotals()"
                        class="w-full rounded-xl border border-gray-700 bg-gray-800 px-3 py-2.5 text-sm text-white placeholder-gray-600 focus:border-cyan-500 focus:outline-none">
                </label>
                <label class="block">
                    <span class="mb-1.5 block text-xs text-slate-400">Width (cm)</span>
                    <input type="number" name="packages[${idx}][width]" data-field="width"
                        value="${data.width||''}" min="0.01" step="0.01" placeholder="Optional"
                        oninput="updatePackageTotals()"
                        class="w-full rounded-xl border border-gray-700 bg-gray-800 px-3 py-2.5 text-sm text-white placeholder-gray-600 focus:border-cyan-500 focus:outline-none">
                </label>
                <label class="block">
                    <span class="mb-1.5 block text-xs text-slate-400">Height (cm)</span>
                    <input type="number" name="packages[${idx}][height]" data-field="height"
                        value="${data.height||''}" min="0.01" step="0.01" placeholder="Optional"
                        oninput="updatePackageTotals()"
                        class="w-full rounded-xl border border-gray-700 bg-gray-800 px-3 py-2.5 text-sm text-white placeholder-gray-600 focus:border-cyan-500 focus:outline-none">
                </label>
                <div>
                    <span class="mb-1.5 block text-xs text-slate-400">Volumetric weight</span>
                    <div class="rounded-xl border border-cyan-200 bg-cyan-50 px-3 py-2.5 text-sm font-semibold text-cyan-700" data-field="volumetric">0.00 kg</div>
                </div>
            </div>
        </div>`;
    container.appendChild(packageCard);
    updatePackageTotals();
}

function calculatePackageWeights(row) {
    const actualWeight = parseFloat(row.querySelector('[data-field="weight"]')?.value) || 0;
    const length = parseFloat(row.querySelector('[data-field="length"]')?.value) || 0;
    const width = parseFloat(row.querySelector('[data-field="width"]')?.value) || 0;
    const height = parseFloat(row.querySelector('[data-field="height"]')?.value) || 0;
    const hasAllDimensions = length > 0 && width > 0 && height > 0;
    const rawVolumetricWeight = hasAllDimensions ? (length * width * height) / 5000 : 0;
    const volumetricWeight = rawVolumetricWeight > 0 ? Math.max(Math.round((rawVolumetricWeight + Number.EPSILON) * 100) / 100, 0.01) : 0;
    const rawChargeableWeight = Math.max(actualWeight, rawVolumetricWeight);
    const chargeableWeight = rawChargeableWeight > 0 ? Math.max(Math.round((rawChargeableWeight + Number.EPSILON) * 100) / 100, 0.01) : 0;

    let source = 'Actual weight required';
    if (actualWeight > 0 && hasAllDimensions) {
        source = Math.abs(rawVolumetricWeight - actualWeight) < 0.005
            ? 'Both are equal'
            : (rawVolumetricWeight > actualWeight ? 'Volumetric is higher' : 'Actual is higher');
    } else if (hasAllDimensions) {
        source = 'Volumetric weight';
    } else if (actualWeight > 0) {
        source = 'Actual weight';
    }

    return { actualWeight, volumetricWeight, chargeableWeight, source };
}

function removePackage(btn) {
    const rows = document.querySelectorAll('.package-row');
    if (rows.length <= 1) { alert('At least one package is required.'); return; }
    btn.closest('.package-row').remove();
    updatePackageTotals();
}

function updatePackageTotals() {
    let totalPkgs = 0, totalVolumetric = 0, totalWt = 0;
    document.querySelectorAll('.package-row').forEach(row => {
        const q = parseInt(row.querySelector('[data-field="quantity"]').value) || 0;
        const weights = calculatePackageWeights(row);
        totalPkgs += q;
        totalVolumetric += weights.volumetricWeight * q;
        totalWt += weights.chargeableWeight * q;
        row.querySelector('[data-field="chargeable"]').textContent = `${weights.chargeableWeight.toFixed(2)} kg`;
        row.querySelector('[data-field="volumetric"]').textContent = `${weights.volumetricWeight.toFixed(2)} kg`;
        row.querySelector('[data-field="weight-source"]').textContent = weights.source;
    });
    document.getElementById('totalPackagesDisplay').textContent = totalPkgs;
    document.getElementById('totalVolumetricDisplay').textContent = totalVolumetric.toFixed(2);
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
    let code    = document.querySelector('[name="receiver_country_code"]')?.value || '';
    
    if (!code && country && typeof window !== 'undefined' && window.COUNTRIES) {
        const found = window.COUNTRIES.find(c => c.name.toLowerCase() === country.toLowerCase().trim());
        if (found) {
            code = found.code;
            const codeInput = document.querySelector('[name="receiver_country_code"]');
            if (codeInput) codeInput.value = code;
        }
    }
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
                            <h4 style="font-weight: 600; font-size: 0.875rem; color: ${String(selectedCarrierId) === String(c.id) ? '#7c3aed' : '#334155'}; white-space: normal; word-break: break-word;" data-carrier-title="${c.id}">${c.name}</h4>
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

    // Refresh CSRF token before submission to avoid 419 Page Expired on long-lived sessions
    fetch('/csrf-token')
        .then(r => r.json())
        .then(data => {
            const meta = document.querySelector('meta[name="csrf-token"]');
            if (meta) meta.setAttribute('content', data.token);
            const input = document.querySelector('[name="_token"]');
            if (input) input.value = data.token;
            return data.token;
        })
        .catch(e => getCSRF()) // fallback to existing token
        .then(token => {
            return fetch('{{ route("admin.shipments.store", [], false) }}', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: fd,
            });
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
            const waybillBtn = document.getElementById('waybillBtn');
            const invoiceBtn = document.getElementById('invoiceBtn');
            if (data.waybill_url) {
                waybillBtn.href = data.waybill_url;
                waybillBtn.classList.remove('hidden');
            } else {
                waybillBtn.classList.add('hidden');
            }
            if (data.invoice_url && fd.get('shipment_type') !== 'document') {
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
        ${g('shipment_purpose') && g('shipment_type') !== 'document' ? `<p>Purpose: <span class="text-white">${g('shipment_purpose')}</span></p>` : ''}
        <p>Carrier: <span class="text-white">${g('carrier_name')}</span></p>
        <p>Status: <span class="text-yellow-400">Pending</span></p>`;

    document.getElementById('reviewPackages').innerHTML = `
        <p>Total Packages: <span class="text-white">${g('total_packages')}</span></p>
        <p>Chargeable Weight: <span class="text-white">${g('total_weight')} kg</span></p>`;
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
