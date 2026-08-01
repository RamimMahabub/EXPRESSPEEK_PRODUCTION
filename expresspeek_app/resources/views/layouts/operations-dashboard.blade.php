<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Express Peek') }} — @yield('title', 'Dashboard')</title>
    @include('partials.favicon')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --ops-ink:#0b1739; --ops-muted:#697894; --ops-line:#e5eaf3; --ops-purple:#6534f5; --ops-blue:#2670f5; --ops-green:#12a870; --ops-orange:#f78a19; --ops-red:#ef3152; }
        * { box-sizing:border-box; }
        html { scroll-behavior:smooth; }
        body { margin:0; min-width:320px; overflow-x:hidden; color:var(--ops-ink); background:#f7f9fe; font-family:'Manrope',sans-serif; -webkit-font-smoothing:antialiased; }
        a { color:inherit; text-decoration:none; }
        button,input { font:inherit; }
        .ops-icon { width:20px; height:20px; fill:none; stroke:currentColor; stroke-width:1.9; stroke-linecap:round; stroke-linejoin:round; }
        .ops-topbar { position:sticky; top:0; z-index:40; height:78px; display:flex; align-items:center; padding:0 30px; border-bottom:1px solid var(--ops-line); background:rgba(255,255,255,.95); backdrop-filter:blur(18px); }
        .ops-topbar-inner { width:100%; max-width:1780px; min-width:0; margin:0 auto; display:flex; align-items:center; gap:30px; }
        .ops-topbar-inner.is-admin { gap:14px; }
        .ops-topbar-inner.is-admin .ops-logo { width:165px; }
        .ops-topbar-inner.is-admin .ops-nav { gap:2px; }
        .ops-topbar-inner.is-admin .ops-nav-link { gap:5px; padding:10px 8px; font-size:10px; }
        .ops-topbar-inner.is-admin .ops-nav-link .ops-icon { width:16px; height:16px; }
        .ops-topbar-inner.is-admin .ops-profile { max-width:190px; }
        .ops-topbar-inner.is-admin .ops-profile-copy { width:100px; }
        .ops-topbar-inner.is-customer { gap:18px; }
        .ops-topbar-inner.is-customer .ops-nav { gap:3px; }
        .ops-topbar-inner.is-customer .ops-nav-link { padding-inline:11px; }
        .ops-topbar-inner.is-customer > .ops-role-pill { display:none; }
        .ops-logo { width:180px; flex:0 0 auto; }
        .ops-logo img { display:block; width:158px; height:48px; object-fit:contain; }
        .ops-nav { min-width:0; flex:1; display:flex; align-items:center; gap:5px; }
        .ops-nav-link { display:flex; align-items:center; gap:8px; padding:12px 14px; border-radius:12px; color:#536484; font-size:12px; font-weight:650; white-space:nowrap; transition:.2s ease; }
        .ops-nav-link:hover,.ops-nav-link.active { color:var(--ops-purple); background:#f1edff; }
        .ops-nav-link .ops-icon { width:18px; height:18px; }
        .ops-role-pill { flex:0 0 auto; padding:5px 9px; border-radius:99px; color:#5f37da; background:#f2edff; font-size:9px; font-weight:800; text-transform:uppercase; letter-spacing:.08em; }
        .ops-top-actions { min-width:0; flex:0 0 auto; display:flex; align-items:center; gap:8px; }
        .ops-icon-btn { position:relative; width:39px; height:39px; flex:0 0 auto; display:grid; place-items:center; border:0; border-radius:11px; color:#576886; background:transparent; cursor:pointer; transition:.2s; }
        .ops-icon-btn:hover { color:var(--ops-purple); background:#f3efff; }
        .ops-notice { position:absolute; top:2px; right:1px; min-width:16px; height:16px; padding:0 4px; display:grid; place-items:center; border:2px solid white; border-radius:99px; color:white; background:var(--ops-red); font-size:7px; font-weight:800; }
        .ops-profile { min-width:0; max-width:238px; overflow:hidden; display:flex; align-items:center; gap:10px; margin-left:5px; padding-left:14px; border-left:1px solid var(--ops-line); }
        .ops-avatar { width:42px; height:42px; flex:0 0 auto; display:grid; place-items:center; overflow:hidden; border-radius:50%; color:white; background:linear-gradient(135deg,#286de1,#6937eb); box-shadow:0 8px 20px rgba(75,56,201,.2); font-size:12px; font-weight:800; object-fit:cover; }
        .ops-profile-copy { display:block; width:122px; min-width:0; overflow:hidden; }
        .ops-profile-name,.ops-profile-email { display:block; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .ops-profile-name { color:var(--ops-ink); font-size:12px; font-weight:800; }
        .ops-profile-email { margin-top:2px; color:#8290a8; font-size:8px; }
        .ops-home-action { height:39px; flex:0 0 auto; display:inline-flex; align-items:center; gap:7px; padding:0 11px; border-radius:11px; color:#536484; font-size:10px; font-weight:750; white-space:nowrap; transition:.2s ease; }
        .ops-home-action:hover { color:var(--ops-purple); background:#f3efff; }
        .ops-home-action .ops-icon { width:17px; height:17px; }
        .ops-main { max-width:1780px; margin:0 auto; padding:24px 30px 38px; }
        .ops-card { background:rgba(255,255,255,.98); border:1px solid var(--ops-line); border-radius:16px; box-shadow:0 8px 28px rgba(22,42,84,.045); }
        .ops-title { margin:0; color:var(--ops-ink); font-size:16px; font-weight:800; }
        .ops-section-head { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:16px; }
        .ops-link { display:inline-flex; align-items:center; gap:5px; color:var(--ops-purple); font-size:10px; font-weight:800; }
        .ops-link:hover { color:#3f17bd; }
        .ops-flash { margin-bottom:14px; padding:13px 16px; border-radius:12px; font-size:11px; }
        .ops-flash.success { color:#087b52; background:#edfbf5; border:1px solid #ccefe0; }
        .ops-flash.error { color:#c8203e; background:#fff2f4; border:1px solid #ffd8df; }

        .ops-hero { position:relative; min-height:220px; overflow:hidden; border:1px solid var(--ops-line); border-radius:18px; background:linear-gradient(105deg,#fbfdff 0%,#f4f8fc 34%,#e2edf6 63%,#d5e5f1 78%,#edf4fa 100%); box-shadow:0 12px 36px rgba(41,56,132,.08); }
        .ops-hero::before { content:''; position:absolute; z-index:0; inset:0; background:url('/images/dashboard-logistics-hero-light.png') 18% center/auto 100% no-repeat; -webkit-mask-image:linear-gradient(90deg,transparent 5%,#000 13%,#000 43%,transparent 54%); mask-image:linear-gradient(90deg,transparent 5%,#000 13%,#000 43%,transparent 54%); }
        .ops-hero::after { content:''; position:absolute; z-index:1; inset:0; background:linear-gradient(90deg,rgba(250,252,255,.98) 0%,rgba(250,252,255,.76) 21%,rgba(250,252,255,.06) 38%,rgba(250,252,255,0) 65%,rgba(250,252,255,.04) 100%); }
        .ops-hero-copy { position:relative; z-index:2; width:35%; min-width:420px; padding:31px 26px; }
        .ops-kicker { display:inline-flex; align-items:center; gap:7px; margin-bottom:9px; padding:5px 9px; border-radius:99px; color:#5932db; background:rgba(239,234,255,.92); font-size:9px; font-weight:800; text-transform:uppercase; letter-spacing:.08em; }
        .ops-hero h1 { margin:0; color:var(--ops-ink); font-size:25px; line-height:1.2; letter-spacing:-.5px; font-weight:800; }
        .ops-hero p { margin:7px 0 18px; color:#687793; font-size:12px; }
        .ops-command { width:310px; padding:12px 14px; border:1px solid rgba(218,225,239,.94); border-radius:13px; background:rgba(255,255,255,.92); box-shadow:0 9px 24px rgba(27,43,88,.09); backdrop-filter:blur(9px); }
        .ops-command-head { display:flex; align-items:center; justify-content:space-between; gap:10px; margin-bottom:10px; }
        .ops-command-title { display:flex; align-items:center; gap:8px; color:#263555; font-size:9px; font-weight:800; }
        .ops-command-title::before { content:''; width:8px; height:8px; flex:0 0 auto; border-radius:50%; background:#24c886; box-shadow:0 0 0 4px rgba(36,200,134,.11); }
        .ops-command-link { color:var(--ops-purple); font-size:8px; font-weight:800; }
        .ops-command-metrics { display:grid; grid-template-columns:1fr 1fr; }
        .ops-command-metric { min-width:0; display:flex; align-items:center; gap:8px; }
        .ops-command-metric + .ops-command-metric { padding-left:12px; border-left:1px solid #e7ebf3; }
        .ops-command-metric strong { color:var(--ops-ink); font-size:20px; line-height:1; }
        .ops-command-metric span { color:#71809b; font-size:8px; line-height:1.25; }
        .ops-hero-actions { position:absolute; z-index:3; top:34px; right:28px; width:50%; display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:14px; }
        .ops-hero-actions.has-five-actions { width:62%; grid-template-columns:repeat(5,minmax(0,1fr)); }
        .ops-action { min-height:152px; padding:23px 16px 17px; border:1px solid rgba(224,229,240,.94); border-radius:15px; background:rgba(255,255,255,.90); backdrop-filter:blur(7px); box-shadow:0 12px 28px rgba(31,47,96,.075); transition:.2s ease; }
        .ops-action:hover { transform:translateY(-4px); border-color:#d8ceff; box-shadow:0 16px 30px rgba(80,55,190,.13); }
        .ops-action-icon,.ops-soft-icon { display:grid; place-items:center; border-radius:12px; }
        .ops-action-icon { width:46px; height:46px; margin-bottom:13px; color:white; }
        .ops-action-icon.purple { background:linear-gradient(135deg,#7e59fa,#5127e6); }
        .ops-action-icon.blue { background:linear-gradient(135deg,#62b3ff,#286af4); }
        .ops-action-icon.green { background:linear-gradient(135deg,#61d9a7,#17aa73); }
        .ops-action-icon.orange { background:linear-gradient(135deg,#ffc362,#ff901d); }
        .ops-action-icon.teal { background:linear-gradient(135deg,#36d6d0,#0d8f8a); }
        .ops-action strong { display:block; color:#13203f; font-size:12px; }
        .ops-action small { display:block; margin-top:5px; color:#8290a8; font-size:9px; }

        .ops-stats { display:grid; grid-template-columns:repeat(5,minmax(0,1fr)); gap:14px; margin-top:14px; }
        .ops-stat { position:relative; min-height:108px; padding:17px; overflow:hidden; display:block; color:inherit; text-decoration:none; transition:transform .2s ease,border-color .2s ease,box-shadow .2s ease; }
        .ops-stat:hover { transform:translateY(-3px); border-color:#d9d0fa; box-shadow:0 14px 30px rgba(66,48,150,.11); }
        .ops-stat:focus-visible { outline:3px solid rgba(103,65,239,.22); outline-offset:2px; }
        .ops-stat-main { display:flex; align-items:flex-start; gap:11px; }
        .ops-soft-icon { width:38px; height:38px; flex:0 0 auto; }
        .ops-soft-icon.purple { color:var(--ops-purple); background:#f1edff; }
        .ops-soft-icon.blue { color:var(--ops-blue); background:#edf3ff; }
        .ops-soft-icon.green { color:var(--ops-green); background:#eaf9f3; }
        .ops-soft-icon.orange { color:var(--ops-orange); background:#fff5e8; }
        .ops-soft-icon.red { color:var(--ops-red); background:#fff0f3; }
        .ops-stat-label { margin:0; color:#5e6d88; font-size:10px; font-weight:700; }
        .ops-stat-value { margin:4px 0 0; color:var(--ops-ink); font-size:25px; line-height:1; font-weight:800; }
        .ops-stat-note { margin:10px 0 0 49px; color:#71809a; font-size:8px; }
        .ops-stat-note strong { color:var(--ops-green); }
        .ops-stat-note.warning strong { color:var(--ops-orange); }
        .ops-stat-note.danger strong { color:var(--ops-red); }
        .ops-grid { display:grid; grid-template-columns:minmax(0,1.1fr) minmax(0,1.3fr) 350px; gap:14px; margin-top:14px; align-items:start; }
        .ops-panel { padding:18px; }
        .ops-side { display:grid; gap:12px; }
        .ops-chart { height:190px; }
        .ops-chart svg { display:block; width:100%; height:165px; overflow:visible; }
        .ops-chart-labels { display:grid; grid-template-columns:repeat(7,1fr); color:#7b89a2; font-size:8px; text-align:center; }
        .ops-legend { display:grid; grid-template-columns:repeat(4,1fr); gap:8px; margin-top:12px; padding:11px; border:1px solid var(--ops-line); border-radius:10px; }
        .ops-legend span { color:#687792; font-size:8px; }
        .ops-legend strong { display:block; margin-top:3px; color:var(--ops-ink); font-size:11px; }
        .ops-table-wrap { overflow:auto; }
        .ops-table { width:100%; border-collapse:collapse; font-size:9px; }
        .ops-table th { padding:0 7px 11px; color:#8290a9; font-size:8px; font-weight:600; text-align:left; white-space:nowrap; }
        .ops-table td { padding:10px 7px; border-top:1px solid #edf0f5; color:#475774; vertical-align:middle; }
        .ops-table strong { display:block; color:#273756; font-weight:700; }
        .ops-table small { display:block; margin-top:2px; color:#8a97ae; font-size:8px; }
        .ops-tracking { color:#5d39ea!important; font-weight:800; }
        .ops-status { display:inline-flex; align-items:center; gap:4px; padding:4px 7px; border-radius:99px; font-size:8px; font-weight:800; white-space:nowrap; }
        .ops-status::before { content:''; width:5px; height:5px; border:1.5px solid currentColor; border-radius:50%; }
        .ops-status.pending { color:#dd7909; background:#fff4e4; }
        .ops-status.in_transit,.ops-status.picked_up,.ops-status.out_for_delivery { color:#266fe9; background:#edf4ff; }
        .ops-status.connected_to_carrier,.ops-status.delivered { color:#059560; background:#e9f9f2; }
        .ops-status.failed,.ops-status.returned { color:#e82d4b; background:#fff0f2; }
        .ops-action-link { display:inline-flex; padding:6px 9px; border-radius:7px; color:#5a34d8; background:#f2edff; font-size:8px; font-weight:800; }
        .ops-quick-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:9px; }
        .ops-quick { min-height:77px; padding:12px 8px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:7px; border:1px solid #eef0f6; border-radius:11px; color:#324260; background:#fbfcff; text-align:center; font-size:8px; font-weight:800; transition:.2s; }
        .ops-quick:hover { color:white; border-color:transparent; background:linear-gradient(135deg,#734cf2,#4f27dc); box-shadow:0 10px 20px rgba(82,49,202,.18); }
        .ops-list { display:grid; }
        .ops-list-row { display:grid; grid-template-columns:minmax(0,1fr) auto; gap:10px; align-items:center; padding:10px 0; border-top:1px solid #edf0f5; }
        .ops-list-row:first-child { padding-top:0; border-top:0; }
        .ops-list-row span { color:#687792; font-size:9px; }
        .ops-list-row strong { color:var(--ops-ink); font-size:10px; }
        .ops-progress { height:5px; margin-top:5px; overflow:hidden; border-radius:99px; background:#eef1f7; }
        .ops-progress i { display:block; height:100%; border-radius:inherit; background:linear-gradient(90deg,#7141ef,#3977f3); }
        .ops-empty { padding:58px 18px; color:#7e8ba4; text-align:center; font-size:10px; }
        .ops-empty .ops-soft-icon { margin:0 auto 11px; }
        .ops-mobile-header,.ops-bottom-nav { display:none; }
        .ops-footer { display:flex; align-items:center; justify-content:space-between; gap:18px; padding:20px 30px; border-top:1px solid var(--ops-line); color:#7b89a2; background:white; font-size:9px; }

        /* Premium admin workspace primitives. Kept here so deployed Blade views do not
           depend on a freshly generated Tailwind bundle. */
        .ap-shell { --ap-ink:#101a38; --ap-muted:#72809a; --ap-line:#e3e9f3; --ap-purple:#6c3cf0; --ap-blue:#2877f5; display:flex; flex-direction:column; gap:20px; }
        .ap-page-head { position:relative; min-height:138px; overflow:hidden; display:flex; align-items:center; justify-content:space-between; gap:24px; padding:27px 30px; border:1px solid rgba(114,92,225,.18); border-radius:24px; color:white; background:radial-gradient(circle at 78% -20%,rgba(102,151,255,.42),transparent 36%),linear-gradient(125deg,#0b173a 0%,#14275d 58%,#4c2cb7 130%); box-shadow:0 20px 44px rgba(19,37,85,.16); }
        .ap-page-head::after { content:''; position:absolute; right:-45px; bottom:-90px; width:340px; height:220px; border:1px solid rgba(255,255,255,.12); border-radius:50%; box-shadow:0 0 0 38px rgba(255,255,255,.035),0 0 0 78px rgba(255,255,255,.025); pointer-events:none; }
        .ap-head-copy,.ap-head-actions { position:relative; z-index:1; }
        .ap-eyebrow { display:inline-flex; align-items:center; gap:8px; margin-bottom:9px; color:#b9c9ff; font-size:10px; font-weight:800; letter-spacing:.13em; text-transform:uppercase; }
        .ap-eyebrow::before { content:''; width:7px; height:7px; border-radius:50%; background:#46dcaa; box-shadow:0 0 0 5px rgba(70,220,170,.12); }
        .ap-page-head h1 { margin:0; color:white; font-size:27px; line-height:1.14; letter-spacing:-.7px; font-weight:800; }
        .ap-page-head p { max-width:650px; margin:8px 0 0; color:#bdc9e4; font-size:12px; line-height:1.65; }
        .ap-head-actions { display:flex; align-items:center; gap:10px; }
        .ap-button { min-height:42px; display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:0 16px; border:1px solid transparent; border-radius:12px; font-size:11px; font-weight:800; cursor:pointer; transition:transform .18s ease,box-shadow .18s ease,background .18s ease; }
        .ap-button:hover { transform:translateY(-2px); }
        .ap-button.primary { color:white; background:linear-gradient(135deg,#8051f7,#5b2ee5); box-shadow:0 10px 22px rgba(93,47,221,.27); }
        .ap-button.light { color:#172342; border-color:rgba(255,255,255,.55); background:white; box-shadow:0 10px 24px rgba(8,19,49,.2); }
        .ap-button.ghost { color:#51617e; border-color:#dce3ee; background:#fff; }
        .ap-button.danger { color:#d8344e; border-color:#ffd5dc; background:#fff4f5; }
        .invoice-download-button { min-height:38px; display:inline-flex; align-items:center; justify-content:center; gap:7px; padding:0 14px; border:1px solid #173963; border-radius:7px; color:#fff!important; background:#203a5d; box-shadow:0 3px 8px rgba(20,43,74,.16); font-size:11px; font-weight:800; line-height:1; white-space:nowrap; transition:background .18s ease,border-color .18s ease,transform .18s ease; }
        .invoice-download-button:hover { color:#fff!important; border-color:#102e52; background:#162f50; transform:translateY(-1px); }
        .invoice-download-button svg { width:15px; height:15px; flex:0 0 auto; }
        .ap-card { border:1px solid var(--ap-line); border-radius:21px; background:rgba(255,255,255,.98); box-shadow:0 13px 34px rgba(24,43,84,.07); }
        .ap-card-head { display:flex; align-items:center; justify-content:space-between; gap:18px; padding:21px 23px; border-bottom:1px solid #edf1f6; }
        .ap-card-title { margin:0; color:var(--ap-ink); font-size:15px; font-weight:800; }
        .ap-card-copy { margin:4px 0 0; color:var(--ap-muted); font-size:10px; line-height:1.5; }
        .ap-kpi-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:13px; }
        .ap-kpi { position:relative; min-height:104px; overflow:hidden; padding:18px 19px; border:1px solid var(--ap-line); border-radius:18px; background:#fff; box-shadow:0 10px 26px rgba(25,43,82,.055); }
        .ap-kpi::after { content:''; position:absolute; right:-23px; top:-24px; width:84px; height:84px; border-radius:50%; background:var(--kpi-soft,#f1edff); }
        .ap-kpi-top { display:flex; align-items:center; gap:10px; }
        .ap-kpi-icon { position:relative; z-index:1; width:35px; height:35px; display:grid; place-items:center; border-radius:11px; color:var(--kpi,#6c3cf0); background:var(--kpi-soft,#f1edff); }
        .ap-kpi-icon svg { width:17px; height:17px; fill:none; stroke:currentColor; stroke-width:2; }
        .ap-kpi-label { color:#71809a; font-size:10px; font-weight:750; }
        .ap-kpi-value { margin:10px 0 0; color:var(--ap-ink); font-size:24px; line-height:1; font-weight:800; }
        .ap-kpi-note { margin:6px 0 0; color:var(--kpi,#6c3cf0); font-size:9px; font-weight:750; }
        .ap-kpi.blue { --kpi:#2673ec; --kpi-soft:#eaf2ff; } .ap-kpi.green { --kpi:#0c9a69; --kpi-soft:#e6f8f1; } .ap-kpi.orange { --kpi:#e47b0a; --kpi-soft:#fff2df; } .ap-kpi.red { --kpi:#df3853; --kpi-soft:#fff0f3; }
        .ap-toolbar { display:flex; align-items:center; justify-content:space-between; gap:14px; padding:15px 17px; border:1px solid var(--ap-line); border-radius:17px; background:rgba(255,255,255,.92); box-shadow:0 8px 24px rgba(25,43,82,.045); }
        .ap-tabs { display:flex; align-items:center; gap:6px; padding:4px; border-radius:12px; background:#f2f5fa; }
        .ap-tab { min-height:34px; display:inline-flex; align-items:center; justify-content:center; padding:0 14px; border-radius:9px; color:#71809a; font-size:10px; font-weight:750; transition:.18s; }
        .ap-tab:hover { color:#4f2fd0; background:#fff; }
        .ap-tab.active { color:white; background:linear-gradient(135deg,#7d4ff3,#5c31e1); box-shadow:0 7px 16px rgba(96,52,221,.25); }
        .ap-field { position:relative; }
        .ap-field svg { position:absolute; left:14px; top:50%; width:17px; height:17px; transform:translateY(-50%); fill:none; stroke:#8390a7; stroke-width:2; pointer-events:none; }
        .ap-input,.ap-select { width:100%; min-height:43px; padding:0 14px; border:1px solid #dbe3ef!important; border-radius:12px!important; color:#17213b!important; background:#fff!important; box-shadow:0 2px 6px rgba(25,43,82,.025); font-size:11px!important; outline:none; transition:.18s; }
        .ap-field .ap-input { padding-left:42px; }
        .ap-input::placeholder { color:#a0abc0!important; }
        .ap-input:focus,.ap-select:focus { border-color:#835df2!important; box-shadow:0 0 0 4px rgba(108,60,240,.09)!important; }
        .ap-table-card { overflow:hidden; }
        .ap-table-scroll { overflow:auto; }
        .ap-table { width:100%; border-collapse:separate; border-spacing:0; color:#4f5e79; font-size:11px; }
        .ap-table thead th { padding:14px 18px; border-bottom:1px solid #e8edf4; color:#75829a; background:#f7f9fc; font-size:9px; font-weight:800; letter-spacing:.06em; text-align:left; text-transform:uppercase; white-space:nowrap; }
        .ap-table tbody td { padding:15px 18px; border-bottom:1px solid #edf1f6; vertical-align:middle; }
        .ap-table tbody tr:last-child td { border-bottom:0; }
        .ap-table tbody tr { background:#fff; transition:.16s; }
        .ap-table tbody tr:hover { background:#faf9ff; }
        .ap-avatar { width:38px; height:38px; flex:0 0 auto; display:grid; place-items:center; border-radius:12px; color:white; background:linear-gradient(145deg,#8256f7,#296ff3); box-shadow:0 8px 18px rgba(76,72,212,.22); font-size:10px; font-weight:800; }
        .ap-person { display:flex; align-items:center; gap:11px; }
        .ap-person strong { display:block; color:#1c2947; font-size:11px; font-weight:800; }
        .ap-person small { display:block; margin-top:2px; color:#8a97ac; font-size:9px; }
        .ap-badge { display:inline-flex; align-items:center; gap:5px; padding:5px 9px; border-radius:99px; color:#5c37d5; background:#f1edff; font-size:9px; font-weight:800; white-space:nowrap; }
        .ap-badge::before { content:''; width:5px; height:5px; border-radius:50%; background:currentColor; }
        .ap-badge.admin { color:#7139df; background:#f2ebff; } .ap-badge.agent { color:#246bdd; background:#eaf2ff; } .ap-badge.customer { color:#09885d; background:#e7f8f1; }
        .ap-empty { grid-column:1/-1; min-height:250px; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:40px; color:#8290a8; text-align:center; }
        .ap-empty-icon { width:56px; height:56px; margin-bottom:14px; display:grid; place-items:center; border-radius:17px; color:#6f43e8; background:#f0ebff; }
        .ap-empty strong { color:#253250; font-size:13px; }
        .ap-empty p { max-width:340px; margin:6px 0 0; font-size:10px; line-height:1.6; }
        .ap-form-card { padding:23px; }
        .ap-form-card input:not([type="checkbox"]):not([type="radio"]):not([type="hidden"]),.ap-form-card select,.ap-form-card textarea { color:#18233f!important; border-color:#dce4ef!important; background:white!important; box-shadow:0 2px 6px rgba(25,43,82,.025)!important; }
        .ap-form-card input:focus,.ap-form-card select:focus,.ap-form-card textarea:focus { border-color:#7b55ed!important; box-shadow:0 0 0 4px rgba(105,62,231,.09)!important; }
        .ap-form-card label { color:#5f6e89!important; font-weight:700!important; }
        .ap-form-card h3,.ap-form-card h4 { color:#17213c!important; }

        /* Legacy panel uplift: agent/customer screens inherit the same finish even
           before their markup is migrated to the semantic ap-* components. */
        .ops-main .neon-card { border:1px solid #e2e8f2!important; border-radius:21px!important; background:#fff!important; box-shadow:0 13px 34px rgba(24,43,84,.07)!important; }
        .ops-main .neon-card h1,.ops-main .neon-card h2,.ops-main .neon-card h3,.ops-main .neon-card h4,.ops-main .neon-card .text-white,.ops-main .neon-card .text-gray-200 { color:#17213c!important; }
        .ops-main .neon-card .text-slate-300 { color:#52617d!important; }.ops-main .neon-card .text-slate-400,.ops-main .neon-card .text-slate-500 { color:#7d8aa1!important; }
        .ops-main .neon-card input:not([type="checkbox"]):not([type="radio"]):not([type="hidden"]),.ops-main .neon-card select,.ops-main .neon-card textarea { color:#18233f!important; border-color:#dce4ef!important; background:#fff!important; box-shadow:0 2px 6px rgba(25,43,82,.025)!important; }
        .ops-main .neon-card input::placeholder,.ops-main .neon-card textarea::placeholder { color:#a2aec1!important; }
        .ops-main .neon-card input:focus,.ops-main .neon-card select:focus,.ops-main .neon-card textarea:focus { border-color:#7b55ed!important; box-shadow:0 0 0 4px rgba(105,62,231,.09)!important; outline:0!important; }
        .ops-main .neon-card thead { background:#f7f9fc!important; }.ops-main .neon-card th { color:#73819a!important; border-color:#e7ecf3!important; font-size:9px!important; font-weight:800!important; letter-spacing:.05em; text-transform:uppercase; }.ops-main .neon-card td { border-color:#edf1f6!important; }.ops-main .neon-card tbody tr { transition:.16s; }.ops-main .neon-card tbody tr:hover { background:#faf9ff!important; }
        .ops-main .neon-button { border:0!important; color:#fff!important; background:linear-gradient(135deg,#7e4ff4,#5a2cde)!important; box-shadow:0 9px 20px rgba(92,45,218,.24)!important; }
        .ops-main [id$="_country_dropdown"] { border:1px solid #dce4ef!important; background:#fff!important; box-shadow:0 16px 36px rgba(17,34,75,.14)!important; }.ops-main .country-option { color:#42516d!important; }.ops-main .country-option:hover { background:#f3f0ff!important; }
        .ops-main .neon-card .neon-button.text-white,.ops-main .neon-card button.neon-button,.ops-main .neon-card a.neon-button { color:#fff!important; }
        .premium-wizard{width:min(1240px,100%);max-width:none!important;margin-inline:auto}.wizard-hero{min-height:128px}.wizard-secure{position:relative;z-index:2;display:flex;align-items:center;gap:11px;padding:13px 15px;border:1px solid rgba(255,255,255,.2);border-radius:15px;background:rgba(255,255,255,.1);backdrop-filter:blur(10px)}.wizard-secure svg{width:24px;height:24px;fill:none;stroke:#70e6bd;stroke-width:1.8}.wizard-secure strong,.wizard-secure small{display:block}.wizard-secure strong{color:#fff;font-size:10px}.wizard-secure small{margin-top:3px;color:#b9c7e3;font-size:8px}.wizard-progress-card{position:relative;padding:21px 30px 17px;border:1px solid #e2e8f2;border-radius:18px;background:#fff;box-shadow:0 10px 28px rgba(24,43,84,.06)}
        .premium-wizard .step-circle{width:40px!important;height:40px!important;border-width:0!important;color:#8491a8!important;background:#eef2f7!important;box-shadow:0 0 0 5px #fff,0 0 0 6px #e5eaf2}.premium-wizard .step-circle.bg-violet-600{color:#fff!important;background:linear-gradient(145deg,#8252f6,#5a2de0)!important;box-shadow:0 0 0 5px #fff,0 0 0 6px #cfbff9,0 7px 17px rgba(91,45,218,.25)}.premium-wizard .step-circle.bg-emerald-600{color:#fff!important;background:linear-gradient(145deg,#35cea0,#0d9f71)!important}.premium-wizard .step-label{color:#77859e!important;font-size:10px!important;font-weight:750!important}.premium-wizard #progressLine{height:3px!important;background:linear-gradient(90deg,#7040e9,#2e76f0)!important}.premium-wizard .wizard-progress-card .bg-gray-800{height:3px!important;background:#edf0f5!important}
        .premium-wizard .neon-card{border:1px solid #e2e8f2!important;border-radius:21px!important;background:#fff!important;box-shadow:0 13px 34px rgba(24,43,84,.07)!important}.premium-wizard .neon-card h3,.premium-wizard .neon-card h4{color:#17213c!important}.premium-wizard label{color:#63728d!important}.premium-wizard [data-save-address-book-button]{border-color:#afe8d2!important;color:#087b58!important;background:linear-gradient(135deg,#eafaf4,#def7ed)!important;font-weight:800!important}.premium-wizard [onclick^="openAddressBookPicker"]{border-color:#ddd2fb!important;color:#6538db!important;background:#f4f0ff!important;font-weight:800!important}.premium-wizard #wizardNav{position:sticky;bottom:16px;z-index:20;margin:22px 0 0;padding:11px;border:1px solid rgba(218,225,238,.9);border-radius:16px;background:rgba(255,255,255,.92);box-shadow:0 14px 35px rgba(20,37,75,.13);backdrop-filter:blur(14px)}.premium-wizard #nextBtn{min-height:43px;color:#fff!important;border-radius:12px;background:linear-gradient(135deg,#7d4df3,#5a2cdd)!important;box-shadow:0 9px 20px rgba(92,45,218,.25)!important}.premium-wizard #prevBtn{min-height:43px;color:#50607c!important;border-color:#dce3ee!important;background:#fff!important}
        @media(min-width:768px){
            .ops-hero{border-color:#1b3264;background:radial-gradient(circle at 48% 10%,rgba(53,115,221,.28),transparent 28%),linear-gradient(120deg,#081535 0%,#10265c 55%,#2b2377 100%);box-shadow:0 22px 48px rgba(8,23,60,.2)}
            .ops-hero::before{opacity:.66;filter:saturate(1.1) contrast(1.05);-webkit-mask-image:linear-gradient(90deg,transparent 3%,#000 16%,#000 54%,transparent 69%);mask-image:linear-gradient(90deg,transparent 3%,#000 16%,#000 54%,transparent 69%)}
            .ops-hero::after{background:linear-gradient(90deg,rgba(5,17,48,.99) 0%,rgba(7,22,57,.78) 24%,rgba(10,29,69,.16) 48%,rgba(22,26,92,.15) 67%,rgba(35,26,100,.68) 100%)}
            .ops-kicker{color:#c4b8ff;background:rgba(124,91,240,.17);border:1px solid rgba(194,181,255,.14)}.ops-hero h1{color:#fff}.ops-hero p{color:#bdc9e2}.ops-command{border-color:rgba(255,255,255,.16);background:rgba(255,255,255,.1);box-shadow:0 14px 32px rgba(0,0,0,.16)}.ops-command-title,.ops-command-metric strong{color:#fff}.ops-command-metric span{color:#b9c5de}.ops-command-metric+.ops-command-metric{border-color:rgba(255,255,255,.14)}.ops-command-link{color:#c3b5ff}
            .ops-action{border-color:rgba(255,255,255,.15);background:rgba(255,255,255,.94);box-shadow:0 16px 34px rgba(3,12,39,.18)}
        }

        @media (max-width:900px) { .ap-kpi-grid { grid-template-columns:repeat(2,1fr); } .ap-page-head { align-items:flex-start; flex-direction:column; } .ap-toolbar { align-items:stretch; flex-direction:column; } }
        @media (max-width:600px) { .ap-page-head { min-height:0; padding:22px 19px; border-radius:19px; } .ap-page-head h1 { font-size:22px; } .ap-head-actions { width:100%; } .ap-head-actions .ap-button { flex:1; } .ap-kpi-grid { grid-template-columns:1fr 1fr; gap:9px; } .ap-kpi { min-height:96px; padding:15px; } .ap-toolbar { padding:11px; } .ap-tabs { overflow-x:auto; } .ap-tab { flex:0 0 auto; } }
        @media (max-width:767px) { .wizard-progress-card{padding:18px 9px 15px}.premium-wizard .step-circle{width:34px!important;height:34px!important}.premium-wizard .step-label{max-width:58px;text-align:center;white-space:normal!important}.wizard-secure{display:none} }

        /*
         * The admin header has more navigation items than the other roles. Keep
         * those labels readable and reclaim space from the secondary account area
         * before the compact, icon-only layout is needed.
         */
        @media (max-width:1700px) {
            .ops-topbar-inner.is-admin { gap:8px; }
            .ops-topbar-inner.is-admin .ops-logo { width:150px; }
            .ops-topbar-inner.is-admin .ops-profile { width:53px; flex:0 0 53px; margin-left:0; padding-left:10px; }
            .ops-topbar-inner.is-admin .ops-profile-copy { display:none; }
            .ops-topbar-inner.is-admin .ops-home-action span { display:none; }
            .ops-topbar-inner.is-admin .ops-home-action { width:39px; padding:0; justify-content:center; }
        }
        @media (max-width:1450px) {
            .ops-topbar { padding-left:15px; padding-right:15px; }
            .ops-nav-link { padding-inline:10px; }
            .ops-home-action span { display:none; }
            .ops-home-action { width:39px; padding:0; justify-content:center; }
            .ops-topbar-inner.is-admin { gap:5px; }
            .ops-topbar-inner.is-admin .ops-logo { width:135px; }
            .ops-topbar-inner.is-admin .ops-logo img { width:130px; }
            .ops-topbar-inner.is-admin .ops-nav { gap:0; }
            .ops-topbar-inner.is-admin .ops-nav-link { gap:4px; padding:9px 5px; font-size:9px; }
            .ops-topbar-inner.is-admin .ops-nav-link .ops-icon { width:15px; height:15px; }
            .ops-topbar-inner.is-admin .ops-top-actions { gap:4px; }
            .ops-topbar-inner.is-admin .ops-icon-btn,
            .ops-topbar-inner.is-admin .ops-home-action { width:35px; height:35px; }
            .ops-topbar-inner.is-admin .ops-profile { width:49px; flex-basis:49px; padding-left:7px; }
            .ops-topbar-inner.is-admin .ops-avatar { width:38px; height:38px; }
            .ops-hero-actions { width:52%; gap:10px; }
            .ops-hero-actions.has-five-actions { width:62%; }
            .ops-grid { grid-template-columns:minmax(0,1fr) minmax(0,1.2fr) 310px; }
        }
        @media (max-width:1200px) {
            .ops-topbar { height:auto; padding-top:7px; padding-bottom:7px; }
            .ops-topbar-inner { flex-wrap:wrap; row-gap:5px; }
            .ops-logo { order:0; }
            .ops-role-pill { order:0; margin-left:auto; }
            .ops-top-actions { order:0; }
            .ops-nav { order:1; flex:1 0 100%; overflow-x:auto; padding-bottom:2px; scrollbar-width:thin; }
            .ops-nav-link { flex:0 0 auto; }
        }
        @media (max-width:1120px) {
            .ops-topbar,.ops-main { padding-left:20px; padding-right:20px; }
            .ops-logo { width:158px; }
            .ops-profile-copy { display:none; }
            .ops-hero { min-height:350px; }
            .ops-hero-actions { top:auto; left:20px; right:20px; bottom:18px; width:auto; }
            .ops-hero-actions.has-five-actions { width:auto; }
            .ops-action { min-height:123px; padding:14px; }
            .ops-action-icon { width:38px; height:38px; margin-bottom:8px; }
            .ops-stats { grid-template-columns:repeat(3,1fr); }
            .ops-grid { grid-template-columns:1fr 1fr; }
            .ops-side { grid-column:1/-1; grid-template-columns:1fr 1fr; }
        }
        @media (max-width:767px) {
            body { padding-bottom:76px; }
            .ops-topbar,.ops-footer { display:none; }
            .ops-mobile-header { position:sticky; top:0; z-index:40; width:100%; max-width:100vw; height:76px; padding:0 14px; overflow:hidden; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid var(--ops-line); background:rgba(255,255,255,.97); backdrop-filter:blur(16px); }
            .ops-mobile-header img { width:146px; height:44px; object-fit:contain; }
            .ops-mobile-actions { min-width:0; flex:0 0 auto; display:flex; align-items:center; gap:8px; }
            .ops-mobile-actions .ops-avatar { width:38px; height:38px; }
            .ops-main { padding:22px 14px 28px; }
            .ops-hero { min-height:220px; margin-top:74px; overflow:visible; border:0; background:linear-gradient(180deg,#eef5fb 0%,#f9fbfd 48%,#e7f0f7 100%); }
            .ops-hero::before { background-image:url('/images/dashboard-logistics-hero-light.png'); background-position:center bottom; background-size:100% auto; background-repeat:no-repeat; -webkit-mask-image:none; mask-image:none; }
            .ops-hero::after { bottom:auto; height:220px; border-radius:18px; background:linear-gradient(90deg,rgba(249,252,255,.96) 0%,rgba(249,252,255,.76) 43%,rgba(249,252,255,.12) 76%,rgba(249,252,255,0) 100%); }
            .ops-hero-copy { width:100%; min-width:0; padding:22px 20px; }
            .ops-kicker { position:absolute; top:-67px; left:0; }
            .ops-hero h1 { position:absolute; top:-40px; left:0; color:var(--ops-ink); font-size:20px; white-space:nowrap; }
            .ops-hero p { display:none; }
            .ops-command { width:min(310px,calc(100vw - 68px)); }
            .ops-hero-actions { position:relative; inset:auto; width:auto; margin-top:14px; padding:20px 8px; grid-template-columns:repeat(4,minmax(0,1fr)); border:1px solid var(--ops-line); border-radius:16px; background:white; }
            .ops-hero-actions.has-five-actions { grid-template-columns:repeat(5,minmax(0,1fr)); }
            .ops-action { min-width:0; min-height:92px; padding:4px; border:0; box-shadow:none; text-align:center; }
            .ops-action:hover { transform:none; box-shadow:none; }
            .ops-action:not(:last-child) { border-right:1px solid var(--ops-line); border-radius:0; }
            .ops-action-icon { width:42px; height:42px; margin:0 auto 9px; }
            .ops-action strong { font-size:9px; line-height:1.3; }
            .ops-action small { display:none; }
            .ops-stats { grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; }
            .ops-stat { min-height:125px; padding:14px; }
            .ops-stat:last-child { grid-column:1/-1; }
            .ops-stat-main { display:block; }
            .ops-stat-label { margin-top:8px; }
            .ops-stat-value { margin-top:5px; }
            .ops-stat-note { position:absolute; left:14px; bottom:11px; margin:0; }
            .ops-grid { display:flex; flex-direction:column; }
            .ops-panel { width:100%; padding:16px 14px; }
            .ops-grid > :nth-child(2) { order:-1; }
            .ops-side { width:100%; display:contents; }
            .ops-table th { display:none; }
            .ops-table,.ops-table tbody,.ops-table tr,.ops-table td { display:block; }
            .ops-table tr { position:relative; min-height:68px; padding:11px 92px 11px 46px; border-top:1px solid #edf0f5; }
            .ops-table tr:first-child { border-top:0; }
            .ops-table td { padding:0; border:0; }
            .ops-table td:first-child { margin-bottom:4px; font-size:10px; }
            .ops-table td:nth-child(2) { color:#66758e; font-size:9px; }
            .ops-table td:nth-child(3) { display:none; }
            .ops-table td:nth-child(4) { position:absolute; right:2px; top:13px; }
            .ops-table td:last-child { position:absolute; right:2px; bottom:11px; }
            .ops-table tr::before { content:'□'; position:absolute; left:3px; top:14px; width:34px; height:34px; display:grid; place-items:center; border-radius:10px; color:var(--ops-purple); background:#f1edff; font-size:18px; }
            .ops-bottom-nav { position:fixed; z-index:50; left:0; right:0; bottom:0; height:76px; display:grid; grid-template-columns:repeat(6,minmax(0,1fr)); padding:7px 3px max(7px,env(safe-area-inset-bottom)); border-top:1px solid var(--ops-line); background:rgba(255,255,255,.97); backdrop-filter:blur(16px); box-shadow:0 -8px 26px rgba(25,39,79,.08); }
            .ops-bottom-link { position:relative; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:4px; color:#697791; font-size:8px; font-weight:650; }
            .ops-bottom-link.active { color:var(--ops-purple); }
            .ops-bottom-link.active::before { content:''; position:absolute; top:-7px; width:36px; height:3px; border-radius:99px; background:var(--ops-purple); }
            .ops-bottom-link .ops-icon { width:22px; height:22px; }
        }
        @media (max-width:420px) {
            .ops-main { padding-left:11px; padding-right:11px; }
            .ops-action strong { font-size:8px; }
            .ops-legend { grid-template-columns:repeat(2,1fr); }
        }
    </style>
    @stack('styles')
</head>
<body class="role-panel">
@php
    $user = auth()->user();
    $primaryRole = $user->primaryRoleName();
    $isAdmin = $primaryRole === 'admin';
    $isCustomer = $primaryRole === 'customer';
    $dashboardRoute = $user->dashboardRouteName();
    $shipmentsRoute = $user->shipmentsRouteName();
    $createRoute = $user->shipmentCreateRouteName();
    $addressRoute = $user->addressBookRouteName();
    $roleLabel = $isAdmin ? 'Admin' : ($isCustomer ? 'Customer' : 'Agent');
    $shipmentsPattern = $isAdmin ? 'admin.shipments.*' : ($isCustomer ? 'customer.shipments.*' : 'agent.shipments.*');
    $initials = str($user->name)->explode(' ')->take(2)->map(fn ($part) => str($part)->substr(0,1))->join('');
@endphp
<div>
    <header class="ops-topbar">
        <div class="ops-topbar-inner {{ $isAdmin ? 'is-admin' : ($isCustomer ? 'is-customer' : '') }}">
            <a class="ops-logo" href="{{ route($dashboardRoute) }}"><img src="/images/express-peek-logo.webp" alt="Express Peek"></a>
            <nav class="ops-nav" aria-label="Primary navigation">
                <a class="ops-nav-link {{ request()->routeIs($dashboardRoute) ? 'active' : '' }}" href="{{ route($dashboardRoute) }}" aria-label="Dashboard" title="Dashboard"><svg class="ops-icon" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg><span>Dashboard</span></a>
                @if($isCustomer)
                    <a class="ops-nav-link {{ request()->routeIs('customer.shipments.*') ? 'active' : '' }}" href="{{ route('customer.shipments.index') }}"><svg class="ops-icon" viewBox="0 0 24 24"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/><path d="m4.5 7.5 7.5 4 7.5-4M12 11.5V21"/></svg><span>Shipments</span></a>
                    <a class="ops-nav-link" href="{{ route('customer.dashboard') }}#track"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg><span>Tracking</span></a>
                    <a class="ops-nav-link {{ request()->routeIs('customer.address-book.*') ? 'active' : '' }}" href="{{ route('customer.address-book.page') }}"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M5 4h14v17H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/><path d="M8 9h7M8 13h7"/></svg><span>Address Book</span></a>
                    <a class="ops-nav-link {{ request()->routeIs('customer.sourcing-requests.*') ? 'active' : '' }}" href="{{ route('customer.sourcing-requests.index') }}"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M6 8h12l1 13H5L6 8Z"/><path d="M9 9V6a3 3 0 0 1 6 0v3"/></svg><span>Requests</span></a>
                @elseif($isAdmin)
                    <a class="ops-nav-link {{ request()->routeIs('admin.shipments.*') ? 'active' : '' }}" href="{{ route('admin.shipments.index') }}" aria-label="Shipments" title="Shipments"><svg class="ops-icon" viewBox="0 0 24 24"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/><path d="m4.5 7.5 7.5 4 7.5-4M12 11.5V21"/></svg><span>Shipments</span></a>
                    <a class="ops-nav-link {{ request()->routeIs('volumetric-weight-calculator') ? 'active' : '' }}" href="{{ route('volumetric-weight-calculator') }}" aria-label="Volumetric Weight Calculator" title="Volumetric Weight Calculator"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M4 19V5h16v14H4Z"/><path d="M8 5v4M12 5v2M16 5v4M8 19v-4M12 19v-2M16 19v-4"/></svg><span>Calculator</span></a>
                    <a class="ops-nav-link" href="{{ route('admin.dashboard') }}#track" aria-label="Tracking" title="Tracking"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg><span>Tracking</span></a>
                    <a class="ops-nav-link" href="{{ route('quote') }}" aria-label="Quotes" title="Quotes" data-open-quote-modal><svg class="ops-icon" viewBox="0 0 24 24"><path d="M6 3h10l3 3v15H6z"/><path d="M16 3v4h4M9 11h6M9 15h6"/></svg><span>Quotes</span></a>
                    <a class="ops-nav-link {{ request()->routeIs($createRoute) ? 'active' : '' }}" href="{{ route($createRoute) }}" aria-label="Create Shipment" title="Create Shipment"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg><span>Create Shipment</span></a>
                    <a class="ops-nav-link {{ request()->routeIs($addressRoute) ? 'active' : '' }}" href="{{ route($addressRoute) }}" aria-label="Address Book" title="Address Book"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M5 4h14v17H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/><path d="M8 9h7M8 13h7"/></svg><span>Address Book</span></a>
                    <a class="ops-nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}" aria-label="Users" title="Users"><svg class="ops-icon" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3"/><path d="M3 20a6 6 0 0 1 12 0M16 4a3 3 0 0 1 0 6M17 14a5 5 0 0 1 4 5"/></svg><span>Users</span></a>
                    <a class="ops-nav-link {{ request()->routeIs('admin.sourcing-requests.*') ? 'active' : '' }}" href="{{ route('admin.sourcing-requests.index') }}" aria-label="Sourcing Requests" title="Sourcing Requests"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M6 8h12l1 13H5L6 8Z"/><path d="M9 9V6a3 3 0 0 1 6 0v3"/></svg><span>Sourcing Requests</span></a>
                    <a class="ops-nav-link {{ request()->routeIs('admin.rates.import.*') ? 'active' : '' }}" href="{{ route('admin.rates.import.create') }}" aria-label="Rate Upload" title="Rate Upload"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M12 16V4M7 9l5-5 5 5M4 14v6h16v-6"/></svg><span>Rate Upload</span></a>
                @else
                    <a class="ops-nav-link {{ request()->routeIs('agent.shipments.*') ? 'active' : '' }}" href="{{ route('agent.shipments.index') }}"><svg class="ops-icon" viewBox="0 0 24 24"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/><path d="m4.5 7.5 7.5 4 7.5-4M12 11.5V21"/></svg><span>Delivery Queue</span></a>
                    <a class="ops-nav-link {{ request()->routeIs('volumetric-weight-calculator') ? 'active' : '' }}" href="{{ route('volumetric-weight-calculator') }}" title="Volumetric Weight Calculator"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M4 19V5h16v14H4Z"/><path d="M8 5v4M12 5v2M16 5v4M8 19v-4M12 19v-2M16 19v-4"/></svg><span>Calculator</span></a>
                    <a class="ops-nav-link" href="{{ route('agent.dashboard') }}#track"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg><span>Tracking</span></a>
                    <a class="ops-nav-link" href="{{ route('quote') }}" data-open-quote-modal><svg class="ops-icon" viewBox="0 0 24 24"><path d="M6 3h10l3 3v15H6z"/><path d="M16 3v4h4M9 11h6M9 15h6"/></svg><span>Quotes</span></a>
                    <a class="ops-nav-link {{ request()->routeIs($createRoute) ? 'active' : '' }}" href="{{ route($createRoute) }}"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg><span>Create Shipment</span></a>
                    <a class="ops-nav-link {{ request()->routeIs($addressRoute) ? 'active' : '' }}" href="{{ route($addressRoute) }}"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M5 4h14v17H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/><path d="M8 9h7M8 13h7"/></svg><span>Address Book</span></a>
                @endif
            </nav>
            <span class="ops-role-pill">{{ $roleLabel }}</span>
            <div class="ops-top-actions">
                <a class="ops-icon-btn" href="{{ route($shipmentsRoute) }}" aria-label="Shipment updates"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg>@hasSection('notification-count')<span class="ops-notice">@yield('notification-count')</span>@endif</a>
                <a class="ops-icon-btn" href="{{ route('customer-service') }}" aria-label="Support"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M21 15a3 3 0 0 1-3 3H8l-5 3V6a3 3 0 0 1 3-3h12a3 3 0 0 1 3 3Z"/><path d="M8 10h.01M12 10h.01M16 10h.01"/></svg></a>
                <a class="ops-home-action" href="{{ route('home') }}" aria-label="Back to Home"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M3 11 12 3l9 8v10h-6v-6H9v6H3V11Z"/></svg><span>Back to Home</span></a>
                <a class="ops-profile" href="{{ route('profile.edit') }}">@if($user->avatarUrl())<img class="ops-avatar" src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}">@else<span class="ops-avatar">{{ strtoupper($initials) }}</span>@endif<span class="ops-profile-copy"><span class="ops-profile-name">{{ $user->name }}</span><span class="ops-profile-email">{{ $user->email }}</span></span></a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="ops-icon-btn" type="submit" aria-label="Sign out"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M10 17l5-5-5-5M15 12H3M14 4h5a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-5"/></svg></button></form>
            </div>
        </div>
    </header>

    <header class="ops-mobile-header"><a href="{{ route($dashboardRoute) }}"><img src="/images/express-peek-logo.webp" alt="Express Peek"></a><div class="ops-mobile-actions"><span class="ops-role-pill">{{ $roleLabel }}</span><a href="{{ route('profile.edit') }}">@if($user->avatarUrl())<img class="ops-avatar" src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}">@else<span class="ops-avatar">{{ strtoupper($initials) }}</span>@endif</a></div></header>

    <main class="ops-main panel-content">
        @if(session('success'))<div class="ops-flash success">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="ops-flash error">{{ session('error') }}</div>@endif
        @yield('content')
    </main>

    <footer class="ops-footer"><span>© {{ now()->year }} Express Peek. All rights reserved.</span><span>Secure logistics operations</span></footer>

    <nav class="ops-bottom-nav" aria-label="Mobile navigation">
        <a class="ops-bottom-link {{ request()->routeIs($dashboardRoute) ? 'active' : '' }}" href="{{ route($dashboardRoute) }}"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M3 11 12 3l9 8v10h-6v-6H9v6H3V11Z"/></svg><span>Dashboard</span></a>
        <a class="ops-bottom-link {{ request()->routeIs($shipmentsPattern) ? 'active' : '' }}" href="{{ route($shipmentsRoute) }}"><svg class="ops-icon" viewBox="0 0 24 24"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/><path d="m4.5 7.5 7.5 4 7.5-4M12 11.5V21"/></svg><span>Shipments</span></a>
        @if($isCustomer)
            <a class="ops-bottom-link" href="{{ route('customer.dashboard') }}#track"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg><span>Tracking</span></a>
            <a class="ops-bottom-link {{ request()->routeIs('customer.sourcing-requests.*') ? 'active' : '' }}" href="{{ route('customer.sourcing-requests.index') }}"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M6 8h12l1 13H5L6 8Z"/></svg><span>Requests</span></a>
        @elseif($isAdmin)
            <a class="ops-bottom-link" href="{{ route('admin.dashboard') }}#track"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg><span>Tracking</span></a>
            <a class="ops-bottom-link" href="{{ route('quote') }}" data-open-quote-modal><svg class="ops-icon" viewBox="0 0 24 24"><path d="M6 3h10l3 3v15H6z"/><path d="M9 11h6M9 15h6"/></svg><span>Quote</span></a>
            <a class="ops-bottom-link {{ request()->routeIs($createRoute) ? 'active' : '' }}" href="{{ route($createRoute) }}"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg><span>Create</span></a>
        @else
            <a class="ops-bottom-link" href="{{ route('agent.dashboard') }}#track"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg><span>Tracking</span></a>
            <a class="ops-bottom-link" href="{{ route('quote') }}" data-open-quote-modal><svg class="ops-icon" viewBox="0 0 24 24"><path d="M6 3h10l3 3v15H6z"/><path d="M9 11h6M9 15h6"/></svg><span>Quote</span></a>
        @endif
        <a class="ops-bottom-link" href="{{ route('home') }}"><svg class="ops-icon" viewBox="0 0 24 24"><path d="M3 11 12 3l9 8v10h-6v-6H9v6H3V11Z"/></svg><span>Home</span></a>
        @unless($isAdmin)
            <a class="ops-bottom-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.edit') }}"><svg class="ops-icon" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg><span>Profile</span></a>
        @endunless
    </nav>
    @php
        $quoteCountries = $countries ?? \App\Models\CountryZone::query()
            ->select('country_code', \Illuminate\Support\Facades\DB::raw('MAX(country_name) as original_name'))
            ->whereNotNull('country_code')
            ->groupBy('country_code')
            ->get()
            ->map(fn ($country) => (object) [
                'country_code' => $country->country_code,
                'country_name' => \App\Helpers\CountryHelper::getCanonicalName($country->country_code),
            ])
            ->filter(fn ($country) => !empty($country->country_name))
            ->sortBy('country_name')
            ->values();
    @endphp
    @include('components.quote-modal', ['countries' => $quoteCountries])
    @stack('scripts')
</div>
</body>
</html>
