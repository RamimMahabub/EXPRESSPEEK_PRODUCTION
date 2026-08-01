@extends('layouts.operations-dashboard')

@section('title', 'Freight Invoice - ' . $shipment->tracking_number)
@section('page-title', 'Create Freight Invoice')
@section('page-subtitle', 'Prepare a customer freight receipt for shipment ' . $shipment->tracking_number)

@section('content')
@php
    $baseFreight = (float) old('base_freight', 0);
    $otherCharges = old('other_charges', []);
    $otherChargesForJs = collect($otherCharges)->values()->map(fn ($charge, $index) => [
        'id' => 'other-charge-' . $index,
        'description' => $charge['description'] ?? '',
        'amount' => (float) ($charge['amount'] ?? 0),
    ])->all();

    $receiverLocality = collect([
        $shipment->receiver_city,
        $shipment->receiver_state,
        $shipment->receiver_postal_code,
    ])->filter()->join(', ');
    $senderLocality = collect([
        $shipment->sender_city,
        $shipment->sender_state,
        $shipment->sender_postal_code,
    ])->filter()->join(', ');
@endphp

<div class="freight-page">
    <a href="{{ route('admin.shipments.edit', $shipment) }}" class="freight-back">
        <span aria-hidden="true">‹</span> Back to shipment
    </a>

    @if($errors->any())
        <div class="freight-errors" role="alert">
            <strong>Correct the following before generating the invoice:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        action="{{ route('admin.shipments.invoice.store', $shipment) }}"
        method="POST"
        class="freight-document"
        x-data="shipmentInvoiceCharges"
    >
        @csrf

        <header class="freight-document-head">
            <div class="freight-company">
                <img src="/images/express-peek-logo.svg" alt="Express Peek">
                <div>
                    <strong>EXPRESS PEEK</strong>
                    <span>International courier and freight services</span>
                </div>
            </div>
            <div class="freight-document-title">
                <p>Freight Invoice</p>
                <span>Shipment receipt / customer copy</span>
            </div>
        </header>

        <section class="freight-reference-grid" aria-label="Invoice references">
            <div><span>Tracking number</span><strong>{{ $shipment->tracking_number }}</strong></div>
            <div><span>Carrier</span><strong>{{ $shipment->carrier_name ?: 'Not assigned' }}</strong></div>
            <div><span>Pieces</span><strong>{{ $shipment->total_packages ?: 1 }}</strong></div>
            <div><span>Chargeable weight</span><strong>{{ number_format((float) ($shipment->total_weight ?: $shipment->weight), 2) }} kg</strong></div>
        </section>

        <section class="freight-parties">
            <div class="freight-party">
                <h2>Shipper / From</h2>
                <strong>{{ $shipment->sender_name ?: 'Not provided' }}</strong>
                @if($shipment->sender_company)<p>{{ $shipment->sender_company }}</p>@endif
                <p>{{ $shipment->sender_address }}</p>
                @if($shipment->sender_address2)<p>{{ $shipment->sender_address2 }}</p>@endif
                <p>{{ $senderLocality }}</p>
                <p>{{ $shipment->sender_country }}</p>
                @if($shipment->sender_phone)<p>Tel: {{ $shipment->sender_phone_code }}{{ $shipment->sender_phone }}</p>@endif
            </div>
            <div class="freight-party">
                <h2>Consignee / Bill To</h2>
                <strong>{{ $shipment->receiver_name ?: 'Not provided' }}</strong>
                @if($shipment->receiver_company)<p>{{ $shipment->receiver_company }}</p>@endif
                <p>{{ $shipment->receiver_address }}</p>
                @if($shipment->receiver_address2)<p>{{ $shipment->receiver_address2 }}</p>@endif
                <p>{{ $receiverLocality }}</p>
                <p>{{ $shipment->receiver_country }}</p>
                @if($shipment->receiver_phone)<p>Tel: {{ $shipment->receiver_phone_code }}{{ $shipment->receiver_phone }}</p>@endif
            </div>
        </section>

        <section class="freight-settings">
            <div class="freight-field">
                <label for="invoice_currency">Currency</label>
                <select id="invoice_currency" name="currency" x-model="currency">
                    @foreach(['BDT','USD','GBP','EUR','AUD','AED','SGD','CAD'] as $currency)
                        <option value="{{ $currency }}" @selected(old('currency', 'BDT') === $currency)>{{ $currency }}</option>
                    @endforeach
                </select>
            </div>
            <div class="freight-field">
                <label for="invoice_due_date">Payment due date <span>(optional)</span></label>
                <input id="invoice_due_date" type="date" name="due_date" value="{{ old('due_date') }}">
            </div>
        </section>

        <section class="freight-charges">
            <div class="freight-section-head">
                <div>
                    <h2>Freight and service charges</h2>
                    <p>Enter the agreed transportation price and any additional service fees.</p>
                </div>
                <button type="button" class="freight-add" @click="addCharge()">+ Add charge</button>
            </div>

            <div class="freight-table-wrap">
                <table class="freight-charge-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th class="freight-amount-heading">Amount</th>
                            <th class="freight-action-heading">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <strong>Base Freight</strong>
                                <span>Main transport charge</span>
                            </td>
                            <td class="freight-amount-cell">
                                <div class="freight-money">
                                    <span x-text="currency"></span>
                                    <input
                                        id="base_freight"
                                        type="number"
                                        name="base_freight"
                                        x-model.number="baseFreight"
                                        step="0.01"
                                        min="0"
                                        max="9999999999.99"
                                        placeholder="0.00"
                                        aria-label="Base freight amount"
                                        required
                                    >
                                </div>
                            </td>
                            <td></td>
                        </tr>
                        <template x-for="(charge, index) in otherCharges" :key="charge.id">
                            <tr>
                                <td>
                                    <input
                                        type="text"
                                        :name="`other_charges[${index}][description]`"
                                        x-model="charge.description"
                                        placeholder="Fuel surcharge, handling, insurance..."
                                        aria-label="Other charge description"
                                        required
                                    >
                                </td>
                                <td class="freight-amount-cell">
                                    <div class="freight-money">
                                        <span x-text="currency"></span>
                                        <input
                                            type="number"
                                            :name="`other_charges[${index}][amount]`"
                                            x-model.number="charge.amount"
                                            step="0.01"
                                            min="0"
                                            max="9999999999.99"
                                            placeholder="0.00"
                                            aria-label="Other charge amount"
                                            required
                                        >
                                    </div>
                                </td>
                                <td class="freight-action-cell">
                                    <button type="button" @click="removeCharge(charge.id)" aria-label="Remove charge">Remove</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2">
                                <span>Total payable</span>
                                <strong><span x-text="currency"></span> <span x-text="totalFormatted()"></span></strong>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>

        <section class="freight-notes">
            <label for="invoice_notes">Payment instructions / customer notes</label>
            <textarea id="invoice_notes" name="notes" rows="4" placeholder="Payment instructions or other notes">{{ old('notes', 'Please deposit the total amount to our bank account or via bKash. Contact us on WhatsApp if you have any questions.') }}</textarea>
        </section>

        <footer class="freight-form-footer">
            <p>The generated PDF will be saved with this shipment and made available to the customer.</p>
            <div>
                <a href="{{ route('admin.shipments.edit', $shipment) }}">Cancel</a>
                <button type="submit">Generate freight invoice</button>
            </div>
        </footer>
    </form>
