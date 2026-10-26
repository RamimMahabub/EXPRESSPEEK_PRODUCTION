

<?php $__env->startSection('title', 'Shipment Receipt - ' . $shipment->tracking_number); ?>
<?php $__env->startSection('page-title', 'Create Shipment Receipt'); ?>
<?php $__env->startSection('page-subtitle', 'Prepare a shipment receipt for ' . $shipment->tracking_number); ?>

<?php $__env->startSection('content'); ?>
<?php
    $baseFreight = (float) old('base_freight', 0);
    $otherChargesForJs = collect(old('other_charges', []))->values()->map(fn ($charge, $index) => [
        'id' => 'other-charge-' . $index,
        'description' => $charge['description'] ?? '',
        'amount' => (float) ($charge['amount'] ?? 0),
    ])->all();
    $packages = collect($shipment->packages ?? []);
    $goods = collect($shipment->items ?? []);
    $isDocument = $shipment->shipment_type === 'document';
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
?>

<div class="receipt-create">
    <a href="<?php echo e(route('admin.shipments.edit', $shipment)); ?>" class="receipt-back">&lsaquo; Back to shipment</a>

    <?php if($errors->any()): ?>
        <div class="receipt-errors" role="alert">
            <strong>Correct the following before generating the receipt:</strong>
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('admin.shipments.invoice.store', $shipment)); ?>" method="POST" x-data="shipmentInvoiceCharges">
        <?php echo csrf_field(); ?>
        <article class="receipt-sheet" aria-label="Shipment receipt creation preview">
            <header class="receipt-header">
                <img src="/images/express-peek-logo.webp" alt="Express Peek" width="168" height="47">
                <div>
                    <h1>Shipment Receipt</h1>
                    <p class="receipt-meta">Draft &nbsp; | &nbsp; <?php echo e(now()->format('Y-m-d')); ?></p>
                </div>
            </header>

            <section class="receipt-parties receipt-columns" aria-label="Sender and receiver">
                <div>
                    <h2>Shipment From</h2>
                    <p><?php echo e($shipment->sender_name); ?></p>
                    <?php if($shipment->sender_company): ?><p><?php echo e($shipment->sender_company); ?></p><?php endif; ?>
                    <?php $__currentLoopData = [$shipment->sender_address, $shipment->sender_address2, $shipment->sender_address3, $senderLocality, $shipment->sender_country]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($line): ?><p><?php echo e($line); ?></p><?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php if($shipment->sender_phone): ?><p><?php echo e($shipment->sender_phone_code); ?><?php echo e($shipment->sender_phone); ?></p><?php endif; ?>
                    <?php if($shipment->sender_email): ?><p class="receipt-email"><?php echo e($shipment->sender_email); ?></p><?php endif; ?>
                </div>
                <div>
                    <h2>Shipment To</h2>
                    <p><?php echo e($shipment->receiver_name); ?></p>
                    <?php if($shipment->receiver_company): ?><p><?php echo e($shipment->receiver_company); ?></p><?php endif; ?>
                    <?php $__currentLoopData = [$shipment->receiver_address, $shipment->receiver_address2, $shipment->receiver_address3, $receiverLocality, $shipment->receiver_country]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($line): ?><p><?php echo e($line); ?></p><?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php if($shipment->receiver_phone): ?><p><?php echo e($shipment->receiver_phone_code); ?><?php echo e($shipment->receiver_phone); ?></p><?php endif; ?>
                    <?php if($shipment->receiver_email): ?><p class="receipt-email"><?php echo e($shipment->receiver_email); ?></p><?php endif; ?>
                </div>
            </section>

            <div class="receipt-columns receipt-information">
                <section>
                    <h2>Shipment Details</h2>
                    <dl class="receipt-details">
                        <dt>Shipment Date:</dt><dd><?php echo e($shipment->created_at?->format('Y-m-d') ?? ''); ?></dd>
                        <dt>Waybill Number:</dt><dd><?php echo e($shipment->carrier_tracking_number ?: $shipment->tracking_number); ?></dd>
                        <dt>Tracking Number:</dt><dd><?php echo e($shipment->tracking_number); ?></dd>
                        <dt>Service Type:</dt><dd><?php echo e($shipment->carrier_name); ?></dd>
                        <dt>Packaging Type:</dt><dd><?php echo e($isDocument ? 'Documents' : 'Parcel (Non-Document)'); ?></dd>
                        <dt>Number of Pieces:</dt><dd><?php echo e($shipment->total_packages ?: max(1, $packages->sum(fn ($package) => max(1, (int) ($package['quantity'] ?? 1))))); ?></dd>
                        <dt>Total Weight:</dt><dd><?php echo e(number_format($actualWeight, 2)); ?> kg</dd>
                        <dt>Dimensional:</dt><dd><?php echo e($hasDimensions ? number_format($dimensionalWeight, 2) . ' kg' : ''); ?></dd>
                        <dt>Chargeable:</dt><dd><?php echo e(number_format($chargeableWeight, 2)); ?> kg</dd>
                    </dl>
                </section>
                <section>
                    <h2>International Information</h2>
                    <dl class="receipt-details">
                        <dt>Declared Value:</dt><dd><?php echo e(!$isDocument && $goods->isNotEmpty() ? number_format($declaredValue, 2) . ' USD' : ''); ?></dd>
                        <dt>Shipment Purpose:</dt><dd><?php echo e($shipment->shipment_purpose ?? ''); ?></dd>
                        <dt>Destination:</dt><dd><?php echo e($shipment->receiver_country); ?></dd>
                        <dt>Estimated Del date:</dt><dd><?php echo e($shipment->estimated_delivery?->format('l, d M, Y') ?? ''); ?></dd>
                        <dt>Remarks:</dt><dd class="receipt-remarks"><?php echo e($shipment->remarks ?? ''); ?></dd>
                    </dl>
                </section>
            </div>

            <div class="receipt-columns receipt-billing">
                <section>
                    <h2>Billing Information</h2>
                    <dl class="receipt-details receipt-billing-fields">
                        <dt>Courier:</dt><dd><?php echo e($shipment->carrier_name); ?></dd>
                        <dt>Receipt Number:</dt><dd class="receipt-muted">Draft</dd>
                        <dt><label for="invoice_currency">Currency:</label></dt>
                        <dd>
                            <select id="invoice_currency" name="currency" x-model="currency">
                                <?php $__currentLoopData = ['BDT','USD','GBP','EUR','AUD','AED','SGD','CAD']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($currency); ?>" <?php if(old('currency', 'BDT') === $currency): echo 'selected'; endif; ?>><?php echo e($currency); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </dd>
                        <dt>Payment Status:</dt><dd>Unpaid</dd>
                        <dt><label for="invoice_due_date">Payment Due:</label><small>Optional</small></dt>
                        <dd><input id="invoice_due_date" type="date" name="due_date" value="<?php echo e(old('due_date')); ?>" aria-label="Payment due date (optional)"></dd>
                    </dl>
                </section>
                <section class="receipt-charge-section">
                    <h2>Charge Breakdown</h2>
                    <table class="receipt-charges">
                        <thead><tr><th>Service / description</th><th class="receipt-amount">Amount (<span x-text="currency"><?php echo e(old('currency', 'BDT')); ?></span>)</th><th class="receipt-remove-column"><span class="receipt-sr-only">Action</span></th></tr></thead>
                        <tbody>
                            <tr>
                                <td><label for="base_freight">Base Freight</label></td>
                                <td class="receipt-amount"><input id="base_freight" type="number" name="base_freight" x-model.number="baseFreight" value="<?php echo e($baseFreight); ?>" min="0" max="9999999999.99" step="0.01" placeholder="0.00" aria-label="Base freight amount" required></td>
                                <td></td>
                            </tr>
                            <tr x-show="otherCharges.length" x-cloak><td colspan="3" class="receipt-services-title">Special Services</td></tr>
                            <template x-for="(charge, index) in otherCharges" :key="charge.id">
                                <tr>
                                    <td><input type="text" :name="`other_charges[${index}][description]`" x-model="charge.description" maxlength="255" placeholder="Service name" aria-label="Other charge description" required></td>
                                    <td class="receipt-amount"><input type="number" :name="`other_charges[${index}][amount]`" x-model.number="charge.amount" min="0" max="9999999999.99" step="0.01" placeholder="0.00" aria-label="Other charge amount" required></td>
                                    <td class="receipt-remove-column"><button type="button" class="receipt-remove" @click="removeCharge(charge.id)" aria-label="Remove charge">&times;</button></td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot>
                            <tr><th>Total (<span x-text="currency"><?php echo e(old('currency', 'BDT')); ?></span>)</th><td class="receipt-amount"><output aria-live="polite" x-text="totalFormatted()"><?php echo e(number_format($baseFreight, 2)); ?></output></td><td></td></tr>
                        </tfoot>
                    </table>
                    <button type="button" class="receipt-add" @click="addCharge()" :disabled="otherCharges.length >= 20">+ Add special service</button>
                </section>
            </div>

            <section class="receipt-reference">
                <h2>Reference Information</h2>
                <dl class="receipt-details"><dt>Reference:</dt><dd><?php echo e($shipment->invoice_number ?? ''); ?></dd></dl>
            </section>

            <section class="receipt-contents">
                <h2>Description of Contents</h2>
                <?php if($isDocument): ?>
                    <p><?php echo e($shipment->document_description ?? ''); ?></p>
                <?php else: ?>
                    <?php $__currentLoopData = $goods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <p><?php echo e($item['name'] ?? $item['description'] ?? ''); ?><?php echo e(isset($item['quantity']) ? ' - ' . $item['quantity'] . ' pcs' : ''); ?></p>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </section>

            <section class="receipt-notes">
                <label for="invoice_notes">Payment instructions / notes <small>(optional)</small></label>
                <textarea id="invoice_notes" name="notes" rows="3" maxlength="2000" placeholder="Add payment instructions or a note"><?php echo e(old('notes')); ?></textarea>
            </section>

            <div class="receipt-paper-footer"><span>Express Peek | Shipment Receipt</span><span><?php echo e($shipment->tracking_number); ?></span></div>
        </article>

        <footer class="receipt-actions">
            <p>Review the receipt details and charges before generating.</p>
            <div><a href="<?php echo e(route('admin.shipments.edit', $shipment)); ?>">Cancel</a><button type="submit">Generate shipment receipt</button></div>
        </footer>
    </form>
