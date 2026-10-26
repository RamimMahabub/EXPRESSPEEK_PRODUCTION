@extends('layouts.operations-dashboard')

@section('title', 'Agent Dashboard')
@section('notification-count', $stats['pending'] ?: '1')

@section('content')
@php
    $statusClass = fn ($status) => in_array($status, ['pending','in_transit','picked_up','connected_to_carrier','out_for_delivery','delivered','failed','returned']) ? $status : 'pending';
    $activityValues = $activity->pluck('value')->map(fn ($value) => (int) $value)->values();
    $chartValues = $activityValues->contains(fn ($value) => $value > 0) ? $activityValues : collect([5,9,7,13,10,16,14]);
    $chartMax = max(10, $chartValues->max());
    $chartPoints = $chartValues->map(fn ($value, $index) => (10 + ($index * 113.33)).','.(150 - (($value / $chartMax) * 112)))->join(' ');
    $completed = $stats['total'] > 0 ? round(($shipments->whereIn('status',['connected_to_carrier','delivered'])->count() / $stats['total']) * 100) : 100;
@endphp

<section class="ops-hero">
    <div class="ops-hero-copy">
        <span class="ops-kicker">Delivery operations</span>
        <h1>Welcome back, {{ str(auth()->user()->name)->before(' ') }}.</h1>
        <p>Your assigned deliveries, priorities, and updates are organized here.</p>
        <div class="ops-command">
            <div class="ops-command-head"><span class="ops-command-title">Today’s workload</span><a class="ops-command-link" href="{{ route('agent.shipments.index') }}">Open queue →</a></div>
            <div class="ops-command-metrics">
                <div class="ops-command-metric"><strong>{{ $stats['pending'] }}</strong><span>Pickups<br>requiring action</span></div>
                <div class="ops-command-metric"><strong>{{ $stats['out_for_delivery'] }}</strong><span>Final-mile<br>deliveries</span></div>
            </div>
        </div>
    </div>
    <div class="ops-hero-actions has-five-actions">
        <a class="ops-action" href="{{ route('agent.shipments.create') }}"><span class="ops-action-icon purple"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg></span><strong>Create Shipment</strong><small>Book a new delivery</small></a>
        <a class="ops-action" href="{{ route('agent.shipments.index') }}"><span class="ops-action-icon blue"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M6 4h12v17H6z"/><path d="M9 9h6M9 13h6M9 17h4"/></svg></span><strong>Delivery Queue</strong><small>Manage assignments</small></a>
        <a class="ops-action" href="{{ route('agent.address-book.page') }}"><span class="ops-action-icon green"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M5 4h14v17H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/><path d="M8 9h7M8 13h7"/></svg></span><strong>Address Book</strong><small>Saved contacts</small></a>
        <a class="ops-action" href="{{ route('agent.dashboard') }}#track"><span class="ops-action-icon orange"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg></span><strong>Track Shipment</strong><small>Real-time tracking</small></a>
        <a class="ops-action" href="{{ route('quote') }}" data-open-quote-modal><span class="ops-action-icon teal"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M6 3h10l3 3v15H6z"/><path d="M16 3v4h4M9 11h6M9 15h6"/></svg></span><strong>Get a Check Delivery Cost</strong><small>Compare shipping rates</small></a>
    </div>
</section>

<section class="ops-stats">
    @foreach([
        ['Total Assigned',$stats['total'],'purple','All assigned shipments','up',route('agent.shipments.index')],
        ['Pending Pickup',$stats['pending'],'orange','Needs attention','warning',route('agent.shipments.index',['statuses'=>['pending']])],
        ['In Transit',$stats['in_transit'],'blue','Moving now','up',route('agent.shipments.index',['statuses'=>['in_transit']])],
        ['Out for Delivery',$stats['out_for_delivery'],'red','Final-mile queue','danger',route('agent.shipments.index',['statuses'=>['out_for_delivery']])],
        ['Completed Today',$stats['delivered_today'],'green','Connected or delivered','up',route('agent.shipments.index',['statuses'=>['connected_to_carrier','delivered'],'today'=>1])],
    ] as [$label,$value,$tone,$note,$noteClass,$href])
        <a class="ops-stat ops-card" href="{{ $href }}" aria-label="Open {{ $label }}"><div class="ops-stat-main"><span class="ops-soft-icon {{ $tone }}"><svg class="ops-icon" viewBox="0 0 24 24">@if($tone==='orange')<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>@elseif($tone==='green')<circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/>@elseif($tone==='red')<path d="M3 15h3l2-6 4 10 3-7 2 3h4"/>@else<path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/><path d="m4.5 7.5 7.5 4 7.5-4"/>@endif</svg></span><div><p class="ops-stat-label">{{ $label }}</p><p class="ops-stat-value">{{ number_format($value) }}</p></div></div><p class="ops-stat-note {{ $noteClass }}"><strong>{{ $note }}</strong></p></a>
    @endforeach
</section>

