<!DOCTYPE html>
{{-- Versioned label template: avoids stale compiled Blade markup on cPanel. --}}
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shipment Label {{ $shipment->tracking_number }}</title>
    <style>
        @page { size:75mm 100mm; margin:0; }
        * { box-sizing:border-box; }
        html,body { width:75mm; height:100mm; margin:0; padding:0; overflow:hidden; }
        body { color:#000; background:#fff; font-family:Helvetica,Arial,sans-serif; font-size:5.8pt; line-height:1.1; }
        strong { font-weight:700; }
        .label { position:relative; width:75mm; height:99.4mm; margin:0; padding:0; overflow:hidden; page-break-after:avoid; page-break-inside:avoid; background:#fff; }
        .panel { position:absolute; left:6mm; right:0; overflow:hidden; }
        table { width:100%; height:100%; border-collapse:collapse; table-layout:fixed; }
        td { padding:0; vertical-align:top; }
        .line-bottom { border-bottom:.32mm solid #000; }

        .logo-panel { top:1.5mm; height:12.7mm; text-align:right; }
        .logo-crop { display:inline-block; position:relative; width:12.7mm; height:12.2mm; overflow:hidden; }
        .logo-mark { position:absolute; top:0; left:0; width:43.6mm; height:12.2mm; max-width:none; }

        .title-panel { top:3.5mm; height:9mm; }
        .title-panel td { vertical-align:middle; text-align:center; }
        .service-code { width:25%; font-size:14pt; font-weight:700; letter-spacing:.7pt; }
        .waybill-title { width:50%; border-left:.25mm solid #000; border-right:.25mm solid #000; }
        .waybill-title strong { display:block; font-size:11.5pt; line-height:1; font-weight:700; letter-spacing:.2pt; }
        .waybill-title span { display:block; margin-top:.8mm; font-size:6.3pt; line-height:1; font-weight:700; letter-spacing:.25pt; }
        .title-spacer { width:25%; }

        .awb-panel { top:12.5mm; height:8.5mm; padding-top:0.5mm; text-align:center; }
        .awb-line { height:6.5mm; text-align:left; font-size:9.2pt; white-space:nowrap; }
        .awb-line strong { font-size:15pt; line-height:1; font-weight:700; font-style:normal; letter-spacing:.55pt; }
        .barcode { display:block; width:67.5mm; height:9.6mm; margin:0 auto; }

        .party-panel { font-size:6.5pt; line-height:1.3; }
        .party-inner { position:absolute; top:1.1mm; left:1mm; right:1mm; bottom:.4mm; overflow:hidden; }
        .shipper-panel .party-inner { top:1.2mm; }
        .shipper-panel { top:21mm; height:21mm; }
        .consignee-panel { top:42mm; height:21mm; }
        .party-panel.dense { font-size:5.8pt; line-height:1.25; }
        .section-title { margin-bottom:.5mm; font-size:7.5pt; line-height:1; font-weight:700; }
        .party-name { font-size:7.5pt; font-weight:700; }
        .party-panel.dense .party-name { font-size:6.5pt; }
        .party-line { margin-top:.4mm; }
        .party-meta { margin-top:.35mm; }
        .party-meta td:first-child { width:66%; padding-right:1mm; }
        .party-meta td:last-child { width:34%; text-align:left; overflow-wrap:anywhere; }

        .tracking-panel { top:63mm; height:6mm; }
        .tracking-panel td { padding:.75mm 1mm .45mm; vertical-align:middle; }
        .tracking-label { width:47%; font-size:6.7pt; font-weight:700; white-space:nowrap; }
        .tracking-number { width:53%; font-size:10pt; font-weight:700; letter-spacing:.35pt; text-align:right; overflow-wrap:anywhere; }

        .metrics-panel { top:69mm; height:6mm; }
        .metrics-panel td { padding:.4mm .9mm .25mm; border-right:.22mm solid #000; vertical-align:middle; }
        .metrics-panel td:last-child { border-right:0; }
        .metric-label { display:block; font-size:4.8pt; line-height:1; font-weight:700; }
        .metric-value { display:block; margin-top:.3mm; font-size:5.6pt; line-height:1; font-weight:700; }

        .bottom-panel { top:75mm; height:24mm; }
        .goods-box { position:absolute; top:0; bottom:0; left:0; width:62%; padding:.7mm 1mm 4.7mm; border-right:.25mm solid #000; overflow:hidden; }
        .goods-title { font-size:6.2pt; line-height:1; font-weight:700; }
        .goods-description { max-height:7.5mm; margin-top:.4mm; overflow:hidden; font-size:5pt; line-height:1.03; font-weight:700; }
        .goods-line { margin-top:.45mm; font-size:4.9pt; line-height:1; }
        .carrier-label { margin-top:.5mm; font-size:5.1pt; line-height:1; font-weight:700; }
        .carrier-service { margin-top:.3mm; font-size:6.3pt; line-height:1; font-weight:700; }
        .payment { position:absolute; bottom:0.8mm; left:0; right:0; height:2.4mm; padding:.45mm 1mm 0; border-top:.2mm solid #000; font-size:4.9pt; line-height:1; font-weight:700; }
        .qr-box { position:absolute; top:0; right:0; bottom:0; width:38%; padding:3.2mm .5mm; text-align:center; overflow:hidden; }
        .qr { display:block; width:15.6mm; height:15.6mm; margin:0 auto; }
    </style>
</head>
<body>
@php
    $tracking = (string) $shipment->tracking_number;
    $awbDisplay = trim(chunk_split($tracking, 4, ' '));
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
    if ($shipment->user) {
        if ($shipment->user->hasRole('agent')) {
            $creatorLabel = 'AGENT';
        } elseif ($shipment->user->hasRole('admin') || $shipment->user->hasRole('super-admin')) {
            $creatorLabel = 'ADMIN';
        } else {
            $creatorLabel = 'CUSTOMER';
        }
        $creatorName = $shipment->user->name;
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
    $senderDense = mb_strlen($senderAddress . $senderLocality . $senderDisplayName) > 125;
    $receiverDense = mb_strlen($receiverAddress . $receiverLocality . $receiverDisplayName) > 115;

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
    $dimensions = $packages->map(function ($package) {
        $length = (float) ($package['length'] ?? 0);
        $width = (float) ($package['width'] ?? 0);
        $height = (float) ($package['height'] ?? 0);
        $quantity = max((int) ($package['quantity'] ?? 1), 1);

        if ($length <= 0 || $width <= 0 || $height <= 0) {
            return null;
        }

        $formatted = sprintf('%gx%gx%g cm', $length, $width, $height);
        return $quantity > 1 ? $quantity . 'x ' . $formatted : $formatted;
    })->filter()->implode('; ');
    $dimensions = $dimensions ?: ($shipment->dimensions ?: 'Not provided');

    $pieces = max((int) ($shipment->total_packages ?: 1), 1);
    $weight = (float) ($shipment->total_weight ?: $shipment->weight);
    $barcodeData = 'data:image/svg+xml;base64,' . base64_encode($barcodeSvg);
    $qrData = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);
@endphp

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
    </section>

    <section class="panel party-panel shipper-panel line-bottom {{ $senderDense ? 'dense' : '' }}">
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

    <section class="panel party-panel consignee-panel line-bottom {{ $receiverDense ? 'dense' : '' }}">
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
                <td class="tracking-label">{{ $creatorLabel }} :</td>
                <td class="tracking-number" style="{{ mb_strlen($creatorName) > 24 ? 'font-size:5.5pt' : (mb_strlen($creatorName) > 16 ? 'font-size:7.2pt' : '') }}">{{ strtoupper($creatorName) }}</td>
            </tr>
        </table>
    </section>

    <section class="panel metrics-panel line-bottom">
        <table>
            <tr>
                <td style="width:20%">
                    <span class="metric-label">PIECES</span>
                    <span class="metric-value">1 / {{ $pieces }}</span>
                </td>
                <td style="width:25%">
                    <span class="metric-label">WEIGHT</span>
                    <span class="metric-value">{{ number_format($weight, 2) }} kg</span>
                </td>
                <td style="width:55%">
                    <span class="metric-label">DIMENSIONS</span>
                    <span class="metric-value" style="{{ mb_strlen($dimensions) > 42 ? 'font-size:4.6pt' : '' }}">{{ $dimensions }}</span>
                </td>
            </tr>
        </table>
    </section>

    <section class="panel bottom-panel">
        <div class="goods-box">
            <div class="goods-title">GOODS DESCRIPTION</div>
            <div class="goods-description" style="font-size:{{ $goodsFontSize }}">Content: {{ $goodsDescription }}</div>
            <div class="goods-line"><strong>DECLARED VALUE:</strong> {{ $declaredValue > 0 ? number_format($declaredValue, 2) . ' USD' : 'Not provided' }}</div>
            <div class="carrier-label">CARRIER SERVICE</div>
            <div class="carrier-service">{{ strtoupper($carrierName) }}</div>
            <div class="carrier-label" style="margin-top:1.5mm;">TRACKING NUMBER: <span style="font-size:7pt;">{{ $carrierTracking ?: 'Not provided' }}</span></div>
            <div class="payment">PAYMENT TERMS: PPD</div>
        </div>
        <div class="qr-box">
            <img class="qr" src="{{ $qrData }}" alt="QR code for {{ $trackingUrl }}">
        </div>
    </section>
</main>
<script>
    window.onload = function() {
        window.print();
    };
</script>
</body>
</html>
