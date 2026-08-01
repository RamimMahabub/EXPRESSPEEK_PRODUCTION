@extends('layouts.customer')

@section('title', 'Request Submitted — Sourcing')

@section('content')
<section class="min-h-[70vh] flex items-center justify-center px-6 py-20">
    <div class="max-w-lg w-full text-center">

        {{-- Animated Check --}}
        <div class="relative mx-auto w-28 h-28 mb-8">
            <div class="absolute inset-0 rounded-full bg-emerald-500/20 animate-ping"></div>
            <div class="relative w-28 h-28 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center shadow-2xl shadow-emerald-500/40 bounce-in">
                <svg class="w-14 h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
        </div>

        <h1 class="text-3xl md:text-4xl font-black text-slate-100 mb-3">Request Submitted! 🎉</h1>
        <p class="text-slate-400 text-lg mb-6 leading-relaxed">
            We've received your sourcing request. Our team will review it and reach out to you on WhatsApp within <strong class="text-gray-700">24 hours</strong>.
        </p>

        {{-- Reference Number --}}
        @if($ref)
        <div class="bg-violet-50 border-2 border-violet-200 rounded-2xl px-6 py-5 mb-8 inline-block w-full">
            <p class="text-xs font-bold text-violet-500 uppercase tracking-widest mb-1">Your Reference Number</p>
            <p class="text-3xl font-black text-violet-700 font-mono tracking-wider">{{ $ref }}</p>
            <p class="text-xs text-violet-400 mt-2">Save this number to track your request status.</p>
        </div>
        @endif

        {{-- What's Next --}}
        <div class="bg-white border border-white/10 rounded-2xl p-6 mb-8 text-left shadow-sm">
            <h3 class="font-bold text-slate-100 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                What happens next?
            </h3>
            <div class="space-y-3">
                @foreach([
                    ['icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z', 'text' => 'Our team searches for your product in Bangladesh', 'color' => 'violet'],
                    ['icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'text' => 'We contact you on WhatsApp with price and availability', 'color' => 'blue'],
                    ['icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'text' => 'After your approval, you pay and we purchase the item', 'color' => 'amber'],
                    ['icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'text' => 'We ship it directly to your destination with full tracking', 'color' => 'emerald'],
                ] as $step)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-{{ $step['color'] }}-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-{{ $step['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] }}"/>
                        </svg>
                    </div>
                    <p class="text-sm text-slate-500">{{ $step['text'] }}</p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- CTAs --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ route('sourcing.create') }}"
               class="w-full sm:w-auto px-6 py-3 rounded-xl bg-gradient-to-r from-violet-600 to-blue-700 text-white font-bold text-sm hover:opacity-90 transition-opacity shadow-lg shadow-violet-500/25">
                Submit Another Request
            </a>
            <a href="{{ route('customer.dashboard') }}"
               class="w-full sm:w-auto px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-700 font-bold text-sm hover:border-violet-300 hover:text-violet-700 transition-colors">
                Go to Homepage
            </a>
        </div>
    </div>
</section>

<style>
    @keyframes bounceIn { 0%{transform:scale(0.5);opacity:0} 70%{transform:scale(1.1)} 100%{transform:scale(1);opacity:1} }
    .bounce-in { animation: bounceIn 0.6s cubic-bezier(0.36,0.07,0.19,0.97) forwards; }
</style>
@endsection
