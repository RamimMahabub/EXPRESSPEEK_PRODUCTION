@extends('layouts.operations-dashboard')

@section('title', 'Address Book')
@section('page-title', 'Address Book')
@section('page-subtitle', 'Manage saved sender and receiver addresses')

@section('content')

<div class="ap-shell ap-address-page">
    <section class="ap-page-head">
        <div class="ap-head-copy">
            <span class="ap-eyebrow">Contact intelligence</span>
            <h1>Your delivery address library</h1>
            <p>Keep frequently used sender and receiver details ready, accurate, and one click away from every new shipment.</p>
        </div>
        <div class="ap-head-actions">
            <a href="{{ route('admin.shipments.create') }}" class="ap-button light">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                Create shipment
            </a>
        </div>
    </section>

    <div class="ap-address-grid">
    <div>
        <div class="ap-card ap-form-card ap-address-form">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <span class="ap-address-step">01</span>
                    <h3 class="ap-card-title">Add a new address</h3>
                    <p class="ap-card-copy">Complete the essential delivery details below.</p>
                </div>
            </div>

            <form id="addressBookForm" novalidate>
                @include('agent.partials.contact-form', ['prefix' => 'book'])
            </form>
        </div>
    </div>

    <div>
        <div class="ap-card ap-address-list-card">
            <div class="ap-card-head ap-address-list-head">
                <div>
                    <span class="ap-address-step">02</span>
                    <h3 class="ap-card-title">Saved addresses</h3>
                    <p class="ap-card-copy">Search and manage the contacts already in your library.</p>
                </div>
                <div class="ap-field ap-address-search">
                    <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
                    <input type="text" id="addressSearch" placeholder="Search by name, company, city..."
                        oninput="filterAddressBook(this.value)"
                        class="ap-input">
                </div>
            </div>

            <div id="addressBookList" class="ap-address-list">
                <div class="ap-empty"><span class="ap-loader"></span><strong>Loading your address book</strong></div>
            </div>
        </div>
    </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .ap-address-grid{display:grid;grid-template-columns:minmax(360px,.78fr) minmax(0,1.22fr);gap:18px;align-items:start}
    .ap-address-form{position:sticky;top:100px}
    .ap-address-step{float:left;margin:0 11px 14px 0;width:34px;height:34px;display:grid;place-items:center;border-radius:11px;color:#6537df;background:#efeaff;font-size:9px;font-weight:800}
    .ap-address-list-card{min-height:620px}
    .ap-address-list-head{align-items:center}
    .ap-address-search{width:min(360px,46%);flex:0 0 auto}
    .ap-address-list{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:13px;padding:18px}
    .ap-address-entry{position:relative;min-height:190px;padding:17px;border:1px solid #e5eaf3;border-radius:16px;background:linear-gradient(145deg,#fff,#fafbff);box-shadow:0 7px 19px rgba(23,40,79,.045);transition:.2s ease}
    .ap-address-entry:hover{transform:translateY(-3px);border-color:#d6c9fa;box-shadow:0 14px 29px rgba(62,44,144,.1)}
    .ap-address-entry-top{display:flex;align-items:flex-start;gap:11px}
    .ap-address-avatar{width:38px;height:38px;flex:0 0 auto;display:grid;place-items:center;border-radius:12px;color:#fff;background:linear-gradient(145deg,#7e51f6,#2d73ed);font-size:10px;font-weight:800;box-shadow:0 8px 17px rgba(77,65,207,.19)}
    .ap-address-entry-copy{min-width:0;flex:1}
    .ap-address-entry-copy strong,.ap-address-entry-copy span,.ap-address-entry-copy small{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .ap-address-entry-copy strong{color:#17233e;font-size:11px}.ap-address-entry-copy span{margin-top:2px;color:#687691;font-size:9px}.ap-address-entry-copy small{margin-top:4px;color:#8793a8;font-size:9px}
    .ap-address-delete{width:31px;height:31px;flex:0 0 auto;display:grid;place-items:center;border:1px solid #edf0f5;border-radius:9px;color:#9aa5b8;background:#fff;cursor:pointer;transition:.18s}.ap-address-delete:hover{color:#dc3550;border-color:#ffd2da;background:#fff2f4}
    .ap-address-details{min-height:70px;margin:14px 0 12px;padding-top:12px;border-top:1px solid #edf0f5;color:#65738d;font-size:9px;line-height:1.65}.ap-address-details p{margin:0}
    .ap-loader{width:29px;height:29px;margin-bottom:14px;border:3px solid #e6e0fa;border-top-color:#7042e7;border-radius:50%;animation:ap-spin .8s linear infinite}@keyframes ap-spin{to{transform:rotate(360deg)}}
    .ap-empty-error strong{color:#cf3049}
    .ap-address-form [id$="_country_dropdown"]{border:1px solid #dce4ef!important;background:#fff!important;box-shadow:0 16px 36px rgba(17,34,75,.14)!important}
    .ap-address-form .country-option{color:#42516d!important}.ap-address-form .country-option:hover{background:#f3f0ff!important}
    .ap-address-form [data-save-address-book-button]{border-color:#a9ead1!important;color:#087a56!important;background:linear-gradient(135deg,#eafaf4,#ddf7ec)!important;font-weight:800!important}
    @media(max-width:1120px){.ap-address-grid{grid-template-columns:1fr}.ap-address-form{position:static}.ap-address-list-card{min-height:420px}}
    @media(max-width:680px){.ap-address-list-head{align-items:stretch;flex-direction:column}.ap-address-search{width:100%}.ap-address-list{grid-template-columns:1fr;padding:12px}.ap-address-entry{min-height:0}}
</style>
@endpush

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
    list.innerHTML = '<div class="ap-empty"><span class="ap-loader"></span><strong>Loading your address book</strong></div>';

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
        list.innerHTML = '<div class="ap-empty ap-empty-error"><strong>We could not load your addresses</strong><p>Please refresh the page and try again.</p></div>';
    });
}

function renderAddressBook(entries) {
    const list = document.getElementById('addressBookList');

    if (!entries.length) {
        list.innerHTML = '<div class="ap-empty"><span class="ap-empty-icon">⌂</span><strong>No saved addresses yet</strong><p>Add your first delivery contact using the form. It will appear here instantly.</p></div>';
        return;
    }

    list.innerHTML = entries.map(entry => `
        <article class="ap-address-entry">
            <div class="ap-address-entry-top">
                <span class="ap-address-avatar">${escapeHtml((entry.name || 'U').slice(0, 2).toUpperCase())}</span>
                <div class="ap-address-entry-copy">
                    <strong>${escapeHtml(entry.name || 'Unnamed')}</strong>
                    ${entry.company ? `<span>${escapeHtml(entry.company)}</span>` : ''}
                    <small>${escapeHtml(entry.city || 'Unknown city')}${entry.country_name ? `, ${escapeHtml(entry.country_name)}` : ''}</small>
                </div>
                <button type="button" onclick="deleteAddressEntry(${entry.id})"
                    class="ap-address-delete" aria-label="Delete address">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
            <div class="ap-address-details">
                ${entry.address ? `<p>${escapeHtml(entry.address)}</p>` : ''}
                ${entry.address2 ? `<p>${escapeHtml(entry.address2)}</p>` : ''}
                ${entry.address3 ? `<p>${escapeHtml(entry.address3)}</p>` : ''}
                <p>${escapeHtml([entry.postal_code, entry.state].filter(Boolean).join(' '))}</p>
                ${entry.phone ? `<p>${escapeHtml(entry.phone_code || '')} ${escapeHtml(entry.phone)}</p>` : ''}
            </div>
            ${entry.label ? `<span class="ap-badge">${escapeHtml(entry.label)}</span>` : ''}
        </article>
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
