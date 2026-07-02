<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ExpressPeek') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Inter', sans-serif; }
        </style>
    </head>
    <body class="text-gray-100 antialiased" style="background: #0b1220;">
        <div class="min-h-screen relative overflow-hidden" style="background: radial-gradient(circle at top left, rgba(99,102,241,0.28), transparent 35%), radial-gradient(circle at top right, rgba(14,165,233,0.16), transparent 28%), linear-gradient(135deg, #0b1220 0%, #111827 55%, #090d16 100%);">
            <div class="absolute inset-0 opacity-35" style="background-image: linear-gradient(rgba(148,163,184,0.08) 1px, transparent 1px), linear-gradient(90deg, rgba(148,163,184,0.08) 1px, transparent 1px); background-size: 48px 48px;"></div>

            <div class="relative mx-auto flex min-h-screen w-full max-w-7xl items-center px-4 py-8 sm:px-6 lg:px-8">
                <div class="grid w-full grid-cols-1 overflow-hidden rounded-[2rem] border border-white/10 shadow-[0_30px_100px_rgba(0,0,0,0.45)] backdrop-blur-xl lg:grid-cols-[1.15fr_0.85fr]" style="background: rgba(15, 23, 42, 0.88);">
                    <div class="relative flex flex-col justify-between overflow-hidden px-8 py-10 sm:px-12 lg:px-14 lg:py-14" style="background: linear-gradient(135deg, rgba(91,33,182,0.28), rgba(15,23,42,0.28), rgba(14,165,233,0.12));">
                        <div class="absolute inset-0"></div>
                        <div class="relative z-10 flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl shadow-lg shadow-violet-950/30" style="background: rgba(255,255,255,0.95);">
                                <img src="/images/express-peek-logo.webp" alt="ExpressPeek" class="h-9 w-9 object-contain">
                            </div>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.3em] text-cyan-200/80">ExpressPeek</p>
                                <p class="text-sm text-slate-300">Shipping platform</p>
                            </div>
                        </div>

                        <div class="relative z-10 max-w-xl py-16 lg:py-24">
                            <p class="mb-4 text-xs font-bold uppercase tracking-[0.35em] text-violet-200/80">Track, quote, ship</p>
                            <h1 class="text-4xl font-black leading-tight text-white sm:text-5xl lg:text-6xl">
                                One account.
                                <span class="block bg-gradient-to-r from-violet-300 to-cyan-300 bg-clip-text text-transparent">Three ways to ship.</span>
                            </h1>
                            <p class="mt-5 max-w-lg text-sm leading-6 text-slate-300 sm:text-base">
                                Log in, create an account, or continue without login to start a shipment from the same ExpressPeek flow.
                            </p>

                            <div class="mt-8 grid max-w-lg gap-3 sm:grid-cols-3">
                                <div class="rounded-2xl border border-white/10 bg-white/8 px-4 py-4 backdrop-blur">
                                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Login</p>
                                    <p class="mt-2 text-sm text-slate-200">Return users land back on their saved flow.</p>
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-white/8 px-4 py-4 backdrop-blur">
                                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Create</p>
                                    <p class="mt-2 text-sm text-slate-200">New accounts stay connected to the shipment.</p>
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-white/8 px-4 py-4 backdrop-blur">
                                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Guest</p>
                                    <p class="mt-2 text-sm text-slate-200">Continue immediately, then sign in later if needed.</p>
                                </div>
                            </div>
                        </div>

                        <div class="relative z-10 flex flex-wrap gap-3 text-xs text-slate-400">
                            <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1.5">Fast shipping quotes</span>
                            <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1.5">Live carrier selection</span>
                            <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1.5">Customer / agent routing</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-center px-6 py-10 sm:px-8 lg:px-10" style="background: rgba(2, 6, 23, 0.72);">
                        <div class="w-full max-w-md rounded-[1.75rem] border border-white/10 p-6 shadow-2xl shadow-black/40 sm:p-8" style="background: rgba(15, 23, 42, 0.96);">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
