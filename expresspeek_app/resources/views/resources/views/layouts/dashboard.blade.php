<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="ExpressPeak — Your intelligent logistics aggregation platform. Fast, reliable, trackable.">

    <title>{{ config('app.name', 'ExpressPeak') }} — @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="h-full bg-gray-950 text-gray-100">

@if(auth()->check())

<div class="flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    @include('components.sidebar')

    {{-- Main Content Area --}}
    <div class="flex flex-col flex-1 overflow-hidden">

        {{-- Top Navbar --}}
        @include('components.navbar')

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto p-6 bg-gray-950">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="mb-4 flex items-center gap-3 bg-emerald-900/30 border border-emerald-500/40 text-emerald-300 px-4 py-3 rounded-xl" role="alert">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 flex items-center gap-3 bg-red-900/30 border border-red-500/40 text-red-300 px-4 py-3 rounded-xl" role="alert">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Slot --}}
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</div>

@else

<div class="min-h-screen bg-gray-950 text-gray-100">
    <header class="flex items-center justify-between px-6 py-4 bg-gray-900/80 backdrop-blur border-b border-white/10">
        <div class="flex items-center gap-3">
            <img src="/images/express-peek-logo.webp" alt="ExpressPeak" class="h-10 w-auto rounded-lg bg-white/95 p-1">
            <div>
                <p class="text-sm font-semibold text-white">ExpressPeak</p>
                <p class="text-xs text-slate-400">Continue without login</p>
            </div>
        </div>
        <div class="flex items-center gap-3 text-sm">
            <a href="{{ route('login') }}" class="rounded-xl border border-gray-700 px-4 py-2 text-gray-200 hover:bg-gray-800 transition-colors">Log in</a>
            <a href="{{ route('register') }}" class="rounded-xl bg-violet-600 px-4 py-2 text-white hover:bg-violet-500 transition-colors">Create account</a>
        </div>
    </header>

    <main class="p-6 bg-gray-950">
        @if (session('success'))
            <div class="mb-4 flex items-center gap-3 bg-emerald-900/30 border border-emerald-500/40 text-emerald-300 px-4 py-3 rounded-xl" role="alert">
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 flex items-center gap-3 bg-red-900/30 border border-red-500/40 text-red-300 px-4 py-3 rounded-xl" role="alert">
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
        @endif

        @yield('content')
    </main>
</div>

@stack('scripts')

@endif
</body>
</html>
