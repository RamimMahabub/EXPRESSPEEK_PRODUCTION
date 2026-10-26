@extends('layouts.operations-dashboard')

@section('title', 'Sourcing Requests')
@section('page-title', 'Sourcing Requests')
@section('page-subtitle', 'Manage Shop from Bangladesh customer orders')

@section('content')
@php
    $counts = \App\Models\SourcingRequest::selectRaw('status, count(*) as cnt')->groupBy('status')->pluck('cnt', 'status');
    $totalAll = $counts->sum();
@endphp

<div class="ap-shell ap-sourcing-page">
    @if(session('success'))
        <div class="ap-success"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/></svg>{{ session('success') }}</div>
    @endif

    <section class="ap-page-head">
        <div class="ap-head-copy">
            <span class="ap-eyebrow">Commerce operations</span>
            <h1>Shop From Bangladesh command desk</h1>
            <p>Turn customer requests into trackable work, spot urgent follow-ups, and move every order toward delivery.</p>
        </div>
        <div class="ap-head-actions">
            <span class="ap-head-metric"><strong>{{ $newCount }}</strong><span>new requests<br>need attention</span></span>
        </div>
    </section>

    <section class="ap-kpi-grid">
        @foreach([
            ['Total requests', $totalAll, '', 'Complete request history'],
            ['New queue', $newCount, 'blue', 'Ready for first review'],
            ['Awaiting payment', $counts['payment_pending'] ?? 0, 'orange', 'Customer action needed'],
            ['Completed', $counts['completed'] ?? 0, 'green', 'Successfully fulfilled'],
        ] as [$label, $value, $tone, $note])
            <article class="ap-kpi {{ $tone }}">
                <div class="ap-kpi-top"><span class="ap-kpi-icon"><svg viewBox="0 0 24 24"><path d="M6 8h12l1 13H5L6 8Z"/><path d="M9 9V6a3 3 0 0 1 6 0v3"/></svg></span><span class="ap-kpi-label">{{ $label }}</span></div>
                <p class="ap-kpi-value">{{ number_format($value) }}</p>
                <p class="ap-kpi-note">{{ $note }}</p>
            </article>
        @endforeach
    </section>

    <form method="GET" action="{{ route('admin.sourcing-requests.index') }}" class="ap-toolbar ap-source-filters">
        <div class="ap-field ap-source-search">
            <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search name, reference or WhatsApp number" class="ap-input">
        </div>
        <select name="status" class="ap-select ap-source-select">
            <option value="">All statuses</option>
            @foreach($statuses as $key => $label)
                <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="ap-button primary">Apply filters</button>
        @if(request('search') || request('status'))
            <a href="{{ route('admin.sourcing-requests.index') }}" class="ap-button ghost">Clear</a>
        @endif
    </form>

    <section class="ap-card ap-table-card">
        <div class="ap-card-head">
            <div><h2 class="ap-card-title">Request pipeline</h2><p class="ap-card-copy">{{ $sourcingRequests->total() }} matching {{ str('request')->plural($sourcingRequests->total()) }}</p></div>
            <span class="ap-directory-pill">Live pipeline</span>
        </div>
        @if($sourcingRequests->isEmpty())
            <div class="ap-empty"><span class="ap-empty-icon">⌕</span><strong>No requests match these filters</strong><p>Try a broader search or clear the current filters.</p>@if(request('search') || request('status'))<a href="{{ route('admin.sourcing-requests.index') }}" class="ap-button ghost ap-empty-action">Clear filters</a>@endif</div>
        @else
            <div class="ap-table-scroll">
                <table class="ap-table ap-source-table">
                    <thead><tr><th>Reference</th><th>Customer</th><th>Destination</th><th>Product brief</th><th>Status</th><th>Received</th><th>Actions</th></tr></thead>
                    <tbody>
                        @foreach($sourcingRequests as $req)
                            <tr class="{{ $req->status !== 'new' ? 'ap-row-accepted' : '' }}">
                                <td>
                                    <span class="ap-reference">{{ $req->reference_number }}</span>
                                    @if($req->status === 'new')<span class="ap-new-dot" title="New request"></span>@endif
                                </td>
                                <td>
                                    <div class="ap-person ap-source-person">
                                        <span class="ap-avatar">{{ strtoupper(substr($req->customer_name, 0, 2)) }}</span>
                                        <div><strong>{{ $req->customer_name }}</strong><a href="{{ $req->whatsapp_link }}" target="_blank" rel="noopener">{{ $req->whatsapp_country_code }}{{ $req->whatsapp_number }}</a></div>
                                    </div>
                                </td>
                                <td><span class="ap-destination"><svg viewBox="0 0 24 24"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>{{ $req->destination_country }}</span></td>
                                <td class="ap-product"><p>{{ $req->product_description }}</p>@if($req->product_image)<span>Image attached</span>@endif</td>
                                <td><span class="ap-source-status status-{{ str_replace('_', '-', $req->status) }}">{{ $req->status_label }}</span></td>
                                <td><span class="ap-date">{{ $req->created_at->format('M d, Y') }}</span><small class="ap-date-relative">{{ $req->created_at->diffForHumans() }}</small></td>
                                <td>
                                    <div class="ap-row-actions">
                                        <a href="{{ route('admin.sourcing-requests.show', $req) }}" class="ap-row-button primary">View <span>→</span></a>
                                        <form action="{{ route('admin.sourcing-requests.destroy', $req) }}" method="POST" onsubmit="return confirm('Delete this Shop From Bangladesh?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="ap-row-button danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($sourcingRequests->hasPages())<div class="ap-pagination">{{ $sourcingRequests->links() }}</div>@endif
        @endif
    </section>
