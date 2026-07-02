<header class="panel-topbar flex items-center justify-between gap-3 px-4 py-3.5 sm:px-6 bg-white/90 backdrop-blur-xl border-b border-slate-200 flex-shrink-0">

    <div class="flex items-center gap-4">
        {{-- Mobile Sidebar Toggle --}}
        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 -ml-2 rounded-lg text-slate-500 hover:text-violet-700 hover:bg-violet-50 transition-colors" aria-label="Open navigation">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        {{-- Page Title --}}
        <div>
            <h1 class="text-base sm:text-lg font-semibold text-slate-900">@yield('page-title', 'Dashboard')</h1>
            <p class="hidden sm:block text-xs text-slate-500">@yield('page-subtitle', config('app.name'))</p>
        </div>
    </div>

    {{-- Right side --}}
    <div class="flex items-center gap-2 sm:gap-3">

        {{-- Notifications placeholder --}}
        <button class="relative p-2 rounded-xl text-slate-500 hover:text-violet-700 hover:bg-violet-50 transition-colors" aria-label="Notifications">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-violet-600 rounded-full ring-2 ring-white"></span>
        </button>

        {{-- Action Buttons --}}
        <div class="hidden lg:flex items-center gap-1 pl-3 border-l border-slate-200">
            <a href="{{ route('home') }}" class="text-sm font-medium text-slate-600 hover:text-violet-700 px-3 py-2 rounded-lg hover:bg-violet-50 transition-colors">
                Back to Home
            </a>
            <form method="POST" action="{{ route('logout') }}" class="inline m-0 p-0">
                @csrf
                <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700 px-3 py-2 rounded-lg hover:bg-red-50 transition-colors">
                    Log Out
                </button>
            </form>
        </div>

        {{-- User Avatar & Info --}}
        <div class="flex items-center gap-3 pl-3 border-l border-slate-200">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-violet-600 to-indigo-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-sm shadow-violet-200">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div class="hidden md:block text-left max-w-40">
                <p class="text-sm font-medium text-slate-900 leading-none truncate">{{ auth()->user()->name }}</p>
                <p class="hidden xl:block text-xs text-slate-500 mt-1 truncate">{{ auth()->user()->email }}</p>
            </div>
        </div>
    </div>
</header>
