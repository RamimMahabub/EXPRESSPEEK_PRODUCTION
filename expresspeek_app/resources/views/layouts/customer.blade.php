<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Dynamic SEO Meta Tags --}}
    <title>@yield('seo_title', 'ExpressPeek | Send Parcels & Courier from Bangladesh to 220+ Countries')</title>
    <meta name="description" content="@yield('seo_description', 'Ship parcels, documents, and cargo from Bangladesh to the USA, UK, Canada & 220+ countries. Trusted courier service for Bangladeshi expats and businesses. Get an instant quote.')">
    <meta name="robots" content="@yield('robots', 'index, follow')">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    {{-- Open Graph --}}
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('seo_title', 'ExpressPeek | Send Parcels & Courier from Bangladesh to 220+ Countries')">
    <meta property="og:description" content="@yield('seo_description', 'Ship parcels, documents, and cargo from Bangladesh to the USA, UK, Canada & 220+ countries. Trusted courier service for Bangladeshi expats and businesses.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="ExpressPeek">
    <meta property="og:locale" content="{{ app()->getLocale() === 'bn' ? 'bn_BD' : 'en_US' }}">
    <meta property="og:image" content="@yield('og_image', asset('images/express-peek-logo.webp'))">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('seo_title', 'ExpressPeek | Send Parcels & Courier from Bangladesh to 220+ Countries')">
    <meta name="twitter:description" content="@yield('seo_description', 'Ship parcels, documents, and cargo from Bangladesh to the USA, UK, Canada & 220+ countries.')">

    {{-- Geo Meta Tags for Bangladesh --}}
    <meta name="geo.region" content="BD">
    <meta name="geo.placename" content="Dhaka">
    <meta name="geo.position" content="23.7276;90.4178">
    <meta name="ICBM" content="23.7276, 90.4178">

    {{-- hreflang for multilingual SEO --}}
    @hasSection('hreflang')
        @yield('hreflang')
    @else
        <link rel="alternate" hreflang="en" href="{{ url()->current() }}">
        <link rel="alternate" hreflang="x-default" href="{{ url()->current() }}">
    @endif

    @stack('seo')

    @include('partials.favicon')

    <!-- Inter Font -->
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
<body class="bg-white text-slate-900 antialiased">

{{-- ===== MAIN HEADER ===== --}}
@include('partials.header')

{{-- Page Content --}}
@yield('content')

{{-- ===== FOOTER ===== --}}
@include('partials.footer')

@stack('scripts')
@include('components.quote-modal')

{{-- Schema.org Structured Data --}}
@include('partials.schema')
@stack('schema')
</body>
</html>
