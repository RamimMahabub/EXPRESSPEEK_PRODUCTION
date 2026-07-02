<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="ExpressPeak — Fast, reliable international logistics and courier services.">
    <title>ExpressPeak — Logistics & Shipping</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { font-family: 'Inter', sans-serif; }

        .hero-bg {
            background-color: #0f172a;
            overflow: hidden;
        }

        .hero-bg video {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        .service-card { transition: all 0.25s ease; }
        .service-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.10); }

        .drop-menu { display: none; }
        .drop-trigger:hover .drop-menu,
        .drop-trigger:focus-within .drop-menu { display: block; }

        @keyframes fadeUp { to { opacity: 1; transform: translateY(0); } }
        .fade-up { opacity: 0; transform: translateY(24px); animation: fadeUp 0.65s ease forwards; }
        .fade-up-1 { animation-delay: 0.05s; }
        .fade-up-2 { animation-delay: 0.2s; }
        .fade-up-3 { animation-delay: 0.35s; }
    </style>
</head>
<body class="bg-white text-slate-100 antialiased">

{{-- ===== TOP UTILITY BAR ===== --}}
<div class="bg-gray-900 text-slate-400 text-xs py-2.5 px-6 hidden md:flex items-center justify-between">
    <div class="flex items-center gap-6">
        <a href="#" class="hover:text-white transition-colors flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Find a Service Point
        </a>
        <a href="#" class="hover:text-white transition-colors flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            Support
        </a>
    </div>
    <div class="flex items-center gap-1.5">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
        Bangladesh
    </div>
</div>

{{-- ===== MAIN NAVBAR ===== --}}
<header class="bg-white sticky top-0 z-50 border-b border-white/10" style="box-shadow:0 1px 4px rgba(0,0,0,0.07)">
    <div class="max-w-7xl mx-auto px-6 flex items-center justify-between h-16">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex items-center flex-shrink-0">
            <img src="/images/express-peek-logo.webp" alt="Express Peek" class="h-12 w-auto">
        </a>

        {{-- Nav Links --}}
        <nav class="hidden md:flex items-center gap-1">
            <a href="#track"
               class="px-4 py-2 text-sm font-semibold text-gray-700 hover:text-violet-700 transition-colors rounded-lg hover:bg-violet-50">
                Track
            </a>

            {{-- Ship Dropdown --}}
            <div class="relative drop-trigger">
                <button class="px-4 py-2 text-sm font-semibold text-gray-700 hover:text-violet-700 transition-colors rounded-lg hover:bg-violet-50 flex items-center gap-1">
                    Ship
                    <svg class="w-3.5 h-3.5 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="drop-menu absolute top-full left-0 mt-1 bg-white border border-white/10 rounded-xl shadow-xl py-2 w-48 z-50">
                    @auth
                    <a href="{{ route('customer.shipments.index') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-violet-50 hover:text-violet-700 transition-colors">My Shipments</a>
                    @endauth
                    <a href="#" data-open-quote-modal class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-violet-50 hover:text-violet-700 transition-colors">Get a Quote</a>
                    <a href="#" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-violet-50 hover:text-violet-700 transition-colors">Schedule Pickup</a>
                </div>
            </div>

            <a href="#"
               class="px-4 py-2 text-sm font-semibold text-gray-700 hover:text-violet-700 transition-colors rounded-lg hover:bg-violet-50">
                Customer Service
            </a>
            <a href="{{ route('sourcing.create') }}"
               class="px-4 py-2 text-sm font-bold text-amber-700 hover:text-amber-900 transition-colors rounded-lg hover:bg-amber-50 flex items-center gap-1.5">
                🛒 Sourcing
            </a>
        </nav>

        {{-- Right: Login or My Account --}}
        <div class="flex items-center gap-3">
            @auth
            {{-- Logged-in user dropdown --}}
            <div class="relative drop-trigger">
                <button class="flex items-center gap-2 px-4 py-2 rounded-xl border border-violet-200 bg-violet-50 hover:bg-violet-100 transition-colors text-sm font-semibold text-violet-700">
                    <div class="w-6 h-6 rounded-lg bg-gradient-to-br from-violet-500 to-blue-600 flex items-center justify-center text-white text-xs font-bold select-none">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    {{ auth()->user()->name }}
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="drop-menu absolute right-0 top-full mt-1 bg-white border border-white/10 rounded-xl shadow-xl py-2 w-54 z-50 w-52">
                    <div class="px-4 py-2.5 border-b border-white/10">
                        <p class="text-xs font-semibold text-slate-100">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ auth()->user()->email }}</p>
                    </div>
                    @if(auth()->user()->isCustomer())
                    <a href="{{ route('customer.shipments.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-violet-50 hover:text-violet-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        My Shipments
                    </a>
                    @endif
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-violet-50 hover:text-violet-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Admin Panel
                    </a>
                    @endif
                    @if(auth()->user()->isAgent())
                    <a href="{{ route('agent.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-violet-50 hover:text-violet-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Agent Panel
                    </a>
                    @endif
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-violet-50 hover:text-violet-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Profile Settings
                    </a>
                    <div class="border-t border-white/10 mt-1 pt-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors text-left">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @else
            {{-- Guest: just Login button --}}
            <a href="{{ route('login') }}"
               class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-violet-600 to-blue-700 text-white text-sm font-bold hover:opacity-90 transition-opacity shadow-sm shadow-violet-500/30">
                Login
            </a>
            @endauth
        </div>
    </div>
