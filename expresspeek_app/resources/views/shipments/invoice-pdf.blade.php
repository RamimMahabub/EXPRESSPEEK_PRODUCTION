<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $shipment->shipment_purpose === 'Commercial' ? 'Commercial' : 'Proforma' }} Invoice {{ $shipmentInvoice->invoice_number }}</title>
    <style>
        @page { margin: 15mm 15mm; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            color: #000;
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
        }
        table { width: 100%; border-collapse: collapse; }
        td, th { vertical-align: top; }
        .document-title {
            margin: 0 0 15px 0;
            font-size: 24px;
            font-weight: bold;
        }
        .header-info {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .header-info span {
            margin-right: 20px;
        }
        
        .parties-table { margin-bottom: 20px; }
        .parties-table td { width: 50%; padding-right: 15px; }
        .party-title { font-weight: bold; font-size: 10px; margin-bottom: 3px; }
        .party-details { font-size: 10px; line-height: 1.4; }
        
        .references {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .remarks {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .remarks span {
            margin-left: 20px;
            font-weight: normal;
        }

        .items-table { margin-bottom: 20px; border: 1px solid #000; }
        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 5px;
            font-size: 10px;
        }
        .items-table th {
            font-weight: bold;
            text-align: left;
        }
        
        .totals-container {
            display: table;
            width: 100%;
            font-size: 10px;
            margin-bottom: 40px;
        }
        .totals-left {
            display: table-cell;
            width: 50%;
            padding-right: 15px;
        }
        .totals-right {
            display: table-cell;
            width: 50%;
        }
        .totals-table { width: 100%; margin-bottom: 15px; }
        .totals-table td { padding: 2px 0; }
        .totals-table td.label { width: 45%; }
        
        .certification {
            font-size: 10px;
            margin-bottom: 30px;
        }
        
        .signature-section {
            display: table;
            width: 100%;
            font-size: 11px;
        }
        .signature-left {
            display: table-cell;
            width: 45%;
        }
        .signature-middle {
            display: table-cell;
            width: 30%;
        }
        .signature-right {
            display: table-cell;
            width: 25%;
            text-align: center;
        }
        
        .sig-row { display: table; width: 100%; margin-bottom: 5px; }
        .sig-label { display: table-cell; width: 120px; font-weight: bold; }
        .sig-value { display: table-cell; border-bottom: 1px solid transparent; }
        
    </style>
</head>
<body>
@php
    $shipment = $shipmentInvoice->shipment;
    $charges = collect($shipmentInvoice->items ?? [])->values();
    
    // Sender formatting
    $senderLocality = collect([$shipment->sender_city, $shipment->sender_postal_code])->filter()->join(', ');
    $receiverLocality = collect([$shipment->receiver_city, $shipment->receiver_postal_code])->filter()->join(', ');
    
    // Tracking number is AWB No
    $awbNo = str_replace(' ', '', $shipment->tracking_number ?? '');
    
    $invoiceDate = $shipmentInvoice->created_at ? $shipmentInvoice->created_at->format('Y-m-d') : date('Y-m-d');
    $invoiceNo = $shipmentInvoice->invoice_number;
    
@endphp

<h1 class="document-title">{{ $shipment->shipment_purpose === 'Commercial' ? 'Commercial' : 'Proforma' }} Invoice</h1>

<div class="header-info">
    <span>AWB No: {{ $awbNo }}</span>
    <span>Invoice Date: {{ $invoiceDate }}</span>
    <span>Invoice No: {{ $invoiceNo }}</span>
</div>

<table class="parties-table">
    <tr>
        <td>
            <div class="party-title">SHIP FROM:</div>
            <div class="party-details">
                @if($shipment->sender_company){{ $shipment->sender_company }}<br>@endif
                @if($shipment->sender_name){{ $shipment->sender_name }}<br>@endif
                @if($shipment->sender_address){{ $shipment->sender_address }}<br>@endif
                @if($shipment->sender_address2){{ $shipment->sender_address2 }}<br>@endif
                @if($senderLocality){{ $senderLocality }}<br>@endif
                @if($shipment->sender_country){{ $shipment->sender_country }}<br>@endif
                @if($shipment->sender_phone)+{{ $shipment->sender_phone_code }}{{ $shipment->sender_phone }}<br>@endif
                @if($shipment->sender_email){{ $shipment->sender_email }}<br>@endif
                Trader Type: {{ $shipment->sender_is_business ? 'BUSINESS' : 'INDIVIDUAL' }}<br>
                VAT No:<br>
                EORI:<br>
                TAX ID:
            </div>
        </td>
        <td>
            <div class="party-title">SHIP TO:</div>
            <div class="party-details">
                @if($shipment->receiver_company){{ $shipment->receiver_company }}<br>@endif
                @if($shipment->receiver_name){{ $shipment->receiver_name }}<br>@endif
                @if($shipment->receiver_address){{ $shipment->receiver_address }}<br>@endif
                @if($shipment->receiver_address2){{ $shipment->receiver_address2 }}<br>@endif
                @if($receiverLocality){{ $receiverLocality }}<br>@endif
                @if($shipment->receiver_country){{ $shipment->receiver_country }}<br>@endif
                @if($shipment->receiver_phone)+{{ $shipment->receiver_phone_code }}{{ $shipment->receiver_phone }}<br>@endif
                Trader Type: {{ $shipment->receiver_is_business ? 'BUSINESS' : 'INDIVIDUAL' }}<br>
                VAT No:<br>
                EORI:
            </div>
        </td>
    </tr>
</table>

<div class="references">
    Shipper Reference:<br>
    Receiver Reference:
</div>

<div class="remarks">
    Remarks: <span>SAMPLE OF NO COMMERCIAL VALUE, THIS VALUE CUSTOMS PURPOSE ONLY.</span>
</div>

<table class="items-table">
    <thead>
        <tr>
            <th>Ite-<br>m</th>
            <th>Description</th>
            <th>Commo-<br>dity Code</th>
            <th>Tax Paid</th>
            <th>Item Weight/<br>Item Total<br>Weight</th>
            <th>COO</th>
            <th>Reference Type &<br>ID</th>
            <th>QTY</th>
            <th>Unit<br>Value</th>
            <th>Sub Total<br>Value</th>
        </tr>
    </thead>
    <tbody>
        @forelse($charges as $index => $charge)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $charge['description'] ?? 'Item description' }}</td>
                <td>{{ $charge['commodity_code'] ?? '' }}</td>
                <td></td>
                <td>
                    {{ isset($charge['weight']) ? $charge['weight'] . ' kg' : '' }}<br>
                    {{ isset($charge['total_weight']) ? $charge['total_weight'] . ' kg' : '' }}
                </td>
                <td>{{ $charge['coo'] ?? $shipment->sender_country_code ?? 'BANGLADESH' }}</td>
                <td></td>
                <td>{{ $charge['quantity'] ?? '1' }} PCS</td>
                <td>{{ number_format((float) ($charge['amount'] ?? 0), 2) }} {{ $shipmentInvoice->currency }}</td>
                <td>{{ number_format((float) (($charge['amount'] ?? 0) * ($charge['quantity'] ?? 1)), 2) }} {{ $shipmentInvoice->currency }}</td>
            </tr>
        @empty
            <tr>
                <td>1</td>
                <td>Freight Charge</td>
                <td></td>
                <td></td>
                <td><br></td>
                <td>BANGLADESH</td>
                <td></td>
                <td>1 PCS</td>
                <td>{{ number_format((float) $shipmentInvoice->total_amount, 2) }} {{ $shipmentInvoice->currency }}</td>
                <td>{{ number_format((float) $shipmentInvoice->total_amount, 2) }} {{ $shipmentInvoice->currency }}</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="totals-container">
    <div class="totals-left">
        <table class="totals-table">
            <tr><td class="label">Total Goods Value:</td><td>{{ number_format((float) $shipmentInvoice->total_amount, 2) }} {{ $shipmentInvoice->currency }}</td></tr>
            <tr><td class="label">Total Invoice Amount:</td><td>{{ number_format((float) $shipmentInvoice->total_amount, 2) }} {{ $shipmentInvoice->currency }}</td></tr>
            <tr><td class="label">Currency Code:</td><td>{{ $shipmentInvoice->currency }}</td></tr>
            <tr><td class="label">Terms of Payment:</td><td></td></tr>
            <tr><td class="label">Terms of Trade:</td><td>Delivered at Place</td></tr>
            <tr><td class="label">Place of Incoterm:</td><td>{{ $shipment->receiver_country }}</td></tr>
            <tr><td class="label">Reason for Export:</td><td>Sample</td></tr>
            <tr><td class="label">Type of Export:</td><td>Sample</td></tr>
            <tr><td class="label">Total Net Weight:</td><td>{{ $shipment->total_weight ?? $shipment->weight }} kg</td></tr>
            <tr><td class="label">Total Gross Weight:</td><td>{{ $shipment->total_weight ?? $shipment->weight }} kg</td></tr>
        </table>
    </div>
    <div class="totals-right">
        <table class="totals-table">
            <tr><td class="label">Total line items:</td><td>{{ max(count($charges), 1) }}</td></tr>
            <tr><td class="label">Number of Pallets:</td><td></td></tr>
            <tr><td class="label">Total units:</td><td>{{ $shipment->total_packages ?? $shipment->packages ?? 1 }}</td></tr>
            <tr><td class="label">Package Marks / Other Info:</td><td></td></tr>
        </table>
        
        <br>
        
        <table class="totals-table">
            <tr><td class="label">Payer of GST / VAT:</td><td></td></tr>
            <tr><td class="label">Duty / taxes acct:</td><td>Receiver Will Pay</td></tr>
            <tr><td class="label">Requiere Pedimento:</td><td>No</td></tr>
            <tr><td class="label">Duty / tax billing service:</td><td></td></tr>
            <tr><td class="label">Carrier:</td><td>{{ $shipment->carrier_name ?? 'DHL' }}</td></tr>
            <tr><td class="label">Ultimate Consignee:</td><td></td></tr>
            <tr><td class="label">Exemption Citation:</td><td></td></tr>
        </table>
    </div>
</div>

<div class="certification">
    I/We hereby certify that the information contained in the invoice is true and correct and that the contents of this shipment are as stated above.
</div>

<div class="signature-section">
    <div class="signature-left">
        <div class="sig-row">
            <div class="sig-label">Name:</div>
            <div class="sig-value">{{ $shipment->sender_name ?? 'MR.MITHU / MR. ANISUR / MR. ANTO' }}</div>
        </div>
        <div class="sig-row">
            <div class="sig-label">Position:</div>
            <div class="sig-value">Merchandising Department. / Technical Department.</div>
        </div>
        <div class="sig-row">
            <div class="sig-label">Date of Signature:</div>
            <div class="sig-value">{{ $invoiceDate }}</div>
        </div>
    </div>
    <div class="signature-middle">
        <div class="sig-row">
            <div class="sig-label" style="width:70px;">Signature:</div>
            <div class="sig-value"></div>
        </div>
    </div>
    <div class="signature-right">
        <div style="font-weight: bold;">Company Stamp</div>
        <br><br><br>
        Page 1 of 1
    </div>
</div>

</body>
</html>

