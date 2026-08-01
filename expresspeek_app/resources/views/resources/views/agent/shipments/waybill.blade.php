<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Waybill {{ $shipment->awb_number }}</title>
    <style>
        @page {
            size: 4in 6in;
            margin: 0.04in;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
            background: #fff;
            font-size: 7px;
            line-height: 1.1;
        }

        .label {
            width: 100%;
            max-width: 100%;
            border: 1px solid #000;
            padding: 2px;
            overflow: hidden;
        }

        .top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 3px;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
            margin-bottom: 2px;
        }

        .title {
            font-size: 11px;
            font-weight: 800;
            line-height: 1;
        }

        .meta {
            margin-top: 2px;
            font-size: 7px;
            font-weight: 700;
        }

        .brand {
            text-align: right;
            font-size: 9px;
            font-weight: 800;
            line-height: 1.1;
        }

        .wpx {
            display: inline-block;
            background: #000;
            color: #fff;
            font-size: 15px;
            font-weight: 800;
            padding: 0 4px;
            margin-bottom: 2px;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2px;
            width: 100%;
        }

        .box {
            border: 1px solid #000;
            padding: 2px;
            min-height: 60px;
            overflow: hidden;
            word-wrap: break-word;
        }

        .box-title {
            font-size: 7px;
            font-weight: 800;
            margin-bottom: 2px;
            border-bottom: 1px solid #000;
            padding-bottom: 1px;
        }

        .strong {
            font-weight: 800;
            font-size: 8px;
        }

        .divider {
            border-top: 1px solid #000;
            margin: 2px 0;
        }

        .summary {
            border: 1px solid #000;
            padding: 2px;
        }

        .summary-head {
            font-size: 8px;
            font-weight: 800;
            margin-bottom: 3px;
        }

        .summary-row {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 4px;
            margin-top: 0;
            font-size: 7px;
        }

        .big {
            font-size: 10px;
            font-weight: 800;
        }

        .code-row {
            display: grid;
            grid-template-columns: 1fr 68px;
            gap: 2px;
            align-items: start;
            margin-top: 2px;
            width: 100%;
        }

        .barcode {
            height: 36px;
            width: 100%;
            border: 1px solid #000;
            background: repeating-linear-gradient(
                90deg,
                #000 0px,
                #000 2px,
                #fff 2px,
                #fff 3px,
                #000 3px,
                #000 5px,
                #fff 5px,
                #fff 7px,
                #000 7px,
                #000 8px,
                #fff 8px,
                #fff 10px
            );
        }

        .awb {
            text-align: center;
            font-size: 8px;
            font-weight: 800;
            margin-top: 2px;
        }

        .contents {
            margin-top: 1px;
            font-size: 6px;
            font-weight: 700;
            border: 1px solid #000;
            padding: 1px 2px;
            word-wrap: break-word;
            overflow: hidden;
        }

        .qr {
            width: 68px;
            height: 68px;
            border: 1px solid #000;
            padding: 1px;
            text-align: center;
        }

        .qr img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .license {
            margin-top: 1px;
            border: 1px solid #000;
            padding: 1px 2px;
        }

        .license-title {
            font-size: 7px;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .license-line {
            font-size: 7px;
            line-height: 1.15;
        }
    </style>
</head>
<body>
@php
    $docType = strtoupper($shipment->shipment_type === 'document' ? 'DOC' : 'NON-DOC');
    $carrierLabel = strtoupper(str_replace('-Bangladesh', '', $shipment->carrier_name ?? 'DHL'));
    $shipperPhone = trim(($shipment->sender_phone_code ?? '') . ($shipment->sender_phone ?? ''));
    $receiverPhone = trim(($shipment->receiver_phone_code ?? '') . ($shipment->receiver_phone ?? ''));
    $items = is_array($shipment->items) ? $shipment->items : [];
    $contentStr = collect($items)->pluck('name')->implode(', ');
    $licenseBase = 'JD' . str_pad((string) ($shipment->awb_number ?? '0000000000'), 10, '0', STR_PAD_LEFT);
    $awbSpaced = implode(' ', str_split((string) ($shipment->awb_number ?? '0000000000'), 2));
@endphp

<div class="label">
    <div class="top">
        <div>
            <div class="title">WAYBILL {{ $docType }}</div>
            <div class="meta">Date: {{ now()->format('Y-m-d') }}</div>
            <div class="meta">Tracking: {{ $shipment->tracking_number }}</div>
            <div class="meta">AWB: {{ $shipment->awb_number }}</div>
        </div>
        <div class="brand">
            <div class="wpx">WPX</div>
            <div>{{ $carrierLabel }}</div>
        </div>
    </div>

    <div class="grid-2">
        <div class="box">
            <div class="box-title">FROM</div>
            <div class="strong">{{ $shipment->sender_name }}</div>
            @if($shipment->sender_company)<div>{{ $shipment->sender_company }}</div>@endif
            <div>{{ $shipment->sender_address }}</div>
            @if($shipment->sender_address2)<div>{{ $shipment->sender_address2 }}</div>@endif
            @if($shipment->sender_address3)<div>{{ $shipment->sender_address3 }}</div>@endif
            <div>{{ $shipment->sender_city }} {{ $shipment->sender_postal_code }}</div>
            <div>{{ $shipment->sender_country }}</div>
            <div>Phone: {{ $shipperPhone ?: '-' }}</div>
        </div>

        <div class="box">
            <div class="box-title">TO</div>
            <div class="strong">{{ $shipment->receiver_name }}</div>
            @if($shipment->receiver_company)<div>{{ $shipment->receiver_company }}</div>@endif
            <div>{{ $shipment->receiver_address }}</div>
            @if($shipment->receiver_address2)<div>{{ $shipment->receiver_address2 }}</div>@endif
            @if($shipment->receiver_address3)<div>{{ $shipment->receiver_address3 }}</div>@endif
            <div>{{ $shipment->receiver_city }} {{ $shipment->receiver_postal_code }}</div>
            <div>{{ $shipment->receiver_country }}</div>
            <div>Phone: {{ $receiverPhone ?: '-' }}</div>
        </div>
    </div>

    <div class="divider"></div>

    <div class="summary">
        <div class="summary-head">SHIPMENT DETAILS</div>
        <div class="summary-row"><span>Status</span><strong>{{ $shipment->status_label }}</strong></div>
        <div class="summary-row"><span>Type</span><strong>{{ ucfirst(str_replace('_', ' ', $shipment->shipment_type ?? '')) }}</strong></div>
        <div class="summary-row"><span>Total Weight</span><span class="big">{{ number_format((float) $shipment->total_weight, 1) }} kg</span></div>
        <div class="summary-row"><span>number of packages</span><span class="big">{{ (int) $shipment->total_packages }}</span></div>
    </div>

    <div class="code-row">
        <div>
            <div class="barcode"></div>
            <div class="awb">WAYBILL {{ $awbSpaced }}</div>
            <div class="contents">Contents: {{ $contentStr ?: ($shipment->document_description ?: '-') }}</div>
        </div>
        <div class="qr">
            <img src="data:image/svg+xml;base64,{{ base64_encode($qrSvg ?? '') }}" alt="QR">
        </div>
    </div>

    <div class="license">
        <div class="license-title">License Plates</div>
        @for($i = 1; $i <= min(max((int) $shipment->total_packages, 1), 2); $i++)
            <div class="license-line">{{ $licenseBase }}{{ str_pad((string) $i, 7, '0', STR_PAD_LEFT) }}</div>
        @endfor
    </div>
</div>
</body>
</html>
