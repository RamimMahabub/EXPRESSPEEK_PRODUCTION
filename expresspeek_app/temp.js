
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

    return fetch('{{ route("agent.address-book.index", [], false) }}', {
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
            class="rounded-2xl border border-white/10 bg-gray-900/80 p-4 text-left transition-all hover:border-violet-500/50 hover:bg-violet-500/10">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-white">${escapeHtml(entry.name || 'Unnamed')}</p>
                    ${entry.company ? `<p class="truncate text-xs text-slate-400">${escapeHtml(entry.company)}</p>` : ''}
                    <p class="mt-1 text-xs text-slate-400">${escapeHtml(entry.city || '')}${entry.country_name ? `, ${escapeHtml(entry.country_name)}` : ''}</p>
                </div>
                <span class="shrink-0 rounded-full border border-violet-500/30 bg-violet-500/10 px-2 py-1 text-[10px] font-semibold text-violet-300">Use</span>
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
        const panel = card.querySelector('div.relative');
        const isSelected = selected === type;

        card.setAttribute('aria-pressed', String(isSelected));
        if (badge) {
            if (isSelected) {
                badge.classList.remove('hidden');
                badge.classList.add('inline-flex');
            } else {
                badge.classList.add('hidden');
                badge.classList.remove('inline-flex');
            }
        }
        if (note) note.classList.toggle('hidden', !isSelected);
        if (panel) {
            panel.style.borderColor = isSelected ? '#8b5cf6' : '#374151';
            panel.style.background = isSelected ? 'rgba(124, 58, 237, 0.14)' : 'transparent';
            panel.style.boxShadow = isSelected ? '0 14px 34px rgba(91, 33, 182, 0.18)' : '';
            panel.style.transform = isSelected ? 'translateY(-2px)' : '';
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

    fetch(`{{ $guestMode ? route('shipment.carriers.available', [], false) : route('agent.carriers.available', [], false) }}?country_code=${encodeURIComponent(code || country)}&country=${encodeURIComponent(country)}&weight=${encodeURIComponent(totalWeight || 1)}&type=${encodeURIComponent(shipmentType)}`, {
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
                <input type="radio" name="_carrier_select" value="${c.id}" class="sr-only peer" ${String(selectedCarrierId) === String(c.id) ? 'checked' : ''} onchange="selectCarrier(${c.id})">
                <div class="relative border-2 rounded-2xl p-5 transition-all duration-200 peer-checked:border-violet-400 peer-checked:bg-violet-600/15 peer-checked:shadow-lg peer-checked:shadow-violet-900/20 hover:border-gray-500 hover:-translate-y-0.5"
                    style="border-color: ${String(selectedCarrierId) === String(c.id) ? '#8b5cf6' : '#374151'}; background: ${String(selectedCarrierId) === String(c.id) ? 'rgba(124, 58, 237, 0.14)' : 'transparent'};">
                    <div class="absolute top-4 right-4 items-center gap-1 rounded-full bg-violet-500 px-2 py-1 text-[10px] font-semibold text-white shadow-sm ${String(selectedCarrierId) === String(c.id) ? '' : 'hidden'}" data-carrier-selected-badge="${c.id}">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Selected
                    </div>
                    <div class="flex items-center justify-between gap-3 mb-3 pr-14">
                        <div class="min-w-0">
                            <h4 class="font-semibold text-white text-sm truncate">${c.name}</h4>
                            <p class="text-[11px] text-violet-300 mt-1 ${String(selectedCarrierId) === String(c.id) ? '' : 'hidden'}" data-carrier-selected-text="${c.id}">This carrier is selected</p>
                        </div>
                        <span class="shrink-0 text-xs rounded-lg px-2 py-1 ${String(selectedCarrierId) === String(c.id) ? 'bg-violet-500/20 text-violet-200' : 'bg-gray-800 text-slate-400'}">${c.currency}</span>
                    </div>
                    <p class="text-xs text-slate-400">Estimated rate: ${Number(c.price).toFixed(2)} ${c.currency}</p>
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
        const panel = card.querySelector('div.relative');

        card.setAttribute('aria-pressed', String(isSelected));
        if (badge) {
            if (isSelected) {
                badge.classList.remove('hidden');
                badge.classList.add('inline-flex');
            } else {
                badge.classList.add('hidden');
                badge.classList.remove('inline-flex');
            }
        }
        if (selectedText) selectedText.classList.toggle('hidden', !isSelected);
        if (panel) {
            panel.style.borderColor = isSelected ? '#8b5cf6' : '#374151';
            panel.style.background = isSelected ? 'rgba(124, 58, 237, 0.14)' : 'transparent';
            panel.style.boxShadow = isSelected ? '0 14px 34px rgba(91, 33, 182, 0.18)' : '';
            panel.style.transform = isSelected ? 'translateY(-2px)' : '';
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

    fetch('{{ $guestMode ? route("shipment.store", [], false) : route("agent.shipments.store", [], false) }}', {
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

    fetch('{{ route("agent.address-book.store", [], false) }}', {
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
