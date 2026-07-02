@extends('layouts.customer')
@section('title', 'Instant Shipping Quote — ExpressPeek')

@push('head')
<style>
    .quote-hero {
        background-image: url('/images/quotes-bg.webp');
        background-size: cover;
        background-position: top center;
        background-repeat: no-repeat;
        position: relative;
        overflow: hidden;
    }
    .quote-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background-color: rgba(15, 23, 42, 0.35); /* Lighter overlay to let the vibrant blue show through */
    }
    .glow-orb-1 { position:absolute; top:-80px; right:-80px; width:400px; height:400px; border-radius:50%; background:radial-gradient(circle, rgba(139,92,246,0.35), transparent 70%); pointer-events:none; z-index: 1; }
    .glow-orb-2 { position:absolute; bottom:-100px; left:-60px; width:350px; height:350px; border-radius:50%; background:radial-gradient(circle, rgba(59,130,246,0.25), transparent 70%); pointer-events:none; z-index: 1; }
    .stat-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .stat-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.12); }
    .feature-card { transition: all 0.25s ease; border: 1px solid rgba(124,58,237,0.08); }
    .feature-card:hover { border-color: rgba(124,58,237,0.25); box-shadow: 0 12px 40px rgba(124,58,237,0.08); transform: translateY(-2px); }
    @keyframes countUp { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
    .count-animate { animation: countUp 0.6s ease forwards; }
</style>
@endpush

@section('content')

{{-- ===== HERO SECTION ===== --}}
<section class="quote-hero pt-32 md:pt-40" style="padding-bottom: 10rem;">
    <div class="glow-orb-1"></div>
    <div class="glow-orb-2"></div>
    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 text-center z-10">
        <div class="inline-flex items-center gap-2 bg-white/20 border border-white/30 text-white text-xs font-bold px-4 py-2 rounded-full mb-6 shadow-lg shadow-blue-900/20">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            Live Rates — Instant Results
        </div>
        <h1 class="text-4xl md:text-6xl font-black text-white leading-tight tracking-tight mb-6 drop-shadow-md">
            The Smartest Way to<br>
            <span class="text-transparent bg-clip-text drop-shadow-sm" style="background-image: linear-gradient(90deg, #c4b5fd, #93c5fd)">Compare Shipping Rates</span>
        </h1>
        <p class="text-white text-lg md:text-xl max-w-3xl mx-auto mb-12 leading-relaxed drop-shadow-sm font-medium">
            Get instant, transparent quotes from <strong class="text-white">{{ $carrierCount }} global carriers</strong> covering <strong class="text-white">{{ $countryCount }}+ destinations</strong> — all in seconds. No sign-up required to compare.
        </p>

        {{-- ===== LIVE STATS ROW ===== --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto" style="margin-bottom: 2rem;">
            @foreach([
                ['value' => $carrierCount, 'suffix' => '', 'label' => 'Global Carriers', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'color' => 'from-violet-500 to-purple-600'],
                ['value' => $countryCount, 'suffix' => '+', 'label' => 'Countries Covered', 'icon' => 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064', 'color' => 'from-blue-500 to-blue-600'],
                ['value' => '1', 'suffix' => 's', 'label' => 'Avg. Quote Time', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => 'from-amber-500 to-orange-500'],
                ['value' => '100', 'suffix' => '%', 'label' => 'Price Transparency', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'color' => 'from-emerald-500 to-emerald-600'],
            ] as $stat)
            <div class="stat-card bg-white/10 backdrop-blur-sm border border-white/15 rounded-2xl p-5 text-center">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $stat['color'] }} flex items-center justify-center mx-auto mb-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"/>
                    </svg>
                </div>
                <p class="text-3xl md:text-4xl font-black text-white leading-none">{{ $stat['value'] }}<span class="text-violet-300">{{ $stat['suffix'] }}</span></p>
                <p class="text-indigo-300 text-xs font-medium mt-1">{{ $stat['label'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== QUOTE FORM ===== --}}
<div class="bg-gray-50 relative">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 -mt-16 relative z-20 pb-16">
        <div x-data="quoteEnginePage()" class="bg-white rounded-3xl shadow-2xl shadow-indigo-900/10 border border-gray-100 overflow-hidden">

            <div class="bg-gradient-to-r from-violet-600 to-blue-700 px-8 py-6 text-white flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="bg-white/95 rounded-xl px-3 py-2 shadow-lg shadow-blue-900/30">
                        <img src="/images/express-peek-logo.webp" alt="Express Peek" class="h-8 w-auto object-contain">
                    </div>
                    <div>
                        <h3 class="text-lg font-bold tracking-tight">Shipping Quote Engine</h3>
                        <p class="text-violet-200 text-xs mt-0.5">Comparing {{ $carrierCount }} carriers across {{ $countryCount }}+ countries</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 bg-white/10 border border-white/20 rounded-xl px-4 py-2 text-xs font-bold text-white">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Live Pricing Active
                </div>
            </div>

            <div class="p-6 md:p-10">
                {{-- Package rows --}}
                <div class="space-y-6 mb-8">
                    <template x-for="(product, index) in form.products" :key="index">
                        <div class="rounded-2xl border border-gray-100 bg-gray-50/50 p-6 shadow-sm relative">
                            <div class="flex items-center justify-between mb-5">
                                <h4 class="text-sm font-black text-violet-700 uppercase tracking-widest" x-text="'Package ' + (index + 1)"></h4>
                                <button
                                    type="button"
                                    @click="removeProduct(index)"
                                    x-show="form.products.length > 1"
                                    class="text-xs text-red-500 font-bold uppercase tracking-wide hover:text-red-600 hover:bg-red-50 px-3 py-1.5 rounded-lg transition-colors"
                                >
                                    Remove
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Destination Country</label>
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
                                            <input type="text" x-model="q" :placeholder="label || 'Search country...'" @focus="onFocus()" @keydown="onKey($event)" autocomplete="off" class="w-full bg-white border border-gray-200 rounded-xl pl-9 pr-8 py-3.5 text-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-200 transition-all outline-none text-slate-900 shadow-sm">
                                            <button x-show="product.country" type="button" @click="clear()" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg></button>
                                        </div>
                                        <div x-show="open" x-transition class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-100 rounded-xl shadow-xl z-30 max-h-56 overflow-y-auto">
                                            <template x-for="(c,i) in opts" :key="c.code">
                                                <div @click="pick(c)" :class="hi===i?'bg-violet-50 text-violet-700':'text-gray-700 hover:bg-gray-50'" class="flex items-center justify-between px-4 py-3 text-sm cursor-pointer transition-colors min-h-[44px]">
                                                    <span x-text="c.name" class="font-medium"></span>
                                                    <span x-text="c.code" class="text-xs font-mono text-slate-400 ml-2 shrink-0"></span>
                                                </div>
                                            </template>
                                            <div x-show="opts.length===0" class="px-4 py-3 text-sm text-slate-400 italic">No countries found</div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Shipment Type</label>
                                    <select x-model="product.type" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3.5 text-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-200 transition-all outline-none text-slate-900 shadow-sm appearance-none">
                                        <option value="">Select Type</option>
                                        <option value="document">Document</option>
                                        <option value="non_document">Non-document</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Weight</label>
                                    <div class="relative">
                                        <input type="number" step="0.5" min="0" x-model="product.weight" placeholder="e.g. 5.5" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3.5 text-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-200 transition-all outline-none pl-4 pr-12 text-slate-900 shadow-sm">
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs uppercase">KG</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div class="flex justify-start">
                        <button type="button" @click="addProduct()" class="px-5 py-2.5 bg-violet-50 text-violet-700 font-bold text-sm rounded-xl hover:bg-violet-100 transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Another Package
                        </button>
                    </div>
                </div>

                <div class="flex justify-center border-t border-gray-100 pt-8">
                    <button
                        @click="getQuotes()"
                        :disabled="loading || !canSubmit()"
                        class="px-12 py-4 bg-gradient-to-r from-violet-600 to-blue-700 text-white font-black text-lg rounded-2xl shadow-xl shadow-violet-500/30 hover:shadow-violet-500/40 hover:-translate-y-0.5 disabled:bg-none disabled:bg-gray-200 disabled:text-gray-400 disabled:shadow-none disabled:hover:translate-y-0 transition-all flex items-center gap-3 w-full md:w-auto justify-center"
                    >
                        <template x-if="loading">
                            <svg class="animate-spin h-6 w-6 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <template x-if="!loading">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </template>
                        <span x-text="loading ? 'Comparing ' + {{ $carrierCount }} + ' Carriers...' : 'Compare All Carriers Now'"></span>
                    </button>
                </div>

                {{-- Results --}}
                <div x-show="results?.options?.length > 0" x-cloak class="mt-12">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="h-px bg-gray-200 flex-1"></div>
                        <span class="text-sm font-black text-slate-400 uppercase tracking-widest bg-gray-50 px-4 py-1 rounded-full border border-gray-100">Available Options</span>
                        <div class="h-px bg-gray-200 flex-1"></div>
                    </div>

                    {{-- Recommended --}}
                    <template x-if="results && results.recommended">
                        <div class="bg-gradient-to-br from-violet-50 to-blue-50 border-2 border-violet-200 rounded-3xl p-6 md:p-8 mb-6 relative overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                            <div class="absolute top-0 right-0 py-1.5 px-6 bg-violet-600 text-white text-[10px] font-black uppercase tracking-tighter rounded-bl-2xl shadow-lg">
                                Recommended Match
                            </div>
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                                <div class="flex items-center gap-5">
                                    <div class="w-16 h-16 rounded-2xl bg-white shadow-sm border border-violet-100 flex items-center justify-center text-violet-600">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-3 mb-1">
                                            <p class="text-violet-900 font-black text-2xl" x-text="results.recommended.carrier"></p>
                                            <span class="bg-amber-100 text-amber-700 text-[10px] px-2 py-0.5 rounded-full font-bold uppercase ring-1 ring-amber-200">Fastest &amp; Value for Money</span>
                                        </div>
                                        <p class="text-violet-600 text-sm font-medium">Standard International Express Delivery</p>
                                    </div>
                                </div>
                                <div class="text-left md:text-right">
                                    <p class="text-4xl font-black text-slate-900 leading-none mb-1" x-text="formatPrice(results.recommended.total_price)"></p>
                                    <p class="text-xs font-bold text-slate-500 uppercase" x-text="'Currency: ' + results.recommended.currency"></p>
                                    <button @click="openShipment(results.recommended)" class="mt-4 px-6 py-2 bg-violet-600 text-white text-sm font-bold rounded-xl hover:bg-violet-700 transition-colors w-full md:w-auto">
                                        Select &amp; Ship
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- All Options --}}
                    <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
                        <template x-if="results">
                            <template x-for="option in results.options" :key="option.carrier">
                                <div class="w-full p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:bg-gray-50/80 transition-colors last:border-0 group">
                                    <div class="flex items-center gap-4 min-w-0 flex-1">
                                        <div class="w-12 h-12 flex-shrink-0 rounded-2xl bg-gray-100 flex items-center justify-center text-slate-400 group-hover:bg-violet-100 group-hover:text-violet-600 transition-colors">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-2 mb-0.5">
                                                <p class="text-base font-bold text-slate-900" x-text="option.carrier"></p>
                                                <span x-show="option.carrier === results.recommended?.carrier" class="bg-violet-100 text-violet-700 text-[10px] px-2 py-0.5 rounded-full font-bold uppercase whitespace-nowrap hidden md:inline-block">Recommended</span>
                                            </div>
                                            <p class="text-xs text-slate-500 font-medium" x-text="'Base rate: ' + formatPrice(option.base_price)"></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between md:justify-end gap-6 md:gap-8 mt-2 md:mt-0">
                                        <div class="text-left md:text-right">
                                            <p class="text-2xl font-black text-slate-900 whitespace-nowrap leading-none" x-text="formatPrice(option.total_price)"></p>
                                            <p class="text-xs font-bold text-slate-400 uppercase mt-1" x-text="option.currency"></p>
                                        </div>
                                        <button @click="openShipment(option)" class="px-5 py-2 bg-gray-900 text-white text-sm font-bold rounded-xl hover:bg-gray-800 transition-colors shrink-0">
                                            Select
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </template>
                    </div>

                    <div class="mt-6 bg-blue-50/50 border border-blue-100 rounded-2xl p-5 flex items-start gap-4">
                        <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            Quotes are estimates. Fuel surcharges and standard handling fees are already included. Actual price may vary based on volumetric weight, destination cities (e.g. remote areas), type of goods (e.g. critical or non-conveyable goods), special packing costs, and insurance selections.
                        </p>
                    </div>
                </div>

                {{-- Error --}}
                <div x-show="error" x-cloak class="mt-8 bg-red-50 border border-red-200 text-red-700 px-6 py-5 rounded-2xl text-sm flex items-center gap-4 shadow-sm">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <span class="font-medium" x-text="error"></span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===== WHY OUR QUOTE SYSTEM ===== --}}
