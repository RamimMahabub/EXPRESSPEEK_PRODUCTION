<table class="charge-table">
    <thead><tr><th>Service / description</th><th class="amount">Amount ({{ $currency }})</th></tr></thead>
    <tbody>
        @foreach($baseCharges as $charge)
            <tr><td>{{ $charge['description'] ?? 'Base Freight' }}</td><td class="amount">{{ number_format((float) ($charge['amount'] ?? 0) * (float) ($charge['quantity'] ?? 1), 2) }}</td></tr>
        @endforeach
        @if($serviceCharges->isNotEmpty())
            <tr><td colspan="2"><strong>Special Services</strong></td></tr>
            @foreach($serviceCharges as $charge)
                <tr><td>{{ $charge['description'] ?? '' }}</td><td class="amount">{{ number_format((float) ($charge['amount'] ?? 0) * (float) ($charge['quantity'] ?? 1), 2) }}</td></tr>
            @endforeach
        @endif
        @if($charges->isEmpty())
            <tr><td>Freight Charge</td><td class="amount">{{ number_format((float) $shipmentInvoice->total_amount, 2) }}</td></tr>
        @endif
        <tr class="total"><td>Total ({{ $currency }})</td><td class="amount">{{ number_format((float) $shipmentInvoice->total_amount, 2) }}</td></tr>
    </tbody>
</table>
