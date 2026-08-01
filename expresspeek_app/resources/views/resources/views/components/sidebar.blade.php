<aside id="sidebar"
    class="relative flex flex-col w-64 min-h-screen bg-gray-900 border-r border-white/10 transition-all duration-300 flex-shrink-0">

    {{-- Logo --}}
    <div class="px-4 py-4 border-b border-white/10 bg-gray-800/40">
        <a href="{{ route('admin.dashboard') }}" class="block rounded-xl bg-white/95 overflow-hidden h-28">
            <img src="/images/express-peek-logo.webp" alt="ExpressPeak" class="w-full h-full object-cover">
        </a>
    </div>

    {{-- Role Badge --}}
    <div class="px-6 py-3">
        @php $role = auth()->user()->getRoleNames()->first() ?? 'guest'; @endphp
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium
            @if($role === 'admin') bg-violet-900/50 text-violet-300 border border-violet-700/50
            @elseif($role === 'agent') bg-blue-900/50 text-blue-300 border border-blue-700/50
            @else bg-emerald-900/50 text-emerald-300 border border-emerald-700/50
            @endif">
            <span class="w-1.5 h-1.5 rounded-full
                @if($role === 'admin') bg-violet-400
                @elseif($role === 'agent') bg-blue-400
                @else bg-emerald-400
                @endif"></span>
            {{ ucfirst($role) }}
        </span>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-4 pb-4 overflow-y-auto">

        {{-- ---- ADMIN MENU ---- --}}
        @if(auth()->user()->isAdmin())
        <div class="mb-2">
            <p class="px-2 mb-2 text-xs font-semibold uppercase tracking-widest text-slate-500">Overview</p>
            <x-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')">
                <x-slot:icon>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </x-slot:icon>
                Dashboard
            </x-nav-link>
        </div>
        <div class="mb-2">
            <p class="px-2 mb-2 text-xs font-semibold uppercase tracking-widest text-slate-500">Management</p>
            <x-nav-link href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.*')">
                <x-slot:icon>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </x-slot:icon>
                Users
            </x-nav-link>
            <x-nav-link href="{{ route('admin.shipments.index') }}" :active="request()->routeIs('admin.shipments.*')">
                <x-slot:icon>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </x-slot:icon>
                Shipments
            </x-nav-link>
            <x-nav-link href="{{ route('admin.rates.import.create') }}" :active="request()->routeIs('admin.rates.import.*')">
                <x-slot:icon>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 16v-8m0 0l-3 3m3-3l3 3m6 5a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h4l2-2h2l2 2h4a2 2 0 012 2v9z"/>
                </x-slot:icon>
                Rate Upload
            </x-nav-link>
        </div>
        @endif

        {{-- ---- CUSTOMER MENU ---- --}}
        @if(auth()->user()->isCustomer())
        <div class="mb-2">
            <p class="px-2 mb-2 text-xs font-semibold uppercase tracking-widest text-slate-500">My Account</p>
            <x-nav-link href="{{ route('customer.dashboard') }}" :active="request()->routeIs('customer.dashboard')">
                <x-slot:icon>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </x-slot:icon>
                Dashboard
            </x-nav-link>
            <x-nav-link href="{{ route('customer.shipments.index') }}" :active="request()->routeIs('customer.shipments.*')">
                <x-slot:icon>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </x-slot:icon>
                My Shipments
            </x-nav-link>
        </div>
        @endif

        {{-- ---- AGENT MENU ---- --}}
        @if(auth()->user()->isAgent())
        {{-- Create Shipment CTA --}}
        <div class="mb-4">
            <a href="{{ route('agent.shipments.create') }}"
                class="flex items-center justify-center gap-2 w-full neon-button text-white font-semibold text-sm rounded-xl px-4 py-2.5 transition-all hover:scale-105 shadow-lg shadow-violet-900/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create Shipment
            </a>
        </div>
        <div class="mb-2">
            <p class="px-2 mb-2 text-xs font-semibold uppercase tracking-widest text-slate-500">Deliveries</p>
            <x-nav-link href="{{ route('agent.address-book.page') }}" :active="request()->routeIs('agent.address-book.page')">
                <x-slot:icon>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v11.494m0 0A11.953 11.953 0 017.5 20.5a11.953 11.953 0 01-4.5-1.753V5.253a11.953 11.953 0 004.5 1.753A11.953 11.953 0 0012 5.253m0 12.494a11.953 11.953 0 014.5 1.753A11.953 11.953 0 0021 16.747V5.253a11.953 11.953 0 00-4.5 1.753A11.953 11.953 0 0012 5.253m0 0V4.5"/>
                </x-slot:icon>
                Address Book
            </x-nav-link>
            <x-nav-link href="{{ route('agent.dashboard') }}" :active="request()->routeIs('agent.dashboard')">
                <x-slot:icon>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </x-slot:icon>
                Dashboard
            </x-nav-link>
            <x-nav-link href="{{ route('agent.shipments.index') }}" :active="request()->routeIs('agent.shipments.index') || request()->routeIs('agent.shipments.edit')">
                <x-slot:icon>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </x-slot:icon>
                Delivery Queue
            </x-nav-link>
        </div>
        @endif


        {{-- Common --}}
        <div class="mt-4 border-t border-white/10 pt-4">
            <x-nav-link href="{{ route('profile.edit') }}" :active="request()->routeIs('profile.*')">
                <x-slot:icon>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </x-slot:icon>
                My Profile
            </x-nav-link>
        </div>
    </nav>

    {{-- Logout --}}
    <div class="px-4 py-4 border-t border-white/10">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-slate-400 hover:bg-red-900/20 hover:text-red-400 transition-colors duration-200 group">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Sign Out
            </button>
        </form>
    </div>
</aside>
