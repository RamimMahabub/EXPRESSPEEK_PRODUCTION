@extends('layouts.operations-dashboard')

@section('title', 'Admin Dashboard')
@section('notification-count', $newSourcingCount ?: '1')

@section('content')
@php
    $statusClass = fn ($status) => in_array($status, ['pending','in_transit','picked_up','connected_to_carrier','out_for_delivery','delivered','failed','returned']) ? $status : 'pending';
    $activityValues = $activity->pluck('value')->map(fn ($value) => (int) $value)->values();
    $chartValues = $activityValues->contains(fn ($value) => $value > 0) ? $activityValues : collect([12,22,25,38,31,20,24]);
    $chartMax = max(10, $chartValues->max());
    $chartPoints = $chartValues->map(fn ($value, $index) => (10 + ($index * 113.33)).','.(150 - (($value / $chartMax) * 112)))->join(' ');
    $totalUsers = max(1, $userStats['total']);
@endphp

<section class="ops-hero">
    <div class="ops-hero-copy">
        <span class="ops-kicker">Platform control center</span>
        <h1>Good to see you, {{ str(auth()->user()->name)->before(' ') }}.</h1>
        <p>Monitor shipments, customers, agents, and sourcing operations from one place.</p>
        <div class="ops-command">
            <div class="ops-command-head"><span class="ops-command-title">Operations pulse</span><a class="ops-command-link" href="{{ route('admin.shipments.index') }}">Open queue →</a></div>
            <div class="ops-command-metrics">
                <div class="ops-command-metric"><strong>{{ $shipmentStats['pending'] }}</strong><span>Pickups<br>awaiting action</span></div>
                <div class="ops-command-metric"><strong>{{ $newSourcingCount }}</strong><span>Sourcing<br>reviews waiting</span></div>
            </div>
        </div>
    </div>
    <div class="ops-hero-actions">
        <a class="ops-action" href="{{ route('admin.shipments.create') }}"><span class="ops-action-icon purple"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg></span><strong>Create Shipment</strong><small>Start a new delivery</small></a>
        <a class="ops-action" href="{{ route('admin.shipments.index') }}"><span class="ops-action-icon blue"><svg class="ops-icon" viewBox="0 0 24 24"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/><path d="m4.5 7.5 7.5 4 7.5-4"/></svg></span><strong>Manage Shipments</strong><small>View the full queue</small></a>
        <a class="ops-action" href="{{ route('admin.users.index') }}"><span class="ops-action-icon green"><svg class="ops-icon" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3"/><path d="M3 20a6 6 0 0 1 12 0M16 4a3 3 0 0 1 0 6M17 14a5 5 0 0 1 4 5"/></svg></span><strong>Manage Users</strong><small>Customers and agents</small></a>
        <a class="ops-action" href="{{ route('admin.sourcing-requests.index') }}"><span class="ops-action-icon orange"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M6 8h12l1 13H5L6 8Z"/><path d="M9 9V6a3 3 0 0 1 6 0v3"/></svg></span><strong>Shop From Bangladesh</strong><small>{{ $newSourcingCount }} awaiting review</small></a>
    </div>
</section>

<section class="ops-stats">
    @foreach([
        ['Total Shipments',$shipmentStats['total'],'purple','All platform shipments','up',route('admin.shipments.index')],
        ['Pending Pickup',$shipmentStats['pending'],'orange','Awaiting action','warning',route('admin.shipments.index',['statuses'=>['pending']])],
        ['In Transit',$shipmentStats['in_transit'],'blue','Active movements','up',route('admin.shipments.index',['statuses'=>['in_transit']])],
        ['Successful Outcomes',$shipmentStats['delivered'],'green','Carrier-connected or delivered','up',route('admin.shipments.index',['statuses'=>['connected_to_carrier','delivered']])],
        ['Sourcing Requests',$totalSourcingCount,'red',$newSourcingCount.' new requests','danger',route('admin.sourcing-requests.index')],
    ] as [$label,$value,$tone,$note,$noteClass,$href])
        <a class="ops-stat ops-card" href="{{ $href }}" aria-label="Open {{ $label }}"><div class="ops-stat-main"><span class="ops-soft-icon {{ $tone }}"><svg class="ops-icon" viewBox="0 0 24 24">@if($tone==='orange')<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>@elseif($tone==='green')<circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/>@elseif($tone==='red')<path d="M6 8h12l1 13H5L6 8Z"/><path d="M9 9V6a3 3 0 0 1 6 0v3"/>@else<path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/><path d="m4.5 7.5 7.5 4 7.5-4"/>@endif</svg></span><div><p class="ops-stat-label">{{ $label }}</p><p class="ops-stat-value">{{ number_format($value) }}</p></div></div><p class="ops-stat-note {{ $noteClass }}"><strong>{{ $note }}</strong></p></a>
    @endforeach
</section>

