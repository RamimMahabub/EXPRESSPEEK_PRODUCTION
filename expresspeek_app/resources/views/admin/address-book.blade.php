@extends('layouts.dashboard')

@section('title', 'Address Book')
@section('page-title', 'Address Book')
@section('page-subtitle', 'Manage saved sender and receiver addresses')

@section('content')

<div class="grid grid-cols-1 xl:grid-cols-5 gap-6">
    <div class="xl:col-span-2">
        <div class="neon-card rounded-2xl p-6 sticky top-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-base font-semibold text-white">Add New Address</h3>
                    <p class="text-xs text-slate-400 mt-1">Save contacts here, then use them while creating shipments.</p>
                </div>
                <a href="{{ route('agent.shipments.create') }}" class="text-xs text-violet-400 hover:text-violet-300 transition-colors">Create shipment →</a>
            </div>

            <form id="addressBookForm" novalidate>
                @include('agent.partials.contact-form', ['prefix' => 'book'])
            </form>
        </div>
    </div>

    <div class="xl:col-span-3">
        <div class="neon-card rounded-2xl p-6">
            <div class="flex items-center justify-between gap-4 mb-5">
                <div>
                    <h3 class="text-base font-semibold text-white">Saved Addresses</h3>
                    <p class="text-xs text-slate-400 mt-1">Search, review, and remove stored addresses.</p>
                </div>
                <div class="w-full max-w-sm">
                    <input type="text" id="addressSearch" placeholder="Search by name, company, city..."
                        oninput="filterAddressBook(this.value)"
                        class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500">
                </div>
            </div>

            <div id="addressBookList" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2 text-center py-12 text-slate-500 text-sm">Loading addresses...</div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let addressBookEntries = [];

function getCSRF() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        || document.querySelector('[name="_token"]')?.value
        || '';
}

