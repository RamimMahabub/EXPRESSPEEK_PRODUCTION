<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="ExpressPeek — Your intelligent logistics aggregation platform. Fast, reliable, trackable.">

    <title>{{ config('app.name', 'ExpressPeek') }} — @yield('title', 'Dashboard')</title>

    @include('partials.favicon')

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="role-panel h-full bg-slate-50 text-slate-800 antialiased">

@if(auth()->check())

<div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

    {{-- Mobile Sidebar Backdrop --}}
    <div x-show="sidebarOpen" style="display: none;" class="fixed inset-0 z-40 bg-slate-900/35 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false" x-transition.opacity></div>

    {{-- Sidebar --}}
    @include('components.sidebar')

    {{-- Main Content Area --}}
    <div class="flex flex-col flex-1 overflow-hidden min-w-0">

        {{-- Top Navbar --}}
        @include('components.navbar')

        {{-- Page Content --}}
        <main class="panel-canvas panel-content flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="mb-5 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl shadow-sm" role="alert">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl shadow-sm" role="alert">
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

@else

<div class="min-h-screen bg-slate-50 text-slate-800">
    <header class="flex items-center justify-between px-6 py-4 bg-white/90 backdrop-blur border-b border-slate-200">
        <div class="flex items-center gap-3">
            <img src="/images/express-peek-logo.webp" alt="ExpressPeek" class="h-10 w-auto rounded-lg bg-white/95 p-1">
            <div>
                <p class="text-sm font-semibold text-slate-900">ExpressPeek</p>
                <p class="text-xs text-slate-500">Continue without login</p>
            </div>
        </div>
        <div class="flex items-center gap-3 text-sm">
            <a href="{{ route('login') }}" class="rounded-xl border border-slate-300 px-4 py-2 text-slate-700 hover:bg-slate-50 transition-colors">Log in</a>
            <a href="{{ route('register') }}" class="rounded-xl bg-violet-600 px-4 py-2 text-white hover:bg-violet-700 transition-colors">Create account</a>
        </div>
    </header>

    <main class="panel-canvas panel-content min-h-[calc(100vh-73px)] p-6">
        @if (session('success'))
            <div class="mb-4 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl" role="alert">
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl" role="alert">
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
        @endif

        @yield('content')
    </main>
</div>

@stack('scripts')

@endif
@include('components.quote-modal')
</body>
</html>
