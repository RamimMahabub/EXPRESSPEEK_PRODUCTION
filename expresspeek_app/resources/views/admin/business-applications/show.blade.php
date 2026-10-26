@extends('layouts.operations-dashboard')

@section('title', 'Review Business Application - ' . $application->company_name)
@section('page-title', 'Application Details')
@section('page-subtitle', 'Review and process corporate account request')

@section('content')
<div class="ap-shell ap-business-show-page">
    
    <div class="ap-action-bar">
        <a href="{{ route('admin.business-applications.index') }}" class="ap-back-link"><svg viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg>Back to applications</a>
        <div class="ap-action-group">
            <span class="ap-source-status status-{{ $application->status }}" style="margin-right: 15px;">{{ ucfirst($application->status) }}</span>
            <button class="ap-button primary" onclick="alert('Accept functionality coming soon')">Accept Account</button>
            <button class="ap-button danger" onclick="alert('Reject functionality coming soon')">Reject</button>
        </div>
    </div>

    <div class="ap-detail-grid">
        {{-- Left Column --}}
        <div class="ap-detail-main">
            <section class="ap-card">
                <div class="ap-card-head">
                    <h2 class="ap-card-title">Business Information</h2>
                </div>
                <div class="ap-card-body">
                    <div class="ap-info-grid">
                        <div class="ap-info-item">
                            <label>Company Name</label>
                            <p class="ap-info-value" style="font-size: 16px; font-weight: 800; color: #1f2937;">{{ $application->company_name }}</p>
                        </div>
                        <div class="ap-info-item">
                            <label>Trade License</label>
                            <p class="ap-info-value">{{ $application->trade_license ?: 'Not Provided' }}</p>
                        </div>
                        <div class="ap-info-item">
                            <label>Proprietor Name</label>
                            <p class="ap-info-value">{{ $application->proprietor_name }}</p>
                        </div>
                        <div class="ap-info-item">
                            <label>NID No (Proprietor)</label>
                            <p class="ap-info-value" style="font-family: monospace; font-size: 13px;">{{ $application->nid_no }}</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        {{-- Right Column --}}
        <div class="ap-detail-side">
            <section class="ap-card">
                <div class="ap-card-head">
                    <h2 class="ap-card-title">Contact Information</h2>
                </div>
                <div class="ap-card-body">
                    <div class="ap-contact-card">
                        <div class="ap-contact-icon bg-blue-100 text-blue-600">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        </div>
                        <div class="ap-contact-details">
                            <label>Email Address</label>
                            <a href="mailto:{{ $application->email }}">{{ $application->email }}</a>
                        </div>
                    </div>
                    
                    <div class="ap-contact-card">
                        <div class="ap-contact-icon bg-emerald-100 text-emerald-600">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        </div>
                        <div class="ap-contact-details">
                            <label>Mobile No (Alternative)</label>
                            <a href="tel:{{ $application->mobile_no }}">{{ $application->mobile_no }}</a>
                        </div>
                    </div>

                    <div class="ap-contact-card" style="align-items: flex-start; margin-bottom: 0;">
                        <div class="ap-contact-icon bg-orange-100 text-orange-600" style="margin-top: 2px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        </div>
                        <div class="ap-contact-details">
                            <label>Address</label>
                            <p style="margin:0; font-size: 13px; color: #4b5563; line-height: 1.5;">{{ $application->address }}</p>
                        </div>
                    </div>
                </div>
            </section>
            
            <section class="ap-card mt-5">
                <div class="ap-card-body" style="padding: 15px;">
                    <div style="font-size: 11px; color: #6b7280; display: flex; justify-content: space-between;">
                        <span>Applied On:</span>
                        <strong>{{ $application->created_at->format('M d, Y h:i A') }}</strong>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .ap-action-bar{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;padding-bottom:15px;border-bottom:1px solid #e5e7eb}
    .ap-back-link{display:flex;align-items:center;gap:4px;color:#6b7280;font-size:13px;font-weight:600;text-decoration:none}
    .ap-back-link:hover{color:#111827}
    .ap-back-link svg{width:16px;height:16px;fill:none;stroke:currentColor;stroke-width:2}
    .ap-action-group{display:flex;align-items:center;gap:10px}
    
    .ap-detail-grid{display:grid;grid-template-columns:2fr 1fr;gap:25px}
    
    .ap-info-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px}
    .ap-info-item label{display:block;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:5px}
    .ap-info-value{margin:0;font-size:14px;color:#374151;font-weight:500}
    
    .ap-card-body { padding: 23px; }

    .ap-contact-card{display:flex;align-items:center;gap:15px;margin-bottom:20px;padding:12px;border-radius:12px;background:#f9fafb;border:1px solid #f3f4f6}
    .ap-contact-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .ap-contact-icon svg{width:20px;height:20px}
    .ap-contact-details label{display:block;font-size:11px;font-weight:600;color:#6b7280;margin-bottom:2px}
    .ap-contact-details a{font-size:14px;font-weight:600;color:#2563eb;text-decoration:none}
    .ap-contact-details a:hover{text-decoration:underline}
    
    .ap-source-status{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:99px;color:#52617c;background:#eef1f6;font-size:11px;font-weight:800;white-space:nowrap}
    .ap-source-status:before{content:'';width:6px;height:6px;border-radius:50%;background:currentColor}
    .ap-source-status.status-pending{color:#d97706;background:#fef3c7}
    .ap-source-status.status-reviewed{color:#2563eb;background:#dbeafe}
    .ap-source-status.status-accepted{color:#059669;background:#d1fae5}
    .ap-source-status.status-rejected{color:#dc2626;background:#fee2e2}
    
    @media(max-width: 768px) {
        .ap-detail-grid { grid-template-columns: 1fr; }
        .ap-info-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush
