<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Freight Invoice {{ $shipmentInvoice->invoice_number }}</title>
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
        .status {
            display: inline-block;
            margin-top: 6px;
            padding: 3px 8px;
            border: 1px solid #68758a;
            color: #354257;
            font-size: 7.5px;
            font-weight: 700;
            letter-spacing: .6px;
            text-transform: uppercase;
        }
        .status-paid { border-color: #2f6e4f; color: #24593f; }
        .status-unpaid { border-color: #9a6c18; color: #7d5510; }
        .status-cancelled { border-color: #9a3030; color: #842626; }
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
        .party-table td { width: 50%; min-height: 118px; padding: 9px 10px; }
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
        .shipment-table { margin-bottom: 12px; border: 1px solid #7f8b9b; }
        .shipment-table .section-heading th {
            padding: 5px 8px;
            border-bottom: 1px solid #7f8b9b;
            background: #dfe5ec;
            color: #25364c;
            font-size: 7.5px;
            letter-spacing: .65px;
            text-align: left;
            text-transform: uppercase;
        }
        .shipment-table td { padding: 7px 8px; border-right: 1px solid #aeb8c5; }
        .shipment-table td:last-child { border-right: 0; }
        .charges { margin-bottom: 12px; border: 1px solid #657184; }
        .charges th {
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
        .charges th:last-child { border-right: 0; text-align: right; }
        .charges td { padding: 7px 8px; border-right: 1px solid #c5ccd6; border-bottom: 1px solid #c5ccd6; }
        .charges td:last-child { border-right: 0; text-align: right; }
        .charges .number { width: 7%; text-align: center; }
        .charges .description { width: 67%; }
        .charges .amount { width: 26%; }
        .charges .base-row td { font-weight: 700; }
        .charges .total-label {
            padding: 8px;
            border-top: 2px solid #526074;
            background: #f2f4f7;
            font-size: 8.5px;
            font-weight: 800;
            letter-spacing: .35px;
            text-align: right;
            text-transform: uppercase;
        }
        .charges .total-amount {
            padding: 8px;
            border-top: 2px solid #526074;
            background: #f2f4f7;
            color: #123e78;
            font-size: 12px;
            font-weight: 800;
            text-align: right;
        }
        .payment-table { margin-bottom: 12px; }
        .payment-table td { width: 62%; }
        .payment-table .summary-cell { width: 38%; padding-left: 15px; }
        .notes-box { min-height: 62px; padding: 8px 9px; border: 1px solid #aeb8c5; color: #465469; font-size: 8.5px; }
        .notes-title { margin-bottom: 5px; color: #27364b; font-size: 7.5px; font-weight: 800; letter-spacing: .55px; text-transform: uppercase; }
        .summary { border: 1px solid #657184; }
        .summary td { padding: 6px 8px; border-bottom: 1px solid #c5ccd6; }
        .summary tr:last-child td { border-bottom: 0; }
        .summary td:first-child { color: #59667a; }
        .summary td:last-child { font-weight: 700; text-align: right; }
        .summary .due-row td { background: #e8ecf1; color: #172033; font-size: 10px; font-weight: 800; }
        .declaration {
            margin-top: 13px;
            padding-top: 8px;
            border-top: 1px solid #aeb8c5;
            color: #59667a;
            font-size: 7.5px;
            line-height: 1.5;
        }
        .signature-table { margin-top: 24px; }
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
    $shipment = $shipmentInvoice->shipment;
    $charges = collect($shipmentInvoice->items ?? [])->values();
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
    $origin = collect([$shipment->sender_city, $shipment->sender_country])->filter()->join(', ');
    $destination = collect([$shipment->receiver_city, $shipment->receiver_country])->filter()->join(', ');
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
            <h1 class="document-title">Freight Invoice</h1>
            <div class="copy-label">Shipment receipt — customer copy</div>
            <div class="invoice-number">No. {{ $shipmentInvoice->invoice_number }}</div>
            <span class="status status-{{ strtolower($shipmentInvoice->status) }}">{{ $shipmentInvoice->status_label }}</span>
        </td>
    </tr>
</table>

<table class="reference-table">
    <tr>
        <td><span class="field-label">Invoice date</span><span class="field-value">{{ $shipmentInvoice->created_at->format('d M Y') }}</span></td>
        <td><span class="field-label">Payment due</span><span class="field-value">{{ $shipmentInvoice->due_date?->format('d M Y') ?? 'Due on receipt' }}</span></td>
        <td><span class="field-label">Tracking number</span><span class="field-value">{{ $shipment->tracking_number }}</span></td>
        <td><span class="field-label">Currency</span><span class="field-value">{{ $shipmentInvoice->currency }}</span></td>
    </tr>
</table>

<table class="party-table">
    <tr>
        <td>
            <div class="block-title">Shipper / From</div>
            <div class="party-name">{{ $shipment->sender_name ?: 'Not provided' }}</div>
            @if($shipment->sender_company)<p class="party-line">{{ $shipment->sender_company }}</p>@endif
            <p class="party-line">{{ $shipment->sender_address }}</p>
            @if($shipment->sender_address2)<p class="party-line">{{ $shipment->sender_address2 }}</p>@endif
            @if($senderLocality)<p class="party-line">{{ $senderLocality }}</p>@endif
            <p class="party-line">{{ $shipment->sender_country }}</p>
            @if($shipment->sender_email)<p class="party-line">{{ $shipment->sender_email }}</p>@endif
            @if($shipment->sender_phone)<p class="party-line">Tel: {{ $shipment->sender_phone_code }}{{ $shipment->sender_phone }}</p>@endif
        </td>
        <td>
            <div class="block-title">Consignee / Bill To</div>
            <div class="party-name">{{ $shipment->receiver_name ?: 'Not provided' }}</div>
            @if($shipment->receiver_company)<p class="party-line">{{ $shipment->receiver_company }}</p>@endif
            <p class="party-line">{{ $shipment->receiver_address }}</p>
            @if($shipment->receiver_address2)<p class="party-line">{{ $shipment->receiver_address2 }}</p>@endif
            @if($receiverLocality)<p class="party-line">{{ $receiverLocality }}</p>@endif
            <p class="party-line">{{ $shipment->receiver_country }}</p>
            @if($shipment->receiver_email)<p class="party-line">{{ $shipment->receiver_email }}</p>@endif
            @if($shipment->receiver_phone)<p class="party-line">Tel: {{ $shipment->receiver_phone_code }}{{ $shipment->receiver_phone }}</p>@endif
        </td>
    </tr>
</table>

<table class="shipment-table">
    <tr class="section-heading"><th colspan="5">Shipment particulars</th></tr>
    <tr>
        <td><span class="field-label">Route</span><span class="field-value">{{ $origin ?: 'Origin' }} to {{ $destination ?: 'Destination' }}</span></td>
        <td><span class="field-label">Carrier</span><span class="field-value">{{ $shipment->carrier_name ?: 'Not assigned' }}</span></td>
        <td><span class="field-label">Carrier reference</span><span class="field-value">{{ $shipment->carrier_tracking_number ?: '—' }}</span></td>
        <td><span class="field-label">Pieces</span><span class="field-value">{{ $shipment->total_packages ?: 1 }}</span></td>
        <td><span class="field-label">Chargeable weight</span><span class="field-value">{{ number_format((float) ($shipment->total_weight ?: $shipment->weight), 2) }} kg</span></td>
    </tr>
</table>

<table class="charges">
    <thead>
        <tr>
            <th class="number">No.</th>
            <th class="description">Freight / service description</th>
            <th class="amount">Amount ({{ $shipmentInvoice->currency }})</th>
        </tr>
    </thead>
    <tbody>
        @forelse($charges as $charge)
            <tr class="{{ ($charge['type'] ?? null) === 'base_freight' ? 'base-row' : '' }}">
                <td class="number">{{ $loop->iteration }}</td>
                <td>{{ $charge['description'] ?? 'Service charge' }}</td>
                <td>{{ number_format((float) ($charge['amount'] ?? 0), 2) }}</td>
            </tr>
        @empty
            <tr><td class="number">1</td><td>Freight charge</td><td>0.00</td></tr>
        @endforelse
        <tr>
            <td colspan="2" class="total-label">Invoice total</td>
            <td class="total-amount">{{ $shipmentInvoice->currency }} {{ number_format((float) $shipmentInvoice->total_amount, 2) }}</td>
        </tr>
    </tbody>
</table>

<table class="payment-table">
    <tr>
        <td>
            <div class="notes-box">
                <div class="notes-title">Payment instructions / remarks</div>
                @if($shipmentInvoice->notes)
                    {!! nl2br(e($shipmentInvoice->notes)) !!}
                @else
                    Payment is due according to the terms shown on this invoice.
                @endif
            </div>
        </td>
        <td class="summary-cell">
            <table class="summary">
                <tr><td>Subtotal</td><td>{{ number_format((float) $shipmentInvoice->total_amount, 2) }}</td></tr>
                <tr><td>Additional tax</td><td>0.00</td></tr>
                <tr class="due-row">
                    <td>{{ $shipmentInvoice->status === 'paid' ? 'Amount paid' : 'Amount due' }}</td>
                    <td>{{ $shipmentInvoice->currency }} {{ number_format((float) $shipmentInvoice->total_amount, 2) }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<div class="declaration">
    This document records freight and related service charges for the shipment referenced above. Any customs duties,
    destination taxes, storage, inspection, or government fees not listed are excluded unless expressly stated.
</div>

<table class="signature-table">
    <tr>
        <td><div class="signature-line">Prepared by</div></td>
        <td><div class="signature-line">Authorized signature</div></td>
        <td><div class="signature-line">Customer acknowledgement</div></td>
    </tr>
</table>

<div class="footer">
    EXPRESS PEEK &nbsp;|&nbsp; International Courier &amp; Freight Services &nbsp;|&nbsp;
    Invoice {{ $shipmentInvoice->invoice_number }} &nbsp;|&nbsp; Tracking {{ $shipment->tracking_number }}
</div>
</body>
</html>
