<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ExpressPeek') }}</title>

        @include('partials.favicon')

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            *, *::before, *::after { box-sizing: border-box; }
            body { font-family: 'Inter', sans-serif; color-scheme: light; margin: 0; }

            .auth-wrapper {
                display: flex;
                min-height: 100vh;
                background: #f8fafc;
            }

            /* ─── Left branded panel ─── */
            .auth-brand {
                display: none;
                position: relative;
                width: 50%;
                overflow: hidden;
                background: linear-gradient(135deg, #0f172a 0%, #1e293b 40%, #312e81 100%);
            }
            @media (min-width: 1024px) {
                .auth-brand { display: flex; align-items: center; justify-content: center; }
            }

            .auth-brand::before {
                content: '';
                position: absolute;
                inset: 0;
                background:
                    radial-gradient(ellipse at 20% 80%, rgba(99, 102, 241, 0.35) 0%, transparent 50%),
                    radial-gradient(ellipse at 80% 20%, rgba(139, 92, 246, 0.25) 0%, transparent 50%),
                    radial-gradient(ellipse at 50% 50%, rgba(79, 70, 229, 0.15) 0%, transparent 70%);
                z-index: 1;
            }

            .brand-content {
                position: relative;
                z-index: 2;
                padding: 3rem;
                max-width: 520px;
                text-align: center;
            }

            .brand-illustration {
                width: 100%;
                max-width: 360px;
                margin: 0 auto 2.5rem;
                border-radius: 2rem;
                opacity: 0.92;
                filter: drop-shadow(0 32px 64px rgba(0, 0, 0, 0.4));
                animation: heroFloat 6s ease-in-out infinite;
            }

            .brand-tagline {
                color: #ffffff;
                font-size: 1.875rem;
                font-weight: 800;
                line-height: 1.2;
                letter-spacing: -0.025em;
                margin-bottom: 1rem;
            }

            .brand-tagline span {
                background: linear-gradient(135deg, #a5b4fc, #c4b5fd, #e879f9);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .brand-subtitle {
                color: rgba(203, 213, 225, 0.8);
                font-size: 1rem;
                font-weight: 400;
                line-height: 1.6;
            }

            /* Floating grid pattern */
            .grid-pattern {
                position: absolute;
                inset: 0;
                z-index: 0;
                opacity: 0.04;
                background-image:
                    linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px);
                background-size: 60px 60px;
            }

            /* Floating orbs */
            .orb {
                position: absolute;
                border-radius: 50%;
                z-index: 1;
            }
            .orb-1 {
                width: 300px; height: 300px;
                top: -80px; right: -60px;
                background: rgba(139, 92, 246, 0.15);
                filter: blur(80px);
                animation: orbDrift 18s ease-in-out infinite alternate;
            }
            .orb-2 {
                width: 250px; height: 250px;
                bottom: -50px; left: -40px;
                background: rgba(99, 102, 241, 0.2);
                filter: blur(70px);
                animation: orbDrift 22s ease-in-out infinite alternate-reverse;
            }

            /* Trust badges */
            .trust-badges {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 2rem;
                margin-top: 2.5rem;
                padding-top: 2rem;
                border-top: 1px solid rgba(255, 255, 255, 0.08);
            }

            .trust-badge {
                text-align: center;
            }

            .trust-badge-value {
                font-size: 1.5rem;
                font-weight: 800;
                color: #ffffff;
            }

            .trust-badge-label {
                font-size: 0.75rem;
                color: rgba(203, 213, 225, 0.6);
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                margin-top: 0.125rem;
            }

            /* ─── Right form panel ─── */
            .auth-form-panel {
                flex: 1;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 2rem 1.5rem;
                position: relative;
                overflow-y: auto;
                background:
                    radial-gradient(ellipse at 30% 0%, rgba(238, 242, 255, 0.6) 0%, transparent 50%),
                    radial-gradient(ellipse at 70% 100%, rgba(245, 243, 255, 0.5) 0%, transparent 50%),
                    #f8fafc;
            }

            .auth-form-container {
                width: 100%;
                max-width: 440px;
            }

            /* Logo */
            .auth-logo {
                display: flex;
                justify-content: center;
                margin-bottom: 2.5rem;
            }

            .auth-logo a {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 0.75rem 1.25rem;
                background: #ffffff;
                border-radius: 1rem;
                border: 1px solid rgba(226, 232, 240, 0.8);
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
                transition: all 0.3s ease;
                text-decoration: none;
            }

            .auth-logo a:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
            }

            .auth-logo img {
                height: 2rem;
                width: auto;
                object-fit: contain;
            }

            /* Card */
            .auth-card {
                background: #ffffff;
                border-radius: 1.5rem;
                padding: 2.5rem;
                border: 1px solid rgba(226, 232, 240, 0.6);
                box-shadow:
                    0 0 0 1px rgba(0, 0, 0, 0.02),
                    0 1px 3px rgba(0, 0, 0, 0.03),
                    0 12px 40px rgba(0, 0, 0, 0.04);
                position: relative;
                overflow: hidden;
            }

            .auth-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 3px;
                background: linear-gradient(90deg, #6366f1, #8b5cf6, #a855f7);
            }

            @media (min-width: 640px) {
                .auth-form-panel { padding: 3rem 2rem; }
                .auth-card { padding: 3rem; }
            }

            /* Footer */
            .auth-footer {
                margin-top: 2rem;
                text-align: center;
                font-size: 0.8125rem;
                color: #94a3b8;
                font-weight: 400;
            }

            /* Back to Home Link */
            .back-home-link {
                position: absolute;
                top: 2rem;
                left: 2rem;
                z-index: 10;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.625rem 1rem;
                background: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: 9999px;
                color: #ffffff;
                font-size: 0.875rem;
                font-weight: 600;
                text-decoration: none;
                backdrop-filter: blur(8px);
                -webkit-backdrop-filter: blur(8px);
                transition: all 0.3s ease;
            }

            .back-home-link:hover {
                background: rgba(255, 255, 255, 0.2);
                transform: translateX(-4px);
            }
            
            .back-home-link svg {
                width: 1.25rem;
                height: 1.25rem;
                transition: transform 0.3s ease;
            }
            
            .back-home-link:hover svg {
                transform: translateX(-2px);
            }

            /* Animations */
            @keyframes heroFloat {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-14px); }
            }
            @keyframes orbDrift {
                0% { transform: translate(0, 0) scale(1); }
                100% { transform: translate(40px, -30px) scale(1.15); }
            }
            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(16px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .fade-in-up {
                animation: fadeInUp 0.5s ease-out forwards;
            }
            .fade-in-up-delay-1 { animation-delay: 0.1s; opacity: 0; }
            .fade-in-up-delay-2 { animation-delay: 0.2s; opacity: 0; }
        </style>
    </head>
    <body class="antialiased">
        <div class="auth-wrapper">
            <!-- Left branded panel (desktop only) -->
            <div class="auth-brand">
                <a href="/" class="back-home-link">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    Back to Home
                </a>
                
                <div class="grid-pattern"></div>
                <div class="orb orb-1"></div>
                <div class="orb orb-2"></div>

                <div class="brand-content">
                    <img src="/images/express-delivery-plane.png" alt="Express Delivery" class="brand-illustration">

                    <h1 class="brand-tagline">
                        Ship Smarter with <span>ExpressPeek</span>
                    </h1>
                    <p class="brand-subtitle">
                        Compare rates, book shipments, and track packages — all from one powerful platform.
                    </p>

                    <div class="trust-badges">
                        <div class="trust-badge">
                            <div class="trust-badge-value">50+</div>
                            <div class="trust-badge-label">Carriers</div>
                        </div>
                        <div class="trust-badge">
                            <div class="trust-badge-value">190+</div>
                            <div class="trust-badge-label">Countries</div>
                        </div>
                        <div class="trust-badge">
                            <div class="trust-badge-value">24/7</div>
                            <div class="trust-badge-label">Support</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right form panel -->
            <div class="auth-form-panel">
                <div class="auth-form-container">
                    <!-- Logo -->
                    <div class="auth-logo fade-in-up">
                        <a href="/">
                            <img src="/images/express-peek-logo.webp" alt="ExpressPeek">
                        </a>
                    </div>

                    <!-- Card -->
                    <div class="auth-card fade-in-up fade-in-up-delay-1">
                        {{ $slot }}
                    </div>

                    <!-- Footer -->
                    <div class="auth-footer fade-in-up fade-in-up-delay-2">
                        &copy; {{ date('Y') }} ExpressPeek. All rights reserved.
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