<section class="ops-grid">
    <article class="ops-panel ops-card">
        <div class="ops-section-head"><h2 class="ops-title">Shipment Activity</h2><span class="ops-link">Last 7 days</span></div>
        <div class="ops-chart">
            <svg viewBox="0 0 700 165" preserveAspectRatio="none" role="img" aria-label="Seven day shipment activity"><defs><linearGradient id="adminActivityFill" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#7245f4" stop-opacity=".3"/><stop offset="1" stop-color="#7245f4" stop-opacity=".02"/></linearGradient></defs><g stroke="#e9edf4" stroke-width="1" stroke-dasharray="5 5"><line x1="0" y1="38" x2="700" y2="38"/><line x1="0" y1="75" x2="700" y2="75"/><line x1="0" y1="112" x2="700" y2="112"/><line x1="0" y1="150" x2="700" y2="150"/></g><polygon points="10,150 {{ $chartPoints }} 690,150" fill="url(#adminActivityFill)"/><polyline points="{{ $chartPoints }}" fill="none" stroke="#6637f1" stroke-width="3" vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"/>@foreach($chartValues as $index => $value)<circle cx="{{ 10 + ($index * 113.33) }}" cy="{{ 150 - (($value / $chartMax) * 112) }}" r="4" fill="#6637f1"/>@endforeach</svg>
            <div class="ops-chart-labels">@foreach($activity as $point)<span>{{ $point['label'] }}</span>@endforeach</div>
        </div>
        <div class="ops-legend"><span>Pending<strong>{{ $shipmentStats['pending'] }}</strong></span><span>In Transit<strong>{{ $shipmentStats['in_transit'] }}</strong></span><span>Successful<strong>{{ $shipmentStats['delivered'] }}</strong></span><span>Total<strong>{{ $shipmentStats['total'] }}</strong></span></div>
    </article>

    <article class="ops-panel ops-card">
        <div class="ops-section-head"><h2 class="ops-title">Recent Shipments</h2><a class="ops-link" href="{{ route('admin.shipments.index') }}">View all →</a></div>
        @if($recentShipments->isEmpty())
            <div class="ops-empty"><span class="ops-soft-icon purple"><svg class="ops-icon" viewBox="0 0 24 24"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/></svg></span>No shipments have been created yet.</div>
        @else
            <div class="ops-table-wrap"><table class="ops-table"><thead><tr><th>Tracking #</th><th>Sender</th><th>Destination</th><th>Status</th><th>Action</th></tr></thead><tbody>
                @foreach($recentShipments as $shipment)
                    <tr><td class="ops-tracking">{{ $shipment->tracking_number }}</td><td><strong>{{ $shipment->sender_name ?: ($shipment->sender?->name ?: 'Unknown') }}</strong><small>{{ $shipment->receiver_name }}</small></td><td><strong>{{ $shipment->receiver_city }}</strong><small>{{ $shipment->receiver_country }}</small></td><td><span class="ops-status {{ $statusClass($shipment->status) }}">{{ $shipment->status_label }}</span></td><td><a class="ops-action-link" href="{{ route('admin.shipments.edit',$shipment) }}">Manage</a></td></tr>
                @endforeach
            </tbody></table></div>
        @endif
    </article>

    <aside class="ops-side">
        <article class="ops-panel ops-card"><div class="ops-section-head"><h2 class="ops-title">User Overview</h2><a class="ops-link" href="{{ route('admin.users.index') }}">Manage</a></div><div class="ops-list">
            @foreach([['Admins',$userStats['admins']],['Customers',$userStats['customers']],['Agents',$userStats['agents']]] as [$label,$count])
                <div class="ops-list-row"><div><span>{{ $label }}</span><div class="ops-progress"><i style="width:{{ max(4,round(($count/$totalUsers)*100)) }}%"></i></div></div><strong>{{ $count }}</strong></div>
            @endforeach
            <div class="ops-list-row"><span>Total users</span><strong>{{ $userStats['total'] }}</strong></div>
        </div></article>
        <article class="ops-panel ops-card"><div class="ops-section-head"><h2 class="ops-title">Quick Access</h2></div><div class="ops-quick-grid">
            <a class="ops-quick" href="{{ route('admin.address-book.page') }}"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M5 4h14v17H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/></svg>Address Book</a>
            <a class="ops-quick" href="{{ route('admin.rates.import.create') }}"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M12 16V4M7 9l5-5 5 5M4 14v6h16v-6"/></svg>Rate Upload</a>
            <a class="ops-quick" href="{{ route('admin.dashboard') }}#track"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>Track Shipment</a>
            <a class="ops-quick" href="{{ route('quote') }}" data-open-quote-modal><svg class="ops-icon" viewBox="0 0 24 24"><path d="M6 3h10l3 3v15H6z"/><path d="M9 11h6M9 15h6"/></svg>Get Check Delivery Cost</a>
            <a class="ops-quick" href="{{ route('admin.sourcing-requests.index') }}"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M6 8h12l1 13H5L6 8Z"/></svg>Sourcing</a>
            <a class="ops-quick" href="{{ route('profile.edit') }}"><svg class="ops-icon" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>My Profile</a>
        </div></article>
    </aside>
</section>

<x-dashboard-tracking-search 
    :action="route('admin.dashboard')" 
    :tracking-number="$trackingNumber" 
    :shipment="$trackedShipment" 
    :error="$trackingError" 
    :lookup-unavailable="$trackingLookupUnavailable" 
/>


@endsection