<section class="bg-white py-16 border-b border-gray-100">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-12">
            <p class="text-violet-600 text-xs font-bold uppercase tracking-widest mb-3">Why ExpressPeek Quotes?</p>
            <h2 class="text-3xl md:text-4xl font-black text-slate-900 mb-4">Built for Speed. Built for Savings.</h2>
            <p class="text-slate-500 max-w-2xl mx-auto">Our quoting engine compares every carrier in real time so you always get the best rate — with zero guesswork.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach([
                [
                    'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
                    'color' => 'bg-violet-100 text-violet-600',
                    'glow' => 'shadow-violet-100',
                    'title' => 'Instant Multi-Carrier Comparison',
                    'desc' => 'Our engine queries all ' . $carrierCount . ' carriers simultaneously and returns ranked results in under 1 second — no waiting, no back-and-forth.',
                    'badge' => $carrierCount . ' Carriers',
                    'badge_color' => 'bg-violet-100 text-violet-700',
                ],
                [
                    'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                    'color' => 'bg-emerald-100 text-emerald-600',
                    'glow' => 'shadow-emerald-100',
                    'title' => 'All-Inclusive Pricing',
                    'desc' => 'Fuel surcharges and standard handling fees are pre-calculated and shown upfront. To ensure full transparency, final prices may vary for remote destination cities, critical or non-conveyable goods, and special packing requirements.',
                    'badge' => 'No Hidden Fees',
                    'badge_color' => 'bg-emerald-100 text-emerald-700',
                ],
                [
                    'icon' => 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064',
                    'color' => 'bg-blue-100 text-blue-600',
                    'glow' => 'shadow-blue-100',
                    'title' => 'Global Coverage',
                    'desc' => 'With ' . $countryCount . '+ destination countries in our network, we can quote virtually any international shipment — documents and parcels alike.',
                    'badge' => $countryCount . '+ Countries',
                    'badge_color' => 'bg-blue-100 text-blue-700',
                ],
            ] as $feat)
            <div class="feature-card bg-white rounded-3xl p-8 shadow-sm">
                <div class="w-14 h-14 rounded-2xl {{ $feat['color'] }} flex items-center justify-center mb-5 shadow-lg {{ $feat['glow'] }}">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $feat['icon'] }}"/>
                    </svg>
                </div>
                <div class="flex items-center gap-2 mb-3">
                    <h3 class="font-black text-slate-900 text-lg">{{ $feat['title'] }}</h3>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $feat['badge_color'] }} shrink-0">{{ $feat['badge'] }}</span>
                </div>
                <p class="text-slate-500 text-sm leading-relaxed">{{ $feat['desc'] }}</p>
            </div>
            @endforeach
        </div>

        {{-- ===== PROCESS STEPS ===== --}}
        <div class="mt-16 relative">
            <div class="hidden md:block absolute top-8 left-[16.67%] right-[16.67%] h-px bg-gradient-to-r from-violet-200 via-blue-200 to-emerald-200" style="width:66%; left:17%"></div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
                @foreach([
                    ['step' => '01', 'title' => 'Enter Package Details', 'desc' => 'Select destination, shipment type (document or parcel), and weight. Add multiple packages in one quote.', 'color' => 'from-violet-500 to-violet-600'],
                    ['step' => '02', 'title' => 'We Compare All Carriers', 'desc' => 'Our engine instantly fetches live rates from all ' . $carrierCount . ' carriers and ranks them by price and speed.', 'color' => 'from-blue-500 to-blue-600'],
                    ['step' => '03', 'title' => 'Pick & Ship Instantly', 'desc' => 'Select your preferred option and proceed directly to booking — no re-entry of information needed.', 'color' => 'from-emerald-500 to-emerald-600'],
                ] as $step)
                <div class="text-center">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br {{ $step['color'] }} flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <span class="text-white font-black text-xl">{{ $step['step'] }}</span>
                    </div>
                    <h3 class="font-black text-slate-900 text-base mb-2">{{ $step['title'] }}</h3>
                    <p class="text-slate-500 text-sm leading-relaxed max-w-xs mx-auto">{{ $step['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ===== TRUST BAR ===== --}}
<div class="bg-gradient-to-r from-slate-900 to-slate-800 py-5">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="flex flex-wrap items-center justify-center gap-8 md:gap-16">
            @foreach([
                ['icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'text' => 'Secure & Encrypted'],
                ['icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'text' => 'No Hidden Charges'],
                ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'text' => 'Real-Time Rates'],
                ['icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z', 'text' => '24/7 Live Support'],
            ] as $trust)
            <div class="flex items-center gap-2 text-slate-300 text-sm font-medium">
                <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $trust['icon'] }}"/>
                </svg>
                {{ $trust['text'] }}
            </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
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

    if (typeof window.quoteEnginePage === 'undefined') {
        window.quoteEnginePage = function() {
            return {
                loading: false,
                error: null,
                results: { options: [], cheapest: null },
                form: { products: [{ country: '', type: '', weight: '' }] },
                addProduct() { this.form.products.push({ country: '', type: '', weight: '' }); },
                removeProduct(index) {
                    this.form.products.splice(index, 1);
                    if (!this.form.products.length) this.form.products.push({ country: '', type: '', weight: '' });
                },
                buildProductsPayload() {
                    return (this.form.products || [])
                        .filter(p => p.country && p.type && p.weight)
                        .map(p => ({ country: String(p.country).toUpperCase(), type: p.type, weight: parseFloat(p.weight) }))
                        .filter(p => !Number.isNaN(p.weight) && p.weight > 0);
                },
                canSubmit() {
                    const rows = this.form.products || [];
                    return rows.length && rows.every(p => p.country && p.type && p.weight && !Number.isNaN(parseFloat(p.weight)) && parseFloat(p.weight) > 0);
                },
                formatPrice(price) {
                    return new Intl.NumberFormat('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(price);
                },
                openShipment(option) {
                    const product = this.buildProductsPayload()[0] || this.form.products[0] || {};
                    const params = new URLSearchParams();
                    if (option?.carrier_id) params.set('carrier_id', option.carrier_id);
                    if (product.country) params.set('country', product.country);
                    if (product.type) params.set('type', product.type);
                    if (product.weight) params.set('weight', product.weight);
                    const target = `${window.PUBLIC_SHIPMENT_CREATE_URL}?${params.toString()}`;
                    window.location.href = window.IS_LOGGED_IN ? target : `${window.LOGIN_URL}?next=${encodeURIComponent(target)}`;
                },
                async getQuotes() {
                    this.loading = true;
                    this.error = null;
                    this.results = { options: [], cheapest: null };
                    try {
                        const payload = { products: this.buildProductsPayload() };
                        let response;
                        try { response = await axios.post('/api/quote', payload); }
                        catch { response = await axios.post('/quote', payload); }
                        if (response.data.success) {
                            this.results = response.data.data;
                            if (!this.results.options.length) this.error = "No service coverage found for this destination.";
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
@endpush
@endsection