<section class="ops-grid">
    <article class="ops-panel ops-card">
        <div class="ops-section-head"><h2 class="ops-title">Assignment Activity</h2><span class="ops-link">Last 7 days</span></div>
        <div class="ops-chart">
            <svg viewBox="0 0 700 165" preserveAspectRatio="none" role="img" aria-label="Seven day assignment activity"><defs><linearGradient id="agentActivityFill" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#2f73f3" stop-opacity=".28"/><stop offset="1" stop-color="#2f73f3" stop-opacity=".02"/></linearGradient></defs><g stroke="#e9edf4" stroke-width="1" stroke-dasharray="5 5"><line x1="0" y1="38" x2="700" y2="38"/><line x1="0" y1="75" x2="700" y2="75"/><line x1="0" y1="112" x2="700" y2="112"/><line x1="0" y1="150" x2="700" y2="150"/></g><polygon points="10,150 {{ $chartPoints }} 690,150" fill="url(#agentActivityFill)"/><polyline points="{{ $chartPoints }}" fill="none" stroke="#2d70ef" stroke-width="3" vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"/>@foreach($chartValues as $index => $value)<circle cx="{{ 10 + ($index * 113.33) }}" cy="{{ 150 - (($value / $chartMax) * 112) }}" r="4" fill="#2d70ef"/>@endforeach</svg>
            <div class="ops-chart-labels">@foreach($activity as $point)<span>{{ $point['label'] }}</span>@endforeach</div>
        </div>
        <div class="ops-legend"><span>Pending<strong>{{ $stats['pending'] }}</strong></span><span>In Transit<strong>{{ $stats['in_transit'] }}</strong></span><span>Out for Delivery<strong>{{ $stats['out_for_delivery'] }}</strong></span><span>Completed Today<strong>{{ $stats['delivered_today'] }}</strong></span></div>
    </article>

    <article class="ops-panel ops-card">
        <div class="ops-section-head"><h2 class="ops-title">Delivery Queue</h2><a class="ops-link" href="{{ route('agent.shipments.index') }}">View all →</a></div>
        @if($shipments->isEmpty())
            <div class="ops-empty"><span class="ops-soft-icon blue"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M6 4h12v17H6z"/><path d="M9 9h6M9 13h6"/></svg></span>No shipments are currently assigned to you.</div>
        @else
            <div class="ops-table-wrap"><table class="ops-table"><thead><tr><th>Tracking #</th><th>Recipient</th><th>Destination</th><th>Status</th><th>Action</th></tr></thead><tbody>
                @foreach($shipments->take(8) as $shipment)
                    <tr><td class="ops-tracking">{{ $shipment->tracking_number }}</td><td><strong>{{ $shipment->receiver_name }}</strong><small>{{ $shipment->sender_name ?: ($shipment->sender?->name ?: 'Unknown sender') }}</small></td><td><strong>{{ $shipment->receiver_city }}</strong><small>{{ $shipment->receiver_country }}</small></td><td><span class="ops-status {{ $statusClass($shipment->status) }}">{{ $shipment->status_label }}</span></td><td><a class="ops-action-link" href="{{ route('agent.shipments.edit',$shipment) }}">Update</a></td></tr>
                @endforeach
            </tbody></table></div>
        @endif
    </article>

    <aside class="ops-side">
        <article class="ops-panel ops-card"><div class="ops-section-head"><h2 class="ops-title">Today’s Focus</h2></div><div class="ops-list">
            <div class="ops-list-row"><span>Awaiting pickup</span><strong>{{ $stats['pending'] }}</strong></div>
            <div class="ops-list-row"><span>Out for delivery</span><strong>{{ $stats['out_for_delivery'] }}</strong></div>
            <div class="ops-list-row"><span>Completed today</span><strong>{{ $stats['delivered_today'] }}</strong></div>
            <div class="ops-list-row"><div><span>Overall completion</span><div class="ops-progress"><i style="width:{{ max(4,$completed) }}%"></i></div></div><strong>{{ $completed }}%</strong></div>
        </div></article>
        <article class="ops-panel ops-card"><div class="ops-section-head"><h2 class="ops-title">Quick Access</h2></div><div class="ops-quick-grid">
            <a class="ops-quick" href="{{ route('agent.shipments.create') }}"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>New Shipment</a>
            <a class="ops-quick" href="{{ route('agent.shipments.index') }}"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M6 4h12v17H6z"/></svg>Full Queue</a>
            <a class="ops-quick" href="{{ route('agent.address-book.page') }}"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M5 4h14v17H5"/></svg>Address Book</a>
            <a class="ops-quick" href="{{ route('customer-service') }}"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M4 14v-2a8 8 0 0 1 16 0v2M4 14a2 2 0 0 0 2 2h1v-6H6a2 2 0 0 0-2 2M20 14a2 2 0 0 1-2 2h-1v-6h1a2 2 0 0 1 2 2"/></svg>Support</a>
        </div></article>
    </aside>
</section>

<x-dashboard-tracking-search 
    :action="route('agent.dashboard')" 
    :tracking-number="$trackingNumber" 
    :shipment="$trackedShipment" 
    :error="$trackingError" 
    :lookup-unavailable="$trackingLookupUnavailable" 
/>
@endsection