</header>

{{-- ===== HERO ===== --}}
<section class="hero-bg relative min-h-[500px] flex items-center" id="track">
    <video class="pointer-events-none" autoplay muted loop playsinline preload="metadata" poster="/images/hero-bg.png" aria-hidden="true">
        <source src="/videos/cargo-ship-hero-extended.mp4" type="video/mp4">
    </video>
    <div class="absolute inset-0 pointer-events-none bg-gradient-to-r from-gray-900/88 via-gray-900/55 to-transparent"></div>
    <div class="relative max-w-7xl mx-auto px-6 py-20 w-full">
        <div class="max-w-xl">
            <p class="text-violet-300 text-xs font-bold uppercase tracking-widest mb-3 fade-up fade-up-1">ExpressPeak Logistics Platform</p>
            <h1 class="text-4xl md:text-5xl font-black text-white leading-tight mb-4 fade-up fade-up-1">
                Track Your<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-blue-400">Shipment</span>
            </h1>
            <p class="text-slate-300 text-sm mb-8 fade-up fade-up-2 leading-relaxed">
                Enter your tracking number to get real-time updates on your package — anywhere in the world.
            </p>

            {{-- Tracking Form --}}
            <form action="{{ route('track') }}" method="GET" class="fade-up fade-up-2">
                <div class="flex rounded-2xl overflow-hidden shadow-2xl ring-1 ring-white/10">
                    <input
                        id="tracking_number_input"
                        type="text"
                        name="tracking"
                        placeholder="Enter your tracking number(s)..."
                        class="flex-1 bg-white px-5 py-4 text-slate-100 text-sm placeholder-gray-400 focus:outline-none focus:ring-0 border-0"
                    >
                    <button type="submit"
                        class="bg-gradient-to-r from-violet-600 to-blue-700 hover:from-violet-700 hover:to-blue-800 text-white px-8 py-4 font-bold text-sm flex items-center gap-2 transition-all flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Track
                    </button>
                </div>
                <p class="text-xs text-slate-400 mt-2.5 pl-1">Tip: You can enter multiple tracking numbers separated by commas.</p>
            </form>

            @if(session('error'))
            <div class="mt-5 fade-up fade-up-3 bg-red-500/20 backdrop-blur-md border border-red-500/50 text-white shadow-2xl px-5 py-3.5 rounded-2xl text-sm flex items-center gap-3 font-medium">
                <div class="bg-red-500/30 p-1.5 rounded-full text-red-200">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                {{ session('error') }}
            </div>
            @endif

            {{-- Quick recent shipments for logged-in customers --}}
            @auth
            @if($recentShipments->count() > 0)
            <div class="mt-5 fade-up fade-up-3">
                <p class="text-xs text-slate-400 mb-2">📦 Your recent orders — click to fill:</p>
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
<section x-data="{}" class="relative z-10 -mt-14 max-w-7xl mx-auto px-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-0 bg-white rounded-2xl shadow-2xl overflow-hidden border border-white/10">

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
                <span class="text-sm text-violet-600 font-semibold flex items-center gap-1 group-hover:gap-2 transition-all">
                    Get started <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </span>
            </div>
        </div>

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
                <span class="text-sm text-blue-600 font-semibold flex items-center gap-1 group-hover:gap-2 transition-all">
                    Calculate now <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </span>
            </div>
        </div>

        {{-- Shop from Bangladesh Card --}}
        <a href="{{ route('sourcing.create') }}" class="service-card p-7 border-r border-white/10 hover:bg-amber-50 cursor-pointer group block" id="sourcing-card">
            <div class="flex flex-col items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 group-hover:bg-amber-200 flex items-center justify-center transition-colors relative">
                    <span class="text-2xl">🛒</span>
                    <span class="absolute -top-1.5 -right-1.5 w-3.5 h-3.5 bg-red-500 rounded-full border-2 border-white animate-ping"></span>
                    <span class="absolute -top-1.5 -right-1.5 w-3.5 h-3.5 bg-red-500 rounded-full border-2 border-white"></span>
                </div>
                <div>
                    <div class="flex items-center gap-1.5 mb-1">
                        <h3 class="font-bold text-slate-100 text-base">Shop from Bangladesh</h3>
                        <span class="text-[10px] font-black bg-amber-500 text-white px-1.5 py-0.5 rounded-full uppercase tracking-wide">New</span>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed">Abroad? We'll find &amp; ship any Bangladeshi product to you.</p>
                </div>
                <span class="text-sm text-amber-700 font-semibold flex items-center gap-1 group-hover:gap-2 transition-all">
                    Request now <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </span>
            </div>
        </a>

        <div class="service-card p-7 hover:bg-emerald-50 cursor-pointer group">
            <div class="flex flex-col items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 group-hover:bg-emerald-200 flex items-center justify-center transition-colors">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-100 text-base mb-1">ExpressPeak for Business</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Shipping regularly? Get a business account and unlock premium benefits.</p>
                </div>
                <span class="text-sm text-emerald-600 font-semibold flex items-center gap-1 group-hover:gap-2 transition-all">
                    Learn more <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </span>
            </div>
        </div>
    </div>
