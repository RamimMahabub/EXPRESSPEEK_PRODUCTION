@extends('layouts.customer')

@section('title', 'Home — Logistics & Shipping')

@section('content')

{{-- ===== HERO SECTION ===== --}}
<section class="hero-bg relative min-h-[520px] flex items-center" id="track">
    {{-- Gradient overlay --}}
    <div class="absolute inset-0 bg-gradient-to-r from-gray-900/85 via-gray-900/60 to-transparent"></div>

    <div class="relative max-w-7xl mx-auto px-6 py-20 w-full">
        <div class="max-w-xl">
            <p class="text-violet-300 text-sm font-semibold uppercase tracking-widest mb-3 fade-up fade-up-1">ExpressPeak Logistics</p>
            <h1 class="text-4xl md:text-5xl font-black text-white leading-tight mb-4 fade-up fade-up-1">
                Track Your<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-blue-400">Shipment</span>
            </h1>
            <p class="text-slate-300 text-base mb-8 fade-up fade-up-2">Enter your tracking number to get real-time updates on your package anywhere in the world.</p>

            {{-- Tracking Form --}}
            <form action="{{ route('customer.track') ?? '#' }}" method="GET" class="fade-up fade-up-2" id="tracking-form">
                <div class="flex gap-0 rounded-2xl overflow-hidden shadow-2xl ring-1 ring-white/10">
                    <input
                        id="tracking_number_input"
                        type="text"
                        name="tracking"
                        placeholder="Enter your tracking number(s)..."
                        class="track-input flex-1 bg-white px-5 py-4 text-slate-100 text-sm placeholder-gray-400 focus:outline-none focus:ring-0 border-0"
                    >
                    <button type="submit"
                        class="bg-gradient-to-r from-violet-600 to-blue-700 hover:from-violet-700 hover:to-blue-800 text-white px-8 py-4 font-bold text-sm flex items-center gap-2 transition-all flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Track
                    </button>
                </div>
                <p class="text-xs text-slate-400 mt-2.5 pl-1">
                    Tip: You can enter multiple tracking numbers separated by commas.
                </p>
            </form>

            {{-- Recent Tracking (if logged in) --}}
            @auth
            @if($recentShipments->count() > 0)
            <div class="mt-5 fade-up fade-up-3">
                <p class="text-xs text-slate-400 mb-2">📦 Recent orders:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($recentShipments->take(3) as $s)
                    <button onclick="document.getElementById('tracking_number_input').value='{{ $s->tracking_number }}'"
                        class="px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-mono transition-colors border border-white/20">
                        {{ $s->tracking_number }}
                    </button>
                    @endforeach
                </div>
            </div>
            @endif
            @endauth
        </div>
    </div>
</section>

