<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="ExpressPeak — Fast, reliable international logistics and courier services.">

    <title>ExpressPeak — @yield('title', 'Logistics & Shipping')</title>

    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { font-family: 'Inter', sans-serif; }
        .hero-bg {
            background-image: url('/images/hero-bg.png');
            background-size: cover;
            background-position: center top;
            background-repeat: no-repeat;
        }
        .nav-link-hover::after {
            content: '';
            display: block;
            height: 3px;
            background: #7c3aed;
            transform: scaleX(0);
            transition: transform 0.2s ease;
        }
        .nav-link-hover:hover::after { transform: scaleX(1); }
        .track-input:focus { outline: none; box-shadow: 0 0 0 3px rgba(124,58,237,0.25); }
        .service-card { transition: all 0.25s ease; }
        .service-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.12); }
        .fade-up { opacity: 0; transform: translateY(20px); animation: fadeUp 0.6s ease forwards; }
        @keyframes fadeUp { to { opacity: 1; transform: translateY(0); } }
        .fade-up-1 { animation-delay: 0.1s; }
        .fade-up-2 { animation-delay: 0.25s; }
        .fade-up-3 { animation-delay: 0.4s; }
    </style>
    @stack('head')
</head>
<body class="bg-white text-slate-100 antialiased">

{{-- ===== TOP UTILITY BAR ===== --}}
<div class="bg-gray-900 text-slate-400 text-xs py-2 px-6 hidden md:flex items-center justify-between">
    <div class="flex items-center gap-6">
        <a href="#" class="hover:text-white transition-colors flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Find a Service Point
        </a>
        <a href="#" class="hover:text-white transition-colors flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
            Support
        </a>
    </div>
    <div class="flex items-center gap-6">
        <span class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
            </svg>
            Bangladesh
        </span>
        @guest
            <a href="{{ route('login') }}" class="hover:text-white transition-colors">Sign In</a>
            <a href="{{ route('register') }}" class="hover:text-white transition-colors">Register</a>
        @endguest
        @auth
            <span class="text-slate-300">{{ auth()->user()->name }}</span>
        @endauth
    </div>
</div>

{{-- ===== MAIN NAVBAR ===== --}}
<header class="bg-white border-b border-white/10 sticky top-0 z-50" style="box-shadow: 0 1px 3px rgba(0,0,0,0.08)">
    <div class="max-w-7xl mx-auto px-6 flex items-center justify-between h-16">

        {{-- Logo --}}
        <a href="{{ route('customer.dashboard') }}" class="flex items-center flex-shrink-0">
            <img src="/images/express-peek-logo.webp" alt="Express Peek" class="h-12 w-auto">
        </a>

        {{-- Nav Links (Desktop) --}}
        <nav class="hidden md:flex items-center gap-1">
            <a href="#track" class="nav-link-hover px-4 py-2 text-sm font-semibold text-gray-700 hover:text-violet-700 transition-colors">Track</a>
            <div class="relative group">
                <button class="nav-link-hover px-4 py-2 text-sm font-semibold text-gray-700 hover:text-violet-700 transition-colors flex items-center gap-1">
                    Ship
                    <svg class="w-3.5 h-3.5 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="absolute top-full left-0 hidden group-hover:block bg-white border border-white/10 rounded-xl shadow-xl py-2 w-48 z-50">
                    <a href="{{ route('customer.shipments.index') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-violet-50 hover:text-violet-700 transition-colors">My Shipments</a>
                    <a href="#" data-open-quote-modal class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-violet-50 hover:text-violet-700 transition-colors">Get a Quote</a>
                    <a href="#" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-violet-50 hover:text-violet-700 transition-colors">Schedule Pickup</a>
                </div>
            </div>
            <a href="#" class="nav-link-hover px-4 py-2 text-sm font-semibold text-gray-700 hover:text-violet-700 transition-colors">Customer Service</a>
            <a href="{{ route('sourcing.create') }}" class="nav-link-hover px-4 py-2 text-sm font-bold text-amber-700 hover:text-amber-800 transition-colors flex items-center gap-1.5">
                🛒 Sourcing
            </a>
        </nav>

        {{-- Right Side --}}
        <div class="flex items-center gap-3">
            @auth
            <div class="relative group">
                <button class="flex items-center gap-2 px-4 py-2 rounded-xl border border-violet-200 bg-violet-50 hover:bg-violet-100 transition-colors text-sm font-semibold text-violet-700">
                    <div class="w-6 h-6 rounded-lg bg-gradient-to-br from-violet-500 to-blue-600 flex items-center justify-center text-white text-xs font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    My Account
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="absolute right-0 top-full hidden group-hover:block bg-white border border-white/10 rounded-xl shadow-xl py-2 w-52 z-50 mt-1">
                    <div class="px-4 py-2.5 border-b border-white/10">
                        <p class="text-xs font-semibold text-slate-100">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-400">{{ auth()->user()->email }}</p>
                    </div>
                    <a href="{{ route('customer.shipments.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-violet-50 hover:text-violet-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        My Shipments
                    </a>
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
            @endauth

            @guest
            <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl border border-violet-200 text-sm font-semibold text-violet-700 hover:bg-violet-50 transition-colors">
                Sign In
            </a>
            <a href="{{ route('register') }}" class="px-4 py-2 rounded-xl bg-gradient-to-r from-violet-600 to-blue-700 text-sm font-semibold text-white hover:opacity-90 transition-opacity">
                Register
            </a>
            @endguest
        </div>
    </div>
</header>

{{-- Page Content --}}
@yield('content')

{{-- ===== FOOTER ===== --}}
<footer class="bg-gray-900 text-slate-400">
    <div class="max-w-7xl mx-auto px-6 py-14">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">
            {{-- Brand --}}
            <div class="md:col-span-1">
                <div class="flex items-center gap-2.5 mb-4">
                    <img src="/images/express-peek-logo.webp" alt="Express Peek" class="w-56 md:w-72 h-auto object-contain">
                </div>
                <p class="text-sm leading-relaxed">Your intelligent logistics partner. Fast, reliable, and trackable deliveries worldwide.</p>
            </div>

            {{-- Shipping --}}
            <div>
                <h4 class="text-white text-sm font-semibold mb-4">Shipping</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="#" class="hover:text-white transition-colors">Ship Now</a></li>
                    <li><a href="#" data-open-quote-modal class="hover:text-white transition-colors">Get a Quote</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Schedule Pickup</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Find Service Points</a></li>
                </ul>
            </div>

            {{-- Tracking --}}
            <div>
                <h4 class="text-white text-sm font-semibold mb-4">Tracking</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="#track" class="hover:text-white transition-colors">Track a Shipment</a></li>
                    <li><a href="{{ route('customer.shipments.index') }}" class="hover:text-white transition-colors">My Shipments</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Delivery Notifications</a></li>
                </ul>
            </div>

            {{-- Support --}}
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
            <div class="flex items-center gap-5">
                <a href="#" class="hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>
                <a href="#" class="hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                </a>
                <a href="#" class="hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                </a>
            </div>
        </div>
    </div>
</footer>

@stack('scripts')
</body>
</html>
