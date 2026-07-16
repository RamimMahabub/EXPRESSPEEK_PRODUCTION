<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $sourcingInvoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 14px;
            margin: 0;
            padding: 20px;
        }
        .header {
            width: 100%;
            margin-bottom: 40px;
        }
        .header td {
            vertical-align: top;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #4c1d95; /* Violet 900 */
        }
        .invoice-title {
            font-size: 28px;
            color: #333;
            text-align: right;
            margin: 0;
        }
        .info-table {
            width: 100%;
            margin-bottom: 30px;
        }
        .info-table td {
            vertical-align: top;
            width: 50%;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            background: #f3f4f6;
            padding: 10px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            color: #4b5563;
            border-bottom: 2px solid #e5e7eb;
        }
        .items-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        .items-table .amount-col {
            text-align: right;
        }
        .total-row td {
            font-weight: bold;
            font-size: 16px;
            border-bottom: none;
            padding-top: 20px;
        }
        .notes-box {
            background: #f9fafb;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #e5e7eb;
            font-size: 13px;
            color: #4b5563;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 4px;
            margin-top: 5px;
        }
        .status-paid { background: #d1fae5; color: #065f46; border: 1px solid #34d399; }
        .status-unpaid { background: #fef3c7; color: #92400e; border: 1px solid #fbbf24; }
        .status-cancelled { background: #fee2e2; color: #991b1b; border: 1px solid #f87171; }
    </style>
</head>
<body>

    <table class="header">
        <tr>
            <td>
                <div class="logo">ExpressPeek</div>
                <div style="color: #6b7280; font-size: 12px; margin-top: 5px;">
                    Dhaka, Bangladesh<br>
                    support@expresspeek.com<br>
                    expresspeek.com<br>
                    WhatsApp: +880 1400-659902
                </div>
            </td>
            <td style="text-align: right;">
                <h1 class="invoice-title">INVOICE</h1>
                <div style="font-size: 14px; margin-top: 5px; color: #4b5563;">
                    #{{ $sourcingInvoice->invoice_number }}
                </div>
                <div style="margin-top: 10px;">
                    <span class="status-badge status-{{ strtolower($sourcingInvoice->status) }}">
                        {{ $sourcingInvoice->status_label }}
                    </span>
                </div>
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td>
                <div class="section-title">Billed To</div>
                <strong>{{ $sourcingInvoice->sourcingRequest->customer_name }}</strong><br>
                {{ $sourcingInvoice->sourcingRequest->destination_address }}<br>
                {{ $sourcingInvoice->sourcingRequest->destination_city }}
                @if($sourcingInvoice->sourcingRequest->destination_state), {{ $sourcingInvoice->sourcingRequest->destination_state }}@endif
                {{ $sourcingInvoice->sourcingRequest->destination_postal_code }}<br>
                {{ $sourcingInvoice->sourcingRequest->destination_country }}<br>
                WhatsApp: {{ $sourcingInvoice->sourcingRequest->whatsapp_country_code }}{{ $sourcingInvoice->sourcingRequest->whatsapp_number }}
            </td>
            <td style="text-align: right;">
                <div class="section-title">Invoice Details</div>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="text-align: right; padding-bottom: 5px; color: #6b7280;">Date:</td>
                        <td style="text-align: right; padding-bottom: 5px; font-weight: bold;">{{ $sourcingInvoice->created_at->format('M d, Y') }}</td>
                    </tr>
                    @if($sourcingInvoice->due_date)
                    <tr>
                        <td style="text-align: right; padding-bottom: 5px; color: #6b7280;">Due Date:</td>
                        <td style="text-align: right; padding-bottom: 5px; font-weight: bold;">{{ $sourcingInvoice->due_date->format('M d, Y') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="text-align: right; padding-bottom: 5px; color: #6b7280;">Ref:</td>
                        <td style="text-align: right; padding-bottom: 5px; font-weight: bold;">{{ $sourcingInvoice->sourcingRequest->reference_number }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Description</th>
                <th class="amount-col">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sourcingInvoice->items as $item)
            <tr>
                <td>{{ $item['description'] }}</td>
                <td class="amount-col">{{ number_format($item['amount'], 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td style="text-align: right; color: #6b7280;">Total ({{ $sourcingInvoice->currency }})</td>
                <td class="amount-col" style="font-size: 18px; color: #4c1d95;">{{ number_format($sourcingInvoice->total_amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    @if($sourcingInvoice->notes)
    <div class="notes-box">
        <div class="section-title" style="margin-bottom: 10px;">Payment Notes / Instructions</div>
        {!! nl2br(e($sourcingInvoice->notes)) !!}
    </div>
    @endif

</body>
</html>