</div>

<?php $__env->startPush('styles'); ?>
<style>
    .receipt-create { width:min(920px,100%); margin:0 auto; color:#111; }
    .receipt-create [x-cloak] { display:none!important; }
    .receipt-create .receipt-back { display:inline-block; margin-bottom:16px; color:#526176; font-size:13px; text-decoration:none; }
    .receipt-create .receipt-errors { padding:16px 20px; margin-bottom:18px; border:1px solid #e2b3b3; background:#fff7f7; color:#8b2525; font-size:13px; }
    .receipt-create .receipt-errors ul { margin:8px 0 0; padding-left:20px; }
    .receipt-create .receipt-sheet { padding:32px 38px 22px; border:1px solid #e1e3e7; background:#fff; box-shadow:0 6px 24px #1720330a; font-family:Arial,Helvetica,sans-serif; font-size:12px; line-height:1.45; }
    .receipt-create .receipt-sheet p { margin:0; color:#111; font-size:inherit; line-height:inherit; }
    .receipt-create .receipt-sheet h1 { margin:0; color:#111; font-family:inherit; font-size:30px; font-weight:700; letter-spacing:0; line-height:1.2; text-transform:none; }
    .receipt-create .receipt-sheet h2 { margin:0 0 17px; color:#111; font-family:inherit; font-size:19px; font-weight:700; letter-spacing:0; line-height:1.25; text-transform:none; }
    .receipt-create .receipt-header { display:flex; align-items:center; justify-content:space-between; gap:24px; padding-bottom:16px; border-bottom:1px solid #aaa; }
    .receipt-create .receipt-header img { width:168px; height:auto; max-width:30%; object-fit:contain; }
    .receipt-create .receipt-header > div { text-align:right; }
    .receipt-create .receipt-header .receipt-meta { margin-top:10px; color:#666; font-size:10px; }
    .receipt-create .receipt-columns { display:grid; grid-template-columns:minmax(0,1fr) minmax(0,1fr); column-gap:42px; padding-inline:14px; }
    .receipt-create .receipt-columns > * { min-width:0; }
    .receipt-create .receipt-parties { min-height:235px; padding-top:25px; padding-bottom:26px; border-bottom:1px solid #bbb; }
    .receipt-create .receipt-parties p { overflow-wrap:anywhere; }
    .receipt-create .receipt-parties .receipt-email { margin-top:12px; }
    .receipt-create .receipt-information { margin-top:28px; }
    .receipt-create .receipt-details { display:grid; grid-template-columns:minmax(0,46%) minmax(0,54%); gap:7px 0; margin:0; align-items:start; }
    .receipt-create .receipt-details dt { min-width:0; margin:0; padding-right:12px; color:#111; font-weight:400; }
    .receipt-create .receipt-details dd { min-width:0; min-height:17px; margin:0; color:#111; overflow-wrap:anywhere; }
    .receipt-create .receipt-details small { display:block; color:#777; font-size:10px; }
    .receipt-create .receipt-remarks { white-space:pre-line; }
    .receipt-create .receipt-billing { margin-top:32px; }
    .receipt-create .receipt-billing-fields { align-items:center; }
    .receipt-create .receipt-muted { color:#777!important; }
    .receipt-create .receipt-sheet input, .receipt-create .receipt-sheet select, .receipt-create .receipt-sheet textarea { display:block; width:100%; min-width:0; max-width:100%; margin:0; border:1px solid #d2d6dc; border-radius:3px; background:#fff; color:#111; font:inherit; box-shadow:none; }
    .receipt-create .receipt-sheet input, .receipt-create .receipt-sheet select { height:33px; padding:5px 7px; font-size:12px; }
    .receipt-create .receipt-sheet select { padding-right:24px; }
    .receipt-create .receipt-sheet input:focus, .receipt-create .receipt-sheet select:focus, .receipt-create .receipt-sheet textarea:focus { outline:2px solid #c8d5ef; outline-offset:1px; border-color:#627da8; }
    .receipt-create .receipt-charges { width:100%; border-collapse:collapse; table-layout:fixed; color:#111; font-size:12px; }
    .receipt-create .receipt-charges th { padding:0 0 9px; border-bottom:1px solid #aaa; background:transparent; color:#111; font-size:10px; font-weight:700; letter-spacing:0; text-align:left; text-transform:none; }
    .receipt-create .receipt-charges td { padding:9px 0; border-bottom:1px solid #e4e4e4; vertical-align:middle; }
    .receipt-create .receipt-charges td:first-child { padding-right:10px; }
    .receipt-create .receipt-charges .receipt-amount { width:118px; text-align:right; }
    .receipt-create .receipt-charges .receipt-remove-column { width:24px; text-align:right; }
    .receipt-create .receipt-charges .receipt-services-title { padding-top:13px; font-weight:700; }
    .receipt-create .receipt-charges input[type=number] { text-align:right; appearance:textfield; -moz-appearance:textfield; }
    .receipt-create .receipt-charges input[type=number]::-webkit-inner-spin-button { -webkit-appearance:none; }
    .receipt-create .receipt-charges tfoot th, .receipt-create .receipt-charges tfoot td { padding-top:12px; border-top:1px solid #777; border-bottom:0; color:#111; font-size:12px; font-weight:700; }
    .receipt-create .receipt-remove { padding:3px; border:0; background:transparent; color:#a22; font-size:19px; cursor:pointer; }
    .receipt-create .receipt-add { display:block; margin-top:12px; padding:5px 0; border:0; background:transparent; color:#294d81; font:inherit; font-size:12px; font-weight:600; cursor:pointer; }
    .receipt-create .receipt-add:hover { text-decoration:underline; }
    .receipt-create .receipt-add:disabled { color:#888; cursor:default; }
    .receipt-create .receipt-reference { margin:38px 14px 0; }
    .receipt-create .receipt-reference .receipt-details { grid-template-columns:25% 75%; }
    .receipt-create .receipt-contents { margin:32px 14px 0; }
    .receipt-create .receipt-contents p { margin-bottom:4px; overflow-wrap:anywhere; }
    .receipt-create .receipt-notes { margin:24px 14px 0; }
    .receipt-create .receipt-notes label { display:block; margin-bottom:8px; color:#111; font-size:12px; font-weight:700; text-transform:none; }
    .receipt-create .receipt-notes small { color:#777; font-size:11px; font-weight:400; }
    .receipt-create .receipt-notes textarea { padding:9px; resize:vertical; line-height:1.5; }
    .receipt-create .receipt-paper-footer { display:flex; justify-content:space-between; gap:20px; margin-top:36px; padding-top:9px; border-top:1px solid #e2e2e2; color:#666; font-size:10px; }
    .receipt-create .receipt-actions { display:flex; align-items:center; justify-content:space-between; gap:20px; padding:20px 0; }
    .receipt-create .receipt-actions p { margin:0; color:#69768a; font-size:12px; }
    .receipt-create .receipt-actions > div { display:flex; align-items:center; gap:16px; }
    .receipt-create .receipt-actions a { color:#526176; font-size:13px; text-decoration:none; }
    .receipt-create .receipt-actions button { padding:11px 18px; border:1px solid #123e78; border-radius:5px; background:#123e78; color:#fff; font-size:13px; font-weight:700; cursor:pointer; }
    .receipt-create .receipt-actions button:hover { background:#0d3262; }
    .receipt-create .receipt-sr-only { position:absolute; width:1px; height:1px; padding:0; margin:-1px; overflow:hidden; clip:rect(0,0,0,0); white-space:nowrap; border:0; }
    @media(max-width:700px) {
        .receipt-create .receipt-sheet { padding:22px 18px 18px; }
        .receipt-create .receipt-header { gap:12px; }
        .receipt-create .receipt-header img { width:130px; max-width:35%; }
        .receipt-create .receipt-sheet h1 { font-size:23px; }
        .receipt-create .receipt-sheet h2 { font-size:18px; }
        .receipt-create .receipt-columns { grid-template-columns:minmax(0,1fr); row-gap:26px; padding-inline:0; }
        .receipt-create .receipt-parties { min-height:0; }
        .receipt-create .receipt-details { grid-template-columns:46% 54%; }
        .receipt-create .receipt-reference, .receipt-create .receipt-contents, .receipt-create .receipt-notes { margin-left:0; margin-right:0; }
        .receipt-create .receipt-actions { flex-direction:column; align-items:stretch; }
        .receipt-create .receipt-actions > div { justify-content:flex-end; }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('shipmentInvoiceCharges', () => ({
        currency: <?php echo json_encode(old('currency', 'BDT'), 512) ?>,
        baseFreight: <?php echo json_encode($baseFreight, 15, 512) ?>,
        otherCharges: <?php echo json_encode($otherChargesForJs, 15, 512) ?>,
        addCharge() {
            if (this.otherCharges.length >= 20) return;
            this.otherCharges.push({
                id: `other-charge-${Date.now()}-${Math.random()}`,
                description: '',
                amount: 0
            });
        },
        removeCharge(id) {
            this.otherCharges = this.otherCharges.filter(charge => charge.id !== id);
        },
        total() {
            return (parseFloat(this.baseFreight) || 0)
                + this.otherCharges.reduce((sum, charge) => sum + (parseFloat(charge.amount) || 0), 0);
        },
        totalFormatted() {
            return new Intl.NumberFormat('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(this.total());
        }
    }));
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.operations-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projects\expresspeek_app\expresspeek_app\resources\views/admin/shipments/invoice-create.blade.php ENDPATH**/ ?>