{{-- ===== QUICK ACTION CARDS ===== --}}
<section class="relative z-10 -mt-16 max-w-7xl mx-auto px-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-0 bg-white rounded-2xl shadow-2xl overflow-hidden border border-white/10">

        {{-- Ship Now --}}
        <div class="service-card p-7 border-r border-white/10 hover:bg-violet-50 cursor-pointer group">
            <div class="flex flex-col items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-violet-100 group-hover:bg-violet-200 flex items-center justify-center transition-colors">
                    <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-100 text-base mb-1">Ship Now</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Find the right service for your delivery needs.</p>
                </div>
                <a href="#" class="text-sm text-violet-600 font-semibold flex items-center gap-1 group-hover:gap-2 transition-all">
                    Get started
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        {{-- Get a Quote --}}
        <div data-open-quote-modal role="button" tabindex="0" class="service-card p-7 border-r border-white/10 hover:bg-blue-50 cursor-pointer group">
            <div class="flex flex-col items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 group-hover:bg-blue-200 flex items-center justify-center transition-colors">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-100 text-base mb-1">Get a Quote</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">No surprises — know your cost before you ship.</p>
                </div>
                <a href="#" data-open-quote-modal class="text-sm text-blue-600 font-semibold flex items-center gap-1 group-hover:gap-2 transition-all">
                    Calculate now
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        {{-- Shop from Bangladesh --}}
        <a href="{{ route('sourcing.create') }}" class="service-card p-7 border-r border-white/10 hover:bg-amber-50 cursor-pointer group block">
            <div class="flex flex-col items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 group-hover:bg-amber-200 flex items-center justify-center transition-colors">
                    <span class="text-2xl">🛒</span>
                </div>
                <div>
                    <h3 class="font-bold text-slate-100 text-base mb-1">Shop from Bangladesh</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Living abroad? We'll find it and ship it to you.</p>
                </div>
                <span class="text-sm text-amber-600 font-semibold flex items-center gap-1 group-hover:gap-2 transition-all">
                    Request sourcing
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </div>
        </a>

        {{-- Business --}}
        <div class="service-card p-7 hover:bg-emerald-50 cursor-pointer group">
            <div class="flex flex-col items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 group-hover:bg-emerald-200 flex items-center justify-center transition-colors">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-100 text-base mb-1">ExpressPeak for Business</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Shipping regularly? Request a business account and get premium benefits.</p>
                </div>
                <a href="#" class="text-sm text-emerald-600 font-semibold flex items-center gap-1 group-hover:gap-2 transition-all">
                    Learn more
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ===== MY SHIPMENTS SNAPSHOT (only if logged in and has shipments) ===== --}}
@auth
@if($stats['total'] > 0)
<section class="max-w-7xl mx-auto px-6 mt-16">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-black text-slate-100">My Active Shipments</h2>
            <p class="text-slate-400 text-sm mt-1">Quick overview of your recent orders</p>
        </div>
        <a href="{{ route('customer.shipments.index') }}"
           class="px-5 py-2.5 rounded-xl border-2 border-violet-600 text-violet-700 text-sm font-bold hover:bg-violet-600 hover:text-white transition-all">
            View All →
        </a>
    </div>

    {{-- Status Summary Bar --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        @foreach([
            ['label' => 'Total', 'value' => $stats['total'], 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'from' => 'from-violet-500', 'to' => 'to-blue-600'],
            ['label' => 'Pending', 'value' => $stats['pending'], 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'from' => 'from-amber-500', 'to' => 'to-orange-500'],
            ['label' => 'In Transit', 'value' => $stats['in_transit'], 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'from' => 'from-blue-500', 'to' => 'to-cyan-500'],
            ['label' => 'Delivered', 'value' => $stats['delivered'], 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'from' => 'from-emerald-500', 'to' => 'to-teal-500'],
        ] as $stat)
        <div class="bg-white rounded-2xl p-5 border border-white/10 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $stat['from'] }} {{ $stat['to'] }} flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"/>
                </svg>
            </div>
            <p class="text-3xl font-black text-slate-100">{{ $stat['value'] }}</p>
            <p class="text-xs text-slate-400 mt-0.5 font-medium">{{ $stat['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Recent Shipments List --}}
    <div class="bg-white rounded-2xl border border-white/10 shadow-sm overflow-hidden mb-16">
        @foreach($recentShipments as $shipment)
        <div class="flex items-center gap-5 px-6 py-4 border-b border-gray-50 hover:bg-gray-50 transition-colors last:border-b-0 group">
            {{-- Status Icon --}}
            <div class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center
                @if($shipment->status === 'delivered') bg-emerald-100 text-emerald-600
                @elseif($shipment->status === 'in_transit') bg-blue-100 text-blue-600
                @elseif($shipment->status === 'out_for_delivery') bg-purple-100 text-purple-600
                @elseif($shipment->status === 'pending') bg-amber-100 text-amber-600
                @else bg-gray-100 text-slate-400
                @endif">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @if($shipment->status === 'delivered')
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    @elseif($shipment->status === 'in_transit' || $shipment->status === 'out_for_delivery')
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    @endif
                </svg>
            </div>

            {{-- Details --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-3 mb-0.5">
                    <p class="text-sm font-bold text-slate-100 truncate">{{ $shipment->receiver_name }}</p>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                        @if($shipment->status === 'delivered') bg-emerald-100 text-emerald-700
                        @elseif($shipment->status === 'in_transit') bg-blue-100 text-blue-700
                        @elseif($shipment->status === 'out_for_delivery') bg-purple-100 text-purple-700
                        @elseif($shipment->status === 'pending') bg-amber-100 text-amber-700
                        @else bg-gray-100 text-slate-500
                        @endif flex-shrink-0">
                        {{ $shipment->status_label }}
                    </span>
                </div>
                <div class="flex items-center gap-4 text-xs text-slate-400">
                    <span class="font-mono text-violet-600 font-semibold">{{ $shipment->tracking_number }}</span>
                    <span>→ {{ $shipment->receiver_city }}, {{ $shipment->receiver_country }}</span>
                    <span>{{ $shipment->created_at->diffForHumans() }}</span>
                </div>
            </div>

            {{-- Weight + ETA --}}
            <div class="hidden md:block text-right flex-shrink-0">
                <p class="text-sm font-semibold text-gray-700">{{ $shipment->weight }} kg</p>
                @if($shipment->estimated_delivery)
                <p class="text-xs text-slate-400 mt-0.5">ETA {{ $shipment->estimated_delivery->format('M d') }}</p>
                @endif
            </div>

            {{-- Arrow --}}
            <svg class="w-4 h-4 text-slate-300 group-hover:text-violet-500 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </div>
        @endforeach
    </div>
</section>
@endif
@endauth

{{-- ===== PROMO BANNER ===== --}}
<section class="mx-0 mt-8">
    <div class="flex items-stretch min-h-[200px] bg-gradient-to-r from-violet-700 to-blue-800 overflow-hidden">
        {{-- Content --}}
        <div class="flex items-center px-10 py-10 max-w-3xl">
            <div>
                <div class="inline-flex items-center gap-2 bg-white/20 text-white text-xs font-semibold px-3 py-1 rounded-full mb-4">
                    <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span>
                    New for {{ date('Y') }}
                </div>
                <h2 class="text-2xl md:text-3xl font-black text-white mb-3 leading-tight">
                    International Courier & Cargo Service from Bangladesh
                </h2>
                <p class="text-violet-200 text-sm md:text-base leading-relaxed mb-6 max-w-lg">
                    Send parcels, documents, and commercial goods from Dhaka to the USA, UK, Canada, and 220+ countries worldwide. Get instant tracking and transparent, affordable rates with Express Peek.
                </p>
                <a href="#"
                   class="inline-flex items-center gap-2 bg-white text-violet-700 font-bold text-sm px-6 py-3 rounded-xl hover:bg-violet-50 transition-colors shadow-lg">
                    Explore Our Solutions
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ===== SERVICES SECTION ===== --}}
<section class="max-w-7xl mx-auto px-6 py-20">
    <div class="text-center mb-14">
        <p class="text-violet-600 text-sm font-semibold uppercase tracking-widest mb-2">What We Offer</p>
        <h2 class="text-3xl md:text-4xl font-black text-slate-100 mb-4">Document and Parcel Shipping</h2>
        <p class="text-slate-400 max-w-xl mx-auto">For All Shippers — individuals, SMBs, and enterprise businesses</p>
    </div>

    <div class="max-w-3xl mx-auto grid grid-cols-1 gap-8 items-center mb-8">
        {{-- Service Cards --}}
        <div class="space-y-4">
            @foreach([
                ['icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'title' => 'Document Express', 'desc' => 'Time-sensitive documents delivered globally with full tracking and signature confirmation.', 'tag' => 'Fast', 'tc' => 'text-violet-600', 'bc' => 'bg-violet-100'],
                ['icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'title' => 'Parcel Delivery', 'desc' => 'Flexible shipping for packages of all sizes, with real-time tracking at every step.', 'tag' => 'Popular', 'tc' => 'text-blue-600', 'bc' => 'bg-blue-100'],
                ['icon' => 'M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18', 'title' => 'Freight Solutions', 'desc' => 'Heavy cargo, pallets, and bulk freight handled with precision by our partner network.', 'tag' => 'Enterprise', 'tc' => 'text-emerald-600', 'bc' => 'bg-emerald-100'],
            ] as $svc)
            <div class="service-card flex items-start gap-4 p-5 bg-white rounded-2xl border border-white/10 shadow-sm">
                <div class="w-11 h-11 rounded-xl {{ $svc['bc'] }} flex items-center justify-center flex-shrink-0">
                    <svg class="w-5.5 h-5.5 {{ $svc['tc'] }} w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $svc['icon'] }}"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <h3 class="font-bold text-slate-100 text-sm">{{ $svc['title'] }}</h3>
                        <span class="text-xs font-semibold {{ $svc['tc'] }} {{ $svc['bc'] }} px-2 py-0.5 rounded-full">{{ $svc['tag'] }}</span>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed">{{ $svc['desc'] }}</p>
                </div>
                <svg class="w-4 h-4 text-slate-300 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
            @endforeach

            <a href="#"
               class="block w-full text-center py-3.5 rounded-xl bg-gradient-to-r from-violet-600 to-blue-700 text-white font-bold text-sm hover:opacity-90 transition-opacity shadow-lg shadow-violet-500/20">
                View All Services →
            </a>
        </div>
    </div>
</section>

{{-- ===== WHY EXPRESSPEEAK ===== --}}
<section class="bg-gray-50 border-y border-white/10 py-20">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-14">
            <h2 class="text-3xl font-black text-slate-100">Why Choose ExpressPeak?</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach([
                ['icon' => 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064', 'title' => 'Global Network', 'desc' => 'Delivery to 220+ countries and territories through our trusted carrier network.'],
                ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'title' => 'Secure & Insured', 'desc' => 'Every shipment is tracked and backed by comprehensive insurance options.'],
                ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'title' => 'Express Speed', 'desc' => 'Same-day, next-day, and international express options for time-critical deliveries.'],
                ['icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z', 'title' => '24/7 Support', 'desc' => 'Our customer service team is available around the clock to help with any query.'],
            ] as $why)
            <div class="service-card bg-white rounded-2xl p-6 border border-white/10 shadow-sm">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-100 to-blue-100 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $why['icon'] }}"/>
                    </svg>
                </div>
                <h3 class="font-bold text-slate-100 mb-2">{{ $why['title'] }}</h3>
                <p class="text-sm text-slate-400 leading-relaxed">{{ $why['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== CTA SECTION ===== --}}
@guest
<section class="max-w-7xl mx-auto px-6 py-20">
    <div class="bg-gradient-to-br from-violet-600 via-violet-700 to-blue-800 rounded-3xl p-12 text-center relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 right-0 w-96 h-96 rounded-full bg-white translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 left-0 w-72 h-72 rounded-full bg-white -translate-x-1/3 translate-y-1/3"></div>
        </div>
        <div class="relative">
            <p class="text-violet-200 text-sm font-semibold uppercase tracking-widest mb-3">Get Started Today</p>
            <h2 class="text-3xl md:text-4xl font-black text-white mb-4">Ready to Ship Smarter?</h2>
            <p class="text-violet-200 max-w-lg mx-auto mb-8">Join thousands of businesses and individuals who trust ExpressPeak for their logistics needs. Free to register.</p>
            <div class="flex items-center justify-center gap-4 flex-wrap">
                <a href="{{ route('register') }}"
                   class="px-8 py-3.5 rounded-xl bg-white text-violet-700 font-bold text-sm hover:bg-violet-50 transition-colors shadow-xl">
                    Create Free Account
                </a>
                <a href="{{ route('login') }}"
                   class="px-8 py-3.5 rounded-xl border-2 border-white/30 text-white font-bold text-sm hover:border-white transition-colors">
                    Sign In
                </a>
            </div>
        </div>
    </div>
</section>
@endguest

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
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="open = false"></div>

    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div
            class="relative bg-white w-full max-w-2xl rounded-3xl shadow-2xl overflow-hidden border border-white/10"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="scale-95 translate-y-8"
            x-transition:enter-end="scale-100 translate-y-0"
        >
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
                                            <input type="text" x-model="q" :placeholder="label || 'Search country...'" @focus="onFocus(); $dispatch('quote-clear')" @keydown="onKey($event)" autocomplete="off" class="w-full bg-gray-50 border border-white/10 rounded-xl pl-9 pr-8 py-3 text-sm focus:ring-2 focus:ring-violet-500 transition-all outline-none">
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
                                    <select x-model="product.type" class="w-full bg-gray-50 border border-white/10 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-violet-500 transition-all outline-none">
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
                                            class="w-full bg-gray-50 border border-white/10 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-violet-500 transition-all outline-none pl-4 pr-12"
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
                                    <p class="text-3xl font-black text-slate-100 leading-none" x-text="formatPrice(results.recommended.total_price)"></p>
                                    <p class="text-xs font-bold text-slate-400 mt-1 uppercase" x-text="'Currency: ' + results.recommended.currency"></p>
                                </div>
                            </div>
                        </div>
                    </template>



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
                                                <p class="text-sm font-black text-slate-100" x-text="option.carrier"></p>
                                                <span x-show="option.carrier === results.recommended?.carrier" class="bg-amber-100 text-amber-700 text-[9px] px-1.5 py-0.5 rounded font-bold uppercase whitespace-nowrap">Recommended</span>

                                            </div>
                                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight" x-text="'Rate (All-inclusive): ' + formatPrice(option.base_price)"></p>
                                        </div>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <p class="text-lg font-black text-slate-100 whitespace-nowrap" x-text="formatPrice(option.total_price)"></p>
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

                <div x-show="error" x-cloak class="mt-8 bg-red-50 border border-red-100 text-red-600 px-6 py-4 rounded-2xl text-sm flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span x-text="error"></span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
window.SITE_COUNTRIES = @json($countries->map(fn($c) => ['name' => $c->country_name, 'code' => $c->country_code])->values());
window.PUBLIC_SHIPMENT_CREATE_URL = @json(route('shipment.create'));
window.LOGIN_URL = @json(route('login'));
window.IS_LOGGED_IN = @json(auth()->check());

function quoteEngine() {
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
                        this.error = 'No service coverage found for this destination.';
                    }
                } else {
                    this.error = response.data.error || 'Failed to calculate quote.';
                }
            } catch (err) {
                this.error = err.response?.data?.message || 'An error occurred. Please try again.';
            } finally {
                this.loading = false;
            }
        }
    };
}

// Auto-fill tracking from URL param
const urlParams = new URLSearchParams(window.location.search);
const trackingParam = urlParams.get('tracking');
const trackingInput = document.getElementById('tracking_number_input');
if (trackingParam && trackingInput) {
    trackingInput.value = trackingParam;
}
</script>
@endpush

@endsection
