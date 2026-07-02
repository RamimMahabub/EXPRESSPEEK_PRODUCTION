@php
    if (!isset($countries)) {
        $countries = \App\Models\CountryZone::query()
            ->select('country_code', \Illuminate\Support\Facades\DB::raw('MAX(country_name) as original_name'))
            ->whereNotNull('country_code')
            ->groupBy('country_code')
            ->get()
            ->map(function ($c) {
                return (object) [
                    'country_code' => $c->country_code,
                    'country_name' => \App\Helpers\CountryHelper::getCanonicalName($c->country_code)
                ];
            })
            ->filter(fn ($c) => !empty($c->country_name))
            ->sortBy('country_name')
            ->values();
    }
@endphp
{{-- ===== QUOTE MODAL (Alpine.js) ===== --}}
<div
    x-data="quoteEngine()"
    @open-quote-modal.window="open = true"
    @quote-clear="results = { options: [], cheapest: null }; error = null"
    x-show="open"
    class="fixed inset-0 z-[100] overflow-y-auto"
    style="display: none;"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
>
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="open = false"></div>

    {{-- Modal Content --}}
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div
            class="relative bg-white w-full max-w-2xl rounded-3xl shadow-2xl overflow-hidden border border-white/10"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="scale-95 translate-y-8"
            x-transition:enter-end="scale-100 translate-y-0"
        >
            {{-- Header --}}
            <div class="bg-gradient-to-r from-violet-600 to-blue-700 px-8 py-6 text-white flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="bg-white/95 rounded-xl px-3 py-2 shadow-lg shadow-blue-900/30">
                        <img src="/images/express-peek-logo.webp" alt="Express Peek" class="h-10 md:h-11 w-auto object-contain">
                    </div>
                    <div>
                    <h2 class="text-2xl font-black tracking-tight">Shipping Quote Engine</h2>
                    <p class="text-violet-100 text-sm opacity-90 mt-1">Get fast, transparent shipping rates tailored for your delivery.</p>
                    </div>
                </div>
                <button @click="open = false" class="rounded-full p-2 hover:bg-white/10 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-8">
                {{-- Product rows --}}
                <div class="space-y-6 mb-8">
                    <template x-for="(product, index) in form.products" :key="index">
                        <div class="rounded-2xl border border-white/10 p-4">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-xs font-black text-slate-400 uppercase tracking-widest" x-text="'Product ' + (index + 1)"></p>
                                <button
                                    type="button"
                                    @click="removeProduct(index)"
                                    x-show="form.products.length > 1"
                                    class="text-[10px] text-red-500 font-bold uppercase tracking-wide hover:text-red-600"
                                >
                                    Remove
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Destination Country</label>
                                    <div x-data="{
                                        open:false, q:'', hi:-1,
                                        get opts(){ const all=window.SITE_COUNTRIES||[]; if(!this.q) return all.slice(0,60); const s=this.q.toLowerCase(); const byCode=[],byStart=[],byContains=[]; for(const c of all){ const code=c.code.toLowerCase(),name=c.name.toLowerCase(); if(code===s){byCode.push(c);continue;} if(name.startsWith(s)){byStart.push(c);continue;} if(name.includes(s)||code.startsWith(s)){byContains.push(c);} } return [...byCode,...byStart,...byContains].slice(0,15); },
                                        get label(){ return (window.SITE_COUNTRIES||[]).find(c=>c.code===product.country)?.name||''; },
                                        pick(c){ product.country=c.code; this.q=c.name; this.open=false; this.hi=-1; },
                                        clear(){ product.country=''; this.q=''; },
                                        onFocus(){ this.open=true; if(product.country){this.q='';} },
                                        onKey(e){ if(!this.open)return; if(e.key==='ArrowDown'){e.preventDefault();this.hi=Math.min(this.hi+1,this.opts.length-1);} else if(e.key==='ArrowUp'){e.preventDefault();this.hi=Math.max(this.hi-1,0);} else if(e.key==='Enter'){e.preventDefault();if(this.hi>=0)this.pick(this.opts[this.hi]);} else if(e.key==='Escape'){this.open=false;} }
                                    }" class="relative" @click.away="open=false; q=label">
                                        <div class="relative">
                                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                            <input type="text" x-model="q" :placeholder="label || 'Search country...'" @focus="onFocus(); $dispatch('quote-clear')" @keydown="onKey($event)" autocomplete="off" class="w-full bg-gray-50 border border-white/10 rounded-xl pl-9 pr-8 py-3 text-sm focus:ring-2 focus:ring-violet-500 transition-all outline-none text-slate-900">
                                            <button x-show="product.country" type="button" @click="clear()" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-500 transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg></button>
                                        </div>
                                        <div x-show="open" x-transition class="absolute top-full left-0 right-0 mt-1 bg-white border border-white/10 rounded-xl shadow-xl z-30 max-h-56 overflow-y-auto">
                                            <template x-for="(c,i) in opts" :key="c.code">
                                                <div @click="pick(c)" :class="hi===i?'bg-violet-50 text-violet-700':'text-gray-700 hover:bg-gray-50'" class="flex items-center justify-between px-4 py-3 text-sm cursor-pointer transition-colors min-h-[44px]"><span x-text="c.name"></span><span x-text="c.code" class="text-xs font-mono text-slate-400 ml-2 shrink-0"></span></div>
                                            </template>
                                            <div x-show="opts.length===0" class="px-4 py-3 text-sm text-slate-400 italic">No countries found</div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Shipment Type</label>
                                    <select x-model="product.type" class="w-full bg-gray-50 border border-white/10 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-violet-500 transition-all outline-none text-slate-900">
                                        <option value="">Select Type</option>
                                        <option value="document">Document</option>
                                        <option value="non_document">Non-document</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Weight (KG)</label>
                                    <div class="relative">
                                        <input
                                            type="number"
                                            step="0.5"
                                            min="0"
                                            x-model="product.weight"
                                            placeholder="e.g. 5.5"
                                            class="w-full bg-gray-50 border border-white/10 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-violet-500 transition-all outline-none pl-4 pr-12 text-slate-900"
                                        >
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs uppercase">KG</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div class="flex justify-start">
                        <button
                            type="button"
                            @click="addProduct()"
                            class="px-4 py-2 bg-violet-50 text-violet-700 font-black text-xs uppercase tracking-wide rounded-xl hover:bg-violet-100 transition-colors"
                        >
                            + Add Product
                        </button>
                    </div>
                </div>

                <div class="flex justify-center">
                    <button
                        @click="getQuotes()"
                        :disabled="loading || !canSubmit()"
                        class="px-12 py-4 bg-gradient-to-r from-violet-600 to-blue-700 text-white font-black rounded-2xl shadow-xl shadow-violet-500/30 hover:opacity-90 disabled:opacity-50 transition-all flex items-center gap-3"
                    >
                        <template x-if="loading">
                            <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <span x-text="loading ? 'Calculating...' : 'Get Shipping Rates'"></span>
                    </button>
                </div>

                {{-- Results --}}
                <div x-show="results?.options?.length > 0" x-cloak class="mt-10 animate-fade-in">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="h-px bg-gray-100 flex-1"></div>
                        <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Available Options</span>
                        <div class="h-px bg-gray-100 flex-1"></div>
                    </div>

                    {{-- DHL Bangladesh — Recommended (Fastest & Value for Money) --}}
                    <template x-if="results && results.recommended">
                        <div class="bg-violet-50 border-2 border-violet-200 rounded-2xl p-6 mb-3 relative overflow-hidden group">
                            <div class="absolute top-0 right-0 py-1.5 px-6 bg-violet-600 text-white text-[10px] font-black uppercase tracking-tighter rounded-bl-xl shadow-lg ring-1 ring-white/20">
                                Recommended Match
                            </div>
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <p class="text-violet-900 font-black text-xl" x-text="results.recommended.carrier"></p>
                                        <span class="bg-amber-500 text-white text-[10px] px-2 py-0.5 rounded-full font-bold uppercase">Fastest &amp; Value for Money</span>
                                    </div>
                                    <p class="text-violet-600 text-sm font-medium">Standard International Express Delivery</p>
                                </div>
                                <div class="text-left md:text-right">
                                    <p class="text-3xl font-black text-slate-900 leading-none" x-text="formatPrice(results.recommended.total_price)"></p>
                                    <p class="text-xs font-bold text-slate-400 mt-1 uppercase" x-text="'Currency: ' + results.recommended.currency"></p>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Full Options Table --}}
                    <div class="bg-white rounded-2xl border border-white/10 shadow-sm overflow-hidden">
                        <template x-if="results">
                            <template x-for="option in results.options" :key="option.carrier">
                                <button type="button" @click="openShipment(option)" class="w-full p-5 border-b border-gray-50 flex items-center justify-between gap-3 hover:bg-gray-50 transition-colors last:border-0 group text-left cursor-pointer">
                                    <div class="flex items-center gap-3 min-w-0 flex-1">
                                        <div class="w-10 h-10 flex-shrink-0 rounded-xl bg-gray-100 flex items-center justify-center text-slate-400 group-hover:bg-violet-100 group-hover:text-violet-600 transition-colors">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-1.5">
                                                <p class="text-sm font-black text-slate-900" x-text="option.carrier"></p>
                                                <span x-show="option.carrier === results.recommended?.carrier" class="bg-amber-100 text-amber-700 text-[9px] px-1.5 py-0.5 rounded font-bold uppercase whitespace-nowrap">Recommended</span>

                                            </div>
                                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight" x-text="'Rate (All-inclusive): ' + formatPrice(option.base_price)"></p>
                                        </div>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <p class="text-lg font-black text-slate-900 whitespace-nowrap" x-text="formatPrice(option.total_price)"></p>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase" x-text="option.currency"></p>
                                    </div>
                                </button>
                            </template>
                        </template>
                    </div>

                    <p class="text-[10px] text-slate-400 mt-6 text-center leading-relaxed">
                        Quotes provided are estimates. Fuel surcharge and profit margin are already included in the listed rate. <br>
                        Actual price may vary based on final shipment dimensions and insurance selections.
                    </p>
                </div>

                {{-- Error State --}}
                <div x-show="error" x-cloak class="mt-8 bg-red-50 border border-red-100 text-red-600 px-6 py-4 rounded-2xl text-sm flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span x-text="error"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    if (!window.SITE_COUNTRIES) {
        window.SITE_COUNTRIES = @json($countries->map(fn($c) => ['name' => $c->country_name, 'code' => $c->country_code])->values());
    }
    if (!window.PUBLIC_SHIPMENT_CREATE_URL) {
        window.PUBLIC_SHIPMENT_CREATE_URL = @json(route('shipment.create'));
    }
    if (!window.LOGIN_URL) {
        window.LOGIN_URL = @json(route('login'));
    }
    if (typeof window.IS_LOGGED_IN === 'undefined') {
        window.IS_LOGGED_IN = @json(auth()->check());
    }

    if (typeof window.quoteEngine === 'undefined') {
        window.quoteEngine = function() {
            return {
                open: false,
                loading: false,
                error: null,
                results: { options: [], cheapest: null },
                form: {
                    products: [
                        { country: '', type: '', weight: '' }
                    ]
                },
                addProduct() {
                    this.form.products.push({ country: '', type: '', weight: '' });
                },
                removeProduct(index) {
                    this.form.products.splice(index, 1);
                    if (!this.form.products.length) {
                        this.form.products.push({ country: '', type: '', weight: '' });
                    }
                },
                buildProductsPayload() {
                    return (this.form.products || [])
                        .filter(p => p.country && p.type && p.weight)
                        .map(p => ({
                            country: String(p.country).toUpperCase(),
                            type: p.type,
                            weight: parseFloat(p.weight),
                        }))
                        .filter(p => !Number.isNaN(p.weight) && p.weight > 0);
                },
                canSubmit() {
                    const rows = this.form.products || [];
                    if (!rows.length) return false;
    
                    // All visible rows must be complete.
                    return rows.every(p => p.country && p.type && p.weight && !Number.isNaN(parseFloat(p.weight)) && parseFloat(p.weight) > 0);
                },
                formatPrice(price) {
                    return new Intl.NumberFormat('en-US', {
                        style: 'decimal',
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(price);
                },
                openShipment(option) {
                    const product = this.buildProductsPayload()[0] || this.form.products[0] || {};
                    const params = new URLSearchParams();
                    if (option?.carrier_id) params.set('carrier_id', option.carrier_id);
                    if (product.country) params.set('country', product.country);
                    if (product.type) params.set('type', product.type);
                    if (product.weight) params.set('weight', product.weight);
                    const target = `${window.PUBLIC_SHIPMENT_CREATE_URL}?${params.toString()}`;
                    window.location.href = window.IS_LOGGED_IN
                        ? target
                        : `${window.LOGIN_URL}?next=${encodeURIComponent(target)}`;
                },
                async getQuotes() {
                    this.loading = true;
                    this.error = null;
                    this.results = { options: [], cheapest: null };
    
                    try {
                        const payload = { products: this.buildProductsPayload() };
                        let response;
                        try {
                            response = await axios.post('/api/quote', payload);
                        } catch {
                            // Compatibility fallback for platforms that remap /api routes.
                            response = await axios.post('/quote', payload);
                        }
                        if (response.data.success) {
                            this.results = response.data.data;
                            if (!this.results.options.length) {
                                 this.error = "No service coverage found for this destination.";
                            }
                        } else {
                            this.error = response.data.error || "Failed to calculate quote.";
                        }
                    } catch (err) {
                        this.error = err.response?.data?.message || "An error occurred. Please try again.";
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    }
</script>
