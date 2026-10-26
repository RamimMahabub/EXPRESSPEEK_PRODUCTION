@extends('layouts.operations-dashboard')

@section('title', 'Sourcing Invoice - ' . $sourcingRequest->reference_number)
@section('page-title', 'Create Sourcing Invoice')
@section('page-subtitle', 'Prepare a procurement and delivery receipt for ' . $sourcingRequest->reference_number)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/traditional-invoice-form.css') }}">
@endpush

@section('content')
@php
    $requestItems = $sourcingRequest->items;
    $defaultItems = [
        [
            'description' => 'Product Cost',
            'amount' => (float) ($sourcingRequest->quoted_price ?? 0),
        ],
        [
            'description' => 'International Freight',
            'amount' => 0,
        ],
        [
            'description' => 'Sourcing and Handling Fee',
            'amount' => 0,
        ],
    ];
    $invoiceItems = collect(old('items', $defaultItems))
        ->values()
        ->map(fn ($item, $index) => [
            'id' => 'item-' . $index . '-' . uniqid(),
            'description' => $item['description'] ?? '',
            'amount' => $item['amount'] ?? 0,
        ])
        ->all();
    $selectedCurrency = old('currency', $sourcingRequest->quoted_currency ?: 'BDT');
    $destinationLocality = collect([
        $sourcingRequest->destination_city,
        $sourcingRequest->destination_state,
        $sourcingRequest->destination_postal_code,
    ])->filter()->join(', ');
@endphp

