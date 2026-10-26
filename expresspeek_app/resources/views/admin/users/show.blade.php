@extends('layouts.operations-dashboard')

@section('title', 'User Details - ' . $user->name)
@section('page-title', 'User Profile')
@section('page-subtitle', 'Overview and shipment history')

@section('content')
<div class="ap-shell ap-user-profile">
    <section class="ap-page-head" style="margin-bottom: 24px;">
        <div class="ap-head-copy">
            <span class="ap-eyebrow">User Directory</span>
            <h1>{{ $user->name }}</h1>
            <p>
                Account #{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }} &bull; 
                {{ $user->is_business ? 'Business Account' : 'Individual Account' }} 
                @if($user->company_name) &bull; {{ $user->company_name }} @endif
            </p>
        </div>
        <div class="ap-head-actions">
            <a href="{{ route('admin.users.edit', $user) }}" class="ap-button light">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                Edit User
            </a>
        </div>
    </section>

    <div style="display: grid; grid-template-columns: minmax(300px, 1fr) 2fr; gap: 24px; align-items: start;">
        <section class="ap-card" style="padding: 24px;">
            <h2 class="ap-card-title" style="margin-bottom: 16px;">Contact Information</h2>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 11px; font-weight: 800; color: #7d8aa1; text-transform: uppercase; margin-bottom: 4px;">Email</label>
                <a href="mailto:{{ $user->email }}" style="color: #5c31d5; font-weight: 600; text-decoration: none;">{{ $user->email }}</a>
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 11px; font-weight: 800; color: #7d8aa1; text-transform: uppercase; margin-bottom: 4px;">Phone</label>
                @if($user->phone)
                    <a href="tel:{{ $user->phone }}" style="color: #16a779; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16.4v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.3 19.3 0 0 1-6-6A19.8 19.8 0 0 1 1.1 3.7 2 2 0 0 1 3.1 1.5h3A2 2 0 0 1 8 3.2c.1 1 .4 2 .7 2.9a2 2 0 0 1-.4 2.1L7 9.5a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.9.6 2.9.7a2 2 0 0 1 1.7 1.9Z"/></svg>
                        {{ $user->phone }}
                    </a>
                @else
                    <span style="color: #9ba6b8; font-size: 13px;">Not provided</span>
                @endif
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 11px; font-weight: 800; color: #7d8aa1; text-transform: uppercase; margin-bottom: 4px;">Roles</label>
                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                    @foreach($user->roles as $r)
                        <span class="ap-badge {{ $r->name }}">{{ ucfirst($r->name) }}</span>
                    @endforeach
                </div>
            </div>

            <div>
                <label style="display: block; font-size: 11px; font-weight: 800; color: #7d8aa1; text-transform: uppercase; margin-bottom: 4px;">Joined</label>
                <span style="color: #1a2744; font-weight: 600;">{{ $user->created_at->format('M d, Y') }}</span>
                <span style="color: #7d8aa1; font-size: 12px; margin-left: 6px;">({{ $user->created_at->diffForHumans() }})</span>
            </div>
        </section>

        <section class="ap-card ap-table-card">
            <div class="ap-card-head">
                <div>
                    <h2 class="ap-card-title">Shipment History</h2>
                    <p class="ap-card-copy">{{ $shipments->total() }} shipments associated with this account</p>
                </div>
                <a href="{{ route('admin.shipments.index', ['search' => $user->email]) }}" style="font-size: 12px; font-weight: 700; color: #5c31d5; text-decoration: none;">View in Shipments &rarr;</a>
            </div>
            
            <div class="ap-table-scroll">
                <table class="ap-table">
                    <thead>
                        <tr>
                            <th>Tracking #</th>
                            <th>Receiver</th>
                            <th>Destination</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shipments as $shipment)
                            <tr>
                                <td>
                                    <strong>{{ $shipment->tracking_number ?: '—' }}</strong>
                                    @if($shipment->is_sourcing)
                                        <br><span style="font-size: 9px; padding: 2px 6px; border-radius: 4px; background: #fdf4ff; color: #a21caf; font-weight: 700; border: 1px solid #f5d0fe;">SOURCING</span>
                                    @endif
                                </td>
                                <td>{{ $shipment->receiver_name ?: '—' }}</td>
                                <td>{{ $shipment->receiver_city ?: '—' }}{{ $shipment->receiver_country ? ', ' . $shipment->receiver_country : '' }}</td>
                                <td>
                                    <span style="font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 99px; background: #f1f5f9; color: #475569;">
                                        {{ str_replace('_', ' ', ucfirst($shipment->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <span style="color: #43516b; font-weight: 700;">{{ $shipment->created_at->format('M d, Y') }}</span>
                                </td>
                                <td>
                                    <a href="{{ $shipment->is_sourcing ? route('admin.sourcing-requests.show', $shipment) : route('admin.shipments.edit', $shipment) }}" style="display: inline-flex; align-items: center; justify-content: center; height: 31px; padding: 0 11px; border: 1px solid #dfe5ef; border-radius: 9px; color: #52617c; background: #fff; font-size: 11px; font-weight: 800; text-decoration: none;">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><div class="ap-empty" style="padding: 40px 20px; text-align: center;"><strong style="display: block; color: #1a2744; margin-bottom: 4px;">No shipments found</strong><p style="color: #7d8aa1; font-size: 13px; margin: 0;">This user hasn't created any shipments yet.</p></div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($shipments->hasPages())
                <div class="ap-pagination" style="padding: 15px 20px; border-top: 1px solid #edf1f6;">
                    {{ $shipments->links() }}
                </div>
            @endif
        </section>
    </div>
</div>
@endsection

@push('styles')
<style>
    .ap-card { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03), 0 1px 3px rgba(0,0,0,0.05); }
    .ap-card-head { display: flex; align-items: center; justify-content: space-between; padding: 20px 24px; border-bottom: 1px solid #edf1f6; }
    .ap-card-title { margin: 0; font-size: 16px; font-weight: 800; color: #1a2744; }
    .ap-card-copy { margin: 4px 0 0; font-size: 12px; color: #7d8aa1; }
    
    .ap-table { width: 100%; border-collapse: collapse; text-align: left; }
    .ap-table th { padding: 12px 24px; font-size: 11px; font-weight: 800; color: #7d8aa1; text-transform: uppercase; background: #f8fafc; border-bottom: 1px solid #edf1f6; }
    .ap-table td { padding: 16px 24px; font-size: 13px; color: #43516b; border-bottom: 1px solid #edf1f6; vertical-align: middle; }
    .ap-table tr:last-child td { border-bottom: none; }
    .ap-table tbody tr:hover { background: #f8fafc; }
</style>
@endpush