</section>

{{-- ===== MY SHIPMENTS (logged-in customers with shipments) ===== --}}
@auth
@if($stats['total'] > 0)
<section class="max-w-7xl mx-auto px-6 mt-16">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-black text-slate-100">My Active Shipments</h2>
            <p class="text-sm text-slate-400 mt-1">Quick overview of your recent orders</p>
        </div>
        <a href="{{ route('customer.shipments.index') }}"
           class="px-5 py-2.5 rounded-xl border-2 border-violet-600 text-violet-700 text-sm font-bold hover:bg-violet-600 hover:text-white transition-all">
            View All →
        </a>
    </div>

    {{-- Stats row --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        @foreach([
            ['label'=>'Total','value'=>$stats['total'],'icon'=>'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4','from'=>'from-violet-500','to'=>'to-blue-600'],
            ['label'=>'Pending','value'=>$stats['pending'],'icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z','from'=>'from-amber-500','to'=>'to-orange-500'],
            ['label'=>'In Transit','value'=>$stats['in_transit'],'icon'=>'M13 10V3L4 14h7v7l9-11h-7z','from'=>'from-blue-500','to'=>'to-cyan-500'],
            ['label'=>'Delivered','value'=>$stats['delivered'],'icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z','from'=>'from-emerald-500','to'=>'to-teal-500'],
        ] as $s)
        <div class="bg-white rounded-2xl p-5 border border-white/10 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $s['from'] }} {{ $s['to'] }} flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $s['icon'] }}"/>
                </svg>
            </div>
            <p class="text-3xl font-black text-slate-100">{{ $s['value'] }}</p>
            <p class="text-xs text-slate-400 mt-0.5 font-medium">{{ $s['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Shipments list --}}
    <div class="bg-white rounded-2xl border border-white/10 shadow-sm overflow-hidden mb-16">
        @foreach($recentShipments as $shipment)
        <div class="flex items-center gap-5 px-6 py-4 border-b border-gray-50 hover:bg-gray-50 transition-colors last:border-b-0 group">
            <div class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center
                {{ $shipment->status === 'delivered' ? 'bg-emerald-100 text-emerald-600' : ($shipment->status === 'in_transit' ? 'bg-blue-100 text-blue-600' : ($shipment->status === 'out_for_delivery' ? 'bg-purple-100 text-purple-600' : 'bg-amber-100 text-amber-600')) }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @if($shipment->status === 'delivered')
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    @elseif(in_array($shipment->status, ['in_transit','out_for_delivery']))
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    @endif
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-3 mb-0.5">
                    <p class="text-sm font-bold text-slate-100 truncate">{{ $shipment->receiver_name }}</p>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold flex-shrink-0
                        {{ $shipment->status === 'delivered' ? 'bg-emerald-100 text-emerald-700' : ($shipment->status === 'in_transit' ? 'bg-blue-100 text-blue-700' : ($shipment->status === 'out_for_delivery' ? 'bg-purple-100 text-purple-700' : 'bg-amber-100 text-amber-700')) }}">
                        {{ $shipment->status_label }}
                    </span>
                </div>
                <div class="flex items-center gap-4 text-xs text-slate-400">
                    <span class="font-mono text-violet-600 font-semibold">{{ $shipment->tracking_number }}</span>
                    <span>→ {{ $shipment->receiver_city }}, {{ $shipment->receiver_country }}</span>
                    <span>{{ $shipment->created_at->diffForHumans() }}</span>
                </div>
            </div>
            <div class="hidden md:block text-right flex-shrink-0">
                <p class="text-sm font-semibold text-gray-700">{{ $shipment->weight }} kg</p>
                @if($shipment->estimated_delivery)
                <p class="text-xs text-slate-400 mt-0.5">ETA {{ $shipment->estimated_delivery->format('M d') }}</p>
                @endif
            </div>
            <svg class="w-4 h-4 text-slate-300 group-hover:text-violet-500 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </div>
        @endforeach
    </div>
</section>
@endif
@endauth

{{-- ===== SHOP FROM BANGLADESH — FULL PROMO SECTION ===== --}}
<section class="max-w-7xl mx-auto px-6 mt-20" id="sourcing">
    <div class="relative overflow-hidden rounded-3xl" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 45%, #4c1d95 100%)">

        {{-- Decorative pattern --}}
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: url(&quot;data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23fff' fill-opacity='1' fill-rule='evenodd'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/svg%3E&quot;);"></div>

        {{-- Glow orbs --}}
        <div class="absolute top-0 right-0 w-96 h-96 rounded-full opacity-20" style="background: radial-gradient(circle, #f59e0b, transparent 70%); transform: translate(30%, -30%)"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 rounded-full opacity-15" style="background: radial-gradient(circle, #a78bfa, transparent 70%); transform: translate(-20%, 20%)"></div>

        <div class="relative grid grid-cols-1 lg:grid-cols-2 gap-0 min-h-[420px]">

            {{-- Left: Text --}}
            <div class="flex flex-col justify-center px-10 md:px-14 py-14 lg:py-16">
                <div class="inline-flex items-center gap-2 bg-amber-500/20 border border-amber-400/30 text-amber-300 text-xs font-bold px-3.5 py-1.5 rounded-full mb-6 w-fit">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    🇧🇩 New Service — Shop from Bangladesh
                </div>

                <h2 class="text-3xl md:text-4xl lg:text-5xl font-black text-white leading-tight mb-5">
                    Shop from Bangladesh:<br>
                    <span class="text-transparent bg-clip-text" style="background-image: linear-gradient(90deg, #fbbf24, #f97316)">Sourcing & Shipping for Expats</span>
                </h2>

                <p class="text-indigo-200 text-base leading-relaxed mb-8 max-w-md">
                    Missing local Bangladeshi products? Living in the UK, USA, Australia, or Europe? Tell us what you need—whether it's traditional clothing, dry foods, or local goods. We will source it from any shop in Bangladesh and deliver it straight to your door.
                </p>

                {{-- Steps --}}
                <div class="space-y-3 mb-10">
                    @foreach([
                        ['num' => '1', 'text' => 'Submit your product request (name, link, or image)', 'color' => 'bg-amber-500'],
                        ['num' => '2', 'text' => 'We find it &amp; contact you on WhatsApp with price', 'color' => 'bg-violet-400'],
                        ['num' => '3', 'text' => 'Pay &amp; we ship it directly to your country', 'color' => 'bg-blue-400'],
                    ] as $step)
                    <div class="flex items-center gap-3.5">
                        <div class="w-7 h-7 rounded-full {{ $step['color'] }} flex items-center justify-center text-white text-xs font-black flex-shrink-0">{{ $step['num'] }}</div>
                        <p class="text-indigo-100 text-sm">{!! $step['text'] !!}</p>
                    </div>
                    @endforeach
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('sourcing.create') }}"
                       id="sourcing-cta-btn"
                       class="inline-flex items-center gap-2.5 px-7 py-4 rounded-2xl font-black text-sm text-white transition-all hover:scale-105"
                       style="background: linear-gradient(135deg, #f59e0b, #ef4444); box-shadow: 0 8px 25px rgba(245,158,11,0.4)">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        Submit a Sourcing Request
                    </a>
                    <a href="{{ route('sourcing.create') }}"
                       class="inline-flex items-center gap-2 px-7 py-4 rounded-2xl font-bold text-sm text-indigo-200 border border-indigo-400/30 hover:border-white/50 hover:text-white transition-all">
                        Learn more →
                    </a>
                </div>
            </div>

            {{-- Right: Feature Cards --}}
            <div class="flex items-center justify-center px-8 py-12 lg:py-0">
                <div class="grid grid-cols-2 gap-4 w-full max-w-sm">
                    @foreach([
                        ['icon' => '🔍', 'title' => 'We Find It', 'desc' => 'Any product from any shop in Bangladesh'],
                        ['icon' => '💬', 'title' => 'WhatsApp Updates', 'desc' => 'Real-time updates & price confirmation'],
                        ['icon' => '✈️', 'title' => 'Global Delivery', 'desc' => 'Shipped to 220+ countries worldwide'],
                        ['icon' => '🔒', 'title' => 'Pay After Quote', 'desc' => 'No payment until you approve the price'],
                    ] as $feat)
                    <div class="bg-white/10 border border-white/15 rounded-2xl p-4 backdrop-blur-sm hover:bg-white/15 transition-colors">
                        <div class="text-2xl mb-2">{{ $feat['icon'] }}</div>
                        <p class="text-white font-bold text-sm mb-1">{{ $feat['title'] }}</p>
                        <p class="text-indigo-300 text-xs leading-relaxed">{{ $feat['desc'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===== PROMO BANNER WITH AIRPLANE IMAGE ===== --}}
<section class="mt-8">
    <div class="flex items-stretch min-h-full bg-gradient-to-r from-violet-700 to-blue-800 overflow-hidden rounded-3xl shadow-lg mx-auto max-w-6xl">
        {{-- Text Content --}}
        <div class="flex items-center px-10 py-12 md:py-16 flex-1 min-w-0">
            <div>
                <div class="inline-flex items-center gap-2 bg-white/20 text-white text-xs font-semibold px-3 py-1 rounded-full mb-4">
                    <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span>
                    New for {{ date('Y') }}
                </div>
                <h2 class="text-2xl md:text-3xl font-black text-white mb-3 leading-tight">International Courier & Cargo Service from Bangladesh</h2>
                <p class="text-violet-200 text-sm md:text-base leading-relaxed mb-6 max-w-lg">
                    Send parcels, documents, and commercial goods from Dhaka to the USA, UK, Canada, and 220+ countries worldwide. Get instant tracking and transparent, affordable rates with Express Peek.
                </p>
                <a href="{{ route('register') }}"
                   class="inline-flex items-center gap-2 bg-white text-violet-700 font-bold text-sm px-6 py-3 rounded-xl hover:bg-violet-50 transition-colors shadow-lg">
                    Get Started Free
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
        
        {{-- Airplane Image --}}
        <div class="hidden md:flex items-center justify-end flex-1 overflow-hidden">
            <img src="/images/express-delivery-plane.png" alt="Express Delivery Plane" class="h-full w-full object-cover">
        </div>
    </div>
</section>

{{-- ===== SERVICES ===== --}}
<section class="max-w-7xl mx-auto px-6 py-20">
    <div class="text-center mb-14">
        <p class="text-violet-600 text-sm font-semibold uppercase tracking-widest mb-2">What We Offer</p>
        <h2 class="text-3xl md:text-4xl font-black text-slate-100 mb-3">Document and Parcel Shipping</h2>
        <p class="text-slate-400 max-w-xl mx-auto text-sm">For all shippers — individuals, SMBs, and enterprise businesses.</p>
    </div>

    <div class="max-w-3xl mx-auto grid grid-cols-1 gap-10 items-center">
        <div class="space-y-4">
            @foreach([
                ['icon'=>'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z','title'=>'Document Express','desc'=>'Time-sensitive documents delivered globally with full tracking and signature confirmation.','tag'=>'Fast','tc'=>'text-violet-600','bc'=>'bg-violet-100'],
                ['icon'=>'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4','title'=>'Parcel Delivery','desc'=>'Flexible shipping for packages of all sizes, with real-time tracking at every step.','tag'=>'Popular','tc'=>'text-blue-600','bc'=>'bg-blue-100'],
                ['icon'=>'M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18','title'=>'Freight Solutions','desc'=>'Heavy cargo, pallets, and bulk freight handled with precision by our carrier network.','tag'=>'Enterprise','tc'=>'text-emerald-600','bc'=>'bg-emerald-100'],
            ] as $svc)
            <div class="service-card flex items-start gap-4 p-5 bg-white rounded-2xl border border-white/10 shadow-sm">
                <div class="w-11 h-11 rounded-xl {{ $svc['bc'] }} flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 {{ $svc['tc'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <svg class="w-4 h-4 text-slate-300 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
            @endforeach
            <a href="{{ route('login') }}"
               class="block w-full text-center py-3.5 rounded-xl bg-gradient-to-r from-violet-600 to-blue-700 text-white font-bold text-sm hover:opacity-90 transition-opacity shadow-lg shadow-violet-500/20">
                @auth Ship a Package → @else Login to Ship a Package → @endauth
            </a>
        </div>
    </div>
</section>

{{-- ===== DECORATIVE IMAGE — PORT/CARGO ===== --}}
<section class="mb-0">
    <div class="relative w-full h-56 md:h-72 overflow-hidden bg-gradient-to-b from-gray-100 to-gray-50 rounded-3xl mx-auto max-w-6xl shadow-lg my-8">
        <img src="/images/port-cargo.jpg" alt="Global Port Network" class="w-full h-full object-cover">
    </div>
</section>

{{-- ===== WHY EXPRESSPEEAK ===== --}}
<section class="bg-gray-50 border-y border-white/10 py-20">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-black text-slate-100">Why Choose ExpressPeak?</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach([
                ['icon'=>'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064','title'=>'Global Network','desc'=>'Delivery to 220+ countries through our trusted carrier network.'],
                ['icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z','title'=>'Secure & Insured','desc'=>'Every shipment is tracked and backed by comprehensive insurance options.'],
                ['icon'=>'M13 10V3L4 14h7v7l9-11h-7z','title'=>'Express Speed','desc'=>'Same-day, next-day, and international express options for critical deliveries.'],
                ['icon'=>'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z','title'=>'24/7 Support','desc'=>'Our customer service team is available around the clock for any query.'],
            ] as $why)
            <div class="service-card bg-white rounded-2xl p-6 border border-white/10 shadow-sm">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-100 to-blue-100 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $why['icon'] }}"/>
                    </svg>
                </div>
                <h3 class="font-bold text-slate-100 mb-2 text-sm">{{ $why['title'] }}</h3>
                <p class="text-sm text-slate-400 leading-relaxed">{{ $why['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== CTA WITH DELIVERY DRIVER IMAGE — guests only ===== --}}
@guest
<section class="max-w-7xl mx-auto px-6 py-20">
    <div class="bg-gradient-to-br from-violet-600 via-violet-700 to-blue-800 rounded-3xl overflow-hidden relative flex items-stretch">
        {{-- Decorative Shapes --}}
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 right-0 w-96 h-96 rounded-full bg-white translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 left-0 w-72 h-72 rounded-full bg-white -translate-x-1/3 translate-y-1/3"></div>
        </div>
        
        {{-- Text Content --}}
        <div class="flex items-center px-10 py-16 flex-1 relative z-10">
            <div class="text-center md:text-left">
                <h2 class="text-3xl md:text-4xl font-black text-white mb-4">Ready to Ship Smarter?</h2>
                <p class="text-violet-200 max-w-lg mb-8 text-sm leading-relaxed">Join thousands who trust ExpressPeak for their logistics. Free to register.</p>
                <div class="flex items-center justify-center md:justify-start gap-4 flex-wrap">
                    <a href="{{ route('register') }}"
                       class="px-8 py-3.5 rounded-xl bg-white text-violet-700 font-bold text-sm hover:bg-violet-50 transition-colors shadow-xl">
                        Create Free Account
                    </a>
                    <a href="{{ route('login') }}"
                       class="px-8 py-3.5 rounded-xl border-2 border-white/30 text-white font-bold text-sm hover:border-white transition-colors">
                        Login
                    </a>
                </div>
            </div>
        </div>
        
        {{-- Delivery Driver Image --}}
        <div class="hidden md:flex items-center justify-end flex-1 overflow-hidden">
            <img src="/images/delivery-driver.png" alt="Trusted Delivery Service" class="h-full w-full object-cover">
        </div>
    </div>
</section>
@endguest

{{-- ===== FOOTER ===== --}}
<footer class="bg-gray-900 text-slate-400">
    <div class="max-w-7xl mx-auto px-6 py-14">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">
            <div>
                <div class="flex items-center gap-2.5 mb-4">
                    <img src="/images/express-peek-logo.webp" alt="Express Peek" class="w-56 md:w-72 h-auto object-contain">
                </div>
                <p class="text-sm leading-relaxed">Your intelligent logistics partner. Fast, reliable, and trackable deliveries worldwide.</p>
            </div>
            <div>
                <h4 class="text-white text-sm font-semibold mb-4">Shipping</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="#" class="hover:text-white transition-colors">Ship Now</a></li>
                    <li><a href="#" data-open-quote-modal class="hover:text-white transition-colors">Get a Quote</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Schedule Pickup</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Find Service Points</a></li>
                    <li><a href="{{ route('sourcing.create') }}" class="hover:text-amber-400 text-amber-500 font-semibold transition-colors">🛒 Shop from Bangladesh</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white text-sm font-semibold mb-4">Tracking</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="#track" class="hover:text-white transition-colors">Track a Shipment</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">My Shipments</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Delivery Notifications</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white text-sm font-semibold mb-4">Support</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="#" class="hover:text-white transition-colors">Customer Service</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Help Center</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Terms of Service</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Privacy Policy</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-white/10 pt-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="text-xs">© {{ date('Y') }} ExpressPeak Logistics. All rights reserved.</p>
        </div>
    </div>
</footer>

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
                                    <p class="text-3xl font-black text-slate-100 leading-none" x-text="formatPrice(results.recommended.total_price)"></p>
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

    const urlParams = new URLSearchParams(window.location.search);
    const t = urlParams.get('tracking');
    if (t) document.getElementById('tracking_number_input').value = t;
</script>
</body>
</html>
