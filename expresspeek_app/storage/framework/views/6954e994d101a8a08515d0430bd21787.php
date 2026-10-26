<?php
    $shipment = $shipmentInvoice->shipment;
    $charges = collect($shipmentInvoice->items ?? [])->values();
    $packages = collect($shipment->packages ?? []);
    $goods = collect($shipment->items ?? []);
    $isDocument = $shipment->shipment_type === 'document';
    $currency = $shipmentInvoice->currency;
    $shipmentDate = $shipment->created_at?->format('Y-m-d') ?? '';
    $receiptDate = $shipmentInvoice->created_at?->format('Y-m-d') ?? '';
    $waybill = $shipment->carrier_tracking_number ?: $shipment->tracking_number;
    $chargeableWeight = (float) ($shipment->total_weight ?: $shipment->weight);
    $actualWeight = $packages->isNotEmpty()
        ? $packages->sum(fn ($package) => (float) ($package['actual_weight'] ?? $package['weight'] ?? 0) * max(1, (int) ($package['quantity'] ?? 1)))
        : $chargeableWeight;
    $hasDimensions = $packages->contains(fn ($package) => isset($package['volumetric_weight'])
        || (!empty($package['length']) && !empty($package['width']) && !empty($package['height'])));
    $dimensionalWeight = $packages->sum(fn ($package) => (float) ($package['volumetric_weight']
        ?? (($package['length'] ?? 0) * ($package['width'] ?? 0) * ($package['height'] ?? 0) / \App\Support\PackageWeightCalculator::DIVISOR))
        * max(1, (int) ($package['quantity'] ?? 1)));
    $declaredValue = $goods->sum(fn ($item) => (float) ($item['value_per_item'] ?? 0) * (int) ($item['quantity'] ?? 1));
    $senderLocality = collect([$shipment->sender_city, $shipment->sender_state, $shipment->sender_postal_code])->filter()->join(', ');
    $receiverLocality = collect([$shipment->receiver_city, $shipment->receiver_state, $shipment->receiver_postal_code])->filter()->join(', ');
    $senderPhone = collect([$shipment->sender_phone_code, $shipment->sender_phone])->filter()->join('');
    $receiverPhone = collect([$shipment->receiver_phone_code, $shipment->receiver_phone])->filter()->join('');
    $logoPath = public_path('images/express-peek-logo-pdf.png');
    $logoData = is_file($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';
    $baseCharges = $charges->filter(fn ($charge) => ($charge['type'] ?? '') === 'base_freight');
    $serviceCharges = $charges->reject(fn ($charge) => ($charge['type'] ?? '') === 'base_freight');
    $compactCharges = $charges->count() <= 5 && $charges->sum(fn ($charge) => mb_strlen($charge['description'] ?? '')) <= 240;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shipment Receipt <?php echo e($shipmentInvoice->invoice_number); ?></title>
    <style>
        @page { size: A4 portrait; margin: 24pt 30pt 40pt; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #111; font-family: DejaVu Sans, sans-serif; font-size: 8pt; line-height: 1.4; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        td, th { vertical-align: top; overflow-wrap: break-word; word-wrap: break-word; }
        h1 { margin: 0; font-size: 21pt; line-height: 1.2; font-weight: bold; }
        h2 { margin: 0 0 9pt; font-size: 13pt; line-height: 1.25; font-weight: bold; }
        p { margin: 0; }
        .header { border-bottom: 1pt solid #aaa; margin-bottom: 14pt; }
        .header td { vertical-align: middle; padding-bottom: 8pt; }
        .brand { width: 40%; }
        .brand img { width: 126pt; height: auto; }
        .brand-name { font-size: 18pt; color: #172b70; font-weight: bold; }
        .title { width: 60%; text-align: right; }
        .receipt-meta { margin-top: 5pt; font-size: 7pt; color: #555; }
        .parties { border-bottom: 1pt solid #bbb; margin-bottom: 16pt; }
        .parties td { width: 50%; height: 135pt; padding: 0 10pt 12pt; }
        .parties td + td { padding-left: 13pt; }
        .address { line-height: 1.45; }
        .email { margin-top: 8pt; }
        .columns { margin-bottom: 12pt; }
        .column { width: 50%; padding: 0 10pt; }
        .column + .column { padding-left: 13pt; }
        .details td { padding: 1pt 0; line-height: 1.4; }
        .details .label { width: 48%; padding-right: 8pt; }
        .details .value { width: 52%; }
        .billing { margin-top: 4pt; }
        .charge-table th { text-align: left; font-size: 7pt; border-bottom: .6pt solid #aaa; padding: 0 0 5pt; }
        .charge-table td { padding: 5pt 0; border-bottom: .4pt solid #ddd; }
        .charge-table .amount { width: 90pt; text-align: right; padding-left: 6pt; }
        .charge-table .total td { border-top: 1pt solid #777; border-bottom: 0; font-weight: bold; padding-top: 7pt; }
        .charge-table tr, .details tr { page-break-inside: avoid; }
        .section { margin: 12pt 10pt 0; }
        .reference-table .label { width: 25%; }
        .contents { margin-top: 20pt; }
        .contents h2 { page-break-after: avoid; }
        .contents p { margin-bottom: 3pt; }
        .notes { margin-top: 14pt; white-space: pre-line; }
        .notes strong { display: block; margin-bottom: 3pt; }
        .footer { position: fixed; bottom: -22pt; left: 0; right: 0; border-top: .5pt solid #ccc; padding-top: 5pt; font-size: 7pt; color: #555; }
        .footer td:last-child { text-align: right; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td class="brand">
                <?php if($logoData): ?><img src="<?php echo e($logoData); ?>" alt="Express Peek"><?php else: ?><span class="brand-name">EXPRESS PEEK</span><?php endif; ?>
            </td>
            <td class="title">
                <h1>Shipment Receipt</h1>
                <p class="receipt-meta"><?php echo e($shipmentInvoice->invoice_number); ?><?php if($receiptDate): ?> &nbsp; | &nbsp; <?php echo e($receiptDate); ?><?php endif; ?></p>
            </td>
        </tr>
    </table>

    <table class="parties">
        <tr>
            <td>
                <h2>Shipment From</h2>
                <div class="address">
                    <p><?php echo e($shipment->sender_name); ?></p>
                    <?php if($shipment->sender_company): ?><p><?php echo e($shipment->sender_company); ?></p><?php endif; ?>
                    <?php $__currentLoopData = [$shipment->sender_address, $shipment->sender_address2, $shipment->sender_address3, $senderLocality, $shipment->sender_country]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($line): ?><p><?php echo e($line); ?></p><?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php if($shipment->sender_phone): ?><p><?php echo e($senderPhone); ?></p><?php endif; ?>
                    <?php if($shipment->sender_email): ?><p class="email"><?php echo e($shipment->sender_email); ?></p><?php endif; ?>
                </div>
            </td>
            <td>
                <h2>Shipment To</h2>
                <div class="address">
                    <p><?php echo e($shipment->receiver_name); ?></p>
                    <?php if($shipment->receiver_company): ?><p><?php echo e($shipment->receiver_company); ?></p><?php endif; ?>
                    <?php $__currentLoopData = [$shipment->receiver_address, $shipment->receiver_address2, $shipment->receiver_address3, $receiverLocality, $shipment->receiver_country]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($line): ?><p><?php echo e($line); ?></p><?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php if($shipment->receiver_phone): ?><p><?php echo e($receiverPhone); ?></p><?php endif; ?>
                    <?php if($shipment->receiver_email): ?><p class="email"><?php echo e($shipment->receiver_email); ?></p><?php endif; ?>
                </div>
            </td>
        </tr>
    </table>

    <table class="columns">
        <tr>
            <td class="column">
                <h2>Shipment Details</h2>
                <table class="details">
                    <tr><td class="label">Shipment Date:</td><td class="value"><?php echo e($shipmentDate); ?></td></tr>
                    <tr><td class="label">Waybill Number:</td><td class="value"><?php echo e($waybill); ?></td></tr>
                    <tr><td class="label">Tracking Number:</td><td class="value"><?php echo e($shipment->tracking_number); ?></td></tr>
                    <tr><td class="label">Service Type:</td><td class="value"><?php echo e($shipment->carrier_name); ?></td></tr>
                    <tr><td class="label">Packaging Type:</td><td class="value"><?php echo e($isDocument ? 'Documents' : 'Parcel (Non-Document)'); ?></td></tr>
                    <tr><td class="label">Number of Pieces:</td><td class="value"><?php echo e($shipment->total_packages ?: max(1, $packages->sum(fn ($package) => max(1, (int) ($package['quantity'] ?? 1))))); ?></td></tr>
                    <tr><td class="label">Total Weight:</td><td class="value"><?php echo e(number_format($actualWeight, 2)); ?> kg</td></tr>
                    <tr><td class="label">Dimensional:</td><td class="value"><?php echo e($hasDimensions ? number_format($dimensionalWeight, 2) . ' kg' : ''); ?></td></tr>
                    <tr><td class="label">Chargeable:</td><td class="value"><?php echo e(number_format($chargeableWeight, 2)); ?> kg</td></tr>
                </table>
            </td>
            <td class="column">
                <h2>International Information</h2>
                <table class="details">
                    <tr><td class="label">Declared Value:</td><td class="value"><?php echo e(!$isDocument && $goods->isNotEmpty() ? number_format($declaredValue, 2) . ' USD' : ''); ?></td></tr>
                    <tr><td class="label">Shipment Purpose:</td><td class="value"><?php echo e($shipment->shipment_purpose ?? ''); ?></td></tr>
                    <tr><td class="label">Destination:</td><td class="value"><?php echo e($shipment->receiver_country); ?></td></tr>
                    <tr><td class="label">Estimated Del date:</td><td class="value"><?php echo e($shipment->estimated_delivery?->format('l, d M, Y') ?? ''); ?></td></tr>
                    <tr><td class="label">Remarks:</td><td class="value"><?php echo e($shipment->remarks ?? ''); ?></td></tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="columns billing">
        <tr>
            <td class="column">
                <h2>Billing Information</h2>
                <table class="details">
                    <tr><td class="label">Courier:</td><td class="value"><?php echo e($shipment->carrier_name); ?></td></tr>
                    <tr><td class="label">Receipt Number:</td><td class="value"><?php echo e($shipmentInvoice->invoice_number); ?></td></tr>
                    <tr><td class="label">Currency:</td><td class="value"><?php echo e($currency); ?></td></tr>
                    <tr><td class="label">Payment Status:</td><td class="value"><?php echo e($shipmentInvoice->status_label); ?></td></tr>
                    <?php if($shipmentInvoice->due_date): ?>
                        <tr><td class="label">Payment Due:</td><td class="value"><?php echo e($shipmentInvoice->due_date->format('Y-m-d')); ?></td></tr>
                    <?php endif; ?>
                </table>
            </td>
            <td class="column">
                <?php if($compactCharges): ?>
                    <h2>Charge Breakdown</h2>
                    <?php echo $__env->make('shipments.partials.receipt-charges', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <?php if(!$compactCharges): ?>
        <div class="section">
            <h2>Charge Breakdown</h2>
            <?php echo $__env->make('shipments.partials.receipt-charges', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    <?php endif; ?>

    <div class="section">
        <h2>Reference Information</h2>
        <table class="details reference-table">
            <tr><td class="label">Reference:</td><td><?php echo e($shipment->invoice_number ?? ''); ?></td></tr>
        </table>
    </div>

    <div class="section contents">
        <h2>Description of Contents</h2>
        <?php if($isDocument): ?>
            <p><?php echo e($shipment->document_description ?? ''); ?></p>
        <?php else: ?>
            <?php $__currentLoopData = $goods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <p><?php echo e($item['name'] ?? $item['description'] ?? ''); ?><?php echo e(isset($item['quantity']) ? ' - ' . $item['quantity'] . ' pcs' : ''); ?></p>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
        <?php if(filled($shipmentInvoice->notes)): ?>
            <div class="notes"><strong>Payment instructions / notes</strong><?php echo e($shipmentInvoice->notes); ?></div>
        <?php endif; ?>
    </div>

    <div class="footer">
        <table><tr><td>Express Peek | Shipment Receipt</td><td><?php echo e($shipmentInvoice->invoice_number); ?></td></tr></table>
    </div>
</body>
</html>
<?php /**PATH D:\projects\expresspeek_app\expresspeek_app\resources\views/shipments/invoice-pdf.blade.php ENDPATH**/ ?>