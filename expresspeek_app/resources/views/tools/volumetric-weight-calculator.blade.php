@php
    $calculatorDashboard = auth()->check() && auth()->user()->primaryRoleName() !== null;
    $createShipmentUrl = route('shipment.guest.create');

    if ($calculatorDashboard) {
        $createShipmentUrl = route(auth()->user()->shipmentCreateRouteName());
    }
@endphp

@extends($calculatorDashboard ? 'layouts.operations-dashboard' : 'layouts.customer')

@section('title', 'Volumetric Weight Calculator')
@section('seo_title', 'Volumetric Weight Calculator | Express Peek')
@section('seo_description', 'Calculate shipment volumetric weight instantly using length × width × height in centimetres, divided by 5000.')
@section('page-title', 'Volumetric Weight Calculator')
@section('page-subtitle', 'Convert parcel dimensions into billable shipping weight.')

@push('head')
    @include('tools.partials.volumetric-styles')
@endpush

@push('styles')
    @include('tools.partials.volumetric-styles')
@endpush

@section('content')
<div class="vw-page" x-data="volumetricWeightCalculator()">
    <div class="vw-wrap">
        <div class="vw-topline">
            <span class="vw-breadcrumb">Shipment intelligence tool</span>
            <div class="vw-formula-chip" aria-label="Volumetric weight formula">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 19V5M4 19h16M8 16l3-4 3 2 5-7"/></svg>
                <span><strong>Formula:</strong> Length (cm) × Width (cm) × Height (cm) ÷ 5000</span>
            </div>
        </div>

        <div class="vw-grid">
            <section class="vw-copy" aria-labelledby="calculator-title">
                <span class="vw-kicker">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m13 2-8 12h7l-1 8 8-12h-7l1-8Z"/></svg>
                    Instant dimensional analysis
                </span>
                <h1 id="calculator-title">Space, translated into <span>shipping weight.</span></h1>
                <p>See how much room your parcel occupies in the carrier network. Enter the outer dimensions of one package and get its volumetric and chargeable weight instantly.</p>

                <div class="vw-visual" aria-hidden="true">
                    <svg class="vw-box-svg" viewBox="0 0 460 230">
                        <path class="vw-svg-edge" d="M123 74 259 33l112 58-137 43-111-60Z"/>
                        <path class="vw-svg-edge" d="m123 74 111 60v72l-111-61V74Z"/>
                        <path class="vw-svg-edge vw-svg-top" d="m234 134 137-43v72l-137 43v-72Z"/>
                        <path class="vw-svg-arrow" d="M118 165v37M123 199h105M238 211l135-42M99 73 246 29"/>
                        <path fill="#a894ff" d="m114 168 4-8 4 8h-8Zm0 31 4 8 4-8h-8Zm12-4-8 4 8 4v-8Zm99 0 8 4-8 4v-8Z"/>
                        <text class="vw-svg-label" x="52" y="188">H <tspan x="52" dy="16" x-text="displayDimension(height)"></tspan></text>
                        <text class="vw-svg-label" x="158" y="224">L <tspan dx="5" x-text="displayDimension(length)"></tspan></text>
                        <text class="vw-svg-label" x="351" y="201">W <tspan dx="5" x-text="displayDimension(width)"></tspan></text>
                    </svg>
                </div>

                <div class="vw-trust" aria-label="Calculator benefits">
                    <div class="vw-trust-item"><span class="vw-trust-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19V9M10 19V5M16 19v-7M22 19V2M2 19h21"/></svg></span><span>Live calculation<br>as you type</span></div>
                    <div class="vw-trust-item"><span class="vw-trust-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3 20 6v6c0 5-3.4 8-8 9-4.6-1-8-4-8-9V6l8-3Z"/><path d="m8 12 2.5 2.5L16 9"/></svg></span><span>Industry-standard<br>5000 divisor</span></div>
                    <div class="vw-trust-item"><span class="vw-trust-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 4h14v16H5zM8 8h8M8 12h3M14 12h2M8 16h2M13 16h3"/></svg></span><span>Chargeable weight<br>comparison</span></div>
                </div>
            </section>

            <section class="vw-card" aria-label="Volumetric weight inputs and result">
                <div class="vw-card-head">
                    <div>
                        <h2 class="vw-card-title">Package dimensions</h2>
                        <p class="vw-card-subtitle">Measure the longest points of the parcel in centimetres.</p>
                    </div>
                    <span class="vw-live"><i></i> Live result</span>
                </div>

                <div class="vw-dimensions">
                    <div class="vw-field">
                        <label for="vw-length">Length <span>L</span></label>
                        <div class="vw-input-wrap"><input id="vw-length" class="vw-input" type="number" min="0" step="0.1" inputmode="decimal" placeholder="0" x-model.number="length"><span class="vw-unit">cm</span></div>
                    </div>
                    <div class="vw-field">
                        <label for="vw-width">Width <span>W</span></label>
                        <div class="vw-input-wrap"><input id="vw-width" class="vw-input" type="number" min="0" step="0.1" inputmode="decimal" placeholder="0" x-model.number="width"><span class="vw-unit">cm</span></div>
                    </div>
                    <div class="vw-field">
                        <label for="vw-height">Height <span>H</span></label>
                        <div class="vw-input-wrap"><input id="vw-height" class="vw-input" type="number" min="0" step="0.1" inputmode="decimal" placeholder="0" x-model.number="height"><span class="vw-unit">cm</span></div>
                    </div>
                </div>

                <div class="vw-secondary-fields">
                    <div class="vw-field">
                        <label for="vw-quantity">Identical packages <span>Qty</span></label>
                        <div class="vw-input-wrap"><input id="vw-quantity" class="vw-input" type="number" min="1" step="1" inputmode="numeric" x-model.number="quantity"><span class="vw-unit">pcs</span></div>
                    </div>
                    <div class="vw-field">
                        <label for="vw-actual">Total actual weight <span>Optional</span></label>
                        <div class="vw-input-wrap"><input id="vw-actual" class="vw-input" type="number" min="0" step="0.1" inputmode="decimal" placeholder="0" x-model.number="actualWeight"><span class="vw-unit">kg</span></div>
                    </div>
                </div>

                <div class="vw-equation" aria-live="polite">
                    <span x-text="displayValue(length)"></span> × <span x-text="displayValue(width)"></span> × <span x-text="displayValue(height)"></span> ÷ <strong>5000</strong> × <span x-text="safeQuantity"></span>
                </div>

                <div class="vw-result" aria-live="polite">
                    <div class="vw-result-top">
                        <div>
                            <div class="vw-result-label" x-text="actualWeight > 0 ? 'Chargeable weight' : 'Volumetric weight'"></div>
                            <div class="vw-result-number"><span x-text="formatWeight(chargeableWeight)"></span><small>kg</small></div>
                        </div>
                        <div class="vw-result-meta">
                            Volumetric total
                            <strong><span x-text="formatWeight(totalVolumetricWeight)"></span> kg</strong>
                            <span x-show="safeQuantity > 1"><span x-text="formatWeight(perPackageWeight)"></span> kg per package</span>
                        </div>
                    </div>
                </div>

                <div class="vw-actions">
                    <a class="vw-primary" href="{{ $createShipmentUrl }}">
                        Continue to shipment
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                    </a>
                    <button class="vw-reset" type="button" @click="reset()">Reset</button>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function volumetricWeightCalculator() {
        return {
            length: '',
            width: '',
            height: '',
            quantity: 1,
            actualWeight: '',
            get safeQuantity() {
                return Math.max(1, Math.floor(Number(this.quantity) || 1));
            },
            get perPackageWeight() {
                const dimensions = [this.length, this.width, this.height].map(Number);
                return dimensions.every(value => Number.isFinite(value) && value > 0)
                    ? dimensions.reduce((total, value) => total * value, 1) / 5000
                    : 0;
            },
            get totalVolumetricWeight() {
                return this.perPackageWeight * this.safeQuantity;
            },
            get chargeableWeight() {
                return Math.max(this.totalVolumetricWeight, Number(this.actualWeight) || 0);
            },
            formatWeight(value) {
                return Number(value || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            },
            displayValue(value) {
                return Number(value) > 0 ? Number(value).toLocaleString('en-US') : '—';
            },
            displayDimension(value) {
                return Number(value) > 0 ? `${Number(value).toLocaleString('en-US')} cm` : '— cm';
            },
            reset() {
                this.length = '';
                this.width = '';
                this.height = '';
                this.quantity = 1;
                this.actualWeight = '';
            }
        };
    }
</script>
@endpush
