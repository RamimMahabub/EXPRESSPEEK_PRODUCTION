<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo e($shipment->shipment_purpose === 'Commercial' ? 'Commercial' : 'Proforma'); ?> Invoice <?php echo e($shipment->invoice_number); ?></title>
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
            margin-bottom: 10px;
        }
        .header-info span {
            margin-right: 20px;
        }
        
        .parties-table { margin-bottom: 10px; }
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
            margin-bottom: 10px;
        }
        .remarks span {
            margin-left: 20px;
            font-weight: normal;
        }

        .items-table { margin-bottom: 10px; border: 1px solid #000; }
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
            margin-bottom: 20px;
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
            margin-bottom: 15px;
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
<?php
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
            'weight' => $item['weight'] ?? null,
            'hs_code' => $item['hs_code'] ?? null,
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
    $senderPhone = collect([$shipment->sender_phone_code, $shipment->sender_phone])->filter()->join('');
    $receiverPhone = collect([$shipment->receiver_phone_code, $shipment->receiver_phone])->filter()->join('');
    
    $awbNo = str_replace(' ', '', $shipment->tracking_number ?? '');
    $invoiceDate = $shipment->created_at ? $shipment->created_at->format('Y-m-d') : date('Y-m-d');
    $invoiceNo = $shipment->invoice_number ?: 'N/A';
    $currency = 'USD'; // Assuming USD from proforma template
?>

<h1 class="document-title"><?php echo e($shipment->shipment_purpose === 'Commercial' ? 'Commercial' : 'Proforma'); ?> Invoice</h1>

<div class="header-info">
    <span>AWB No: <?php echo e($awbNo); ?></span>
    <span>Invoice Date: <?php echo e($invoiceDate); ?></span>
    <span>Invoice No: <?php echo e($invoiceNo); ?></span>
</div>

<table class="parties-table">
    <tr>
        <td>
            <div class="party-title">SHIP FROM:</div>
            <div class="party-details">
                <?php if($shipment->sender_company): ?><?php echo e($shipment->sender_company); ?><br><?php endif; ?>
                <?php if($shipment->sender_name): ?><?php echo e($shipment->sender_name); ?><br><?php endif; ?>
                <?php if($shipment->sender_address): ?><?php echo e($shipment->sender_address); ?><br><?php endif; ?>
                <?php if($shipment->sender_address2): ?><?php echo e($shipment->sender_address2); ?><br><?php endif; ?>
                <?php if($senderLocality): ?><?php echo e($senderLocality); ?><br><?php endif; ?>
                <?php if($shipment->sender_country): ?><?php echo e($shipment->sender_country); ?><br><?php endif; ?>
                <?php if($senderPhone): ?>+<?php echo e($senderPhone); ?><br><?php endif; ?>
                <?php if($shipment->sender_email): ?><?php echo e($shipment->sender_email); ?><br><?php endif; ?>
                Trader Type: <?php echo e($shipment->sender_is_business ? 'BUSINESS' : 'INDIVIDUAL'); ?><br>
                VAT No:<br>
                EORI:<br>
                TAX ID:
            </div>
        </td>
        <td>
            <div class="party-title">SHIP TO:</div>
            <div class="party-details">
                <?php if($shipment->receiver_company): ?><?php echo e($shipment->receiver_company); ?><br><?php endif; ?>
                <?php if($shipment->receiver_name): ?><?php echo e($shipment->receiver_name); ?><br><?php endif; ?>
                <?php if($shipment->receiver_address): ?><?php echo e($shipment->receiver_address); ?><br><?php endif; ?>
                <?php if($shipment->receiver_address2): ?><?php echo e($shipment->receiver_address2); ?><br><?php endif; ?>
                <?php if($receiverLocality): ?><?php echo e($receiverLocality); ?><br><?php endif; ?>
                <?php if($shipment->receiver_country): ?><?php echo e($shipment->receiver_country); ?><br><?php endif; ?>
                <?php if($receiverPhone): ?>+<?php echo e($receiverPhone); ?><br><?php endif; ?>
                Trader Type: <?php echo e($shipment->receiver_is_business ? 'BUSINESS' : 'INDIVIDUAL'); ?><br>
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
    Remarks: <span><?php echo e($shipment->remarks ?? ''); ?></span>
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
        <?php $__currentLoopData = $invoiceLines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $charge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($index + 1); ?></td>
                <td><?php echo e($charge['description']); ?></td>
                <td><?php echo e($charge['hs_code'] ?? ''); ?></td>
                <td></td>
                <td>
                    <?php echo e(isset($charge['weight']) ? $charge['weight'] . ' kg' : ''); ?><br>
                </td>
                <td><?php echo e($shipment->sender_country_code ?? 'BANGLADESH'); ?></td>
                <td></td>
                <td><?php echo e($charge['quantity']); ?> PCS</td>
                <td><?php echo e(number_format((float) $charge['unit_value'], 2)); ?> <?php echo e($currency); ?></td>
                <td><?php echo e(number_format((float) $charge['line_total'], 2)); ?> <?php echo e($currency); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>

<div class="totals-container">
    <div class="totals-left">
        <table class="totals-table">
            <tr><td class="label">Total Goods Value:</td><td><?php echo e(number_format((float) $totalGoodsValue, 2)); ?> <?php echo e($currency); ?></td></tr>
            <tr><td class="label">Total Invoice Amount:</td><td><?php echo e(number_format((float) $totalGoodsValue, 2)); ?> <?php echo e($currency); ?></td></tr>
            <tr><td class="label">Currency Code:</td><td><?php echo e($currency); ?></td></tr>
            <tr><td class="label">Terms of Payment:</td><td></td></tr>
            <tr><td class="label">Terms of Trade:</td><td>Delivered at Place (DAP)</td></tr>
            <tr><td class="label">Place of Incoterm:</td><td><?php echo e($shipment->receiver_country); ?></td></tr>
            <tr><td class="label">Reason for Export:</td><td><?php echo e($shipment->shipment_purpose ?? ''); ?></td></tr>
            <tr><td class="label">Type of Export:</td><td><?php echo e($shipment->shipment_purpose ?? ''); ?></td></tr>
            <tr><td class="label">Total Net Weight:</td><td><?php echo e($grossWeight); ?> kg</td></tr>
            <tr><td class="label">Total Gross Weight:</td><td><?php echo e($grossWeight); ?> kg</td></tr>
        </table>
    </div>
    <div class="totals-right">
        <table class="totals-table">
            <tr><td class="label">Total line items:</td><td><?php echo e(max(count($invoiceLines), 1)); ?></td></tr>
            <tr><td class="label">Number of Pallets:</td><td></td></tr>
            <tr><td class="label">Total units:</td><td><?php echo e($totalQuantity); ?></td></tr>
            <tr><td class="label">Package Marks / Other Info:</td><td></td></tr>
        </table>
        
        <br>
        
        <table class="totals-table">
            <tr><td class="label">Payer of GST / VAT:</td><td></td></tr>
            <tr><td class="label">Duty / taxes acct:</td><td style="white-space: nowrap;"><span style="border: 1px solid #000; display: inline-block; width: 10px; height: 10px; margin-right: 4px; position: relative; top: 1px;"></span> Shipper Will Pay &nbsp;&nbsp;&nbsp; <span style="border: 1px solid #000; display: inline-block; width: 10px; height: 10px; margin-right: 4px; position: relative; top: 1px;"></span> Receiver Will Pay</td></tr>
            <tr><td class="label">Requiere Pedimento:</td><td>No</td></tr>
            <tr><td class="label">Duty / tax billing service:</td><td></td></tr>
            <tr><td class="label">Carrier:</td><td><?php echo e($shipment->carrier_name ?? 'DHL'); ?></td></tr>
            <tr><td class="label"><?php echo e($shipment->carrier_name ?? 'DHL'); ?> Tracking Number:</td><td><?php echo e($shipment->tracking_number ?? ''); ?></td></tr>
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
            <div class="sig-value"><?php echo e($shipment->sender_name ?? 'MR.MITHU / MR. ANISUR / MR. ANTO'); ?></div>
        </div>
        <div class="sig-row">
            <div class="sig-label">Date of Signature:</div>
            <div class="sig-value"><?php echo e($invoiceDate); ?></div>
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
<script>window.onload = function() { window.print(); };</script>
</html>
<?php /**PATH D:\projects\expresspeek_app\expresspeek_app\resources\views/agent/shipments/invoice.blade.php ENDPATH**/ ?>