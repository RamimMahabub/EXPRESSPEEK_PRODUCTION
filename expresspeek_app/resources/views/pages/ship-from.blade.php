@extends('layouts.customer')

@section('seo_title', $data['seo_title'])
@section('seo_description', $data['seo_description'])

@section('content')
<main class="min-h-screen bg-slate-50">
    {{-- Hero --}}
    <section class="bg-gradient-to-br from-slate-900 via-violet-950 to-slate-900 py-16 md:py-24">
        <div class="max-w-5xl mx-auto px-6">
            <div class="inline-flex items-center gap-2 bg-white/10 border border-white/20 text-white text-xs font-bold px-4 py-2 rounded-full mb-6">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                🇧🇩 Ship from {{ $data['name'] }} ({{ $data['bangla_name'] }})
            </div>
            <h1 class="text-4xl md:text-5xl font-black text-white mb-5 leading-tight">
                International Shipping<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-violet-300 to-blue-400">from {{ $data['name'] }}, Bangladesh</span>
            </h1>
            <p class="text-slate-300 text-base max-w-2xl leading-relaxed mb-8">
                Send parcels, documents, and cargo from {{ $data['name'] }} to 220+ countries worldwide. Pickup available across {{ implode(', ', array_slice($data['areas'], 0, 4)) }} and more.
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('quote') }}"
                   class="inline-flex items-center gap-2 px-7 py-4 rounded-2xl bg-gradient-to-r from-violet-600 to-blue-700 text-white font-bold text-sm hover:opacity-90 transition-opacity shadow-lg shadow-violet-500/30">
                    Get a Shipping Quote
                </a>
                <a href="{{ route('sourcing.create') }}"
                   class="inline-flex items-center gap-2 px-7 py-4 rounded-2xl border-2 border-white/20 text-white font-bold text-sm hover:border-white/40 transition-colors">
                    🛒 Shop from {{ $data['name'] }}
                </a>
            </div>
        </div>
    </section>

    {{-- Content --}}
    <section class="max-w-5xl mx-auto px-6 py-16">
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8 md:p-10">
            <h2 class="text-2xl font-black text-slate-900 mb-4">About Shipping from {{ $data['name'] }}</h2>
            <p class="text-slate-600 leading-relaxed text-base mb-8">{{ $data['description'] }}</p>

            {{-- Features --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                @foreach($data['features'] as $feature)
                <div class="flex items-center gap-3 bg-violet-50 rounded-xl px-5 py-4">
                    <svg class="w-5 h-5 text-violet-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span class="text-sm font-semibold text-slate-700">{{ $feature }}</span>
                </div>
                @endforeach
            </div>

            {{-- Pickup Areas --}}
            <h3 class="text-lg font-bold text-slate-900 mb-4">Pickup Areas in {{ $data['name'] }}</h3>
            <div class="flex flex-wrap gap-2 mb-8">
                @foreach($data['areas'] as $area)
                <span class="bg-slate-100 text-slate-700 text-sm font-medium px-4 py-2 rounded-full">{{ $area }}</span>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Destinations from this city --}}
    <section class="max-w-5xl mx-auto px-6 pb-8">
        <h2 class="text-xl font-black text-slate-900 mb-6">Popular Destinations from {{ $data['name'] }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            @foreach($countries as $c)
            <a href="{{ route('ship-to', $c['slug']) }}" class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm hover:shadow-md hover:border-violet-200 transition-all group">
                <h3 class="font-bold text-slate-900 text-base group-hover:text-violet-700 transition-colors">{{ $c['name'] }}</h3>
                <p class="text-sm text-slate-500 mt-1">{{ $c['transit_days'] }} business days</p>
                <span class="text-xs text-violet-600 font-semibold mt-2 block">View details →</span>
            </a>
            @endforeach
        </div>
    </section>

    {{-- Other Cities --}}
    @if($otherCities->count() > 0)
    <section class="max-w-5xl mx-auto px-6 py-12">
        <h3 class="text-lg font-bold text-slate-900 mb-4">Also Ship From</h3>
        <div class="flex flex-wrap gap-3">
            @foreach($otherCities as $c)
            <a href="{{ route('ship-from', $c['slug']) }}" class="bg-white rounded-xl px-5 py-3 border border-slate-100 hover:border-violet-200 shadow-sm hover:shadow-md transition-all text-sm font-medium text-slate-700 hover:text-violet-700">
                Ship from {{ $c['name'] }} →
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- CTA --}}
    <section class="max-w-5xl mx-auto px-6 pb-16">
        <div class="bg-gradient-to-r from-violet-600 to-blue-700 rounded-3xl p-8 md:p-12 text-center">
            <h2 class="text-2xl md:text-3xl font-black text-white mb-4">Ready to Ship from {{ $data['name'] }}?</h2>
            <p class="text-violet-200 max-w-lg mx-auto mb-6 text-sm leading-relaxed">Get an instant quote, compare carriers, and book your international shipment in minutes.</p>
            <a href="{{ route('quote') }}" class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-white text-violet-700 font-bold text-sm hover:bg-violet-50 transition-colors shadow-xl">
                Get a Quote Now →
            </a>
        </div>
    </section>
</main>
@endsection
