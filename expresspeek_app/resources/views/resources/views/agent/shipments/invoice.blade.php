<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Proforma Invoice {{ $shipment->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        @page { size: A4; margin: 10mm; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 8pt; color: #000; background: #fff; }
        .page { width: 100%; margin: 0 auto; }

        h1 { font-size: 18pt; font-weight: 900; margin-bottom: 4px; }
        .meta { font-size: 7.5pt; font-weight: 700; margin-bottom: 8px; border-bottom: 1px solid #000; padding-bottom: 5px; }

        .address-row { display: flex; gap: 12px; margin-bottom: 8px; font-size: 7.5pt; line-height: 1.35; }
        .address-col { flex: 1; border: 1px solid #000; padding: 5px 6px; min-height: 115px; }
        .address-col .label { font-size: 7pt; font-weight: 800; margin-bottom: 3px; }
        .address-col p { margin: 0 0 1px 0; }

        .summary-row { display: flex; gap: 12px; margin-bottom: 8px; }
        .summary-box { flex: 1; border: 1px solid #000; padding: 5px 6px; font-size: 7.5pt; line-height: 1.35; }
        .summary-box strong { font-weight: 800; }

        table.items { width: 100%; border-collapse: collapse; font-size: 7pt; margin-bottom: 8px; table-layout: fixed; }
        table.items th { background: #f0f0f0; border: 1px solid #000; padding: 3px 3px; text-align: left; font-weight: 700; }
        table.items td { border: 1px solid #ccc; padding: 3px 3px; vertical-align: top; }
        table.items tr:nth-child(even) td { background: #fafafa; }

        .totals { border: 1px solid #000; padding: 5px 6px; font-size: 7.5pt; line-height: 1.35; }
        .totals-grid { display: flex; gap: 12px; }
        .totals-grid > div { flex: 1; }
        .totals table { width: 100%; border-collapse: collapse; }
        .totals td { padding: 1px 0; vertical-align: top; }
        .totals td:first-child { font-weight: 700; width: 58%; }

        .footer-note { margin-top: 6px; font-size: 7pt; color: #444; }

        @media print { body { margin: 0; } .no-print { display: none; } }
    </style>
</head>
<body>
<div class="page">

    <h1>Proforma Invoice</h1>

    <div class="meta">
        AWB No: {{ $shipment->awb_number }}
        &nbsp; | &nbsp;
        Invoice Date: {{ now()->format('Y-m-d') }}
        &nbsp; | &nbsp;
        Invoice No: {{ $shipment->invoice_number }}
    </div>

    @php
        $items = $shipment->items ?? [];
        $lineNum = 0;
        $totalGoodsValue = 0;
    @endphp

    <div class="address-row">
        <div class="address-col">
            <div class="label">SHIP FROM:</div>
            @if($shipment->sender_company)<p><strong>{{ $shipment->sender_company }}</strong></p>@endif
            <p>{{ $shipment->sender_name }}</p>
            <p>{{ $shipment->sender_address }}</p>
            @if($shipment->sender_address2)<p>{{ $shipment->sender_address2 }}</p>@endif
            <p>{{ $shipment->sender_city }}{{ $shipment->sender_postal_code ? ', ' . $shipment->sender_postal_code : '' }}</p>
            <p>{{ $shipment->sender_country }}</p>
            @if($shipment->sender_phone || $shipment->sender_phone_code)<p>Phone: {{ trim(($shipment->sender_phone_code ?? '') . ' ' . ($shipment->sender_phone ?? '')) }}</p>@endif
            @if($shipment->sender_email)<p>Email: {{ $shipment->sender_email }}</p>@endif
        </div>
        <div class="address-col">
            <div class="label">SHIP TO:</div>
            @if($shipment->receiver_company)<p><strong>{{ $shipment->receiver_company }}</strong></p>@endif
            <p>{{ $shipment->receiver_name }}</p>
            <p>{{ $shipment->receiver_address }}</p>
            @if($shipment->receiver_address2)<p>{{ $shipment->receiver_address2 }}</p>@endif
            <p>{{ $shipment->receiver_city }}{{ $shipment->receiver_postal_code ? ', ' . $shipment->receiver_postal_code : '' }}</p>
            <p>{{ $shipment->receiver_country }}</p>
            @if($shipment->receiver_phone || $shipment->receiver_phone_code)<p>Phone: {{ trim(($shipment->receiver_phone_code ?? '') . ' ' . ($shipment->receiver_phone ?? '')) }}</p>@endif
            @if($shipment->receiver_email)<p>Email: {{ $shipment->receiver_email }}</p>@endif
        </div>
    </div>

    <div class="summary-row">
        <div class="summary-box">
            <strong>Shipment:</strong> {{ ucfirst(str_replace('_', ' ', $shipment->shipment_type ?? '')) }}<br>
            <strong>Carrier:</strong> {{ $shipment->carrier_name ?? 'EXPRESS PEEK' }}<br>
            <strong>Status:</strong> {{ $shipment->status_label }}
        </div>
        <div class="summary-box">
            <strong>Packages:</strong> {{ (int) $shipment->total_packages }}<br>
            <strong>Gross Weight:</strong> {{ number_format((float) $shipment->total_weight, 2) }} kg<br>
            <strong>Goods Value:</strong> {{ number_format((float) ($totalGoodsValue ?: 1.00), 2) }} USD
        </div>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th style="width:4%">Item</th>
                <th style="width:20%">Description</th>
                <th style="width:6%">QTY</th>
                <th style="width:9%">Unit Value</th>
                <th style="width:9%">Sub Total Value</th>
            </tr>
        </thead>
        <tbody>
            @if($shipment->shipment_type === 'document')
                <tr>
                    <td>1</td>
                    <td>{{ $shipment->document_description ?: 'Document' }}</td>
                    <td>{{ $shipment->total_packages }} PCS</td>
                    <td>1.00 USD</td>
                    <td>1.00 USD</td>
                </tr>
            @else
                @foreach($items as $i => $item)
                @php
                    $lineNum++;
                    $subVal = ($item['value_per_item'] ?? 0) * ($item['quantity'] ?? 1);
                    $totalGoodsValue += $subVal;
                @endphp
                <tr>
                    <td>{{ $lineNum }}</td>
                    <td>{{ $item['name'] ?? '' }}</td>
                    <td>{{ $item['quantity'] ?? 1 }} PCS</td>
                    <td>{{ number_format($item['value_per_item'] ?? 0, 2) }} USD</td>
                    <td>{{ number_format($subVal, 2) }} USD</td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <div class="totals">
        <div class="totals-grid">
            <div>
                <table>
                    <tr><td>Total Invoice Amount:</td><td>{{ number_format((float) ($totalGoodsValue ?: 1.00), 2) }} USD</td></tr>
                    <tr><td>Currency Code:</td><td>USD</td></tr>
                    <tr><td>Terms of Trade:</td><td>Delivered at Place</td></tr>
                    <tr><td>Reason for Export:</td><td>Gift</td></tr>
                </table>
            </div>
            <div>
                <table>
                    <tr><td>Total Items:</td><td>{{ max(count($items), 1) }}</td></tr>
                    <tr><td>number of packages:</td><td>{{ $shipment->total_packages }}</td></tr>
                    <tr><td>Duty / taxes acct:</td><td>Receiver Will Pay</td></tr>
                    <tr><td>Total Gross Weight:</td><td>{{ number_format($shipment->total_weight, 2) }} kg</td></tr>
                </table>
            </div>
        </div>
        <div class="footer-note">Carrier: {{ $shipment->carrier_name ?? 'EXPRESS PEEK' }} | {{ $shipment->sender_country ?: '' }} → {{ $shipment->receiver_country ?: '' }}</div>
    </div>

</div>
</body>
</html>
