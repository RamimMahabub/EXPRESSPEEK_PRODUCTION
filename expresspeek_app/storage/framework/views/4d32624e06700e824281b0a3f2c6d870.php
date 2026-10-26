<table class="charge-table">
    <thead><tr><th>Service / description</th><th class="amount">Amount (<?php echo e($currency); ?>)</th></tr></thead>
    <tbody>
        <?php $__currentLoopData = $baseCharges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $charge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr><td><?php echo e($charge['description'] ?? 'Base Freight'); ?></td><td class="amount"><?php echo e(number_format((float) ($charge['amount'] ?? 0) * (float) ($charge['quantity'] ?? 1), 2)); ?></td></tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php if($serviceCharges->isNotEmpty()): ?>
            <tr><td colspan="2"><strong>Special Services</strong></td></tr>
            <?php $__currentLoopData = $serviceCharges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $charge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr><td><?php echo e($charge['description'] ?? ''); ?></td><td class="amount"><?php echo e(number_format((float) ($charge['amount'] ?? 0) * (float) ($charge['quantity'] ?? 1), 2)); ?></td></tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
        <?php if($charges->isEmpty()): ?>
            <tr><td>Freight Charge</td><td class="amount"><?php echo e(number_format((float) $shipmentInvoice->total_amount, 2)); ?></td></tr>
        <?php endif; ?>
        <tr class="total"><td>Total (<?php echo e($currency); ?>)</td><td class="amount"><?php echo e(number_format((float) $shipmentInvoice->total_amount, 2)); ?></td></tr>
    </tbody>
</table>
<?php /**PATH D:\projects\expresspeek_app\expresspeek_app\resources\views/shipments/partials/receipt-charges.blade.php ENDPATH**/ ?>