@extends('layouts.operations-dashboard')

@section('title', 'Track Shipment')

@push('styles')
<style>
    .ct-shell { display:grid; gap:16px; }
    .ct-search-hero { position:relative; min-height:260px; overflow:hidden; display:flex; align-items:center; padding:34px; border-radius:20px; color:white; background:#07174d url('/images/tracking-dark-hero.webp') 72% 52%/cover no-repeat; box-shadow:0 18px 45px rgba(18,35,91,.16); }
    .ct-search-hero::after { content:''; position:absolute; inset:0; background:linear-gradient(90deg,rgba(4,15,58,.98) 0%,rgba(12,28,91,.92) 40%,rgba(16,34,112,.38) 68%,rgba(20,35,117,.12)); }
    .ct-search-content { position:relative; z-index:2; width:min(700px,65%); }
    .ct-kicker { display:inline-flex; align-items:center; gap:7px; margin-bottom:10px; padding:6px 10px; border:1px solid rgba(255,255,255,.2); border-radius:99px; background:rgba(255,255,255,.1); font-size:9px; font-weight:800; letter-spacing:.1em; text-transform:uppercase; }
    .ct-kicker::before { content:''; width:7px; height:7px; border-radius:50%; background:#38d896; box-shadow:0 0 0 5px rgba(56,216,150,.12); }
    .ct-search-hero h1 { margin:0; font-size:31px; line-height:1.12; letter-spacing:-.8px; font-weight:800; }
    .ct-search-hero p { max-width:570px; margin:9px 0 20px; color:#d9e3ff; font-size:12px; line-height:1.6; }
    .ct-search-form { max-width:650px; display:flex; gap:7px; padding:6px; border:1px solid rgba(255,255,255,.2); border-radius:15px; background:rgba(255,255,255,.96); box-shadow:0 14px 35px rgba(0,0,0,.2); }
    .ct-search-input-wrap { min-width:0; flex:1; display:flex; align-items:center; gap:10px; padding:0 12px; color:#8290aa; }
    .ct-search-input-wrap svg { width:20px; height:20px; flex:0 0 auto; }
    .ct-search-input { min-width:0; width:100%; height:46px; border:0; outline:0; color:#11203f; background:transparent; font-size:13px; font-weight:650; }
    .ct-search-input::placeholder { color:#9aa6bb; font-weight:500; }
    .ct-search-button { height:46px; padding:0 22px; border:0; border-radius:11px; color:white; background:linear-gradient(135deg,#7950f8,#4d24de); box-shadow:0 8px 18px rgba(83,46,216,.28); font-size:11px; font-weight:800; cursor:pointer; }
    .ct-search-note { display:flex; flex-wrap:wrap; gap:16px; margin-top:13px; color:#d4def8; font-size:9px; font-weight:650; }
    .ct-search-note span { display:inline-flex; align-items:center; gap:5px; }
    .ct-search-note i { width:6px; height:6px; border-radius:50%; background:#38d896; }
    .ct-alert { display:flex; align-items:flex-start; gap:14px; padding:18px; border:1px solid #ffd8df; border-radius:16px; color:#a51f37; background:#fff6f7; box-shadow:0 8px 24px rgba(111,30,50,.05); }
    .ct-alert.unavailable { color:#95610a; border-color:#f6e2ae; background:#fffaf0; }
    .ct-alert-icon { width:38px; height:38px; flex:0 0 auto; display:grid; place-items:center; border-radius:11px; background:rgba(255,255,255,.72); }
    .ct-alert strong { display:block; font-size:13px; }
    .ct-alert p { margin:4px 0 0; color:#6f5870; font-size:10px; line-height:1.5; }
    .ct-result { overflow:hidden; border:1px solid #e5eaf3; border-radius:18px; background:white; box-shadow:0 12px 35px rgba(22,42,84,.07); }
    .ct-result-head { padding:24px 26px; border-bottom:1px solid #edf0f5; background:linear-gradient(135deg,#fff,#fafbff); }
    .ct-result-top { display:flex; align-items:flex-start; justify-content:space-between; gap:18px; }
    .ct-status { display:inline-flex; align-items:center; gap:7px; padding:6px 10px; border-radius:99px; color:#315fc7; background:#edf4ff; font-size:9px; font-weight:800; }
    .ct-status::before { content:''; width:7px; height:7px; border-radius:50%; background:currentColor; }
    .ct-status.delivered { color:#078e5c; background:#eaf9f2; }
    .ct-status.failed,.ct-status.returned { color:#d92d4b; background:#fff0f3; }
    .ct-result-title { margin:10px 0 0; color:#0b1739; font-size:24px; line-height:1.2; font-weight:800; letter-spacing:-.4px; }
    .ct-result-sub { margin:6px 0 0; color:#74829b; font-size:10px; }
    .ct-carrier-link { display:inline-flex; align-items:center; gap:7px; padding:10px 13px; border:1px solid #ddd4ff; border-radius:10px; color:#6035db; background:#f7f4ff; font-size:9px; font-weight:800; white-space:nowrap; }
    .ct-progress { display:grid; grid-template-columns:repeat(5,minmax(0,1fr)); margin-top:25px; }
    .ct-step { position:relative; text-align:center; }
    .ct-step:not(:last-child)::before { content:''; position:absolute; z-index:0; top:12px; left:50%; width:100%; height:3px; background:#e4e9f2; }
    .ct-step.done:not(:last-child)::before { background:linear-gradient(90deg,#6b3ded,#3475f2); }
    .ct-step-dot { position:relative; z-index:1; width:27px; height:27px; margin:0 auto; display:grid; place-items:center; border:4px solid white; border-radius:50%; color:#96a1b4; background:#dfe5ef; box-shadow:0 0 0 1px #dfe5ef; font-size:8px; font-weight:900; }
    .ct-step.active .ct-step-dot,.ct-step.done .ct-step-dot { color:white; background:#6534f5; box-shadow:0 0 0 1px #6534f5,0 5px 12px rgba(101,52,245,.22); }
    .ct-step-label { margin:8px 0 0; color:#9aa5b7; font-size:8px; font-weight:700; }
    .ct-step.active .ct-step-label,.ct-step.done .ct-step-label { color:#334361; }
    .ct-result-grid { display:grid; grid-template-columns:minmax(0,1.35fr) minmax(300px,.65fr); }
    .ct-timeline { padding:24px 26px; border-right:1px solid #edf0f5; }
    .ct-panel-title { margin:0; color:#172443; font-size:15px; font-weight:800; }
    .ct-panel-copy { margin:5px 0 0; color:#8591a7; font-size:9px; }
    .ct-events { margin-top:20px; }
    .ct-event { position:relative; display:grid; grid-template-columns:24px minmax(0,1fr) auto; gap:12px; padding-bottom:22px; }
    .ct-event:not(:last-child)::before { content:''; position:absolute; top:20px; bottom:2px; left:8px; width:1px; background:#dfe5ef; }
    .ct-event-dot { position:relative; z-index:1; width:17px; height:17px; margin-top:2px; border:4px solid #ede8ff; border-radius:50%; background:#6534f5; }
    .ct-event:not(:first-child) .ct-event-dot { border-color:#edf0f5; background:#92a0b6; }
    .ct-event h3 { margin:0; color:#21304d; font-size:11px; font-weight:800; }
    .ct-event p { margin:4px 0 0; color:#6f7e98; font-size:9px; line-height:1.5; }
    .ct-event-location { display:flex; align-items:center; gap:4px; margin-top:5px!important; color:#536583!important; font-weight:700; }
    .ct-event time { color:#8d99ad; font-size:8px; line-height:1.45; text-align:right; white-space:nowrap; }
    .ct-details { padding:24px; background:#fafbfe; }
    .ct-detail-list { display:grid; gap:15px; margin:18px 0 0; }
    .ct-detail { padding-bottom:13px; border-bottom:1px solid #e8ecf3; }
    .ct-detail:last-child { padding-bottom:0; border-bottom:0; }
    .ct-detail dt { color:#8a96aa; font-size:8px; font-weight:800; letter-spacing:.08em; text-transform:uppercase; }
    .ct-detail dd { margin:5px 0 0; color:#20304f; font-size:10px; font-weight:750; line-height:1.5; }
    .ct-detail dd.mono { color:#5c37d9; font-family:ui-monospace,SFMono-Regular,Menlo,monospace; }
    .ct-empty { padding:32px; border:1px dashed #dbe1eb; border-radius:14px; color:#78869e; background:#fbfcfe; text-align:center; font-size:10px; }
    .ct-start { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; }
    .ct-start-card { padding:20px; border:1px solid #e7eaf2; border-radius:15px; background:white; box-shadow:0 8px 24px rgba(22,42,84,.04); }
    .ct-start-icon { width:38px; height:38px; display:grid; place-items:center; border-radius:11px; color:#6534f5; background:#f1edff; font-size:11px; font-weight:900; }
    .ct-start-card h2 { margin:13px 0 0; color:#1b2948; font-size:12px; font-weight:800; }
    .ct-start-card p { margin:5px 0 0; color:#7c899f; font-size:9px; line-height:1.55; }
    @media (max-width:900px) {
        .ct-search-content { width:78%; }
        .ct-result-grid { grid-template-columns:1fr; }
        .ct-timeline { border-right:0; border-bottom:1px solid #edf0f5; }
    }
    @media (max-width:767px) {
        .ct-search-hero { min-height:330px; padding:24px 18px; align-items:flex-end; background-position:63% center; }
        .ct-search-hero::after { background:linear-gradient(180deg,rgba(5,17,65,.3),rgba(5,17,65,.96) 58%); }
        .ct-search-content { width:100%; }
        .ct-search-hero h1 { font-size:24px; }
        .ct-search-form { display:block; padding:6px; }
        .ct-search-input-wrap { padding-inline:9px; }
        .ct-search-button { width:100%; }
        .ct-search-note { gap:10px; }
        .ct-result-head,.ct-timeline,.ct-details { padding:20px 16px; }
        .ct-result-top { display:block; }
        .ct-carrier-link { margin-top:14px; }
        .ct-progress { overflow-x:auto; padding-bottom:5px; }
        .ct-step { min-width:75px; }
        .ct-step-label { font-size:7px; }
        .ct-event { grid-template-columns:22px minmax(0,1fr); }
        .ct-event time { grid-column:2; margin-top:-15px; text-align:left; }
        .ct-start { grid-template-columns:1fr; }
    }
</style>
@endpush

@section('content')
@php
    $statusSteps = ['pending', 'picked_up', 'in_transit', 'out_for_delivery', 'delivered'];
    $statusIndex = $shipment ? array_search($shipment->status, $statusSteps, true) : false;
    $statusIndex = $statusIndex === false ? 0 : $statusIndex;
    $events = $shipment?->trackingEvents ?? collect();
@endphp

<div class="ct-shell">
    <section class="ct-search-hero" aria-labelledby="tracking-title">
        <div class="ct-search-content">
            <span class="ct-kicker">Live shipment visibility</span>
            <h1 id="tracking-title">Track every handoff in one place.</h1>
            <p>Enter your Express Peek number or carrier reference to see the current status, route, delivery estimate, and complete tracking history without leaving your dashboard.</p>
            <form class="ct-search-form" method="GET" action="{{ route('customer.track') }}">
                <label class="ct-search-input-wrap">
                    <svg class="ops-icon" viewBox="0 0 24 24"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                    <input class="ct-search-input" name="tracking" value="{{ $trackingNumber }}" required autocomplete="off" placeholder="Enter tracking number" aria-label="Tracking number">
                </label>
                <button class="ct-search-button" type="submit">Track Shipment</button>
            </form>
            <div class="ct-search-note"><span><i></i>Express Peek updates</span><span><i></i>Carrier references supported</span><span><i></i>Secure lookup</span></div>
        </div>
    </section>

    @if($error)
        <section class="ct-alert {{ $lookupUnavailable ? 'unavailable' : '' }}" aria-live="polite">
            <span class="ct-alert-icon"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M12 9v4M12 17h.01"/><path d="M12 3 2.5 20h19L12 3Z"/></svg></span>
            <div><strong>{{ $lookupUnavailable ? 'Tracking temporarily unavailable' : 'Shipment not found' }}</strong><p>{{ $error }}</p></div>
        </section>
    @elseif($shipment)
        <section class="ct-result" aria-live="polite">
            <header class="ct-result-head">
                <div class="ct-result-top">
                    <div>
                        <span class="ct-status {{ $shipment->status }}">{{ $shipment->status_label }}</span>
                        <h2 class="ct-result-title">Shipment {{ $shipment->tracking_number }}</h2>
                        <p class="ct-result-sub">{{ $shipment->carrier_name ?: 'Express Peek logistics network' }} · Last updated {{ optional($events->first()?->occurred_at)->diffForHumans() ?? 'recently' }}</p>
                    </div>
                    @if($shipment->carrier_tracking_number)
                        <a class="ct-carrier-link" href="{{ $shipment->carrier_tracking_url }}" target="_blank" rel="noopener noreferrer">Optional carrier details <svg class="ops-icon" viewBox="0 0 24 24"><path d="M14 3h7v7M21 3 10 14M5 7v12h12v-5"/></svg></a>
                    @endif
                </div>
                <div class="ct-progress" aria-label="Shipment progress">
                    @foreach(['Booked','Picked up','In transit','Out for delivery','Delivered'] as $index => $label)
                        <div class="ct-step {{ $index < $statusIndex ? 'done' : ($index === $statusIndex ? 'active' : '') }}">
                            <span class="ct-step-dot">{{ $index < $statusIndex ? '✓' : $index + 1 }}</span>
                            <p class="ct-step-label">{{ $label }}</p>
                        </div>
                    @endforeach
                </div>
            </header>

            <div class="ct-result-grid">
                <article class="ct-timeline">
                    <h2 class="ct-panel-title">Tracking history</h2>
                    <p class="ct-panel-copy">Updates recorded by Express Peek and your delivery network.</p>
                    <div class="ct-events">
                        @forelse($events as $event)
                            <div class="ct-event">
                                <span class="ct-event-dot"></span>
                                <div>
                                    <h3>{{ \App\Models\Shipment::statuses()[$event->status] ?? ucwords(str_replace('_', ' ', $event->status)) }}</h3>
                                    <p>{{ $loop->last && $event->status === \App\Models\Shipment::STATUS_PENDING ? $shipment->creation_note : ($event->notes ?: 'Shipment status updated.') }}</p>
                                    @if($event->location)<p class="ct-event-location"><svg class="ops-icon" style="width:12px;height:12px" viewBox="0 0 24 24"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/></svg>{{ $event->location }}</p>@endif
                                </div>
                                <time>{{ optional($event->occurred_at)->format('M d, Y') }}<br>{{ optional($event->occurred_at)->format('g:i A') }}</time>
                            </div>
                        @empty
                            <div class="ct-empty">Your shipment is registered. New tracking events will appear here as the package moves.</div>
                        @endforelse
                    </div>
                </article>

                <aside class="ct-details">
                    <h2 class="ct-panel-title">Shipment details</h2>
                    <p class="ct-panel-copy">The essential information for this delivery.</p>
                    <dl class="ct-detail-list">
                        <div class="ct-detail"><dt>Express Peek tracking</dt><dd class="mono">{{ $shipment->tracking_number }}</dd></div>
                        @if($shipment->carrier_tracking_number)<div class="ct-detail"><dt>Carrier reference</dt><dd class="mono">{{ $shipment->carrier_tracking_number }}</dd></div>@endif
                        <div class="ct-detail"><dt>Route</dt><dd>{{ $shipment->sender_city ?: $shipment->sender_country ?: 'Origin' }} → {{ $shipment->receiver_city ?: $shipment->receiver_country ?: 'Destination' }}</dd></div>
                        <div class="ct-detail"><dt>Estimated delivery</dt><dd>{{ $shipment->estimated_delivery?->format('l, M d, Y') ?? 'To be confirmed' }}</dd></div>
                        <div class="ct-detail"><dt>Package</dt><dd>{{ $shipment->total_packages ?: 1 }} {{ ($shipment->total_packages ?: 1) == 1 ? 'piece' : 'pieces' }}{{ $shipment->total_weight ? ' · '.rtrim(rtrim(number_format($shipment->total_weight, 2), '0'), '.').' kg' : '' }}</dd></div>
                        <div class="ct-detail"><dt>Destination</dt><dd>{{ collect([$shipment->receiver_city, $shipment->receiver_country])->filter()->join(', ') ?: 'Not available' }}</dd></div>
                    </dl>
                </aside>
            </div>
        </section>
    @else
        <section class="ct-start" aria-label="How tracking works">
            @foreach([
                ['01','Search securely','Use either your Express Peek number or the carrier reference.'],
                ['02','Follow the journey','See every recorded handoff, status change, and location in one timeline.'],
                ['03','Plan the arrival','Review the current route and expected delivery without leaving your account.'],
            ] as [$number,$title,$copy])
                <article class="ct-start-card"><span class="ct-start-icon">{{ $number }}</span><h2>{{ $title }}</h2><p>{{ $copy }}</p></article>
            @endforeach
        </section>
    @endif
</div>
@endsection
