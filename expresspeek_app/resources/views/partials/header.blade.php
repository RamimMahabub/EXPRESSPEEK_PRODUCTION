{{-- ===== TOP UTILITY BAR ===== --}}
<div class="bg-gray-900 text-slate-500 text-xs py-2.5 px-6 hidden md:flex items-center justify-between">
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
<header x-data="{ mobileMenuOpen: false }" class="bg-white sticky top-0 z-50 border-b border-white/10" style="box-shadow:0 1px 4px rgba(0,0,0,0.07)">
    <div class="max-w-7xl mx-auto px-6 flex items-center justify-between h-16">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex items-center flex-shrink-0">
            <img src="/images/express-peek-logo.webp" alt="Express Peek" class="h-12 w-auto">
        </a>

        {{-- Nav Links --}}
        <nav class="hidden lg:flex items-center gap-1">
            <a href="{{ route('home') }}"
               class="px-4 py-2 text-sm font-semibold {{ request()->routeIs('home') ? 'text-violet-700' : 'text-gray-700' }} hover:text-violet-700 transition-colors rounded-lg hover:bg-violet-50">
                Home
            </a>

            <a href="{{ route('track') }}"
               class="px-4 py-2 text-sm font-semibold text-gray-700 hover:text-violet-700 transition-colors rounded-lg hover:bg-violet-50">
                Track
            </a>

            {{-- Ship Dropdown --}}
            <div class="relative" x-data="{ open: false, timer: null }" @mouseenter="clearTimeout(timer); open = true" @mouseleave="timer = setTimeout(() => open = false, 150)">
                <button class="px-4 py-2 text-sm font-semibold text-gray-700 hover:text-violet-700 transition-colors rounded-lg hover:bg-violet-50 flex items-center gap-1" @click="open = !open">
                    Ship
                    <svg class="w-3.5 h-3.5 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute top-full left-0 bg-white border border-gray-100 rounded-xl shadow-xl py-2 w-48 z-50" style="margin-top:4px">
                    @auth
                        @if(auth()->user()->isCustomer())
                            <a href="{{ route('customer.shipments.create') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-violet-50 hover:text-violet-700 transition-colors">Create Shipment</a>
                            <a href="{{ route('customer.shipments.index') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-violet-50 hover:text-violet-700 transition-colors">My Shipments</a>
                        @elseif(auth()->user()->isAgent())
                            <a href="{{ route('agent.shipments.create') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-violet-50 hover:text-violet-700 transition-colors">Create Shipment</a>
                            <a href="{{ route('agent.shipments.index') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-violet-50 hover:text-violet-700 transition-colors">My Shipments</a>
                        @elseif(auth()->user()->isAdmin())
                            <a href="{{ route('admin.shipments.create') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-violet-50 hover:text-violet-700 transition-colors">Create Shipment</a>
                            <a href="{{ route('admin.shipments.index') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-violet-50 hover:text-violet-700 transition-colors">All Shipments</a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-violet-50 hover:text-violet-700 transition-colors">Create Shipment</a>
                    @endauth
                    <a href="#" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-violet-50 hover:text-violet-700 transition-colors">Schedule Pickup</a>
                </div>
            </div>

            <a href="{{ route('quote') }}"
               class="px-4 py-2 text-sm font-semibold {{ request()->routeIs('quote') ? 'text-violet-700' : 'text-gray-700' }} hover:text-violet-700 transition-colors rounded-lg hover:bg-violet-50">
                Quote
            </a>

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
            {{-- Social Links --}}
            <div class="hidden lg:flex items-center gap-4 mr-3 pr-5 border-r border-slate-200">
                <a href="https://www.facebook.com/share/1FPx4tcpH3/?mibextid=wwXIfr" target="_blank" rel="noopener noreferrer" class="hover:scale-110 hover:-translate-y-0.5 transition-all duration-300 drop-shadow-sm hover:drop-shadow-md" title="Facebook">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" fill="#1877F2"/>
                        <path d="M16.671 15.542l.532-3.469h-3.328v-2.25c0-.949.465-1.874 1.956-1.874h1.514V5.002s-1.374-.235-2.686-.235c-2.741 0-4.533 1.662-4.533 4.669v2.638H7.078v3.469h3.047v8.385a12.09 12.09 0 003.75 0v-8.385h2.796z" fill="#ffffff"/>
                    </svg>
                </a>
                <a href="https://www.instagram.com/expresspeek?igsh=bmwwbWNlc2Z2dTc2" target="_blank" rel="noopener noreferrer" class="hover:scale-110 hover:-translate-y-0.5 transition-all duration-300 drop-shadow-sm hover:drop-shadow-md" title="Instagram">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="ig-grad1" x1="2%" y1="100%" x2="98%" y2="0%">
                                <stop offset="0%" stop-color="#ffb13b"/>
                                <stop offset="50%" stop-color="#fd5949"/>
                                <stop offset="100%" stop-color="#d6249f"/>
                            </linearGradient>
                        </defs>
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069z" fill="url(#ig-grad1)"/>
                        <path d="M12 7.115a4.885 4.885 0 100 9.77 4.885 4.885 0 000-9.77zm0 8.033a3.148 3.148 0 110-6.296 3.148 3.148 0 010 6.296zm3.176-7.83a1.157 1.157 0 11-2.314 0 1.157 1.157 0 012.314 0z" fill="#ffffff"/>
                        <path d="M12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069z" fill="#ffffff"/>
                    </svg>
                </a>
                <a href="https://wa.me/8801400659902" target="_blank" rel="noopener noreferrer" class="hover:scale-110 hover:-translate-y-0.5 transition-all duration-300 drop-shadow-sm hover:drop-shadow-md" title="WhatsApp">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="12" fill="#25D366"/>
                        <path d="M9.198 6.486c-.276-.613-.564-.625-.828-.636-.216-.009-.462-.01-.708-.01a1.353 1.353 0 00-.985.45c-.339.366-1.293 1.252-1.293 3.053 0 1.801 1.324 3.541 1.509 3.784.185.244 2.585 3.91 6.262 5.48.874.373 1.556.596 2.087.763.878.276 1.678.237 2.308.144.706-.104 2.155-.872 2.463-1.716.308-.845.308-1.567.215-1.717-.092-.15-.338-.244-.708-.426-.369-.183-2.155-1.054-2.493-1.176-.338-.122-.585-.183-.831.183-.246.366-.954 1.176-1.169 1.42-.215.244-.431.275-.8.092-.369-.183-1.538-.561-2.932-1.796-1.085-.96-1.817-2.146-2.032-2.512-.215-.366-.023-.565.161-.747.166-.164.369-.427.554-.64.185-.213.246-.366.369-.61.123-.244.062-.457-.031-.64-.092-.183-.831-1.986-1.139-2.716z" fill="#ffffff"/>
                    </svg>
                </a>
            </div>
            
            @auth
            {{-- Logged-in user dropdown --}}
            <div class="relative hidden lg:block" x-data="{ open: false, timer: null }" @mouseenter="clearTimeout(timer); open = true" @mouseleave="timer = setTimeout(() => open = false, 150)">
                <button class="flex items-center gap-2 px-4 py-2 rounded-xl border border-violet-200 bg-violet-50 hover:bg-violet-100 transition-colors text-sm font-semibold text-violet-700" @click="open = !open">
                    <div class="w-6 h-6 rounded-lg bg-gradient-to-br from-violet-500 to-blue-600 flex items-center justify-center text-white text-xs font-bold select-none">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    {{ auth()->user()->name }}
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 top-full bg-white border border-gray-100 rounded-xl shadow-xl py-2 w-52 z-50" style="margin-top:4px">
                    <div class="px-4 py-2.5 border-b border-gray-100">
                        <p class="text-xs font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">{{ auth()->user()->email }}</p>
                    </div>
                    @if(auth()->user()->isCustomer())
                    <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-violet-50 hover:text-violet-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Dashboard
                    </a>
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
                    <div class="border-t border-gray-100 mt-1 pt-1">
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
               class="hidden lg:inline-flex px-5 py-2.5 rounded-xl bg-gradient-to-r from-violet-600 to-blue-700 text-white text-sm font-bold hover:opacity-90 transition-opacity shadow-sm shadow-violet-500/30">
                Login
            </a>
            @endauth

            {{-- Mobile menu button --}}
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 -mr-2 text-gray-600 hover:text-violet-700 hover:bg-violet-50 rounded-lg transition-colors focus:outline-none">
                <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="mobileMenuOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="mobileMenuOpen" x-cloak x-transition class="lg:hidden border-t border-gray-100 bg-white absolute w-full shadow-lg">
        <div class="px-4 py-3 space-y-1">
            <a href="{{ route('home') }}" class="block px-4 py-2.5 text-base font-semibold text-gray-700 hover:text-violet-700 hover:bg-violet-50 rounded-lg">Home</a>
            <a href="{{ route('track') }}" class="block px-4 py-2.5 text-base font-semibold text-gray-700 hover:text-violet-700 hover:bg-violet-50 rounded-lg">Track</a>
            <a href="{{ route('quote') }}" class="block px-4 py-2.5 text-base font-semibold text-gray-700 hover:text-violet-700 hover:bg-violet-50 rounded-lg">Quote</a>
            <a href="#" class="block px-4 py-2.5 text-base font-semibold text-gray-700 hover:text-violet-700 hover:bg-violet-50 rounded-lg">Customer Service</a>
            <a href="{{ route('sourcing.create') }}" class="block px-4 py-2.5 text-base font-bold text-amber-700 hover:text-amber-900 hover:bg-amber-50 rounded-lg">🛒 Sourcing</a>
            
            @auth
            <div class="border-t border-gray-100 mt-2 pt-2 pb-1 space-y-1">
                <div class="px-4 py-2 mb-1 bg-violet-50/50 rounded-lg mx-2 border border-violet-100">
                    <p class="text-sm font-bold text-slate-900">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-500">{{ auth()->user()->email }}</p>
                </div>
                @if(auth()->user()->isCustomer())
                    <a href="{{ route('customer.dashboard') }}" class="block px-4 py-2.5 text-base font-semibold text-gray-700 hover:text-violet-700 hover:bg-violet-50 rounded-lg">Dashboard</a>
                    <a href="{{ route('customer.shipments.index') }}" class="block px-4 py-2.5 text-base font-semibold text-gray-700 hover:text-violet-700 hover:bg-violet-50 rounded-lg">My Shipments</a>
                @endif
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2.5 text-base font-semibold text-gray-700 hover:text-violet-700 hover:bg-violet-50 rounded-lg">Admin Panel</a>
                @endif
                @if(auth()->user()->isAgent())
                    <a href="{{ route('agent.dashboard') }}" class="block px-4 py-2.5 text-base font-semibold text-gray-700 hover:text-violet-700 hover:bg-violet-50 rounded-lg">Agent Panel</a>
                @endif
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2.5 text-base font-semibold text-gray-700 hover:text-violet-700 hover:bg-violet-50 rounded-lg">Profile Settings</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left block px-4 py-2.5 text-base font-semibold text-red-600 hover:bg-red-50 rounded-lg">Sign Out</button>
                </form>
            </div>
            @else
            <div class="border-t border-gray-100 mt-2 pt-2 pb-1 space-y-1">
                <a href="{{ route('login') }}" class="block px-4 py-2.5 text-base font-semibold text-violet-700 hover:bg-violet-50 rounded-lg">Sign In</a>
                <a href="{{ route('register') }}" class="block px-4 py-2.5 text-base font-semibold text-violet-700 hover:bg-violet-50 rounded-lg">Create Account</a>
            </div>
            @endauth

            <div class="border-t border-gray-100 mt-2 pt-3 pb-2 px-4 flex items-center gap-5">
                <a href="https://www.facebook.com/share/1FPx4tcpH3/?mibextid=wwXIfr" target="_blank" rel="noopener noreferrer">
                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" fill="#1877F2"/>
                        <path d="M16.671 15.542l.532-3.469h-3.328v-2.25c0-.949.465-1.874 1.956-1.874h1.514V5.002s-1.374-.235-2.686-.235c-2.741 0-4.533 1.662-4.533 4.669v2.638H7.078v3.469h3.047v8.385a12.09 12.09 0 003.75 0v-8.385h2.796z" fill="#ffffff"/>
                    </svg>
                </a>
                <a href="https://www.instagram.com/expresspeek?igsh=bmwwbWNlc2Z2dTc2" target="_blank" rel="noopener noreferrer">
                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="ig-grad1-mob" x1="2%" y1="100%" x2="98%" y2="0%">
                                <stop offset="0%" stop-color="#ffb13b"/>
                                <stop offset="50%" stop-color="#fd5949"/>
                                <stop offset="100%" stop-color="#d6249f"/>
                            </linearGradient>
                        </defs>
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069z" fill="url(#ig-grad1-mob)"/>
                        <path d="M12 7.115a4.885 4.885 0 100 9.77 4.885 4.885 0 000-9.77zm0 8.033a3.148 3.148 0 110-6.296 3.148 3.148 0 010 6.296zm3.176-7.83a1.157 1.157 0 11-2.314 0 1.157 1.157 0 012.314 0z" fill="#ffffff"/>
                        <path d="M12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069z" fill="#ffffff"/>
                    </svg>
                </a>
                <a href="https://wa.me/8801400659902" target="_blank" rel="noopener noreferrer">
                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="12" fill="#25D366"/>
                        <path d="M9.198 6.486c-.276-.613-.564-.625-.828-.636-.216-.009-.462-.01-.708-.01a1.353 1.353 0 00-.985.45c-.339.366-1.293 1.252-1.293 3.053 0 1.801 1.324 3.541 1.509 3.784.185.244 2.585 3.91 6.262 5.48.874.373 1.556.596 2.087.763.878.276 1.678.237 2.308.144.706-.104 2.155-.872 2.463-1.716.308-.845.308-1.567.215-1.717-.092-.15-.338-.244-.708-.426-.369-.183-2.155-1.054-2.493-1.176-.338-.122-.585-.183-.831.183-.246.366-.954 1.176-1.169 1.42-.215.244-.431.275-.8.092-.369-.183-1.538-.561-2.932-1.796-1.085-.96-1.817-2.146-2.032-2.512-.215-.366-.023-.565.161-.747.166-.164.369-.427.554-.64.185-.213.246-.366.369-.61.123-.244.062-.457-.031-.64-.092-.183-.831-1.986-1.139-2.716z" fill="#ffffff"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</header>