</div>

@push('styles')
<style>
    .freight-page {
        width: min(1160px, 100%);
        margin: 0 auto;
        color: #172033;
    }
    .freight-back {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        margin: 0 0 18px;
        color: #52627a;
        font-size: 14px;
        font-weight: 650;
        text-decoration: none;
    }
    .freight-back span { font-size: 24px; line-height: 14px; }
    .freight-back:hover { color: #153b75; }
    .freight-errors {
        margin-bottom: 16px;
        padding: 14px 16px;
        border: 1px solid #d9a2a2;
        border-left: 4px solid #a52a2a;
        background: #fff7f7;
        color: #7a2020;
        font-size: 13px;
    }
    .freight-errors ul { margin: 7px 0 0 18px; }
    .freight-document {
        overflow: hidden;
        border: 1px solid #cbd3df;
        border-top: 5px solid #123e78;
        border-radius: 3px;
        background: #fff;
        box-shadow: 0 8px 22px rgba(31, 45, 68, .08);
    }
    .freight-document-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 32px;
        padding: 24px 28px 20px;
        border-bottom: 1px solid #cbd3df;
    }
    .freight-company { display: flex; align-items: center; gap: 14px; }
    .freight-company img { display: block; width: 150px; height: auto; }
    .freight-company div { padding-left: 14px; border-left: 1px solid #cbd3df; }
    .freight-company strong { display: block; color: #123e78; font-size: 15px; letter-spacing: .06em; }
    .freight-company span { display: block; margin-top: 3px; color: #68758a; font-size: 11px; }
    .freight-document-title { text-align: right; }
    .freight-document-title p {
        margin: 0;
        color: #172033;
        font-family: Arial, sans-serif;
        font-size: 26px;
        font-weight: 800;
        letter-spacing: .035em;
        text-transform: uppercase;
    }
    .freight-document-title span { display: block; margin-top: 4px; color: #68758a; font-size: 11px; text-transform: uppercase; letter-spacing: .08em; }
    .freight-reference-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        border-bottom: 1px solid #cbd3df;
        background: #f4f6f9;
    }
    .freight-reference-grid div { min-width: 0; padding: 12px 18px; border-right: 1px solid #cbd3df; }
    .freight-reference-grid div:last-child { border-right: 0; }
    .freight-reference-grid span, .freight-reference-grid strong { display: block; }
    .freight-reference-grid span { color: #69768a; font-size: 10px; font-weight: 750; letter-spacing: .06em; text-transform: uppercase; }
    .freight-reference-grid strong { overflow: hidden; margin-top: 4px; color: #172033; font-size: 13px; text-overflow: ellipsis; white-space: nowrap; }
    .freight-parties { display: grid; grid-template-columns: 1fr 1fr; border-bottom: 1px solid #cbd3df; }
    .freight-party { min-height: 164px; padding: 20px 28px; }
    .freight-party + .freight-party { border-left: 1px solid #cbd3df; }
    .freight-party h2, .freight-section-head h2 {
        margin: 0 0 10px;
        color: #123e78;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .075em;
        text-transform: uppercase;
    }
    .freight-party strong { display: block; margin-bottom: 5px; color: #172033; font-size: 14px; }
    .freight-party p { min-height: 1px; margin: 2px 0; color: #556277; font-size: 12px; line-height: 1.4; }
    .freight-settings {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
        padding: 18px 28px;
        border-bottom: 1px solid #cbd3df;
        background: #fafbfc;
    }
    .freight-field label, .freight-notes label {
        display: block;
        margin-bottom: 6px;
        color: #38465a;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .035em;
        text-transform: uppercase;
    }
    .freight-field label span { color: #7c8798; font-weight: 500; text-transform: none; }
    .freight-field select, .freight-field input, .freight-notes textarea,
    .freight-charge-table input {
        width: 100%;
        border: 1px solid #aeb9c8;
        border-radius: 2px;
        background: #fff;
        color: #172033;
        font: inherit;
        outline: none;
        box-shadow: none;
    }
    .freight-field select, .freight-field input { height: 40px; padding: 0 11px; font-size: 13px; }
    .freight-field select:focus, .freight-field input:focus, .freight-notes textarea:focus,
    .freight-charge-table input:focus { border-color: #245b9f; box-shadow: 0 0 0 2px rgba(36, 91, 159, .12); }
    .freight-charges { padding: 22px 28px 26px; border-bottom: 1px solid #cbd3df; }
    .freight-section-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 20px; margin-bottom: 12px; }
    .freight-section-head h2 { margin-bottom: 3px; }
    .freight-section-head p { margin: 0; color: #69768a; font-size: 12px; }
    .freight-add {
        flex: 0 0 auto;
        padding: 8px 12px;
        border: 1px solid #7e8b9e;
        border-radius: 2px;
        background: #fff;
        color: #24364e;
        font-size: 12px;
        font-weight: 750;
        cursor: pointer;
    }
    .freight-add:hover { border-color: #123e78; color: #123e78; background: #f4f7fb; }
    .freight-table-wrap { overflow-x: auto; border: 1px solid #aeb9c8; }
    .freight-charge-table { width: 100%; min-width: 650px; border-collapse: collapse; table-layout: fixed; }
    .freight-charge-table th {
        padding: 9px 12px;
        border-bottom: 1px solid #7f8b9b;
        background: #e9edf2;
        color: #354257;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .055em;
        text-align: left;
        text-transform: uppercase;
    }
    .freight-charge-table td { padding: 10px 12px; border-bottom: 1px solid #d6dce5; vertical-align: middle; }
    .freight-charge-table tbody tr:last-child td { border-bottom: 0; }
    .freight-charge-table td > strong { display: block; color: #172033; font-size: 13px; }
    .freight-charge-table td > span { display: block; margin-top: 2px; color: #748095; font-size: 11px; }
    .freight-amount-heading, .freight-amount-cell { width: 260px; }
    .freight-action-heading, .freight-action-cell { width: 90px; text-align: center !important; }
    .freight-charge-table input { height: 38px; padding: 0 10px; font-size: 13px; }
    .freight-money { display: flex; align-items: stretch; }
    .freight-money > span {
        display: flex;
        min-width: 48px;
        align-items: center;
        justify-content: center;
        border: 1px solid #aeb9c8;
        border-right: 0;
        background: #f2f4f7;
        color: #4d5a6e;
        font-size: 10px;
        font-weight: 800;
    }
    .freight-money input { border-radius: 0 2px 2px 0; text-align: right; }
    .freight-action-cell button {
        border: 0;
        background: transparent;
        color: #a12626;
        font-size: 11px;
        font-weight: 700;
        text-decoration: underline;
        cursor: pointer;
    }
    .freight-charge-table tfoot td { padding: 13px 12px; border-top: 2px solid #526174; background: #f4f6f9; text-align: right; }
    .freight-charge-table tfoot span { display: inline; color: #536176; font-size: 11px; font-weight: 800; letter-spacing: .04em; text-transform: uppercase; }
    .freight-charge-table tfoot strong { display: inline-block; min-width: 190px; margin-left: 26px; color: #172033; font-size: 19px; }
    .freight-notes { padding: 20px 28px; border-bottom: 1px solid #cbd3df; }
    .freight-notes textarea { min-height: 88px; padding: 10px 11px; resize: vertical; font-size: 12px; line-height: 1.5; }
    .freight-form-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 18px 28px;
        background: #f4f6f9;
    }
    .freight-form-footer p { margin: 0; color: #68758a; font-size: 11px; }
    .freight-form-footer div { display: flex; align-items: center; gap: 10px; }
    .freight-form-footer a { padding: 10px 14px; color: #526176; font-size: 12px; font-weight: 700; text-decoration: none; }
    .freight-form-footer button {
        padding: 10px 18px;
        border: 1px solid #0d3262;
        border-radius: 2px;
        background: #123e78;
        color: #fff;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .025em;
        cursor: pointer;
        text-transform: uppercase;
    }
    .freight-form-footer button:hover { background: #0d3262; }
    @media (max-width: 760px) {
        .freight-document-head, .freight-section-head, .freight-form-footer { align-items: flex-start; flex-direction: column; }
        .freight-document-title { text-align: left; }
        .freight-reference-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .freight-reference-grid div:nth-child(2) { border-right: 0; }
        .freight-reference-grid div:nth-child(-n+2) { border-bottom: 1px solid #cbd3df; }
        .freight-parties, .freight-settings { grid-template-columns: 1fr; }
        .freight-party + .freight-party { border-top: 1px solid #cbd3df; border-left: 0; }
        .freight-company div { display: none; }
        .freight-company img { width: 135px; }
        .freight-form-footer div { width: 100%; justify-content: flex-end; }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('shipmentInvoiceCharges', () => ({
        currency: @json(old('currency', 'BDT')),
        baseFreight: @json($baseFreight),
        otherCharges: @json($otherChargesForJs),
        addCharge() {
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
@endpush
@endsection