<div class="freight-page">
    <a href="{{ route('admin.sourcing-requests.show', $sourcingRequest) }}" class="freight-back">
        <span aria-hidden="true">&larr;</span> Back to Shop From Bangladesh
    </a>

    @if($errors->any())
        <div class="freight-errors" role="alert">
            <strong>Please review the invoice details.</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        action="{{ route('admin.sourcing-requests.invoice.store', $sourcingRequest) }}"
        method="POST"
        class="freight-document"
        x-data="sourcingInvoiceItems"
    >
        @csrf

        <header class="freight-document-head">
            <div class="freight-company">
                <img src="{{ asset('images/express-peek-logo.svg') }}" alt="Express Peek">
                <div>
                    <strong>EXPRESS PEEK</strong>
                    <span>International sourcing, courier and freight services</span>
                </div>
            </div>
            <div class="freight-document-title">
                <p>Sourcing Invoice</p>
                <span>Procurement &amp; delivery service receipt</span>
            </div>
        </header>

        <div class="freight-reference-grid">
            <div>
                <span>Request reference</span>
                <strong>{{ $sourcingRequest->reference_number }}</strong>
            </div>
            <div>
                <span>Request status</span>
                <strong>{{ $sourcingRequest->status_label }}</strong>
            </div>
            <div>
                <span>Requested products</span>
                <strong>{{ $requestItems->count() }}</strong>
            </div>
            <div>
                <span>Destination</span>
                <strong>{{ $sourcingRequest->destination_country ?: 'Not provided' }}</strong>
            </div>
        </div>

        <div class="freight-parties">
            <section class="freight-party">
                <h2>Service Provider</h2>
                <strong>Express Peek</strong>
                <p>Dhaka, Bangladesh</p>
                <p>International product sourcing and delivery</p>
                <p>support@expresspeek.com</p>
                <p>expresspeek.com</p>
                <p>WhatsApp: +880 1400-659902</p>
            </section>
            <section class="freight-party">
                <h2>Customer / Deliver To</h2>
                <strong>{{ $sourcingRequest->customer_name ?: 'Not provided' }}</strong>
                @if($sourcingRequest->destination_address)
                    <p>{{ $sourcingRequest->destination_address }}</p>
                    @if($sourcingRequest->destination_address2)
                        <p>{{ $sourcingRequest->destination_address2 }}</p>
                    @endif
                    @if($sourcingRequest->destination_address3)
                        <p>{{ $sourcingRequest->destination_address3 }}</p>
                    @endif
                @endif
                @if($destinationLocality)
                    <p>{{ $destinationLocality }}</p>
                @endif
                <p>{{ $sourcingRequest->destination_country ?: 'Destination not provided' }}</p>
                @if($sourcingRequest->whatsapp_number)
                    <p>WhatsApp: {{ $sourcingRequest->whatsapp_country_code }}{{ $sourcingRequest->whatsapp_number }}</p>
                @endif
            </section>
        </div>

        <section class="request-summary">
            <h2>Requested Product Summary</h2>
            @if($requestItems->isNotEmpty())
                <ol>
                    @foreach($requestItems as $requestItem)
                        <li>{{ $requestItem->product_description ?: 'Product details not provided' }}</li>
                    @endforeach
                </ol>
            @else
                <ol><li>No individual product descriptions were recorded for this request.</li></ol>
            @endif
        </section>

        <div class="freight-settings">
            <div class="freight-field">
                <label for="currency">Invoice currency</label>
                <select id="currency" name="currency" x-model="currency" required>
                    @foreach(['BDT', 'USD', 'GBP', 'EUR', 'AUD', 'AED', 'SGD', 'CAD'] as $currency)
                        <option value="{{ $currency }}">{{ $currency }}</option>
                    @endforeach
                </select>
            </div>
            <div class="freight-field">
                <label for="due_date">Payment due date <span>(optional)</span></label>
                <input id="due_date" type="date" name="due_date" value="{{ old('due_date') }}">
            </div>
        </div>

        <section class="freight-charges">
            <div class="freight-section-head">
                <div>
                    <h2>Sourcing and Delivery Charges</h2>
                    <p>Enter the actual procurement, freight and service charges billed to the customer.</p>
                </div>
                <button type="button" class="freight-add" @click="addItem()">+ Add charge</button>
            </div>

            <div class="freight-table-wrap">
                <table class="freight-charge-table">
                    <thead>
                        <tr>
                            <th>Charge description</th>
                            <th class="freight-amount-heading">Amount</th>
                            <th class="freight-action-heading">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in items" :key="item.id">
                            <tr>
                                <td>
                                    <input
                                        type="text"
                                        :name="`items[${index}][description]`"
                                        x-model="item.description"
                                        maxlength="255"
                                        placeholder="e.g. Product cost, international freight or customs handling"
                                        required
                                    >
                                </td>
                                <td class="freight-amount-cell">
                                    <div class="freight-money">
                                        <span x-text="currency"></span>
                                        <input
                                            type="number"
                                            :name="`items[${index}][amount]`"
                                            x-model.number="item.amount"
                                            step="0.01"
                                            min="0"
                                            max="9999999999.99"
                                            inputmode="decimal"
                                            required
                                        >
                                    </div>
                                </td>
                                <td class="freight-action-cell">
                                    <button
                                        type="button"
                                        @click="removeItem(item.id)"
                                        x-show="items.length > 1"
                                    >Remove</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3">
                                <span>Invoice total</span>
                                <strong><span x-text="currency"></span> <span x-text="totalFormatted()"></span></strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>

        <div class="freight-notes">
            <label for="notes">Payment instructions / remarks <span>(visible to customer)</span></label>
            <textarea
                id="notes"
                name="notes"
                maxlength="2000"
                placeholder="Add payment terms or other information for the customer."
            >{{ old('notes', 'Please deposit the total amount to our bank account or via bKash. Contact us on WhatsApp if you have any questions.') }}</textarea>
        </div>

        <footer class="freight-form-footer">
            <p>The generated PDF will use the same request, customer and charge information shown above.</p>
            <div>
                <a href="{{ route('admin.sourcing-requests.show', $sourcingRequest) }}">Cancel</a>
                <button type="submit">Generate sourcing invoice</button>
            </div>
        </footer>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('sourcingInvoiceItems', () => ({
        currency: @json($selectedCurrency),
        items: @json($invoiceItems),
        addItem() {
            this.items.push({
                id: `item-${Date.now()}-${Math.random().toString(16).slice(2)}`,
                description: '',
                amount: 0,
            });
        },
        removeItem(id) {
            if (this.items.length > 1) {
                this.items = this.items.filter(item => item.id !== id);
            }
        },
        total() {
            return this.items.reduce((sum, item) => sum + (Number.parseFloat(item.amount) || 0), 0);
        },
        totalFormatted() {
            return new Intl.NumberFormat('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            }).format(this.total());
        },
    }));
});
</script>
@endpush
