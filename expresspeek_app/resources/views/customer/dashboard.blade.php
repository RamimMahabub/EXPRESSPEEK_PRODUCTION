<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Express Peek customer shipping dashboard">
    <title>{{ config('app.name', 'Express Peek') }} — Dashboard</title>
    @include('partials.favicon')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --ep-ink: #0b1739;
            --ep-muted: #60708f;
            --ep-line: #e6eaf2;
            --ep-primary: #6534f5;
            --ep-blue: #246bfd;
            --ep-green: #16ad72;
            --ep-orange: #ff8a1f;
            --ep-red: #f13654;
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            min-width: 320px;
            overflow-x: hidden;
            color: var(--ep-ink);
            background: #f7f9fe;
            font-family: 'Manrope', sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        [x-cloak] { display: none !important; }
        button, input { font: inherit; }
        a { color: inherit; text-decoration: none; }
        .ep-icon { width: 20px; height: 20px; fill: none; stroke: currentColor; stroke-width: 1.9; stroke-linecap: round; stroke-linejoin: round; }
        .ep-shell { min-height: 100vh; }

        .ep-topbar {
            position: sticky; top: 0; z-index: 40;
            height: 78px; display: flex; align-items: center;
            padding: 0 30px; border-bottom: 1px solid var(--ep-line);
            background: rgba(255,255,255,.94); backdrop-filter: blur(18px);
        }
        .ep-topbar-inner { width: 100%; max-width: 1780px; min-width: 0; margin: 0 auto; display: flex; align-items: center; gap: 32px; }
        .ep-brand { width: 194px; flex: 0 0 auto; }
        .ep-brand img { display: block; width: 162px; height: 48px; object-fit: contain; }
        .ep-nav { display: flex; align-items: center; gap: 5px; flex: 1; }
        .ep-nav-link { display: flex; align-items: center; gap: 9px; padding: 12px 15px; border-radius: 12px; color: #536484; font-size: 13px; font-weight: 600; white-space: nowrap; transition: .2s ease; }
        .ep-nav-link:hover { color: var(--ep-primary); background: #f6f3ff; }
        .ep-nav-link.is-active { color: var(--ep-primary); background: #f1edff; }
        .ep-nav-link .ep-icon { width: 18px; height: 18px; }
        .ep-top-actions { min-width: 0; display: flex; align-items: center; gap: 9px; }
        .ep-icon-button { position: relative; width: 40px; height: 40px; border: 0; border-radius: 12px; background: transparent; color: #526483; display: grid; place-items: center; cursor: pointer; transition: .2s ease; }
        .ep-icon-button:hover { background: #f4f1ff; color: var(--ep-primary); }
        .ep-home-action { height:40px; padding:0 12px; display:inline-flex; align-items:center; justify-content:center; gap:7px; border-radius:11px; color:#526483; font-size:11px; font-weight:700; white-space:nowrap; transition:.2s ease; }
        .ep-home-action:hover { color:var(--ep-primary); background:#f4f1ff; }
        .ep-home-action .ep-icon { width:17px; height:17px; }
        .ep-badge { position: absolute; top: 2px; right: 2px; min-width: 17px; height: 17px; padding: 0 4px; border-radius: 10px; display: grid; place-items: center; background: var(--ep-red); color: white; border: 2px solid white; font-size: 8px; font-weight: 800; }
        .ep-profile { position: relative; min-width: 0; max-width: 245px; overflow: hidden; display: flex; align-items: center; gap: 10px; margin-left: 5px; padding-left: 14px; border-left: 1px solid var(--ep-line); }
        .ep-avatar { width: 42px; height: 42px; flex: 0 0 auto; overflow:hidden; border-radius: 50%; display: grid; place-items: center; color: white; background: linear-gradient(135deg,#1657b8,#7040f7); box-shadow: 0 8px 18px rgba(71,71,202,.18); font-weight: 800; font-size: 13px; object-fit:cover; }
        .ep-profile-copy { display: block; min-width: 0; width: 132px; max-width: 132px; overflow: hidden; }
        .ep-profile-name { display: block; margin: 0; color: var(--ep-ink); font-size: 13px; font-weight: 800; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .ep-profile-email { display: block; margin: 2px 0 0; color: #7b89a4; font-size: 9px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .ep-notification-popover { position: fixed; z-index: 60; top: 86px; right: 300px; width: min(350px,calc(100vw - 24px)); padding: 16px; border: 1px solid var(--ep-line); border-radius: 15px; background: white; box-shadow: 0 20px 55px rgba(21,34,74,.17); }

        .ep-main { max-width: 1780px; margin: 0 auto; padding: 24px 30px 38px; }
        .ep-card { background: rgba(255,255,255,.97); border: 1px solid var(--ep-line); border-radius: 16px; box-shadow: 0 8px 28px rgba(22,42,84,.045); }
        .ep-heading { margin: 0; color: var(--ep-ink); font-size: 16px; line-height: 1.3; font-weight: 800; }
        .ep-section-head { display: flex; align-items: center; justify-content: space-between; gap: 14px; margin-bottom: 17px; }
        .ep-view-link { display: inline-flex; align-items: center; gap: 5px; color: var(--ep-primary); font-size: 11px; font-weight: 700; }
        .ep-view-link:hover { color: #4318c9; }

        .ep-hero { position: relative; min-height: 236px; overflow: hidden; border: 1px solid #e4e9f3; border-radius: 18px; background: linear-gradient(105deg,#fbfdff 0%,#f4f8fc 34%,#e2edf6 63%,#d5e5f1 78%,#edf4fa 100%); box-shadow: 0 12px 36px rgba(41,56,132,.08); }
        .ep-hero::before { content: ''; position: absolute; z-index: 0; inset: 0; background: url('/images/dashboard-logistics-hero-light.png') 28% center/auto 100% no-repeat; -webkit-mask-image: linear-gradient(90deg,transparent 8%,#000 17%,#000 51%,transparent 63%); mask-image: linear-gradient(90deg,transparent 8%,#000 17%,#000 51%,transparent 63%); }
        .ep-hero::after { content: ''; position: absolute; z-index: 1; inset: 0; background: linear-gradient(90deg,rgba(249,251,255,.98) 0%,rgba(249,251,255,.76) 21%,rgba(247,250,255,.06) 38%,rgba(247,250,255,0) 65%,rgba(247,250,255,.04) 100%); }
        .ep-hero-content { position: relative; z-index: 2; padding: 25px 26px; width: 38%; min-width: 430px; }
        .ep-hero-eyebrow { display:inline-flex; align-items:center; gap:7px; margin-bottom:7px; color:#6542e8; font-size:9px; font-weight:850; letter-spacing:.12em; text-transform:uppercase; }
        .ep-hero-eyebrow::before { content:''; width:7px; height:7px; border-radius:50%; background:#7650f4; box-shadow:0 0 0 5px rgba(118,80,244,.10); }
        .ep-greeting { margin: 0; color: #091735; font-size: 26px; line-height: 1.15; font-weight: 850; letter-spacing: -.7px; }
        .ep-greeting-sub { margin: 6px 0 13px; max-width:390px; color: #657492; font-size: 12px; }
        .ep-command-card { width:310px; padding:12px 14px; border:1px solid rgba(218,225,239,.92); border-radius:14px; background:rgba(255,255,255,.91); box-shadow:0 10px 28px rgba(24,39,86,.10); backdrop-filter:blur(10px); }
        .ep-command-top { display:flex; align-items:center; justify-content:space-between; gap:10px; margin-bottom:10px; }
        .ep-command-title { display:flex; align-items:center; gap:8px; color:#263555; font-size:9px; font-weight:800; }
        .ep-live-dot { width:8px; height:8px; flex:0 0 auto; border-radius:50%; background:#28c985; box-shadow:0 0 0 4px rgba(40,201,133,.10); }
        .ep-command-link { display:inline-flex; align-items:center; gap:4px; color:var(--ep-primary); font-size:9px; font-weight:800; }
        .ep-command-metrics { display:grid; grid-template-columns:1fr 1fr; }
        .ep-command-metric { min-width:0; display:flex; align-items:center; gap:8px; }
        .ep-command-metric + .ep-command-metric { padding-left:12px; border-left:1px solid #e7ebf3; }
        .ep-command-metric strong { color:var(--ep-ink); font-size:21px; line-height:1; letter-spacing:-.4px; }
        .ep-command-metric span { color:#71809b; font-size:8px; line-height:1.25; }
        .ep-hero-actions { position: absolute; z-index: 3; top: 38px; right: 30px; display: grid; grid-template-columns: repeat(4,minmax(128px,1fr)); gap: 20px; width: 46%; }
        .ep-action-card { min-height: 152px; padding: 24px 17px 18px; border: 1px solid rgba(226,231,242,.92); border-radius: 15px; background: rgba(255,255,255,.95); box-shadow: 0 12px 28px rgba(31,47,96,.08); transition: .22s ease; }
        .ep-action-card:hover { transform: translateY(-4px); border-color: #d9d0ff; box-shadow: 0 16px 30px rgba(80,55,190,.13); }
        .ep-action-icon { width: 48px; height: 48px; border-radius: 13px; display: grid; place-items: center; margin-bottom: 14px; color: white; box-shadow: 0 8px 16px rgba(25,55,130,.12); }
        .ep-action-icon.purple { background: linear-gradient(135deg,#8060ff,#4a21e9); }
        .ep-action-icon.blue { background: linear-gradient(135deg,#61b5ff,#2868f7); }
        .ep-action-icon.green { background: linear-gradient(135deg,#65dda9,#17ad74); }
        .ep-action-icon.orange { background: linear-gradient(135deg,#ffc666,#ff921f); }
        .ep-action-title { display: block; color: #101c3c; font-size: 13px; font-weight: 800; }
        .ep-action-sub { display: block; margin-top: 5px; color: #7c8aa5; font-size: 10px; }

        .ep-stats { display: grid; grid-template-columns: repeat(5,1fr); gap: 14px; margin-top: 14px; }
        .ep-stat { position: relative; min-height: 105px; padding: 17px 18px; overflow: hidden; display:block; color:inherit; text-decoration:none; transition:transform .2s ease,border-color .2s ease,box-shadow .2s ease; }
        .ep-stat:hover { transform:translateY(-3px); border-color:#d9d0fa; box-shadow:0 14px 30px rgba(66,48,150,.11); }
        .ep-stat:focus-visible { outline:3px solid rgba(103,65,239,.22); outline-offset:2px; }
        .ep-stat-row { display: flex; align-items: flex-start; gap: 12px; }
        .ep-soft-icon { width: 38px; height: 38px; flex: 0 0 auto; display: grid; place-items: center; border-radius: 11px; }
        .ep-soft-icon.purple { color: #6534f5; background: #f2edff; }
        .ep-soft-icon.orange { color: #f98512; background: #fff5e8; }
        .ep-soft-icon.blue { color: #246bfd; background: #edf3ff; }
        .ep-soft-icon.green { color: #0ba86b; background: #e9faf3; }
        .ep-soft-icon.red { color: #ef2c4d; background: #fff0f2; }
        .ep-stat-label { margin: 0; color: #53627f; font-size: 11px; font-weight: 700; }
        .ep-stat-value { margin: 3px 0 0; color: var(--ep-ink); font-size: 25px; line-height: 1; font-weight: 800; }
        .ep-trend { margin: 9px 0 0 50px; color: #697791; font-size: 9px; }
        .ep-trend strong { font-weight: 800; }
        .ep-trend.up strong { color: #0cad6d; }
        .ep-trend.down strong { color: #f47f18; }
        .ep-trend.alert strong { color: var(--ep-red); }
        .ep-sparkline { position: absolute; right: 12px; bottom: 18px; width: 58px; height: 25px; opacity: .95; }

        .ep-dashboard-grid { display: grid; grid-template-columns: minmax(0,1.05fr) minmax(0,1.08fr) 360px; gap: 14px; margin-top: 14px; align-items: start; }
        .ep-activity, .ep-recent, .ep-side-card { padding: 18px; }
        .ep-period { appearance: none; border: 1px solid var(--ep-line); border-radius: 8px; background: white; color: #687795; padding: 7px 28px 7px 10px; font-size: 9px; font-weight: 700; }
        .ep-chart-wrap { height: 192px; }
        .ep-chart-svg { display: block; width: 100%; height: 165px; overflow: visible; }
        .ep-chart-labels { display: grid; grid-template-columns: repeat(7,1fr); color: #7786a0; font-size: 8px; text-align: center; }
        .ep-chart-legend { display: grid; grid-template-columns: repeat(5,1fr); gap: 8px; margin-top: 10px; padding: 11px 12px; border: 1px solid var(--ep-line); border-radius: 10px; }
        .ep-legend-item { min-width: 0; color: #667592; font-size: 8px; }
        .ep-legend-item span { display: inline-flex; align-items: center; gap: 5px; }
        .ep-legend-dot { width: 6px; height: 6px; border-radius: 50%; }
        .ep-legend-value { margin: 4px 0 0 11px; color: var(--ep-ink); font-size: 11px; font-weight: 800; }
        .ep-table-wrap { overflow-x: auto; }
        .ep-table { width: 100%; border-collapse: collapse; table-layout: auto; font-size: 9px; }
        .ep-table th { padding: 0 7px 11px; color: #8190a9; font-size: 8px; font-weight: 600; text-align: left; white-space: nowrap; }
        .ep-table td { padding: 9px 7px; border-top: 1px solid #edf0f5; color: #425272; vertical-align: middle; }
        .ep-table .tracking { color: #5c39ec; font-weight: 800; }
        .ep-table strong { display: block; color: #2b3b5c; font-weight: 700; }
        .ep-table small { display: block; color: #8a97ae; font-size: 8px; margin-top: 2px; }
        .ep-status { display: inline-flex; align-items: center; gap: 4px; padding: 4px 7px; border-radius: 99px; font-size: 8px; font-weight: 700; white-space: nowrap; }
        .ep-status::before { content: ''; width: 5px; height: 5px; border: 1.5px solid currentColor; border-radius: 50%; }
        .ep-status.pending { color: #e57c05; background: #fff5e7; }
        .ep-status.in_transit, .ep-status.picked_up, .ep-status.out_for_delivery { color: #2670ed; background: #edf4ff; }
        .ep-status.connected_to_carrier, .ep-status.delivered { color: #069860; background: #e8f8f1; }
        .ep-status.failed, .ep-status.returned { color: #ec2f4e; background: #fff0f2; }
        .ep-row-action { color: #3170ed; display: grid; place-items: center; }
        .ep-side-stack { display: grid; gap: 12px; }
        .ep-quick-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 9px; }
        .ep-quick { min-width: 0; text-align: center; color: #273858; font-size: 8px; font-weight: 700; }
        .ep-quick > span:first-child { width: 48px; height: 44px; margin: 0 auto 7px; display: grid; place-items: center; color: var(--ep-primary); background: linear-gradient(180deg,#fbfaff,#f7f8fd); border: 1px solid #f0f0f7; border-radius: 11px; transition: .2s; }
        .ep-quick:hover > span:first-child { transform: translateY(-3px); color: white; background: linear-gradient(135deg,#7350f7,#4c27e8); box-shadow: 0 8px 18px rgba(88,52,212,.18); }
        .ep-alert-list { display: grid; }
        .ep-alert-row { display: grid; grid-template-columns: 27px minmax(0,1fr) auto; gap: 8px; align-items: start; padding: 9px 0; border-top: 1px solid #edf0f5; }
        .ep-alert-row:first-child { padding-top: 0; border-top: 0; }
        .ep-alert-symbol { width: 22px; height: 22px; border-radius: 50%; display: grid; place-items: center; color: white; }
        .ep-alert-symbol.red { background: var(--ep-red); }
        .ep-alert-symbol.orange { background: var(--ep-orange); }
        .ep-alert-symbol.blue { background: var(--ep-blue); }
        .ep-alert-symbol .ep-icon { width: 12px; height: 12px; }
        .ep-alert-copy strong { display: block; color: #263655; font-size: 9px; }
        .ep-alert-copy span { display: block; margin-top: 2px; color: #74839e; font-size: 8px; line-height: 1.35; }
        .ep-alert-time { color: #7c8ba4; font-size: 8px; white-space: nowrap; }
        .ep-support { position: relative; min-height: 142px; overflow: hidden; padding: 21px 22px; border-radius: 15px; color: white; background: linear-gradient(125deg,#5432dc,#724cf5 65%,#4b29d6); box-shadow: 0 14px 30px rgba(91,55,219,.18); }
        .ep-support::after { content: '?'; position: absolute; right: 26px; top: 7px; width: 92px; height: 92px; border: 10px solid rgba(255,255,255,.12); border-radius: 50%; display: grid; place-items: center; font-size: 54px; font-weight: 800; color: rgba(255,255,255,.75); box-shadow: 0 0 0 10px rgba(255,255,255,.06); }
        .ep-support h3 { position: relative; z-index: 2; margin: 0; font-size: 14px; font-weight: 800; }
        .ep-support p { position: relative; z-index: 2; margin: 7px 0 12px; font-size: 9px; }
        .ep-support-btn { position: relative; z-index: 2; display: inline-flex; align-items: center; gap: 8px; padding: 9px 13px; border-radius: 8px; color: #5932de; background: white; font-size: 9px; font-weight: 800; }

        .ep-benefits { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; margin-top: 14px; padding-right: 374px; }
        .ep-benefit { min-height: 92px; padding: 18px; display: flex; align-items: center; gap: 15px; overflow: hidden; border-radius: 14px; }
        .ep-benefit:nth-child(1) { color: white; background: linear-gradient(135deg,#5d33e8,#7563f6); }
        .ep-benefit:nth-child(2) { background: linear-gradient(135deg,#edf5ff,#f7faff); }
        .ep-benefit:nth-child(3) { background: linear-gradient(135deg,#eafaf3,#f6fcf9); }
        .ep-benefit:nth-child(4) { background: linear-gradient(135deg,#fff7e8,#fffaf3); }
        .ep-benefit-icon { width: 54px; height: 54px; flex: 0 0 auto; display: grid; place-items: center; border-radius: 50%; background: rgba(255,255,255,.66); color: var(--ep-primary); }
        .ep-benefit-icon .ep-icon { width: 28px; height: 28px; }
        .ep-benefit-copy strong { display: block; font-size: 12px; font-weight: 800; }
        .ep-benefit-copy span { display: block; margin-top: 4px; font-size: 9px; line-height: 1.5; opacity: .82; }
        .ep-benefit:nth-child(3) .ep-benefit-copy strong { color: #0d9c66; }
        .ep-benefit:nth-child(4) .ep-benefit-copy strong { color: #e97c07; }

        .ep-mobile-header, .ep-mobile-actions, .ep-mobile-promo, .ep-bottom-nav { display: none; }
        .ep-tracker { position: relative; min-height: 250px; margin-top: 14px; padding: 32px; overflow: hidden; border-radius: 18px; color: white; background: #07174d url('/images/tracking-dark-hero.webp') 72% 53%/cover no-repeat; box-shadow: 0 16px 42px rgba(17,35,92,.15); scroll-margin-top: 100px; }
        .ep-tracker::after { content: ''; position: absolute; inset: 0; background: linear-gradient(90deg,rgba(4,15,58,.98) 0%,rgba(10,25,83,.91) 42%,rgba(15,33,111,.35) 70%,rgba(15,33,111,.08)); }
        .ep-tracker-content { position: relative; z-index: 2; width: min(700px,64%); }
        .ep-tracker-kicker { display: inline-flex; align-items: center; gap: 7px; margin-bottom: 10px; padding: 6px 10px; border: 1px solid rgba(255,255,255,.2); border-radius: 99px; background: rgba(255,255,255,.1); font-size: 9px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; }
        .ep-tracker-kicker::before { content: ''; width: 7px; height: 7px; border-radius: 50%; background: #3bd996; box-shadow: 0 0 0 5px rgba(59,217,150,.12); }
        .ep-tracker-title { margin: 0; max-width: 620px; font-size: 27px; line-height: 1.17; letter-spacing: -.6px; font-weight: 800; }
        .ep-tracker-copy { max-width: 580px; margin: 8px 0 19px; color: #d9e3ff; font-size: 11px; line-height: 1.6; }
        .ep-tracker-form { max-width: 650px; display: flex; gap: 7px; padding: 6px; border: 1px solid rgba(255,255,255,.2); border-radius: 15px; background: rgba(255,255,255,.96); box-shadow: 0 14px 32px rgba(0,0,0,.2); }
        .ep-tracker-field { min-width: 0; flex: 1; display: flex; align-items: center; gap: 9px; padding: 0 11px; color: #8290aa; }
        .ep-tracker-field .ep-icon { flex: 0 0 auto; }
        .ep-tracker-input { min-width: 0; width: 100%; height: 46px; border: 0; outline: 0; color: #11203f; background: transparent; font-size: 13px; font-weight: 650; }
        .ep-tracker-input::placeholder { color: #9ba7bb; font-weight: 500; }
        .ep-tracker-button { height: 46px; padding: 0 22px; border: 0; border-radius: 11px; color: white; background: linear-gradient(135deg,#7950f8,#4d24de); box-shadow: 0 8px 18px rgba(83,46,216,.28); font-size: 11px; font-weight: 800; cursor: pointer; }
        .ep-tracker-benefits { display: flex; flex-wrap: wrap; gap: 16px; margin-top: 13px; color: #d5dff9; font-size: 9px; font-weight: 650; }
        .ep-tracker-benefits span { display: inline-flex; align-items: center; gap: 5px; }
        .ep-tracker-benefits i { width: 6px; height: 6px; border-radius: 50%; background: #3bd996; }
        .ep-footer { display: flex; align-items: center; justify-content: space-between; gap: 20px; padding: 20px 30px; border-top: 1px solid var(--ep-line); color: #7a89a3; background: white; font-size: 9px; }
        .ep-footer-links { display: flex; gap: 32px; }

        @media (max-width: 1450px) {
            .ep-nav-link { padding-inline: 10px; }
            .ep-nav-link span { display: none; }
            .ep-home-action { width:40px; padding:0; }
            .ep-home-action span { display:none; }
            .ep-hero-actions { width: 50%; gap: 12px; }
            .ep-dashboard-grid { grid-template-columns: minmax(0,.95fr) minmax(0,1.2fr) 315px; }
            .ep-benefits { padding-right: 329px; }
        }

        @media (max-width: 1120px) {
            .ep-topbar { padding-inline: 20px; }
            .ep-brand { width: 160px; }
            .ep-nav-link { padding: 10px 8px; }
            .ep-profile-copy { display: none; }
            .ep-main { padding-inline: 20px; }
            .ep-hero { min-height: 355px; }
            .ep-hero-content { width: 55%; }
            .ep-hero-actions { top: auto; left: 20px; right: 20px; bottom: 18px; width: auto; }
            .ep-action-card { min-height: 126px; padding: 15px; }
            .ep-action-icon { width: 39px; height: 39px; margin-bottom: 9px; }
            .ep-stats { grid-template-columns: repeat(3,1fr); }
            .ep-dashboard-grid { grid-template-columns: 1fr 1fr; }
            .ep-side-stack { grid-column: 1 / -1; grid-template-columns: 1fr 1fr 1fr; }
            .ep-benefits { padding-right: 0; grid-template-columns: repeat(2,1fr); }
        }

        @media (max-width: 767px) {
            body { background: #f9faff; padding-bottom: 82px; }
            .ep-topbar, .ep-footer { display: none; }
            .ep-mobile-header { position: sticky; top: 0; z-index: 40; height: 76px; padding: 0 20px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--ep-line); background: rgba(255,255,255,.96); backdrop-filter: blur(16px); }
            .ep-mobile-header img { width: 151px; height: 45px; object-fit: contain; }
            .ep-mobile-user { display: flex; align-items: center; gap: 11px; }
            .ep-mobile-user .ep-icon-button { width: 38px; height: 38px; }
            .ep-mobile-user .ep-avatar { width: 42px; height: 42px; }
            .ep-main { padding: 23px 16px 28px; }
            .ep-notification-popover { top: 82px; right: 12px; }
            .ep-hero { min-height: 224px; margin-top: 92px; overflow: visible; border: 0; border-radius: 18px; background:linear-gradient(180deg,#eef5fb 0%,#f9fbfd 48%,#e7f0f7 100%); }
            .ep-hero::before { background-image:url('/images/dashboard-logistics-hero-light.png'); background-position:center bottom; background-size:100% auto; background-repeat:no-repeat; -webkit-mask-image:none; mask-image:none; }
            .ep-hero::after { background:linear-gradient(90deg,rgba(249,252,255,.96) 0%,rgba(249,252,255,.76) 43%,rgba(249,252,255,.12) 76%,rgba(249,252,255,0) 100%); }
            .ep-hero-content { width: 100%; min-width: 0; padding: 22px 20px; }
            .ep-hero-eyebrow { position:absolute; top:-85px; left:0; }
            .ep-greeting { position: absolute; top: -62px; left: 0; margin: 0; color: var(--ep-ink); font-size: 22px; white-space: nowrap; }
            .ep-greeting-sub { position: absolute; top: -34px; left: 0; margin: 0; max-width:none; color: #64728e; font-size: 11px; white-space: nowrap; }
            .ep-command-card { width:min(310px,calc(100vw - 72px)); }
            .ep-hero-actions { display: none; }
            .ep-mobile-actions { display: grid; grid-template-columns: repeat(4,minmax(0,1fr)); margin-top: 16px; padding: 22px 10px; }
            .ep-mobile-action { position: relative; min-width: 0; text-align: center; color: #142141; font-size: 11px; font-weight: 700; line-height: 1.3; }
            .ep-mobile-action:not(:last-child)::after { content: ''; position: absolute; right: 0; top: 6px; bottom: 6px; width: 1px; background: var(--ep-line); }
            .ep-mobile-action .ep-soft-icon { margin: 0 auto 10px; width: 46px; height: 46px; }
            .ep-stats { grid-template-columns: repeat(2,1fr); gap: 10px; margin-top: 16px; }
            .ep-stat { min-height: 134px; padding: 14px; }
            .ep-stat:last-child { grid-column: 1 / -1; }
            .ep-stat-row { display: block; }
            .ep-soft-icon { width: 36px; height: 36px; }
            .ep-stat-label { margin-top: 9px; }
            .ep-stat-value { position: absolute; top: 75px; left: 14px; }
            .ep-trend { position: absolute; left: 14px; bottom: 12px; margin: 0; }
            .ep-sparkline { right: 12px; bottom: 19px; }
            .ep-dashboard-grid { display: flex; flex-direction: column; gap: 14px; }
            .ep-recent { order: 1; width: 100%; padding: 18px 14px; }
            .ep-activity { order: 4; width: 100%; }
            .ep-side-stack { order: 2; width: 100%; display: contents; }
            .ep-side-card.quick-card { order: 2; width: 100%; }
            .ep-side-card.alert-card { order: 3; width: 100%; }
            .ep-support { order: 6; width: 100%; }
            .ep-table th { display: none; }
            .ep-table, .ep-table tbody, .ep-table tr, .ep-table td { display: block; }
            .ep-table tr { position: relative; min-height: 68px; padding: 11px 105px 11px 51px; border-top: 1px solid #edf0f5; }
            .ep-table tr:first-child { border-top: 0; }
            .ep-table td { padding: 0; border: 0; }
            .ep-table td:nth-child(1) { margin-bottom: 5px; font-size: 11px; }
            .ep-table td:nth-child(2), .ep-table td:nth-child(5) { display: none; }
            .ep-table td:nth-child(3) { color: #6f7e98; font-size: 9px; }
            .ep-table td:nth-child(4) { position: absolute; right: 27px; top: 13px; }
            .ep-table td:nth-child(6) { position: absolute; right: 0; top: 28px; }
            .ep-table tr::before { content: ''; position: absolute; left: 2px; top: 13px; width: 36px; height: 36px; border-radius: 10px; background: #f1edff; }
            .ep-table tr::after { content: '▣'; position: absolute; left: 12px; top: 20px; color: var(--ep-primary); font-size: 16px; }
            .ep-quick-grid { grid-template-columns: repeat(4,1fr); }
            .ep-quick > span:first-child { width: 52px; height: 52px; }
            .ep-benefits { display: none; }
            .ep-tracker { min-height: 360px; padding: 24px 18px; display: flex; align-items: flex-end; background-position: 62% center; }
            .ep-tracker::after { background: linear-gradient(180deg,rgba(4,15,58,.22),rgba(4,15,58,.97) 56%); }
            .ep-tracker-content { width: 100%; }
            .ep-tracker-title { font-size: 22px; }
            .ep-tracker-form { display: block; }
            .ep-tracker-button { width: 100%; }
            .ep-tracker-benefits { gap: 10px; }
            .ep-mobile-promo { position: relative; display: block; min-height: 135px; margin-top: 14px; padding: 22px 44% 20px 22px; overflow: hidden; border-radius: 16px; color: white; background: linear-gradient(110deg,rgba(42,27,169,.96),rgba(86,55,233,.82)), url('/images/dashboard-logistics-hero.png') 62% center/cover no-repeat; }
            .ep-mobile-promo h3 { margin: 0; font-size: 16px; font-weight: 800; }
            .ep-mobile-promo p { margin: 5px 0 12px; font-size: 10px; line-height: 1.5; }
            .ep-mobile-promo a { display: inline-flex; align-items: center; gap: 8px; padding: 9px 13px; border: 1px solid rgba(255,255,255,.36); border-radius: 9px; background: rgba(255,255,255,.09); font-size: 10px; font-weight: 700; }
            .ep-bottom-nav { position: fixed; z-index: 50; left: 0; right: 0; bottom: 0; display: grid; grid-template-columns: repeat(6,minmax(0,1fr)); height: 76px; padding: 8px 3px max(8px,env(safe-area-inset-bottom)); border-top: 1px solid var(--ep-line); background: rgba(255,255,255,.97); backdrop-filter: blur(18px); box-shadow: 0 -8px 26px rgba(25,39,79,.08); }
            .ep-bottom-link { position: relative; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px; color: #697791; font-size: 9px; font-weight: 600; }
            .ep-bottom-link.is-active { color: var(--ep-primary); }
            .ep-bottom-link.is-active::before { content: ''; position: absolute; top: -8px; width: 38px; height: 3px; border-radius: 99px; background: var(--ep-primary); }
            .ep-bottom-link .ep-icon { width: 23px; height: 23px; }
            .ep-chart-wrap { height: 175px; }
            .ep-chart-legend { grid-template-columns: repeat(3,1fr); }
        }

        @media (max-width: 430px) {
            .ep-main { padding-inline: 12px; }
            .ep-greeting { font-size: 20px; }
            .ep-greeting-sub { font-size:10px; }
            .ep-mobile-actions { padding-inline: 4px; }
            .ep-mobile-action { font-size: 9px; }
            .ep-quick-grid { grid-template-columns: repeat(2,1fr); row-gap: 14px; }
            .ep-mobile-promo { padding-right: 25px; }
        }

        /* Premium customer finish */
        .ep-card { border-color:#e2e8f2; border-radius:19px; box-shadow:0 13px 34px rgba(24,43,84,.065); }
        .ep-stat { overflow:hidden; }.ep-stat::after { content:''; position:absolute; right:-28px; top:-30px; width:92px; height:92px; border-radius:50%; background:rgba(105,63,231,.045); }
        .ep-panel { border-radius:20px; }.ep-topbar { box-shadow:0 8px 28px rgba(20,37,75,.045); }
        @media (min-width:768px) {
            .ep-hero { border-color:#1b3264; background:radial-gradient(circle at 48% 10%,rgba(53,115,221,.28),transparent 28%),linear-gradient(120deg,#081535 0%,#10265c 55%,#2b2377 100%); box-shadow:0 22px 48px rgba(8,23,60,.2); }
            .ep-hero::before { opacity:.67; filter:saturate(1.12) contrast(1.04); -webkit-mask-image:linear-gradient(90deg,transparent 3%,#000 16%,#000 57%,transparent 72%); mask-image:linear-gradient(90deg,transparent 3%,#000 16%,#000 57%,transparent 72%); }
            .ep-hero::after { background:linear-gradient(90deg,rgba(5,17,48,.99) 0%,rgba(7,22,57,.78) 24%,rgba(10,29,69,.16) 48%,rgba(22,26,92,.15) 67%,rgba(35,26,100,.68) 100%); }
            .ep-hero-eyebrow { color:#c4b8ff; }.ep-hero-eyebrow::before { background:#46dca9; box-shadow:0 0 0 5px rgba(70,220,169,.12); }.ep-greeting { color:#fff; }.ep-greeting-sub { color:#bdc9e2; }
            .ep-command-card { border-color:rgba(255,255,255,.16); background:rgba(255,255,255,.1); box-shadow:0 14px 32px rgba(0,0,0,.16); }.ep-command-title,.ep-command-metric strong { color:#fff; }.ep-command-metric span { color:#b9c5de; }.ep-command-metric+.ep-command-metric { border-color:rgba(255,255,255,.14); }.ep-command-link { color:#c3b5ff; }
            .ep-action-card { border-color:rgba(255,255,255,.15); box-shadow:0 16px 34px rgba(3,12,39,.18); }
        }

        /* Simplified customer dashboard: one clear action layer and one content layer. */
        .ep-main { max-width:1440px; padding-top:28px; }
        .ep-command-card,
        .ep-activity,
        .ep-side-stack,
        .ep-benefits,
        .ep-mobile-promo { display:none; }
        .ep-dashboard-grid { display:block; margin-top:20px; }
        .ep-recent { width:100%; padding:24px; }
        .ep-stats {
            grid-template-columns:repeat(auto-fit,minmax(190px,1fr));
            gap:16px;
            margin-top:20px;
        }
        .ep-stat { min-height:92px; padding:18px 20px; }
        .ep-stat-row { align-items:center; }
        .ep-stat-value { margin-top:5px; font-size:27px; }
        .ep-trend,
        .ep-sparkline { display:none; }
        .ep-tracker { margin-top:20px; }

        @media (min-width:768px) {
            .ep-hero { min-height:224px; }
            .ep-hero-content { width:47%; min-width:0; padding:42px 34px; }
            .ep-greeting { max-width:520px; font-size:30px; }
            .ep-greeting-sub { max-width:470px; margin-top:10px; font-size:12px; line-height:1.6; }
            .ep-hero-actions {
                top:28px;
                right:28px;
                width:47%;
                grid-template-columns:repeat(2,minmax(0,1fr));
                gap:12px;
            }
            .ep-action-card {
                min-height:78px;
                padding:14px 16px;
                display:grid;
                grid-template-columns:42px minmax(0,1fr);
                grid-template-rows:auto auto;
                column-gap:12px;
                align-content:center;
            }
            .ep-action-card:hover { transform:translateY(-2px); }
            .ep-action-icon {
                grid-row:1 / 3;
                width:42px;
                height:42px;
                margin:0;
                border-radius:11px;
            }
            .ep-action-title { align-self:end; font-size:12px; }
            .ep-action-sub { align-self:start; margin-top:2px; font-size:9px; }
        }

        @media (max-width:767px) {
            .ep-main { padding-top:16px; }
            .ep-stats { grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; }
            .ep-stat { min-height:106px; }
            .ep-stat:last-child { grid-column:auto; }
            .ep-stat-value { position:static; margin-top:5px; }
            .ep-dashboard-grid { margin-top:14px; }
            .ep-recent { padding:17px 14px; }
            .ep-tracker { margin-top:14px; }
        }
    </style>
</head>
<body>
@php
    $firstName = str(auth()->user()->name)->before(' ');
    $initials = str(auth()->user()->name)->explode(' ')->take(2)->map(fn ($part) => str($part)->substr(0, 1))->join('');
    $statusClass = fn ($status) => in_array($status, ['pending', 'in_transit', 'picked_up', 'connected_to_carrier', 'out_for_delivery', 'delivered', 'failed', 'returned']) ? $status : 'pending';
    $activityValues = $activity->pluck('value')->map(fn ($value) => (int) $value)->values();
    $chartValues = $activityValues->contains(fn ($value) => $value > 0) ? $activityValues : collect([12, 22, 25, 38, 31, 20, 24]);
    $chartMax = max(10, $chartValues->max());
    $chartPoints = $chartValues->map(fn ($value, $index) => (10 + ($index * 113.33)).','.(150 - (($value / $chartMax) * 112)))->join(' ');
@endphp

<div class="ep-shell" x-data="{ notificationsOpen: false, notifications: [], unreadCount: 0, init() { this.loadNotifications(); }, toggleNotifications() { this.notificationsOpen = !this.notificationsOpen; if (this.notificationsOpen) { this.unreadCount = 0; this.loadNotifications(); this.markAllNotificationsRead(); } }, loadNotifications() { fetch('/notifications', { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } }).then(response => response.json()).then(data => { this.notifications = data.notifications || []; this.unreadCount = this.notificationsOpen ? 0 : (data.unread_count || 0); }); }, markRead(notification) { if (notification.read_at) return; fetch('/notifications/' + notification.id + '/read', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, Accept: 'application/json' } }).then(() => { notification.read_at = new Date().toISOString(); this.unreadCount = Math.max(0, this.unreadCount - 1); }); }, markAllNotificationsRead() { fetch('/notifications/read-all', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, Accept: 'application/json' } }).then(() => { this.notifications.forEach(notification => notification.read_at = new Date().toISOString()); this.unreadCount = 0; }); } }" x-init="init()">
    <header class="ep-topbar">
        <div class="ep-topbar-inner">
            <a class="ep-brand" href="{{ route('customer.dashboard') }}" aria-label="Express Peek dashboard">
                <img src="/images/express-peek-logo.webp" alt="Express Peek">
            </a>

            <nav class="ep-nav" aria-label="Primary navigation">
                <a href="{{ route('customer.dashboard') }}" class="ep-nav-link is-active">
                    <svg class="ep-icon" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg><span>Dashboard</span>
                </a>
                <a href="{{ route('customer.shipments.index') }}" class="ep-nav-link">
                    <svg class="ep-icon" viewBox="0 0 24 24"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/><path d="m4.5 7.5 7.5 4 7.5-4M12 11.5V21"/></svg><span>Shipments</span>
                </a>
                <a href="#track" class="ep-nav-link">
                    <svg class="ep-icon" viewBox="0 0 24 24"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg><span>Tracking</span>
                </a>
                <a href="{{ route('customer.address-book.page') }}" class="ep-nav-link">
                    <svg class="ep-icon" viewBox="0 0 24 24"><path d="M5 4h13a2 2 0 0 1 2 2v15H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/><path d="M8 2v4M8 10h7M8 14h7"/></svg><span>Address Book</span>
                </a>
                <a href="{{ route('customer.sourcing-requests.index') }}" class="ep-nav-link">
                    <svg class="ep-icon" viewBox="0 0 24 24"><path d="M6 8h12l1 13H5L6 8Z"/><path d="M9 9V6a3 3 0 0 1 6 0v3"/></svg><span>Shop From Bangladesh</span>
                </a>
            </nav>

            <div class="ep-top-actions">
                <button class="ep-icon-button" @click="toggleNotifications()" aria-label="Open notifications">
                    <svg class="ep-icon" viewBox="0 0 24 24"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg><span x-show="unreadCount > 0" x-text="unreadCount" class="ep-badge"></span>
                </button>
                <a href="{{ route('customer-service') }}" class="ep-icon-button" aria-label="Customer support">
                    <svg class="ep-icon" viewBox="0 0 24 24"><path d="M21 15a3 3 0 0 1-3 3H8l-5 3V6a3 3 0 0 1 3-3h12a3 3 0 0 1 3 3Z"/><path d="M8 10h.01M12 10h.01M16 10h.01"/></svg>
                </a>
                <a href="{{ route('home') }}" class="ep-home-action" aria-label="Back to Home">
                    <svg class="ep-icon" viewBox="0 0 24 24"><path d="M3 11 12 3l9 8v10h-6v-6H9v6H3V11Z"/></svg><span>Back to Home</span>
                </a>
                <a href="{{ route('profile.edit') }}" class="ep-profile">
                    @if(auth()->user()->avatarUrl())<img class="ep-avatar" src="{{ auth()->user()->avatarUrl() }}" alt="{{ auth()->user()->name }}">@else<span class="ep-avatar">{{ strtoupper($initials) }}</span>@endif
                    <span class="ep-profile-copy"><span class="ep-profile-name">{{ auth()->user()->name }}</span><span class="ep-profile-email">{{ auth()->user()->email }}</span></span>
                    <svg class="ep-icon" style="width:14px" viewBox="0 0 24 24"><path d="m7 10 5 5 5-5"/></svg>
                </a>
                <form method="POST" action="{{ route('logout') }}" style="margin:0;display:flex;align-items:center;">
                    @csrf
                    <button class="ep-icon-button" type="submit" aria-label="Sign out" style="color:#e82d4b;background:#fff0f2;">
                        <svg class="ep-icon" viewBox="0 0 24 24"><path d="M10 17l5-5-5-5M15 12H3M14 4h5a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-5"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <header class="ep-mobile-header">
        <a href="{{ route('customer.dashboard') }}"><img src="/images/express-peek-logo.webp" alt="Express Peek"></a>
        <div class="ep-mobile-user">
            <button class="ep-icon-button" @click="toggleNotifications()" aria-label="Notifications"><svg class="ep-icon" viewBox="0 0 24 24"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg><span x-show="unreadCount > 0" x-text="unreadCount" class="ep-badge"></span></button>
            <a href="{{ route('profile.edit') }}">@if(auth()->user()->avatarUrl())<img class="ep-avatar" src="{{ auth()->user()->avatarUrl() }}" alt="{{ auth()->user()->name }}">@else<span class="ep-avatar">{{ strtoupper($initials) }}</span>@endif</a>
            <form method="POST" action="{{ route('logout') }}" style="margin:0;display:flex;align-items:center;">
                @csrf
                <button class="ep-icon-button" type="submit" aria-label="Sign out" style="color:#e82d4b;background:#fff0f2;margin-left:4px;">
                    <svg class="ep-icon" viewBox="0 0 24 24"><path d="M10 17l5-5-5-5M15 12H3M14 4h5a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-5"/></svg>
                </button>
            </form>
        </div>
    </header>

    <aside class="ep-notification-popover" x-cloak x-show="notificationsOpen" x-transition.opacity @click.outside="notificationsOpen = false" aria-label="Notifications">
        <div class="ep-section-head"><h2 class="ep-heading">Notifications</h2><button class="ep-icon-button" style="width:28px;height:28px" @click="notificationsOpen = false" aria-label="Close notifications"><svg class="ep-icon" viewBox="0 0 24 24"><path d="m6 6 12 12M18 6 6 18"/></svg></button></div>
        <div class="ep-alert-list">
            <template x-for="notification in notifications.slice(0, 5)" :key="notification.id">
                <a :href="notification.data.action_url || '#'" @click="markRead(notification)" class="ep-alert-row"><span class="ep-alert-symbol blue"><svg class="ep-icon" viewBox="0 0 24 24"><path d="M12 8v4M12 16h.01"/></svg></span><span class="ep-alert-copy"><strong x-text="notification.data.title"></strong><span x-text="notification.data.message"></span></span><span class="ep-alert-time" x-text="new Date(notification.created_at).toLocaleDateString()"></span></a>
            </template>
            <div x-show="notifications.length === 0" class="ep-alert-row"><span class="ep-alert-symbol blue"><svg class="ep-icon" viewBox="0 0 24 24"><path d="M12 8v4M12 16h.01"/></svg></span><span class="ep-alert-copy"><strong>You’re all caught up</strong><span>New updates will appear here.</span></span><span class="ep-alert-time">Now</span></div>
        </div>
        <div class="ep-section-head" style="justify-content:center;border-top:1px solid var(--ep-line);margin-top:8px;padding-top:12px;">
            <a href="{{ route('notifications.all') }}" class="ep-view-link">View all notifications <span>→</span></a>
        </div>
    </aside>

    <main class="ep-main">
        @if (session('success'))
            <div class="ep-card" style="margin-bottom:14px;padding:13px 16px;color:#087b52;background:#edfbf5;border-color:#ccefe0">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="ep-card" style="margin-bottom:14px;padding:13px 16px;color:#c8203e;background:#fff2f4;border-color:#ffd8df">{{ session('error') }}</div>
        @endif

        <section class="ep-hero" aria-labelledby="welcome-heading">
            <div class="ep-hero-content">
                <span class="ep-hero-eyebrow">Live shipment visibility</span>
                <h1 class="ep-greeting" id="welcome-heading">Track every handoff.</h1>
                <p class="ep-greeting-sub">Use your Express Peek number or carrier reference to see the current status and complete event history.</p>
                
                <form class="ep-tracker-form" method="GET" action="{{ route('customer.dashboard') }}" style="margin-top: 24px;">
                    <label class="ep-tracker-field">
                        <svg class="ep-icon" viewBox="0 0 24 24"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                        <input class="ep-tracker-input" name="tracking" value="{{ $trackingNumber }}" required autocomplete="off" placeholder="Enter tracking number" aria-label="Tracking number">
                    </label>
                    <button class="ep-tracker-button" type="submit">Track Shipment</button>
                </form>
                <div class="ep-tracker-benefits" style="color: #657492;">
                    <span><i></i>Express Peek updates</span>
                    <span><i></i>Carrier references supported</span>
                    <span><i></i>Secure lookup</span>
                </div>
                
                <x-dashboard-tracking-results 
                    :shipment="$trackedShipment" 
                    :error="$trackingError" 
                    :lookup-unavailable="$trackingLookupUnavailable" 
                />
            </div>
            <div class="ep-hero-actions">
                <a href="{{ route('customer.shipments.create') }}" class="ep-action-card"><span class="ep-action-icon purple"><svg class="ep-icon" viewBox="0 0 24 24"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/><path d="m4.5 7.5 7.5 4 7.5-4M12 11.5V21"/></svg></span><span class="ep-action-title">Create Shipment</span><span class="ep-action-sub">Start a new delivery</span></a>
                <a href="{{ route('customer.sourcing-requests.create', ['new' => 1]) }}" class="ep-action-card"><span class="ep-action-icon blue"><svg class="ep-icon" viewBox="0 0 24 24"><path d="M6 8h12l1 13H5L6 8Z"/><path d="M9 9V6a3 3 0 0 1 6 0v3"/></svg></span><span class="ep-action-title">Shop From Bangladesh</span><span class="ep-action-sub">Find new products</span></a>
                <a href="{{ route('quote') }}" data-open-quote-modal class="ep-action-card"><span class="ep-action-icon green"><svg class="ep-icon" viewBox="0 0 24 24"><path d="M6 3h10l3 3v15H6z"/><path d="M16 3v4h4M9 11h6M9 15h6"/></svg></span><span class="ep-action-title">Request Check Delivery Cost</span><span class="ep-action-sub">Get rates instantly</span></a>
                <a href="{{ route('volumetric-weight-calculator') }}" class="ep-action-card"><span class="ep-action-icon orange"><svg class="ep-icon" viewBox="0 0 24 24"><path d="M4 19V5h16v14H4Z"/><path d="M8 5v4M12 5v2M16 5v4M8 19v-4M12 19v-2M16 19v-4"/></svg></span><span class="ep-action-title">Weight Calculator</span><span class="ep-action-sub">Plan billable weight</span></a>
            </div>
        </section>

        <section class="ep-mobile-actions ep-card" aria-label="Quick actions">
            <a href="{{ route('customer.shipments.create') }}" class="ep-mobile-action"><span class="ep-soft-icon purple"><svg class="ep-icon" viewBox="0 0 24 24"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/><path d="m4.5 7.5 7.5 4 7.5-4M12 11.5V21"/></svg></span>Create<br>Shipment</a>
            <a href="{{ route('customer.sourcing-requests.create', ['new' => 1]) }}" class="ep-mobile-action"><span class="ep-soft-icon blue"><svg class="ep-icon" viewBox="0 0 24 24"><path d="M6 8h12l1 13H5L6 8Z"/><path d="M9 9V6a3 3 0 0 1 6 0v3"/></svg></span>Sourcing<br>Request</a>
            <a href="{{ route('quote') }}" data-open-quote-modal class="ep-mobile-action"><span class="ep-soft-icon green"><svg class="ep-icon" viewBox="0 0 24 24"><path d="M6 3h10l3 3v15H6z"/><path d="M16 3v4h4M9 11h6M9 15h6"/></svg></span>Request<br>Check Delivery Cost</a>
            <a href="{{ route('volumetric-weight-calculator') }}" class="ep-mobile-action"><span class="ep-soft-icon orange"><svg class="ep-icon" viewBox="0 0 24 24"><path d="M4 19V5h16v14H4Z"/><path d="M8 5v4M12 5v2M16 5v4"/></svg></span>Weight<br>Calculator</a>
        </section>

        <section class="ep-stats" aria-label="Shipment overview">
            @foreach([
                ['label'=>'Total Shipments','value'=>$stats['total'],'tone'=>'purple','trend'=>'↑ 12%','copy'=>'vs last month','href'=>route('customer.shipments.index')],
                ['label'=>'Order Requests','value'=>$stats['order_requests'] ?? 0,'tone'=>'blue','trend'=>'Check status','copy'=>'active requests','href'=>route('customer.sourcing-requests.index')],
                ['label'=>'In Transit','value'=>$stats['in_transit'],'tone'=>'blue','trend'=>'↑ 5','copy'=>'vs yesterday','href'=>route('customer.shipments.index',['statuses'=>['in_transit']])],
                ['label'=>'Successful','value'=>$stats['delivered'],'tone'=>'green','trend'=>'↑ 15%','copy'=>'connected or delivered','href'=>route('customer.shipments.index',['statuses'=>['connected_to_carrier','delivered']])],
                ['label'=>'Exceptions','value'=>$stats['exceptions'],'tone'=>'red','trend'=>'Requires attention','copy'=>'','href'=>route('customer.shipments.index',['statuses'=>['failed','returned']])],
            ] as $stat)
                @continue($stat['tone'] === 'red' && (int) $stat['value'] === 0)
                <a class="ep-stat ep-card" href="{{ $stat['href'] }}" aria-label="Open {{ $stat['label'] }}">
                    <div class="ep-stat-row"><span class="ep-soft-icon {{ $stat['tone'] }}"><svg class="ep-icon" viewBox="0 0 24 24">@if($stat['tone']==='orange')<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>@elseif($stat['tone']==='red')<path d="M12 3 2.5 20h19L12 3Z"/><path d="M12 9v4M12 17h.01"/>@elseif($stat['tone']==='green')<circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/>@else<path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/><path d="m4.5 7.5 7.5 4 7.5-4M12 11.5V21"/>@endif</svg></span><div><p class="ep-stat-label">{{ $stat['label'] }}</p><p class="ep-stat-value">{{ $stat['value'] }}</p></div></div>
                    <p class="ep-trend {{ $stat['tone']==='red' ? 'alert' : ($stat['tone']==='orange' ? 'down' : 'up') }}"><strong>{{ $stat['trend'] }}</strong> {{ $stat['copy'] }}</p>
                    <svg class="ep-sparkline" style="color:var(--ep-{{ $stat['tone']==='purple' ? 'primary' : $stat['tone'] }})" viewBox="0 0 60 26"><path d="M2 14 13 19 24 9 36 16 48 12 58 3" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="58" cy="3" r="2.5" fill="currentColor"/></svg>
                </a>
            @endforeach
        </section>

        <section class="ep-dashboard-grid">
            <article class="ep-activity ep-card">
                <div class="ep-section-head"><h2 class="ep-heading">Shipment Activity</h2><select class="ep-period" aria-label="Activity period"><option>Last 7 Days</option></select></div>
                <div class="ep-chart-wrap" aria-label="Shipment activity over the last seven days">
                    <svg class="ep-chart-svg" viewBox="0 0 700 165" preserveAspectRatio="none" role="img">
                        <defs><linearGradient id="epActivityFill" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#7245f4" stop-opacity=".30"/><stop offset="1" stop-color="#7245f4" stop-opacity=".02"/></linearGradient></defs>
                        <g stroke="#e9edf4" stroke-width="1" stroke-dasharray="5 5"><line x1="0" y1="38" x2="700" y2="38"/><line x1="0" y1="75" x2="700" y2="75"/><line x1="0" y1="112" x2="700" y2="112"/><line x1="0" y1="150" x2="700" y2="150"/></g>
                        <polygon points="10,150 {{ $chartPoints }} 690,150" fill="url(#epActivityFill)"/>
                        <polyline points="{{ $chartPoints }}" fill="none" stroke="#6637f1" stroke-width="3" vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"/>
                        @foreach($chartValues as $index => $value)<circle cx="{{ 10 + ($index * 113.33) }}" cy="{{ 150 - (($value / $chartMax) * 112) }}" r="4" fill="#6637f1" vector-effect="non-scaling-stroke"/>@endforeach
                    </svg>
                    <div class="ep-chart-labels">@foreach($activity as $point)<span>{{ $point['label'] }}</span>@endforeach</div>
                </div>
                <div class="ep-chart-legend">
                    @foreach([['In Transit',$stats['in_transit'],'#6637f1'],['Successful',$stats['delivered'],'#11a96c'],['Pending Pickup',$stats['pending'],'#ff8b15'],['Exceptions',$stats['exceptions'],'#ef3152'],['Total Shipments',$stats['total'],'#71809a']] as [$label,$value,$color])
                        <div class="ep-legend-item"><span><i class="ep-legend-dot" style="background:{{ $color }}"></i>{{ $label }}</span><p class="ep-legend-value">{{ $value }}</p></div>
                    @endforeach
                </div>
            </article>

            <article class="ep-recent ep-card">
                <div class="ep-section-head"><h2 class="ep-heading">Recent Shipments</h2><a class="ep-view-link" href="{{ route('customer.shipments.index') }}">View all shipments <span>→</span></a></div>
                @if($recentShipments->isEmpty())
                    <div style="padding:62px 18px;text-align:center;color:#7d8ba5"><span class="ep-soft-icon purple" style="margin:0 auto 12px"><svg class="ep-icon" viewBox="0 0 24 24"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/></svg></span><strong style="display:block;color:#344461;font-size:12px">No shipments yet</strong><span style="font-size:10px">Create your first shipment to see activity here.</span></div>
                @else
                    <div class="ep-table-wrap">
                        <table class="ep-table">
                            <thead><tr><th>Tracking #</th><th>Sender / Recipient</th><th>Destination</th><th>Status</th><th>Updated</th><th>Action</th></tr></thead>
                            <tbody>
                                @foreach($recentShipments as $shipment)
                                    <tr>
                                        <td class="tracking">{{ $shipment->tracking_number }}</td>
                                        <td><strong>{{ $shipment->sender_name ?: auth()->user()->name }}</strong><small>{{ $shipment->receiver_name }}</small></td>
                                        <td><strong>{{ $shipment->sender_city ?: 'Origin' }} →</strong><small>{{ $shipment->receiver_city }}, {{ $shipment->receiver_country }}</small></td>
                                        <td><span class="ep-status {{ $statusClass($shipment->status) }}">{{ $shipment->status_label }}</span></td>
                                        <td><strong>{{ $shipment->updated_at->format('M d, Y') }}</strong><small>{{ $shipment->updated_at->format('h:i A') }}</small></td>
                                        <td><a class="ep-row-action" href="{{ route('customer.shipments.show', $shipment) }}" aria-label="View shipment {{ $shipment->tracking_number }}"><svg class="ep-icon" viewBox="0 0 24 24"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg></a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </article>

            <div class="ep-side-stack">
                <article class="ep-side-card quick-card ep-card">
                    <div class="ep-section-head"><h2 class="ep-heading">Quick Access</h2></div>
                    <div class="ep-quick-grid">
                        <a class="ep-quick" href="{{ route('customer.shipments.index') }}"><span><svg class="ep-icon" viewBox="0 0 24 24"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/></svg></span><span>My Shipments</span></a>
                        <a class="ep-quick" href="{{ route('customer.sourcing-requests.create', ['new' => 1]) }}"><span><svg class="ep-icon" viewBox="0 0 24 24"><path d="M6 8h12l1 13H5L6 8Z"/><path d="M9 9V6a3 3 0 0 1 6 0v3"/></svg></span><span>Shop From Bangladesh</span></a>
                        <a class="ep-quick" href="{{ route('customer.address-book.page') }}"><span><svg class="ep-icon" viewBox="0 0 24 24"><path d="M5 4h14v17H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/><path d="M8 9h7M8 13h7"/></svg></span><span>Address Book</span></a>
                        <a class="ep-quick" href="{{ route('volumetric-weight-calculator') }}"><span><svg class="ep-icon" viewBox="0 0 24 24"><path d="M4 19V5h16v14H4Z"/><path d="M8 5v4M12 5v2M16 5v4"/></svg></span><span>Weight Calculator</span></a>
                    </div>
                </article>

                <article class="ep-side-card alert-card ep-card">
                    <div class="ep-section-head"><h2 class="ep-heading">Alerts & Updates</h2><a class="ep-view-link" href="{{ route('customer.shipments.index') }}">View all <span>›</span></a></div>
                    <div class="ep-alert-list">
                        @if($recentShipments->isNotEmpty())
                            @foreach($recentShipments->take(3) as $index => $shipment)
                                <a href="{{ route('customer.shipments.show', $shipment) }}" class="ep-alert-row"><span class="ep-alert-symbol {{ $index === 0 && in_array($shipment->status,['failed','returned']) ? 'red' : ($index===1 ? 'orange' : 'blue') }}"><svg class="ep-icon" viewBox="0 0 24 24">@if($index===1)<path d="M12 7v5l3 2"/><circle cx="12" cy="12" r="9"/>@else<path d="M12 8v4M12 16h.01"/>@endif</svg></span><span class="ep-alert-copy"><strong>Shipment {{ $shipment->tracking_number }}</strong><span>{{ $shipment->status_label }} — {{ $shipment->receiver_city }}, {{ $shipment->receiver_country }}</span></span><span class="ep-alert-time">{{ $shipment->updated_at->diffForHumans(short: true) }}</span></a>
                            @endforeach
                        @else
                            <div class="ep-alert-row"><span class="ep-alert-symbol blue"><svg class="ep-icon" viewBox="0 0 24 24"><path d="M12 8v4M12 16h.01"/></svg></span><span class="ep-alert-copy"><strong>Welcome to Express Peek</strong><span>Your shipment updates will appear here.</span></span><span class="ep-alert-time">Now</span></div>
                        @endif
                    </div>
                </article>

                <article class="ep-support"><h3>Need Help?</h3><p>Our support team is here for you</p><a class="ep-support-btn" href="{{ route('customer-service') }}"><svg class="ep-icon" viewBox="0 0 24 24"><path d="M4 14v-2a8 8 0 0 1 16 0v2M4 14a2 2 0 0 0 2 2h1v-6H6a2 2 0 0 0-2 2M20 14a2 2 0 0 1-2 2h-1v-6h1a2 2 0 0 1 2 2M17 16c0 3-2 4-5 4"/></svg>Contact Support <span>›</span></a></article>
            </div>
        </section>

        <section class="ep-benefits" aria-label="Express Peek benefits">
            <article class="ep-benefit"><span class="ep-benefit-icon"><svg class="ep-icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18"/></svg></span><span class="ep-benefit-copy"><strong>Global Coverage</strong><span>Ship to 220+ countries with our trusted partners.</span></span></article>
            <article class="ep-benefit"><span class="ep-benefit-icon"><svg class="ep-icon" viewBox="0 0 24 24"><path d="M12 3 20 6v6c0 5-3.4 8-8 9-4.6-1-8-4-8-9V6l8-3Z"/><path d="m8 12 2.5 2.5L16 9"/></svg></span><span class="ep-benefit-copy"><strong>Secure & Reliable</strong><span>Your shipments are safe with end-to-end security.</span></span></article>
            <article class="ep-benefit"><span class="ep-benefit-icon"><svg class="ep-icon" viewBox="0 0 24 24"><path d="m13 2-8 12h7l-1 8 8-12h-7l1-8Z"/></svg></span><span class="ep-benefit-copy"><strong>Faster Deliveries</strong><span>Optimized routes for faster and on-time delivery.</span></span></article>
            <article class="ep-benefit"><span class="ep-benefit-icon"><svg class="ep-icon" viewBox="0 0 24 24"><path d="M4 19V9M10 19V5M16 19v-7M22 19V2M2 19h21"/></svg></span><span class="ep-benefit-copy"><strong>Real-time Visibility</strong><span>Track every move of your shipment in real-time.</span></span></article>
        </section>

        <section class="ep-mobile-promo"><h3>Ship Smarter with Express Peek</h3><p>Real-time visibility, automated updates, and complete control over your shipments.</p><a href="{{ route('customer.shipments.create') }}">Create New Shipment <span>›</span></a></section>

        <!-- Tracking moved to hero section -->

        @if($trackingNumber !== '' || $trackingError)
            @include('components.dashboard-tracking-result', [
                'shipment' => $trackedShipment,
                'error' => $trackingError,
                'lookupUnavailable' => $trackingLookupUnavailable,
            ])
        @endif
    </main>

    <footer class="ep-footer"><span>© {{ now()->year }} Express Peek. All rights reserved.</span><nav class="ep-footer-links"><a href="{{ route('privacy') }}">Privacy Policy</a><a href="{{ route('terms') }}">Terms of Service</a><a href="{{ route('customer-service') }}">Contact Us</a></nav></footer>

    <nav class="ep-bottom-nav" aria-label="Mobile navigation">
        <a href="{{ route('customer.dashboard') }}" class="ep-bottom-link is-active"><svg class="ep-icon" viewBox="0 0 24 24"><path d="M3 11 12 3l9 8v10h-6v-6H9v6H3V11Z"/></svg><span>Dashboard</span></a>
        <a href="{{ route('customer.shipments.index') }}" class="ep-bottom-link"><svg class="ep-icon" viewBox="0 0 24 24"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/><path d="m4.5 7.5 7.5 4 7.5-4M12 11.5V21"/></svg><span>Shipments</span></a>
        <a href="#track" class="ep-bottom-link"><svg class="ep-icon" viewBox="0 0 24 24"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg><span>Tracking</span></a>
        <a href="{{ route('customer.sourcing-requests.index') }}" class="ep-bottom-link"><svg class="ep-icon" viewBox="0 0 24 24"><path d="M6 8h12l1 13H5L6 8Z"/><path d="M9 9V6a3 3 0 0 1 6 0v3"/></svg><span>Requests</span></a>
        <a href="{{ route('home') }}" class="ep-bottom-link"><svg class="ep-icon" viewBox="0 0 24 24"><path d="M3 11 12 3l9 8v10h-6v-6H9v6H3V11Z"/></svg><span>Home</span></a>
        <a href="{{ route('profile.edit') }}" class="ep-bottom-link"><svg class="ep-icon" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg><span>Profile</span></a>
    </nav>
</div>

<script>
    window.INTERNAL_SHIPMENT_CREATE_URL = @json(route('customer.shipments.create'));
</script>
@include('components.quote-modal')

</body>
</html>
