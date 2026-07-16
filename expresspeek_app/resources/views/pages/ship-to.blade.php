@extends('layouts.customer')

@section('seo_title', $data['seo_title'])
@section('seo_description', $data['seo_description'])

@section('content')
<main class="min-h-screen bg-slate-50">
    {{-- Hero --}}
    <section class="bg-gradient-to-br from-slate-900 via-violet-950 to-slate-900 py-16 md:py-24">
        <div class="max-w-5xl mx-auto px-6">
            <div class="inline-flex items-center gap-2 bg-white/10 border border-white/20 text-white text-xs font-bold px-4 py-2 rounded-full mb-6">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg>
                🇧🇩 Bangladesh → {{ $data['name'] }}
            </div>
            <h1 class="text-4xl md:text-5xl font-black text-white mb-5 leading-tight">
                Send Parcels from Bangladesh<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-violet-300 to-blue-400">to {{ $data['name'] }}</span>
            </h1>
            <p class="text-slate-300 text-base max-w-2xl leading-relaxed mb-8">
                Fast, reliable courier service from Dhaka, Sylhet & Chittagong to {{ $data['name'] }}. {{ $data['transit_days'] }} business days delivery with full tracking. Starting from {{ $data['starting_price'] }}.
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('quote') }}?country={{ $data['country_code'] }}"
                   class="inline-flex items-center gap-2 px-7 py-4 rounded-2xl bg-gradient-to-r from-violet-600 to-blue-700 text-white font-bold text-sm hover:opacity-90 transition-opacity shadow-lg shadow-violet-500/30" id="country-get-quote">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Get a Quote to {{ $data['name'] }}
                </a>
                <a href="{{ route('track') }}"
                   class="inline-flex items-center gap-2 px-7 py-4 rounded-2xl border-2 border-white/20 text-white font-bold text-sm hover:border-white/40 transition-colors">
                    Track a Shipment
                </a>
            </div>
        </div>
    </section>

    {{-- Key Info Cards --}}
    <section class="max-w-5xl mx-auto px-6 -mt-8 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm text-center">
                <div class="w-12 h-12 rounded-xl bg-violet-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-2xl font-black text-slate-900">{{ $data['transit_days'] }} days</p>
                <p class="text-sm text-slate-500 mt-1">Estimated Transit Time</p>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm text-center">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-2xl font-black text-slate-900">{{ $data['starting_price'] }}</p>
                <p class="text-sm text-slate-500 mt-1">Starting From</p>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm text-center">
                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <p class="text-2xl font-black text-slate-900">Full</p>
                <p class="text-sm text-slate-500 mt-1">Tracking & Insurance</p>
            </div>
        </div>
    </section>

    {{-- Diaspora Section --}}
    <section class="max-w-5xl mx-auto px-6 py-16">
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="p-8 md:p-10">
                <h2 class="text-2xl font-black text-slate-900 mb-4">Shipping from Bangladesh to {{ $data['name'] }}</h2>
                <p class="text-slate-600 leading-relaxed text-base mb-8">{{ $data['diaspora_note'] }}</p>

                {{-- Popular Items --}}
                @if(!empty($data['popular_items']))
                <h3 class="text-lg font-bold text-slate-900 mb-4">Popular Items Shipped to {{ $data['name'] }}</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-8">
                    @foreach($data['popular_items'] as $item)
                    <div class="flex items-center gap-3 bg-violet-50 rounded-xl px-4 py-3">
                        <svg class="w-4 h-4 text-violet-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-sm font-medium text-slate-700">{{ $item }}</span>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Restricted Items --}}
                <h3 class="text-lg font-bold text-slate-900 mb-4">Customs Restrictions for {{ $data['name'] }}</h3>
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6">
                    <div class="flex items-start gap-3 mb-3">
                        <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <p class="text-sm font-bold text-amber-900">Items restricted or prohibited when shipping to {{ $data['name'] }}:</p>
                    </div>
                    <ul class="space-y-2 pl-8">
                        @foreach($data['restricted_items'] as $item)
                        <li class="text-sm text-amber-800 list-disc">{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="max-w-5xl mx-auto px-6 pb-8">
        <div class="bg-gradient-to-r from-violet-600 to-blue-700 rounded-3xl p-8 md:p-12 text-center">
            <h2 class="text-2xl md:text-3xl font-black text-white mb-4">Ready to Ship to {{ $data['name'] }}?</h2>
            <p class="text-violet-200 max-w-lg mx-auto mb-6 text-sm leading-relaxed">Get an instant quote with transparent pricing. Compare multiple carriers and choose the best option for your shipment.</p>
            <a href="{{ route('quote') }}?country={{ $data['country_code'] }}"
               class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-white text-violet-700 font-bold text-sm hover:bg-violet-50 transition-colors shadow-xl">
                Get Your Quote Now →
            </a>
        </div>
    </section>

    {{-- Internal Links --}}
    <section class="max-w-5xl mx-auto px-6 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- Ship from cities --}}
            <div>
                <h3 class="text-lg font-bold text-slate-900 mb-4">Ship from These Cities</h3>
                <div class="space-y-2">
                    @foreach($cities as $slug => $cityData)
                    <a href="{{ route('ship-from', $slug) }}" class="flex items-center gap-3 bg-white rounded-xl px-5 py-3 border border-slate-100 hover:border-violet-200 hover:shadow-sm transition-all group">
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-violet-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        <span class="font-medium text-sm text-slate-700 group-hover:text-violet-700 transition-colors">Ship from {{ $cityData['name'] }} to {{ $data['name'] }}</span>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Other countries --}}
            <div>
                <h3 class="text-lg font-bold text-slate-900 mb-4">Other Popular Destinations</h3>
                <div class="space-y-2">
                    @foreach($otherCountries as $c)
                    <a href="{{ route('ship-to', $c['slug']) }}" class="flex items-center gap-3 bg-white rounded-xl px-5 py-3 border border-slate-100 hover:border-violet-200 hover:shadow-sm transition-all group">
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-violet-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064"/></svg>
                        <span class="font-medium text-sm text-slate-700 group-hover:text-violet-700 transition-colors">Ship to {{ $c['name'] }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
