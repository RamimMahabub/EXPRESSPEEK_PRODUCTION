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
            [x-cloak] { display:none!important; }

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

            /* Premium authentication experience */
            .auth-brand { width:55%; align-items:flex-end; justify-content:flex-start; background:#06123f url('/images/tracking-dark-hero.webp') 63% center/cover no-repeat; }
            .auth-brand::before { z-index:1; background:linear-gradient(180deg,rgba(2,8,35,.12),rgba(3,10,43,.3) 42%,rgba(3,9,39,.97)),linear-gradient(90deg,rgba(3,9,39,.94),rgba(5,15,61,.4) 58%,rgba(5,15,61,.08)); }
            .auth-brand::after { content:''; position:absolute; z-index:1; inset:0; background:radial-gradient(circle at 22% 22%,rgba(116,87,255,.25),transparent 32%); pointer-events:none; }
            .brand-content { max-width:670px; padding:clamp(3rem,6vw,6rem); text-align:left; }
            .brand-illustration,.trust-badges,.legacy-brand-copy { display:none; }
            .brand-eyebrow { display:inline-flex; align-items:center; gap:.55rem; margin-bottom:1.15rem; padding:.5rem .8rem; border:1px solid rgba(255,255,255,.15); border-radius:999px; color:#dfe5ff; background:rgba(255,255,255,.08); backdrop-filter:blur(12px); font-size:.7rem; font-weight:800; letter-spacing:.12em; text-transform:uppercase; }
            .brand-eyebrow::before { content:''; width:.45rem; height:.45rem; border-radius:50%; background:#45dfa6; box-shadow:0 0 0 .3rem rgba(69,223,166,.12); }
            .brand-tagline { max-width:590px; margin:0; font-size:clamp(2.3rem,4vw,4.25rem); line-height:1.02; letter-spacing:-.055em; }
            .brand-subtitle { max-width:560px; margin:1.2rem 0 0; color:rgba(226,232,255,.78); font-size:1rem; }
            .auth-assurances { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:.75rem; margin-top:2rem; }
            .auth-assurance { padding:.9rem 1rem; border:1px solid rgba(255,255,255,.1); border-radius:1rem; color:#e9edff; background:rgba(255,255,255,.06); backdrop-filter:blur(14px); font-size:.75rem; font-weight:700; }
            .auth-assurance svg { display:block; width:1.15rem; height:1.15rem; margin-bottom:.55rem; color:#a99aff; }
            .auth-form-panel { background:radial-gradient(circle at 5% 5%,rgba(232,229,255,.9),transparent 32%),linear-gradient(145deg,#f8faff,#eef3fa); }
            .auth-form-container { max-width:480px; }
            .auth-logo { margin-bottom:1.4rem; }
            .auth-logo a,.auth-logo a:hover { border:0; background:transparent; box-shadow:none; }
            .auth-logo img { height:2.65rem; }
            .auth-card { padding:2.2rem; border:1px solid rgba(216,223,236,.9); border-radius:1.6rem; box-shadow:0 24px 70px rgba(28,42,86,.11),0 2px 8px rgba(28,42,86,.04); }
            .auth-card::before { height:4px; background:linear-gradient(90deg,#5b35e8,#7151f5,#2f76ef); }
            .auth-kicker { display:inline-flex; align-items:center; gap:.45rem; margin-bottom:.75rem; color:#6240e6; font-size:.69rem; font-weight:850; letter-spacing:.12em; text-transform:uppercase; }
            .auth-kicker::before { content:''; width:.42rem; height:.42rem; border-radius:50%; background:#6e48ef; box-shadow:0 0 0 .28rem rgba(110,72,239,.1); }
            .auth-heading { margin:0; color:#091735; font-size:1.75rem; line-height:1.15; font-weight:850; letter-spacing:-.035em; }
            .auth-subtitle { margin:.55rem 0 1.65rem; color:#697792; font-size:.9rem; line-height:1.65; }
            .auth-field { margin-top:1.05rem; }
            .auth-label-row { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:.48rem; }
            .auth-label { color:#263652; font-size:.76rem; font-weight:750; }
            .auth-input-wrap { position:relative; }
            .auth-field-icon { position:absolute; z-index:2; left:.9rem; top:50%; width:1.05rem; height:1.05rem; transform:translateY(-50%); color:#8a98b0; pointer-events:none; }
            .auth-input { width:100%; height:3.15rem; padding:0 2.9rem 0 2.7rem; border:1.5px solid #dfe5ef; border-radius:.85rem; outline:0; color:#10203e; background:#f9fbfd; font:600 .88rem/1 'Inter',sans-serif; transition:border-color .2s,box-shadow .2s,background .2s; }
            .auth-input::placeholder { color:#9aa6ba; font-weight:500; }
            .auth-input:focus { border-color:#7050ec; background:white; box-shadow:0 0 0 4px rgba(112,80,236,.1); }
            .auth-password-toggle { position:absolute; z-index:3; top:50%; right:.55rem; width:2.2rem; height:2.2rem; transform:translateY(-50%); display:grid; place-items:center; border:0; border-radius:.65rem; color:#71809a; background:transparent; cursor:pointer; transition:.2s; }
            .auth-password-toggle:hover { color:#6040df; background:#f0edff; }
            .auth-password-toggle svg { width:1.08rem; height:1.08rem; }
            .auth-help { margin:.48rem 0 0; color:#8491a8; font-size:.69rem; line-height:1.5; }
            .auth-link { color:#6342e5; font-size:.76rem; font-weight:750; text-decoration:none; }
            .auth-link:hover { color:#4522c4; }
            .auth-check-row { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-top:1.1rem; }
            .auth-check { display:flex; align-items:center; gap:.5rem; color:#63718a; font-size:.76rem; font-weight:600; cursor:pointer; }
            .auth-check input { width:1rem; height:1rem; accent-color:#6542e8; }
            .auth-primary { width:100%; min-height:3.2rem; margin-top:1.35rem; display:flex; align-items:center; justify-content:center; gap:.55rem; border:0; border-radius:.85rem; color:white; background:linear-gradient(135deg,#6f4bf0,#4f2bd8 58%,#2f6fe9); box-shadow:0 12px 26px rgba(82,49,208,.23); font:800 .86rem/1 'Inter',sans-serif; cursor:pointer; transition:.22s; }
            .auth-primary:hover { transform:translateY(-1px); box-shadow:0 16px 30px rgba(82,49,208,.3); }
            .auth-primary svg { width:1rem; height:1rem; }
            .auth-divider { display:flex; align-items:center; gap:.8rem; margin:1.35rem 0; color:#9aa6b9; font-size:.67rem; font-weight:750; letter-spacing:.08em; text-transform:uppercase; }
            .auth-divider::before,.auth-divider::after { content:''; height:1px; flex:1; background:#e5e9f1; }
            .auth-secondary { min-height:3rem; padding:.75rem 1rem; display:flex; align-items:center; gap:.75rem; border:1.5px solid #e0e5ee; border-radius:.85rem; color:#283852; background:#fbfcfe; text-decoration:none; transition:.2s; }
            .auth-secondary:hover { border-color:#d3cafa; background:#f7f5ff; transform:translateY(-1px); }
            .auth-secondary-icon { width:2rem; height:2rem; flex:0 0 auto; display:grid; place-items:center; border-radius:.6rem; color:#6845e9; background:#eeeaff; }
            .auth-secondary-icon svg { width:1rem; height:1rem; }
            .auth-secondary strong { display:block; color:#22324e; font-size:.76rem; }
            .auth-secondary span { display:block; margin-top:.15rem; color:#7a879e; font-size:.67rem; }
            .auth-switch { margin:1.35rem 0 0; color:#6f7c92; text-align:center; font-size:.78rem; }
            .auth-status { margin-bottom:1rem; padding:.85rem 1rem; border:1px solid #cceedd; border-radius:.8rem; color:#087951; background:#effaf5; font-size:.76rem; line-height:1.5; }
            .auth-security-note { margin-top:1rem; padding:.8rem .9rem; display:flex; align-items:flex-start; gap:.65rem; border-radius:.8rem; color:#5e6e88; background:#f3f6fa; font-size:.69rem; line-height:1.5; }
            .auth-security-note svg { width:1rem; height:1rem; flex:0 0 auto; color:#16a36d; }
            .auth-mobile-home { display:none; }
            @media (max-width:1023px) { .auth-mobile-home { position:absolute; top:1.1rem; left:1.1rem; display:inline-flex; align-items:center; gap:.35rem; color:#60708a; font-size:.75rem; font-weight:700; text-decoration:none; } .auth-mobile-home svg { width:1rem; height:1rem; } }
            @media (max-width:639px) { .auth-form-panel { justify-content:flex-start; padding:4.5rem 1rem 2rem; } .auth-logo { margin-bottom:1rem; } .auth-logo img { height:2.3rem; } .auth-card { padding:1.45rem; border-radius:1.25rem; } .auth-heading { font-size:1.5rem; } }
            @media (prefers-reduced-motion:reduce) { *,*::before,*::after { scroll-behavior:auto!important; animation-duration:.01ms!important; animation-iteration-count:1!important; transition-duration:.01ms!important; } }

            /* Premium portal shell: clean enterprise structure with Express Peek identity. */
            .auth-wrapper { min-height:100vh; display:flex; flex-direction:column; background:#f3f6fb; }
            .auth-topbar { position:relative; z-index:20; height:82px; flex:0 0 auto; display:flex; align-items:center; border-bottom:1px solid #e1e7f0; background:rgba(255,255,255,.94); backdrop-filter:blur(18px); box-shadow:0 1px 0 rgba(15,23,42,.02); }
            .auth-topbar-inner { width:min(1180px,calc(100% - 40px)); margin:auto; display:flex; align-items:center; justify-content:space-between; gap:1rem; }
            .auth-topbar-logo { display:inline-flex; align-items:center; text-decoration:none; }
            .auth-topbar-logo img { display:block; width:auto; height:42px; }
            .auth-topbar-actions { display:flex; align-items:center; gap:.75rem; }
            .auth-secure-pill,.auth-topbar-home { display:inline-flex; align-items:center; gap:.48rem; min-height:38px; padding:0 .85rem; border-radius:999px; font-size:.72rem; font-weight:750; text-decoration:none; }
            .auth-secure-pill { color:#087958; border:1px solid #cceadf; background:#f0faf6; }
            .auth-secure-pill svg,.auth-topbar-home svg { width:.95rem; height:.95rem; }
            .auth-topbar-home { color:#34435e; border:1px solid #dfe5ef; background:#fff; transition:.2s; }
            .auth-topbar-home:hover { color:#5b35e8; border-color:#d2c8fb; background:#f8f6ff; transform:translateY(-1px); }
            .auth-main { position:relative; width:100%; flex:1; display:flex; align-items:center; justify-content:center; padding:46px 24px; overflow:hidden; background:radial-gradient(circle at 12% 15%,rgba(111,75,240,.1),transparent 24rem),radial-gradient(circle at 90% 86%,rgba(47,118,239,.09),transparent 26rem),linear-gradient(145deg,#f9fbff,#eef3f9); }
            .auth-main::before { content:''; position:absolute; inset:0; opacity:.32; pointer-events:none; background-image:linear-gradient(rgba(104,81,224,.06) 1px,transparent 1px),linear-gradient(90deg,rgba(104,81,224,.06) 1px,transparent 1px); background-size:38px 38px; mask-image:linear-gradient(to bottom,transparent,#000 25%,#000 75%,transparent); }
            .auth-portal { position:relative; z-index:2; width:min(1060px,100%); min-height:650px; display:grid; grid-template-columns:minmax(330px,43%) minmax(0,1fr); overflow:hidden; border:1px solid rgba(214,222,235,.92); border-radius:30px; background:#fff; box-shadow:0 32px 90px rgba(30,45,88,.14),0 4px 14px rgba(30,45,88,.05); }
            .auth-brand { display:flex; width:auto; min-width:0; align-items:flex-end; justify-content:flex-start; background:#07174d url('/images/dashboard-logistics-hero.png') 62% center/cover no-repeat; }
            .auth-brand::before { background:linear-gradient(180deg,rgba(3,12,48,.08),rgba(5,17,62,.3) 37%,rgba(4,13,50,.96)),linear-gradient(90deg,rgba(4,13,50,.9),rgba(4,18,70,.2)); }
            .brand-content { padding:clamp(2rem,4vw,3.75rem); }
            .brand-tagline { font-size:clamp(2.15rem,3.2vw,3.45rem); }
            .auth-form-panel { min-width:0; padding:2.5rem clamp(1.5rem,4vw,4.2rem); background:#fff; overflow-y:auto; }
            .auth-form-container { max-width:500px; }
            .auth-logo { display:none; }
            .auth-card { padding:0; border:0; border-radius:0; box-shadow:none; overflow:visible; }
            .auth-card::before { display:none; }
            .auth-footer { margin-top:1.6rem; }
            .auth-mobile-home { display:none!important; }
            .back-home-link { display:none; }
            .auth-phone-grid { display:grid; grid-template-columns:128px minmax(0,1fr); gap:.65rem; }
            .auth-code-select { padding:0 2rem 0 .8rem; appearance:auto; cursor:pointer; }
            .auth-required-note { color:#8a97ab; font-size:.65rem; font-weight:600; }
            .auth-terms { display:flex; align-items:flex-start; gap:.7rem; margin-top:1.2rem; color:#65738b; font-size:.72rem; line-height:1.55; cursor:pointer; }
            .auth-terms input { width:1.05rem; height:1.05rem; margin:.1rem 0 0; flex:0 0 auto; accent-color:#6542e8; }
            .auth-session-note { display:inline-flex; align-items:center; gap:.3rem; color:#8491a7; font-size:.67rem; font-weight:650; }
            .auth-session-note svg { width:.9rem; height:.9rem; color:#14a774; }

            @media (max-width:1023px) {
                .auth-topbar { height:72px; }
                .auth-topbar-logo img { height:36px; }
                .auth-main { align-items:flex-start; padding:28px 16px; }
                .auth-portal { width:min(620px,100%); min-height:0; display:block; border-radius:24px; }
                .auth-brand { display:none; }
                .auth-form-panel { padding:2.25rem; }
                .auth-form-container { max-width:none; }
            }
            @media (max-width:639px) {
                .auth-topbar { height:66px; }
                .auth-topbar-inner { width:calc(100% - 28px); }
                .auth-topbar-logo img { height:32px; }
                .auth-secure-pill { display:none; }
                .auth-topbar-home { min-height:34px; padding:0 .7rem; font-size:.68rem; }
                .auth-main { padding:16px 10px 28px; }
                .auth-portal { border-radius:20px; }
                .auth-form-panel { justify-content:flex-start; padding:1.5rem 1.2rem; }
                .auth-heading { font-size:1.42rem; }
                .auth-subtitle { margin-bottom:1.3rem; }
                .auth-phone-grid { grid-template-columns:112px minmax(0,1fr); gap:.5rem; }
                .auth-check-row { align-items:flex-start; }
                .auth-session-note { display:none; }
            }
        </style>
        @include('partials.meta-pixel')
    </head>
    <body class="antialiased">
        <div class="auth-wrapper">
            <header class="auth-topbar">
                <div class="auth-topbar-inner">
                    <a href="{{ route('home') }}" class="auth-topbar-logo" aria-label="Express Peek home"><img src="/images/express-peek-logo.webp" alt="Express Peek"></a>
                    <div class="auth-topbar-actions">
                        <span class="auth-secure-pill"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3 20 6v6c0 5-3.4 8-8 9-4.6-1-8-4-8-9V6l8-3Z"/><path d="m8 12 2.5 2.5L16 9"/></svg>Secure account portal</span>
                        <a href="{{ route('home') }}" class="auth-topbar-home"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>Back to home</a>
                    </div>
                </div>
            </header>
            <main class="auth-main">
              <div class="auth-portal">
                <!-- Branded experience panel (desktop only) -->
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
                    <span class="brand-eyebrow">Secure logistics workspace</span>
                    <img src="/images/express-delivery-plane.png" alt="Express Delivery" class="brand-illustration">

                    <h1 class="brand-tagline">
                        Your shipments.<br><span>One clear command center.</span>
                    </h1>
                    <p class="brand-subtitle">Book, manage, and track every delivery through a protected account experience built for modern logistics.</p>
                    <p class="brand-subtitle legacy-brand-copy">
                        Compare rates, book shipments, and track packages — all from one powerful platform.
                    </p>

                    <div class="auth-assurances" aria-label="Account protection features">
                        <div class="auth-assurance"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3 20 6v6c0 5-3.4 8-8 9-4.6-1-8-4-8-9V6l8-3Z"/><path d="m8 12 2.5 2.5L16 9"/></svg>Protected access</div>
                        <div class="auth-assurance"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19V9M10 19V5M16 19v-7M22 19V2M2 19h21"/></svg>Live visibility</div>
                        <div class="auth-assurance"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 14v-2a8 8 0 0 1 16 0v2M4 14a2 2 0 0 0 2 2h1v-6H6a2 2 0 0 0-2 2M20 14a2 2 0 0 1-2 2h-1v-6h1a2 2 0 0 1 2 2"/></svg>Human support</div>
                    </div>

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

                <!-- Form panel -->
                <div class="auth-form-panel">
                <a href="{{ route('home') }}" class="auth-mobile-home"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>Home</a>
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
            </main>
        </div>
    </body>
</html>