</div>
@endsection

@push('styles')
<style>
    .ap-success{display:flex;align-items:center;gap:10px;padding:13px 16px;border:1px solid #bfead7;border-radius:14px;color:#087b55;background:#ecfaf5;font-size:11px;font-weight:750}.ap-success svg{width:18px;height:18px;fill:none;stroke:currentColor;stroke-width:2}
    .ap-head-metric{min-width:185px;display:flex;align-items:center;gap:12px;padding:14px 17px;border:1px solid rgba(255,255,255,.2);border-radius:15px;background:rgba(255,255,255,.1);backdrop-filter:blur(10px)}.ap-head-metric strong{font-size:29px;line-height:1}.ap-head-metric span{color:#c4d0e9;font-size:9px;line-height:1.45}
    .ap-source-filters{justify-content:flex-start}.ap-source-search{min-width:260px;flex:1}.ap-source-select{width:220px;flex:0 0 auto}.ap-directory-pill{display:inline-flex;align-items:center;gap:7px;padding:7px 10px;border-radius:99px;color:#087c58;background:#e9f9f2;font-size:9px;font-weight:800}.ap-directory-pill:before{content:'';width:6px;height:6px;border-radius:50%;background:#1ec58b;box-shadow:0 0 0 4px rgba(30,197,139,.11)}
    .ap-source-table{min-width:1180px}.ap-reference{color:#6138dc;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:10px;font-weight:800}.ap-new-dot{display:inline-block;width:6px;height:6px;margin-left:6px;border-radius:50%;background:#3183fb;box-shadow:0 0 0 4px rgba(49,131,251,.1)}.ap-source-person a{display:block;margin-top:3px;color:#0d9870;font-size:8px;font-weight:700}.ap-destination{display:flex;align-items:center;gap:6px;color:#52617c}.ap-destination svg{width:14px;height:14px;fill:none;stroke:#73819a;stroke-width:1.8}.ap-product{max-width:300px}.ap-product p{display:-webkit-box;overflow:hidden;margin:0;color:#52617c;line-height:1.55;-webkit-box-orient:vertical;-webkit-line-clamp:2}.ap-product span{display:inline-block;margin-top:4px;color:#6a40df;font-size:8px;font-weight:750}
    .ap-source-status{display:inline-flex;align-items:center;gap:6px;padding:6px 9px;border-radius:99px;color:#52617c;background:#eef1f6;font-size:8px;font-weight:800;white-space:nowrap}.ap-source-status:before{content:'';width:5px;height:5px;border-radius:50%;background:currentColor}
    tr.ap-row-accepted td { background: rgba(16, 185, 129, 0.06); box-shadow: inset 0 1px 0 #10b981, inset 0 -1px 0 #10b981; }
    tr.ap-row-accepted td:first-child { box-shadow: inset 0 1px 0 #10b981, inset 0 -1px 0 #10b981, inset 2px 0 0 #10b981; border-top-left-radius: 6px; border-bottom-left-radius: 6px; }
    tr.ap-row-accepted td:last-child { box-shadow: inset 0 1px 0 #10b981, inset 0 -1px 0 #10b981, inset -2px 0 0 #10b981; border-top-right-radius: 6px; border-bottom-right-radius: 6px; }
    .ap-source-status.status-new{color:#246be0;background:#eaf2ff}
    .ap-source-status.status-reviewing{color:#4f46e5;background:#e0e7ff}
    .ap-source-status.status-contacted{color:#7040dc;background:#f1ecff}
    .ap-source-status.status-payment-pending{color:#d97706;background:#fef3c7}
    .ap-source-status.status-sourcing{color:#ea580c;background:#ffedd5}
    .ap-source-status.status-shipped{color:#0891b2;background:#cffafe}
    .ap-source-status.status-completed{color:#059669;background:#d1fae5}
    .ap-source-status.status-cancelled{color:#dc2626;background:#fee2e2}
    .ap-date{display:block;color:#43516b;font-weight:700}.ap-date-relative{display:block;margin-top:3px;color:#96a1b3;font-size:8px}.ap-row-actions{display:flex;align-items:center;gap:7px}.ap-row-button{min-height:31px;display:inline-flex;align-items:center;gap:5px;padding:0 11px;border:1px solid #dfe5ef;border-radius:9px;color:#52617c;background:#fff;font-size:9px;font-weight:800;cursor:pointer}.ap-row-button.primary{color:#5d35d9;border-color:#ded4fa;background:#f5f1ff}.ap-row-button.danger{color:#d93b54;border-color:#ffd8de;background:#fff5f6}.ap-pagination{padding:15px 20px;border-top:1px solid #edf1f6}.ap-empty-action{margin-top:16px}
    @media(max-width:900px){.ap-source-select{width:100%}.ap-source-filters .ap-button{width:100%}.ap-source-search{width:100%}}
</style>
@endpush
