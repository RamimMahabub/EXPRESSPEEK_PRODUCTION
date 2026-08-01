<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Proforma Invoice {{ $shipment->invoice_number }}</title>
    <style>
        @page { margin: 18mm 16mm 17mm; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            color: #172033;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 9.5px;
            line-height: 1.45;
        }
        table { width: 100%; border-collapse: collapse; }
        td, th { vertical-align: top; }
        .top-rule { height: 5px; margin-bottom: 14px; background: #123e78; }
        .header { margin-bottom: 14px; }
        .header .brand-cell { width: 56%; }
        .header .title-cell { width: 44%; text-align: right; }
        .logo { width: 155px; height: auto; }
        .logo-fallback { color: #123e78; font-size: 18px; font-weight: 800; letter-spacing: .7px; line-height: 1.05; }
        .logo-fallback span { display: block; font-size: 12px; letter-spacing: 1.3px; }
        .company-details { margin-top: 5px; color: #536176; font-size: 8.5px; line-height: 1.55; }
        .document-title {
            margin: 0;
            color: #172033;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: .7px;
            text-transform: uppercase;
        }
        .copy-label { margin-top: 3px; color: #667388; font-size: 8px; letter-spacing: .8px; text-transform: uppercase; }
        .invoice-number { margin-top: 9px; color: #123e78; font-size: 11px; font-weight: 700; }
        .reference-table { margin-bottom: 12px; border: 1px solid #7f8b9b; }
        .reference-table td { width: 25%; padding: 7px 8px; border-right: 1px solid #aeb8c5; background: #f2f4f7; }
        .reference-table td:last-child { border-right: 0; }
        .field-label {
            display: block;
            margin-bottom: 2px;
            color: #647186;
            font-size: 7px;
            font-weight: 700;
            letter-spacing: .55px;
            text-transform: uppercase;
        }
        .field-value { display: block; color: #172033; font-size: 9px; font-weight: 700; }
        .party-table { margin-bottom: 12px; border: 1px solid #7f8b9b; }
        .party-table td { width: 50%; padding: 9px 10px; }
        .party-table td + td { border-left: 1px solid #7f8b9b; }
        .block-title {
            margin: -9px -10px 8px;
            padding: 5px 10px;
            border-bottom: 1px solid #aeb8c5;
            background: #e8ecf1;
            color: #27364b;
            font-size: 7.5px;
            font-weight: 800;
            letter-spacing: .65px;
            text-transform: uppercase;
        }
        .party-name { margin-bottom: 3px; color: #172033; font-size: 10px; font-weight: 700; }
        .party-line { margin: 0; color: #47566b; line-height: 1.5; }
        .particulars { margin-bottom: 12px; border: 1px solid #7f8b9b; }
        .particulars .section-heading th {
            padding: 5px 8px;
            border-bottom: 1px solid #7f8b9b;
            background: #dfe5ec;
            color: #25364c;
            font-size: 7.5px;
            letter-spacing: .65px;
            text-align: left;
            text-transform: uppercase;
        }
        .particulars td { padding: 7px 8px; border-right: 1px solid #aeb8c5; }
        .particulars td:last-child { border-right: 0; }
        .goods-table { margin-bottom: 12px; border: 1px solid #657184; }
        .goods-table tr { page-break-inside: avoid; }
        .goods-table th {
            padding: 6px 8px;
            border-right: 1px solid #8c97a7;
            border-bottom: 1px solid #657184;
            background: #dfe5ec;
            color: #25364c;
            font-size: 7.5px;
            font-weight: 800;
            letter-spacing: .55px;
            text-align: left;
            text-transform: uppercase;
        }
        .goods-table th:last-child { border-right: 0; text-align: right; }
        .goods-table td { padding: 7px 8px; border-right: 1px solid #c5ccd6; border-bottom: 1px solid #c5ccd6; }
        .goods-table td:last-child { border-right: 0; text-align: right; }
        .goods-table .number { width: 7%; text-align: center; }
        .goods-table .description { width: 51%; }
        .goods-table .quantity { width: 12%; text-align: center; }
        .goods-table .unit-value { width: 15%; text-align: right; }
        .goods-table .line-total { width: 15%; text-align: right; }
        .goods-table .total-label {
            padding: 8px;
            border-top: 2px solid #526074;
            background: #f2f4f7;
            font-size: 8.5px;
            font-weight: 800;
            letter-spacing: .35px;
            text-align: right;
            text-transform: uppercase;
        }
        .goods-table .total-amount {
            padding: 8px;
            border-top: 2px solid #526074;
            background: #f2f4f7;
            color: #123e78;
            font-size: 12px;
            font-weight: 800;
            text-align: right;
        }
        .declaration-table { margin-bottom: 12px; page-break-inside: avoid; }
        .declaration-table > tbody > tr > td { width: 58%; }
        .declaration-table > tbody > tr > td + td { width: 42%; padding-left: 15px; }
        .terms-box { min-height: 100px; padding: 8px 9px; border: 1px solid #aeb8c5; }
        .terms-title { margin-bottom: 6px; color: #27364b; font-size: 7.5px; font-weight: 800; letter-spacing: .55px; text-transform: uppercase; }
        .terms-table td { padding: 2px 0; color: #465469; font-size: 8.5px; }
        .terms-table td:first-child { width: 42%; color: #647186; }
        .terms-table td:last-child { font-weight: 700; }
        .summary { border: 1px solid #657184; }
        .summary td { padding: 6px 8px; border-bottom: 1px solid #c5ccd6; }
        .summary tr:last-child td { border-bottom: 0; }
        .summary td:first-child { color: #59667a; }
        .summary td:last-child { font-weight: 700; text-align: right; }
        .summary .value-row td { background: #e8ecf1; color: #172033; font-size: 10px; font-weight: 800; }
        .notice {
            margin-top: 13px;
            padding-top: 8px;
            border-top: 1px solid #aeb8c5;
            color: #59667a;
            font-size: 7.5px;
            line-height: 1.5;
            page-break-inside: avoid;
        }
        .signature-table { margin-top: 24px; page-break-inside: avoid; }
        .signature-table td { width: 33.333%; padding-right: 24px; }
        .signature-table td:last-child { padding-right: 0; }
        .signature-line { border-top: 1px solid #566276; padding-top: 4px; color: #586579; font-size: 7.5px; text-align: center; }
        .footer {
            position: fixed;
            right: 0;
            bottom: -8mm;
            left: 0;
            padding-top: 5px;
            border-top: 1px solid #9ca7b6;
            color: #6a7688;
            font-size: 7px;
            text-align: center;
        }
    </style>
</head>
<body>
@php
    $isDocument = $shipment->shipment_type === 'document';
    $rawItems = collect($shipment->items ?? [])->values();

    if ($isDocument) {
        $invoiceLines = collect([[
            'description' => $shipment->document_description ?: 'Documents',
            'quantity' => max(1, (int) ($shipment->total_packages ?: 1)),
            'unit_value' => 1.00,
        ]]);
    } elseif ($rawItems->isNotEmpty()) {
        $invoiceLines = $rawItems->map(fn ($item) => [
            'description' => $item['name'] ?? $item['description'] ?? 'Goods as declared',
            'quantity' => max(1, (int) ($item['quantity'] ?? 1)),
            'unit_value' => max(0, (float) ($item['value_per_item'] ?? 0)),
        ]);
    } else {
        $invoiceLines = collect([[
            'description' => $shipment->description ?: 'Goods as declared',
            'quantity' => 1,
            'unit_value' => 1.00,
        ]]);
    }

    $invoiceLines = $invoiceLines->map(function ($item) {
        $item['line_total'] = round($item['quantity'] * $item['unit_value'], 2);
        return $item;
    })->values();

    $totalGoodsValue = round($invoiceLines->sum('line_total'), 2);
    $totalQuantity = $invoiceLines->sum('quantity');
    $recordedPackageCount = (int) ($shipment->total_packages ?: 0);
    $storedPackageCount = count($shipment->packages ?? []);
    $packageCount = max(1, $recordedPackageCount ?: $storedPackageCount ?: 1);
    $grossWeight = (float) ($shipment->total_weight ?: $shipment->weight ?: 0);
    $origin = collect([$shipment->sender_city, $shipment->sender_country])->filter()->join(', ');
    $destination = collect([$shipment->receiver_city, $shipment->receiver_country])->filter()->join(', ');
    $senderLocality = collect([
        $shipment->sender_city,
        $shipment->sender_state,
        $shipment->sender_postal_code,
    ])->filter()->join(', ');
    $receiverLocality = collect([
        $shipment->receiver_city,
        $shipment->receiver_state,
        $shipment->receiver_postal_code,
    ])->filter()->join(', ');
    $senderPhone = collect([$shipment->sender_phone_code, $shipment->sender_phone])->filter()->join(' ');
    $receiverPhone = collect([$shipment->receiver_phone_code, $shipment->receiver_phone])->filter()->join(' ');
    $logoPath = resource_path('images/express-peek-logo-pdf.png');
    $logoDataUri = is_file($logoPath) && is_readable($logoPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
        : null;
@endphp

<div class="top-rule"></div>

<table class="header">
    <tr>
        <td class="brand-cell">
            @if($logoDataUri)
                <img class="logo" src="{{ $logoDataUri }}" alt="">
            @else
                <div class="logo-fallback"><span>EXPRESS</span>PEEK</div>
            @endif
            <div class="company-details">
                Dhaka, Bangladesh<br>
                support@expresspeek.com &nbsp;|&nbsp; expresspeek.com<br>
                WhatsApp: +880 1400-659902
            </div>
        </td>
        <td class="title-cell">
            <h1 class="document-title">Proforma Invoice</h1>
            <div class="copy-label">Customs value declaration - non-negotiable copy</div>
            <div class="invoice-number">No. {{ $shipment->invoice_number ?: 'Not assigned' }}</div>
        </td>
    </tr>
</table>

<table class="reference-table">
    <tr>
        <td>
            <span class="field-label">Invoice date</span>
            <span class="field-value">{{ $shipment->created_at?->format('d M Y') ?? now()->format('d M Y') }}</span>
        </td>
        <td>
            <span class="field-label">Tracking number</span>
            <span class="field-value">{{ $shipment->tracking_number }}</span>
        </td>
        <td>
            <span class="field-label">Carrier reference</span>
            <span class="field-value">{{ $shipment->carrier_tracking_number ?: 'Not assigned' }}</span>
        </td>
        <td>
            <span class="field-label">Value currency</span>
            <span class="field-value">USD</span>
        </td>
    </tr>
</table>

<table class="party-table">
    <tr>
        <td>
            <div class="block-title">Shipper / Exporter</div>
            <div class="party-name">{{ $shipment->sender_name ?: 'Not provided' }}</div>
            @if($shipment->sender_company)<p class="party-line">{{ $shipment->sender_company }}</p>@endif
            @if($shipment->sender_address)<p class="party-line">{{ $shipment->sender_address }}</p>@endif
            @if($shipment->sender_address2)<p class="party-line">{{ $shipment->sender_address2 }}</p>@endif
            @if($shipment->sender_address3)<p class="party-line">{{ $shipment->sender_address3 }}</p>@endif
            @if($senderLocality)<p class="party-line">{{ $senderLocality }}</p>@endif
            @if($shipment->sender_country)<p class="party-line">{{ $shipment->sender_country }}</p>@endif
            @if($senderPhone)<p class="party-line">Tel: {{ $senderPhone }}</p>@endif
            @if($shipment->sender_email)<p class="party-line">{{ $shipment->sender_email }}</p>@endif
        </td>
        <td>
            <div class="block-title">Consignee / Importer</div>
            <div class="party-name">{{ $shipment->receiver_name ?: 'Not provided' }}</div>
            @if($shipment->receiver_company)<p class="party-line">{{ $shipment->receiver_company }}</p>@endif
            @if($shipment->receiver_address)<p class="party-line">{{ $shipment->receiver_address }}</p>@endif
            @if($shipment->receiver_address2)<p class="party-line">{{ $shipment->receiver_address2 }}</p>@endif
            @if($shipment->receiver_address3)<p class="party-line">{{ $shipment->receiver_address3 }}</p>@endif
            @if($receiverLocality)<p class="party-line">{{ $receiverLocality }}</p>@endif
            @if($shipment->receiver_country)<p class="party-line">{{ $shipment->receiver_country }}</p>@endif
            @if($receiverPhone)<p class="party-line">Tel: {{ $receiverPhone }}</p>@endif
            @if($shipment->receiver_email)<p class="party-line">{{ $shipment->receiver_email }}</p>@endif
        </td>
    </tr>
</table>

<table class="particulars">
    <tr class="section-heading"><th colspan="5">Shipment particulars</th></tr>
    <tr>
        <td>
            <span class="field-label">Route</span>
            <span class="field-value">{{ $origin ?: 'Origin not provided' }} to {{ $destination ?: 'Destination not provided' }}</span>
        </td>
        <td>
            <span class="field-label">Contents type</span>
            <span class="field-value">{{ $isDocument ? 'Documents' : 'Non-document goods' }}</span>
        </td>
        <td>
            <span class="field-label">Carrier</span>
            <span class="field-value">{{ $shipment->carrier_name ?: 'Express Peek' }}</span>
        </td>
        <td>
            <span class="field-label">Packages</span>
            <span class="field-value">{{ $packageCount }}</span>
        </td>
        <td>
            <span class="field-label">Gross weight</span>
            <span class="field-value">{{ number_format($grossWeight, 2) }} kg</span>
        </td>
    </tr>
</table>

<table class="goods-table">
    <thead>
        <tr>
            <th class="number">No.</th>
            <th class="description">Detailed description of contents</th>
            <th class="quantity">Quantity</th>
            <th class="unit-value">Unit value (USD)</th>
            <th class="line-total">Total value (USD)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoiceLines as $invoiceLine)
            <tr>
                <td class="number">{{ $loop->iteration }}</td>
                <td>{{ $invoiceLine['description'] }}</td>
                <td class="quantity">{{ $invoiceLine['quantity'] }}</td>
                <td class="unit-value">{{ number_format($invoiceLine['unit_value'], 2) }}</td>
                <td class="line-total">{{ number_format($invoiceLine['line_total'], 2) }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="4" class="total-label">Total declared value</td>
            <td class="total-amount">USD {{ number_format($totalGoodsValue, 2) }}</td>
        </tr>
    </tbody>
</table>

<table class="declaration-table">
    <tr>
        <td>
            <div class="terms-box">
                <div class="terms-title">Customs declaration particulars</div>
                <table class="terms-table">
                    <tr><td>Terms of trade</td><td>Delivered at Place (DAP)</td></tr>
                    <tr><td>Reason for export</td><td>Gift</td></tr>
                    <tr><td>Duty / tax account</td><td>Receiver will pay</td></tr>
                    <tr><td>Country of export</td><td>{{ $shipment->sender_country ?: 'Not provided' }}</td></tr>
                    <tr><td>Destination country</td><td>{{ $shipment->receiver_country ?: 'Not provided' }}</td></tr>
                </table>
            </div>
        </td>
        <td>
            <table class="summary">
                <tr><td>Total line items</td><td>{{ $invoiceLines->count() }}</td></tr>
                <tr><td>Total quantity</td><td>{{ $totalQuantity }}</td></tr>
                <tr><td>Total packages</td><td>{{ $packageCount }}</td></tr>
                <tr><td>Total gross weight</td><td>{{ number_format($grossWeight, 2) }} kg</td></tr>
                <tr class="value-row"><td>Invoice value</td><td>USD {{ number_format($totalGoodsValue, 2) }}</td></tr>
            </table>
        </td>
    </tr>
</table>

<div class="notice">
    I declare that the information shown on this proforma invoice is true and correct and that the contents, quantities,
    weights and values are accurately stated for customs purposes. This document is not a demand for payment.
</div>

<table class="signature-table">
    <tr>
        <td><div class="signature-line">Prepared by</div></td>
        <td><div class="signature-line">Shipper signature</div></td>
        <td><div class="signature-line">Date</div></td>
    </tr>
</table>

<div class="footer">
    EXPRESS PEEK &nbsp;|&nbsp; International Courier &amp; Freight Services &nbsp;|&nbsp;
    Proforma {{ $shipment->invoice_number ?: 'N/A' }} &nbsp;|&nbsp; Tracking {{ $shipment->tracking_number }}
</div>
</body>
</html>