function loadAddressBookEntries() {
    const list = document.getElementById('addressBookList');
    list.innerHTML = '<div class="md:col-span-2 text-center py-12 text-slate-500 text-sm">Loading addresses...</div>';

    fetch('{{ route("admin.address-book.index", [], false) }}', {
        credentials: 'same-origin',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(response => {
        if (!response.ok) throw new Error('HTTP ' + response.status);
        return response.json();
    })
    .then(entries => {
        addressBookEntries = entries;
        renderAddressBook(entries);
    })
    .catch(error => {
        console.error('Address book load error:', error);
        list.innerHTML = '<div class="md:col-span-2 text-center py-12 text-red-400 text-sm">Failed to load addresses. Please refresh.</div>';
    });
}

function renderAddressBook(entries) {
    const list = document.getElementById('addressBookList');

    if (!entries.length) {
        list.innerHTML = '<div class="md:col-span-2 text-center py-12 text-slate-500 text-sm">No saved addresses yet.</div>';
        return;
    }

    list.innerHTML = entries.map(entry => `
        <div class="bg-gray-800/50 border border-gray-700/50 rounded-2xl p-4 hover:border-violet-500/40 transition-colors">
            <div class="flex items-start justify-between gap-3 mb-3">
                <div class="min-w-0">
                    <p class="font-semibold text-white text-sm truncate">${escapeHtml(entry.name || 'Unnamed')}</p>
                    ${entry.company ? `<p class="text-slate-400 text-xs truncate mt-0.5">${escapeHtml(entry.company)}</p>` : ''}
                    <p class="text-slate-400 text-xs mt-1">${escapeHtml(entry.city || 'Unknown city')}${entry.country_name ? `, ${escapeHtml(entry.country_name)}` : ''}</p>
                </div>
                <button type="button" onclick="deleteAddressEntry(${entry.id})"
                    class="text-slate-500 hover:text-red-400 transition-colors flex-shrink-0" aria-label="Delete address">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
            <div class="space-y-1 text-xs text-slate-400">
                ${entry.address ? `<p class="leading-relaxed">${escapeHtml(entry.address)}</p>` : ''}
                ${entry.address2 ? `<p class="leading-relaxed">${escapeHtml(entry.address2)}</p>` : ''}
                ${entry.address3 ? `<p class="leading-relaxed">${escapeHtml(entry.address3)}</p>` : ''}
                <p>${escapeHtml([entry.postal_code, entry.state].filter(Boolean).join(' '))}</p>
                ${entry.phone ? `<p>${escapeHtml(entry.phone_code || '')} ${escapeHtml(entry.phone)}</p>` : ''}
            </div>
            ${entry.label ? `<span class="mt-3 inline-flex text-xs bg-violet-900/40 text-violet-300 rounded px-2 py-0.5">${escapeHtml(entry.label)}</span>` : ''}
        </div>
    `).join('');
}

function filterAddressBook(query) {
    const term = query.toLowerCase();
    const filtered = addressBookEntries.filter(entry =>
        (entry.name || '').toLowerCase().includes(term) ||
        (entry.company || '').toLowerCase().includes(term) ||
        (entry.city || '').toLowerCase().includes(term) ||
        (entry.country_name || '').toLowerCase().includes(term)
    );
    renderAddressBook(filtered);
}

function deleteAddressEntry(id) {
    if (!confirm('Remove this address?')) return;

    fetch(`{{ url('admin/address-book') }}/${id}`, {
        method: 'DELETE',
        credentials: 'same-origin',
        headers: {
            'X-CSRF-TOKEN': getCSRF(),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(async response => {
        const payload = await response.json().catch(() => ({}));

        if (!response.ok) {
            throw new Error(payload.message || 'Failed to remove address');
        }

        return payload;
    })
    .then(data => {
        if (data.success) {
            addressBookEntries = addressBookEntries.filter(entry => entry.id !== id);
            renderAddressBook(addressBookEntries);
            showToast('Address removed');
        }
    })
    .catch(error => {
        console.error('Delete address error:', error);
        showToast(error.message || 'Failed to remove address');
    });
}

function saveToAddressBook(prefix) {
    const getValue = (field) => document.querySelector(`[name="${prefix}_${field}"]`)?.value || '';
    const saveButton = document.querySelector(`[data-save-address-book-button="${prefix}"]`);
    const status = document.getElementById(`${prefix}_save_status`);
    const data = {
        name: getValue('name'),
        company: getValue('company'),
        country_code: getValue('country_code'),
        country_name: getValue('country'),
        address: getValue('address'),
        address2: getValue('address2'),
        address3: getValue('address3'),
        postal_code: getValue('postal_code'),
        city: getValue('city'),
        state: getValue('state'),
        email: getValue('email'),
        phone_type: getValue('phone_type'),
        phone_code: getValue('phone_code'),
        phone: getValue('phone'),
        is_business: document.querySelector(`[name="${prefix}_is_business"]`)?.checked ? 1 : 0,
    };

    if (!data.name || !data.city || !data.address) {
        alert('Name, city, and address are required to save.');
        return;
    }

    setSaveState(prefix, 'Saving address...', true, 'text-slate-400');

    fetch('{{ route("admin.address-book.store", [], false) }}', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCSRF(),
            'Accept': 'application/json'
        },
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
                setSaveState(prefix, 'This address is already in your book.', false, 'text-amber-400');
                return;
            }

            addressBookEntries.unshift(payload.entry);
            renderAddressBook(addressBookEntries);
            document.getElementById('addressSearch').value = '';
            document.getElementById('addressBookForm')?.reset();
            showToast('Address saved successfully');
            setSaveState(prefix, 'Saved successfully.', false, 'text-emerald-400');
        }
    })
    .catch(error => {
        console.error('Save address error:', error);
        setSaveState(prefix, error?.message || 'Failed to save address', false, 'text-red-400');
        showToast(error?.message || 'Failed to save address');
    })
    .finally(() => {
        if (saveButton) {
            saveButton.disabled = false;
            saveButton.querySelector('span') && (saveButton.querySelector('span').textContent = 'Save to Address Book');
        }
        if (status && !status.textContent) {
            status.classList.add('hidden');
        }
    });
}

function setSaveState(prefix, message, busy, colorClass) {
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

function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-6 right-6 z-50 bg-emerald-700 text-white text-sm font-medium py-3 px-5 rounded-xl shadow-xl';
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

function escapeHtml(value) {
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#39;');
}

document.addEventListener('DOMContentLoaded', () => {
    loadAddressBookEntries();
});
</script>
@endpush
