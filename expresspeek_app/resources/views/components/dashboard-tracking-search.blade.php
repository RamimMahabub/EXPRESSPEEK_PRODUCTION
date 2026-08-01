@props([
    'action',
    'trackingNumber' => '',
])

<section id="track" class="dashboard-tracker" aria-labelledby="dashboard-tracking-title">
    <div class="dashboard-tracker-content">
        <span class="dashboard-tracker-kicker">Live shipment visibility</span>
        <h2 id="dashboard-tracking-title" class="dashboard-tracker-title">Track every handoff without leaving Express Peek.</h2>
        <p class="dashboard-tracker-copy">Use your Express Peek number or carrier reference to see the current status, delivery route, estimated arrival, and complete event history inside your dashboard.</p>
        <form class="dashboard-tracker-form" method="GET" action="{{ $action }}#track">
            <label class="dashboard-tracker-field">
                <svg viewBox="0 0 24 24"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                <input class="dashboard-tracker-input" name="tracking" value="{{ $trackingNumber }}" required autocomplete="off" placeholder="Enter tracking number" aria-label="Tracking number">
            </label>
            <button class="dashboard-tracker-button" type="submit">Track Shipment</button>
        </form>
        <div class="dashboard-tracker-benefits"><span><i></i>Express Peek updates</span><span><i></i>Carrier references supported</span><span><i></i>Secure lookup</span></div>
    </div>
</section>
