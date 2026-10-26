<!DOCTYPE html>
{{-- Versioned label template: avoids stale compiled Blade markup on cPanel. --}}
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shipment Label {{ $shipment->tracking_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Share+Tech+Mono&display=swap" rel="stylesheet">
    <style>
        @page { size:75mm 100mm; margin:0; }
        * { box-sizing:border-box; }
        html,body { width:75mm; margin:0; padding:0; }
        body { color:#000; background:#fff; font-family:'Share Tech Mono', 'Courier New', monospace; font-size:5.8pt; line-height:1.1; }
        strong { font-weight:700; }
        .label { position:relative; width:75mm; height:99.4mm; margin:0; padding:0; overflow:hidden; page-break-after:always; page-break-inside:avoid; background:#fff; }
        .label:last-of-type { page-break-after:avoid; }
        .panel { position:absolute; left:6mm; right:6mm; overflow:hidden; }
        table { width:100%; height:100%; border-collapse:collapse; table-layout:fixed; }
        td { padding:0; vertical-align:top; }
        .line-bottom { border-bottom:.32mm solid #000; }

        .logo-panel { top:1.5mm; height:12.7mm; text-align:right; }
        .logo-crop { display:inline-block; position:relative; width:12.7mm; height:12.2mm; overflow:hidden; }
        .logo-mark { position:absolute; top:0; left:0; width:43.6mm; height:12.2mm; max-width:none; }

        .title-panel { top:3.5mm; height:9mm; }
        .title-panel td { vertical-align:middle; text-align:center; }
        .service-code { width:25%; font-size:13pt; font-weight:900; letter-spacing:.7pt; font-family:'Orbitron', monospace; }
        .waybill-title { width:50%; border-left:.25mm solid #000; border-right:.25mm solid #000; }
        .waybill-title strong { display:block; font-size:10pt; line-height:1; font-weight:900; letter-spacing:.3pt; font-family:'Orbitron', monospace; }
        .waybill-title span { display:block; margin-top:.8mm; font-size:5.8pt; line-height:1; font-weight:700; letter-spacing:.5pt; font-family:'Share Tech Mono', monospace; }
        .title-spacer { width:25%; }

        .awb-panel { top:12.5mm; height:12.5mm; padding-top:0.3mm; padding-bottom:1.5mm; text-align:center; }
        .awb-line { height:5mm; text-align:left; font-size:8pt; white-space:nowrap; font-family:'Share Tech Mono', monospace; letter-spacing:.2pt; }
        .awb-line strong { font-size:14pt; line-height:1; font-weight:900; font-style:normal; letter-spacing:.8pt; font-family:'Orbitron', monospace; }
        .awb-carrier { text-align:left; font-size:5.4pt; font-weight:400; margin-top:0.3mm; line-height:1.35; word-break:break-word; overflow:hidden; font-family:'Share Tech Mono', monospace; letter-spacing:.3pt; }
        .barcode { display:block; width:67.5mm; height:9.6mm; margin:0 auto; }

        .party-panel { font-size:6.5pt; line-height:1.25; }
        .party-inner { position:absolute; top:1.5mm; left:1mm; right:1mm; bottom:1mm; overflow:hidden; }
        .shipper-panel .party-inner { top:1.5mm; }
        .shipper-panel { top:26mm; height:19mm; }
        .consignee-panel { top:45mm; height:19mm; }
        .party-panel.dense { font-size:5.4pt; line-height:1.15; }
        .party-panel.dense .party-name { font-size:6pt; }
        .party-panel.extra-dense { font-size:4.8pt; line-height:1.05; }
        .party-panel.extra-dense .party-name { font-size:5.5pt; }
        .party-panel.extra-dense .section-title { margin-bottom:0.4mm; }
        .party-panel.extra-dense .party-line, .party-panel.extra-dense .party-meta { margin-top:0.3mm; }
        .section-title { margin-bottom:0.8mm; font-size:6.5pt; line-height:1; font-weight:700; letter-spacing:.4pt; font-family:'Share Tech Mono', monospace; }
        .party-name { font-size:6.8pt; font-weight:700; margin-top:0.5mm; font-family:'Share Tech Mono', monospace; letter-spacing:.2pt; }
        .party-line { margin-top:0.6mm; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
        .party-meta { margin-top:0.6mm; }
        .party-meta td:first-child { width:66%; padding-right:1mm; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
        .party-meta td:last-child { width:34%; text-align:left; overflow-wrap:anywhere; }

        .tracking-panel { top:64mm; height:6mm; }
        .tracking-panel td { padding:.65mm 1mm .4mm; vertical-align:middle; }
        .tracking-label { width:47%; font-size:6pt; font-weight:700; white-space:nowrap; font-family:'Share Tech Mono', monospace; letter-spacing:.3pt; }
        .tracking-number { width:53%; font-size:6.5pt; font-weight:700; letter-spacing:.3pt; text-align:right; overflow-wrap:anywhere; overflow:hidden; font-family:'Share Tech Mono', monospace; }

        .metrics-panel { top:70mm; height:6mm; }
        .metrics-panel td { padding:.4mm .9mm .25mm; border-right:.22mm solid #000; vertical-align:middle; }
        .metrics-panel td:last-child { border-right:0; }
        .metric-label { display:block; font-size:4.6pt; line-height:1; font-weight:700; letter-spacing:.4pt; font-family:'Share Tech Mono', monospace; }
        .metric-value { display:block; margin-top:.3mm; font-size:5.4pt; line-height:1; font-weight:700; font-family:'Orbitron', monospace; letter-spacing:.2pt; }

        .bottom-panel { top:76mm; height:23.4mm; }
        .goods-box { position:absolute; top:0; bottom:0; left:0; width:62%; padding:.7mm 1mm 4.7mm; border-right:.25mm solid #000; overflow:hidden; }
        .goods-title { font-size:6pt; line-height:1; font-weight:700; letter-spacing:.4pt; font-family:'Share Tech Mono', monospace; }
        .goods-description { max-height:14mm; margin-top:.4mm; overflow:hidden; font-size:5pt; line-height:1.05; font-weight:400; font-family:'Share Tech Mono', monospace; }
        .goods-line { margin-top:.45mm; font-size:4.8pt; line-height:1; font-family:'Share Tech Mono', monospace; letter-spacing:.2pt; }
        .carrier-label { margin-top:.5mm; font-size:5pt; line-height:1; font-weight:700; font-family:'Share Tech Mono', monospace; }
        .carrier-service { margin-top:.3mm; font-size:6pt; line-height:1; font-weight:700; font-family:'Orbitron', monospace; }
        .payment { position:absolute; bottom:0.8mm; left:0; right:0; height:2.4mm; padding:.45mm 1mm 0; border-top:.2mm solid #000; font-size:4.8pt; line-height:1; font-weight:700; font-family:'Share Tech Mono', monospace; letter-spacing:.4pt; }
        .qr-box { position:absolute; top:0; right:0; bottom:0; width:38%; padding:3.2mm .5mm; text-align:center; overflow:hidden; }
        .qr { display:block; width:15.6mm; height:15.6mm; margin:0 auto; }
    </style>
</head>
<body>
@php
    $tracking = (string) $shipment->tracking_number;
    $awbDisplay = $tracking;
    $carrierName = trim((string) ($shipment->carrier_name ?: $shipment->carrier?->name ?: 'Carrier'));
    $carrierTracking = trim((string) $shipment->carrier_tracking_number);

    $senderDisplayName = $shipment->sender_is_business && filled($shipment->sender_company)
        ? $shipment->sender_company
        : $shipment->sender_name;
    $receiverDisplayName = $shipment->receiver_is_business && filled($shipment->receiver_company)
        ? $shipment->receiver_company
        : $shipment->receiver_name;

    $creatorLabel = 'GUEST';
    $creatorName = 'Guest User';
    // Resolve the creator: prefer the sender (the person who placed the booking),
    // then fall back to the agent who created it, then the assigned agent.
    $creatorUser = $shipment->sender
        ?? $shipment->createdByAgent
        ?? $shipment->agent
        ?? null;
    if ($creatorUser) {
        if ($creatorUser->hasRole('agent')) {
            $creatorLabel = 'AGENT';
        } elseif ($creatorUser->hasRole('admin') || $creatorUser->hasRole('super-admin')) {
            $creatorLabel = 'ADMIN';
        } else {
            $creatorLabel = 'CUSTOMER';
        }
        $creatorName = $creatorUser->name;
    }

    $senderAddress = collect([
        $shipment->sender_address,
        $shipment->sender_address2,
        $shipment->sender_address3,
    ])->filter()->implode(', ');
    $receiverAddress = collect([
        $shipment->receiver_address,
        $shipment->receiver_address2,
        $shipment->receiver_address3,
    ])->filter()->implode(', ');
    $senderLocality = collect([
        $shipment->sender_city,
        $shipment->sender_state,
        $shipment->sender_postal_code,
        $shipment->sender_country,
    ])->filter()->implode(', ');
    $receiverLocality = collect([
        $shipment->receiver_city,
        $shipment->receiver_state,
        $shipment->receiver_postal_code,
        $shipment->receiver_country,
    ])->filter()->implode(', ');
    $senderPhone = trim(collect([$shipment->sender_phone_code, $shipment->sender_phone])->filter()->implode(' '));
    $receiverPhone = trim(collect([$shipment->receiver_phone_code, $shipment->receiver_phone])->filter()->implode(' '));
    
    $senderTotalLen = mb_strlen($senderAddress . $senderLocality . $senderDisplayName);
    $receiverTotalLen = mb_strlen($receiverAddress . $receiverLocality . $receiverDisplayName);
    
    $senderDense = $senderTotalLen > 70 || mb_strlen($senderAddress) > 35 || mb_strlen($senderLocality) > 25;
    $senderExtraDense = $senderTotalLen > 110 || mb_strlen($senderAddress) > 60 || mb_strlen($senderLocality) > 40;
    
    $receiverDense = $receiverTotalLen > 70 || mb_strlen($receiverAddress) > 35 || mb_strlen($receiverLocality) > 25;
    $receiverExtraDense = $receiverTotalLen > 110 || mb_strlen($receiverAddress) > 60 || mb_strlen($receiverLocality) > 40;

    $items = collect(is_array($shipment->items) ? $shipment->items : []);
    $goodsDescription = $shipment->shipment_type === 'document'
        ? ($shipment->document_description ?: $shipment->description ?: 'Documents')
        : $items->map(function ($item) {
            $name = $item['name'] ?? $item['description'] ?? null;
            $quantity = max((int) ($item['quantity'] ?? 1), 1);
            return $name ? ($quantity > 1 ? $quantity . ' x ' . $name : $name) : null;
        })->filter()->implode(', ');
    $goodsDescription = $goodsDescription ?: ($shipment->description ?: 'Goods as declared');
    $goodsFontSize = mb_strlen($goodsDescription) > 165
        ? '3.8pt'
        : (mb_strlen($goodsDescription) > 100 ? '4.2pt' : '5.1pt');

    $declaredValue = $items->sum(function ($item) {
        return max((int) ($item['quantity'] ?? 1), 1) * max((float) ($item['value_per_item'] ?? 0), 0);
    });

    $packages = collect(is_array($shipment->packages) ? $shipment->packages : []);
    
    $labelsToPrint = [];
    $currentPiece = 1;
    
    if ($packages->isNotEmpty()) {
        foreach ($packages as $package) {
            $qty = max((int) ($package['quantity'] ?? 1), 1);
            $w = (float) ($package['actual_weight'] ?? $package['weight'] ?? 0);
            $l = (float) ($package['length'] ?? 0);
            $wd = (float) ($package['width'] ?? 0);
            $h = (float) ($package['height'] ?? 0);
            
            $dim = ($l > 0 && $wd > 0 && $h > 0) ? "{$l}x{$wd}x{$h} cm" : 'Not provided';
            
            for ($i = 0; $i < $qty; $i++) {
                $labelsToPrint[] = [
                    'index' => $currentPiece++,
                    'weight' => $w,
                    'dimensions' => $dim,
                ];
            }
        }
    } else {
        $pieces = max((int) ($shipment->total_packages ?: 1), 1);
        $weight = (float) ($shipment->total_weight ?: $shipment->weight);
        $dimensions = $shipment->dimensions ?: 'Not provided';
        for ($i = 0; $i < $pieces; $i++) {
            $labelsToPrint[] = [
                'index' => $currentPiece++,
                'weight' => $weight,
                'dimensions' => $dimensions,
            ];
        }
    }
    
    $totalPieces = max(count($labelsToPrint), 1);
    if (empty($labelsToPrint)) {
        $labelsToPrint[] = [
            'index' => 1,
            'weight' => (float) ($shipment->total_weight ?: $shipment->weight),
            'dimensions' => $shipment->dimensions ?: 'Not provided',
        ];
    }
    $barcodeData = 'data:image/svg+xml;base64,' . base64_encode($barcodeSvg);
    $qrData = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);
@endphp

@foreach($labelsToPrint as $labelData)
<main class="label">
    <section class="panel logo-panel">
        <div class="logo-crop"><img class="logo-mark" src="{{ $logoDataUri }}" alt=""></div>
    </section>

    <section class="panel title-panel line-bottom">
        <table>
            <tr>
                <td class="service-code">{{ $shipment->shipment_type === 'document' ? 'DOX' : 'WPX' }}</td>
                <td class="waybill-title">
                    <strong>AIR WAYBILL</strong>
                    <span>NON-NEGOTIABLE</span>
                </td>
                <td class="title-spacer"></td>
            </tr>
        </table>
    </section>

    <section class="panel awb-panel line-bottom">
        <div class="awb-line">AWB NO: <strong>{{ $awbDisplay }}</strong></div>
        <div class="awb-carrier">
            CARRIER SERVICE: {{ strtoupper($carrierName) }} &nbsp;&nbsp;|&nbsp;&nbsp;
            <br>TRACKING NUMBER: {{ $carrierTracking ?: 'Not provided' }}
        </div>
    </section>

    <section class="panel party-panel shipper-panel line-bottom {{ $senderExtraDense ? 'extra-dense' : ($senderDense ? 'dense' : '') }}">
        <div class="party-inner">
            <div class="section-title">FROM (SHIPPER)</div>
            <div class="party-name">{{ $senderDisplayName ?: 'Not provided' }}</div>
            @if(filled($shipment->sender_name) && $shipment->sender_name !== $senderDisplayName)
                <div class="party-line">Contact: {{ $shipment->sender_name }}</div>
            @endif
            <div class="party-line">{{ $senderAddress ?: 'Address not provided' }}</div>
            <table class="party-meta">
                <tr>
                    <td>{{ $senderLocality ?: 'Location not provided' }}</td>
                    <td>{{ $senderPhone ?: 'Phone: -' }}</td>
                </tr>
            </table>
        </div>
    </section>

    <section class="panel party-panel consignee-panel line-bottom {{ $receiverExtraDense ? 'extra-dense' : ($receiverDense ? 'dense' : '') }}">
        <div class="party-inner">
            <div class="section-title">TO (CONSIGNEE)</div>
            <div class="party-name">{{ $receiverDisplayName ?: 'Not provided' }}</div>
            @if(filled($shipment->receiver_name) && $shipment->receiver_name !== $receiverDisplayName)
                <div class="party-line">Contact: {{ $shipment->receiver_name }}</div>
            @endif
            <div class="party-line">{{ $receiverAddress ?: 'Address not provided' }}</div>
            <table class="party-meta">
                <tr>
                    <td>{{ $receiverLocality ?: 'Location not provided' }}</td>
                    <td>{{ $receiverPhone ?: 'Phone: -' }}</td>
                </tr>
            </table>
        </div>
    </section>

    <section class="panel tracking-panel line-bottom">
        <table>
            <tr>
                <td class="tracking-label">Account Details :</td>
                <td class="tracking-number" style="{{ mb_strlen($creatorLabel . ' ' . $creatorName) > 24 ? 'font-size:5pt' : (mb_strlen($creatorLabel . ' ' . $creatorName) > 16 ? 'font-size:6.2pt' : 'font-size:7pt') }}">{{ strtoupper($creatorLabel) }} {{ strtoupper($creatorName) }}</td>
            </tr>
        </table>
    </section>

    <section class="panel metrics-panel line-bottom">
        <table>
            <tr>
                <td style="width:20%">
                    <span class="metric-label">PIECES</span>
                    <span class="metric-value">{{ $labelData['index'] }} / {{ $totalPieces }}</span>
                </td>
                <td style="width:25%">
                    <span class="metric-label">WEIGHT</span>
                    <span class="metric-value">{{ number_format($labelData['weight'], 2) }} kg</span>
                </td>
                <td style="width:55%">
                    <span class="metric-label">DIMENSIONS</span>
                    <span class="metric-value" style="{{ mb_strlen($labelData['dimensions']) > 42 ? 'font-size:4.6pt' : '' }}">{{ $labelData['dimensions'] }}</span>
                </td>
            </tr>
        </table>
    </section>

    <section class="panel bottom-panel">
        <div class="goods-box">
            <div class="goods-title">GOODS DESCRIPTION</div>
            <div class="goods-description" style="font-size:{{ $goodsFontSize }}">Content: {{ $goodsDescription }}</div>
            <div class="goods-line"><strong>DECLARED VALUE:</strong> {{ $declaredValue > 0 ? number_format($declaredValue, 2) . ' USD' : 'Not provided' }}</div>
            <div class="payment">PAYMENT TERMS: PPD</div>
        </div>
        <div class="qr-box">
            @if(!($hideTrackingQr ?? false))
                <img class="qr" src="{{ $qrData }}" alt="QR code for {{ $trackingUrl }}">
            @endif
        </div>
    </section>
</main>
@endforeach
<script>
    window.onload = function() {
        window.print();
    };
</script>
</body>
</html>
