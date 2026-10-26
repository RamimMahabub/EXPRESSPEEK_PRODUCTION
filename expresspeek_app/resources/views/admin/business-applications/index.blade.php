@extends('layouts.operations-dashboard')

@section('title', 'Business Applications')
@section('page-title', 'Business Applications')
@section('page-subtitle', 'Manage corporate account requests')

@section('content')
@php
    $counts = \App\Models\BusinessApplication::selectRaw('status, count(*) as cnt')->groupBy('status')->pluck('cnt', 'status');
    $totalAll = $counts->sum();
    $newCount = $counts['pending'] ?? 0;
@endphp

<div class="ap-shell ap-business-page">
    @if(session('success'))
        <div class="ap-success"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/></svg>{{ session('success') }}</div>
    @endif

    <section class="ap-page-head">
        <div class="ap-head-copy">
            <span class="ap-eyebrow">Corporate operations</span>
            <h1>Business Applications desk</h1>
            <p>Review new business requests, onboard corporate clients, and unlock premium tiers.</p>
        </div>
        <div class="ap-head-actions">
            <span class="ap-head-metric"><strong>{{ $newCount }}</strong><span>pending requests<br>need review</span></span>
        </div>
    </section>

    <section class="ap-kpi-grid">
        @foreach([
            ['Total requests', $totalAll, '', 'All-time history'],
            ['Pending review', $newCount, 'orange', 'Action needed'],
            ['Reviewed', $counts['reviewed'] ?? 0, 'blue', 'In discussion'],
            ['Accepted', $counts['accepted'] ?? 0, 'green', 'Onboarded clients'],
        ] as [$label, $value, $tone, $note])
            <article class="ap-kpi {{ $tone }}">
                <div class="ap-kpi-top"><span class="ap-kpi-icon"><svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></span><span class="ap-kpi-label">{{ $label }}</span></div>
                <p class="ap-kpi-value">{{ number_format($value) }}</p>
                <p class="ap-kpi-note">{{ $note }}</p>
            </article>
        @endforeach
    </section>

    <section class="ap-card ap-table-card mt-8">
        <div class="ap-card-head">
            <div><h2 class="ap-card-title">Application pipeline</h2><p class="ap-card-copy">{{ $applications->total() }} matching {{ str('application')->plural($applications->total()) }}</p></div>
        </div>
        @if($applications->isEmpty())
            <div class="ap-empty"><span class="ap-empty-icon">⌕</span><strong>No applications found</strong><p>No one has applied for a business account yet.</p></div>
        @else
            <div class="ap-table-scroll">
                <table class="ap-table ap-business-table">
                    <thead><tr><th>Company</th><th>Proprietor</th><th>Contact</th><th>Status</th><th>Applied on</th><th>Actions</th></tr></thead>
                    <tbody>
                        @foreach($applications as $app)
                            <tr class="{{ $app->status === 'pending' ? 'ap-row-new' : '' }}">
                                <td>
                                    <strong>{{ $app->company_name }}</strong>
                                    @if($app->status === 'pending')<span class="ap-new-dot" title="New application"></span>@endif
                                </td>
                                <td>
                                    <div class="ap-person">
                                        <span class="ap-avatar">{{ strtoupper(substr($app->proprietor_name, 0, 2)) }}</span>
                                        <div><strong>{{ $app->proprietor_name }}</strong><span style="display:block;color:#6b7280;font-size:10px;">NID: {{ $app->nid_no }}</span></div>
                                    </div>
                                </td>
                                <td class="ap-contact-info">
                                    <div style="font-size: 11px;">
                                        <div><strong style="color: #4b5563;">Mobile:</strong> {{ $app->mobile_no }}</div>
                                        <div><strong style="color: #4b5563;">Email:</strong> <a href="mailto:{{ $app->email }}">{{ $app->email }}</a></div>
                                    </div>
                                </td>
                                <td><span class="ap-source-status status-{{ $app->status }}">{{ ucfirst($app->status) }}</span></td>
                                <td><span class="ap-date">{{ $app->created_at->format('M d, Y') }}</span><small class="ap-date-relative">{{ $app->created_at->diffForHumans() }}</small></td>
                                <td>
                                    <div class="ap-row-actions">
                                        <a href="{{ route('admin.business-applications.show', $app->id) }}" class="ap-row-button primary">Review <span>→</span></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($applications->hasPages())<div class="ap-pagination">{{ $applications->links() }}</div>@endif
        @endif
    </section>
</div>
@endsection

@push('styles')
<style>
    .ap-success{display:flex;align-items:center;gap:10px;padding:13px 16px;border:1px solid #bfead7;border-radius:14px;color:#087b55;background:#ecfaf5;font-size:11px;font-weight:750}.ap-success svg{width:18px;height:18px;fill:none;stroke:currentColor;stroke-width:2}
    .ap-head-metric{min-width:185px;display:flex;align-items:center;gap:12px;padding:14px 17px;border:1px solid rgba(255,255,255,.2);border-radius:15px;background:rgba(255,255,255,.1);backdrop-filter:blur(10px)}.ap-head-metric strong{font-size:29px;line-height:1}.ap-head-metric span{color:#c4d0e9;font-size:9px;line-height:1.45}
    .ap-new-dot{display:inline-block;width:6px;height:6px;margin-left:6px;border-radius:50%;background:#d97706;box-shadow:0 0 0 4px rgba(217,119,6,.1)}
    
    .ap-source-status{display:inline-flex;align-items:center;gap:6px;padding:6px 9px;border-radius:99px;color:#52617c;background:#eef1f6;font-size:8px;font-weight:800;white-space:nowrap}.ap-source-status:before{content:'';width:5px;height:5px;border-radius:50%;background:currentColor}
    .ap-source-status.status-pending{color:#d97706;background:#fef3c7}
    .ap-source-status.status-reviewed{color:#2563eb;background:#dbeafe}
    .ap-source-status.status-accepted{color:#059669;background:#d1fae5}
    .ap-source-status.status-rejected{color:#dc2626;background:#fee2e2}
    
    tr.ap-row-new td { background: rgba(245, 158, 11, 0.05); }
    
    .ap-date{display:block;color:#43516b;font-weight:700}.ap-date-relative{display:block;margin-top:3px;color:#96a1b3;font-size:8px}.ap-row-actions{display:flex;align-items:center;gap:7px}.ap-row-button{min-height:31px;display:inline-flex;align-items:center;gap:5px;padding:0 11px;border:1px solid #dfe5ef;border-radius:9px;color:#52617c;background:#fff;font-size:9px;font-weight:800;cursor:pointer}.ap-row-button.primary{color:#5d35d9;border-color:#ded4fa;background:#f5f1ff}
    .ap-contact-info a { color: #2563eb; text-decoration: none; }
    .ap-contact-info a:hover { text-decoration: underline; }
</style>
@endpush
