@extends('layouts.operations-dashboard')

@section('page-title', 'Shop from Bangladesh')
@section('page-subtitle', 'Tell us what you want to buy, and we will handle the rest.')

@push('styles')
<style>
    .sourcing-hero {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #4c1d95 100%);
        position: relative;
        overflow: hidden;
    }
    .sourcing-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .step-line::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 100%;
        width: 100%;
        height: 2px;
        background: linear-gradient(90deg, #7c3aed, transparent);
        transform: translateY(-50%);
    }
    .form-card {
        background: rgba(255,255,255,0.97);
        backdrop-filter: blur(20px);
    }
    .sourcing-form-wrapper input:not([type="checkbox"]):not([type="radio"]):not([type="hidden"]), 
    .sourcing-form-wrapper select, 
    .sourcing-form-wrapper textarea,
    .input-field {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.75rem !important;
        font-size: 0.875rem !important;
        color: #0f172a !important;
        background: #ffffff !important;
        transition: border-color 0.2s, box-shadow 0.2s;
        outline: none;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
    }
    .sourcing-form-wrapper input:focus, .sourcing-form-wrapper select:focus, .sourcing-form-wrapper textarea:focus,
    .input-field:focus {
        border-color: #8b5cf6 !important;
        box-shadow: 0 0 0 4px rgba(139,92,246,0.1) !important;
    }
    .sourcing-form-wrapper input::placeholder, .sourcing-form-wrapper textarea::placeholder,
    .input-field::placeholder { 
        color: #94a3b8 !important; 
    }
    .form-label {
        display: block;
        font-size: 0.8125rem;
        font-weight: 500;
        color: #64748b;
        margin-bottom: 0.375rem;
    }
    .upload-zone {
        border: 2px dashed #e2e8f0;
        border-radius: 0.75rem;
        padding: 2rem 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        background: #ffffff;
    }
    .upload-zone:hover, .upload-zone.dragover {
        border-color: #8b5cf6;
        background: #faf5ff;
    }
    .phone-wrapper {
        display: flex;
        gap: 0;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        overflow: hidden;
        background: #ffffff;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .phone-wrapper:focus-within {
        border-color: #8b5cf6;
        box-shadow: 0 0 0 4px rgba(139,92,246,0.1);
    }
    .phone-select {
        border: none;
        background: transparent;
        padding: 0.75rem 0.625rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #334155;
        outline: none;
        flex-shrink: 0;
        cursor: pointer;
        border-right: 1px solid #e2e8f0;
    }
    .phone-input {
        border: none;
        background: transparent;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        color: #0f172a;
        outline: none;
        flex: 1;
        min-width: 0;
    }
    .submit-btn {
        width: 100%;
        padding: 1rem 2rem;
        background: linear-gradient(135deg, #7c3aed, #2563eb);
        color: white;
        font-weight: 800;
        font-size: 1rem;
        border: none;
        border-radius: 1rem;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.625rem;
        box-shadow: 0 8px 25px rgba(124,58,237,0.35);
    }
    .submit-btn:hover { opacity: 0.92; transform: translateY(-1px); box-shadow: 0 12px 30px rgba(124,58,237,0.4); }
    .submit-btn:active { transform: translateY(0); }
    .submit-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
    .error-msg { color: #dc2626; font-size: 0.78rem; margin-top: 0.35rem; font-weight: 500; }
    @keyframes bounceIn { 0%{transform:scale(0.6);opacity:0} 70%{transform:scale(1.05)} 100%{transform:scale(1);opacity:1} }
    .input-with-icon-left {
        padding-left: 2.5rem !important;
    }
    .input-with-icon-right {
        padding-right: 2.5rem !important;
    }
</style>
@endpush

@section('content')

{{-- ===== HERO ===== --}}
<section class="sourcing-hero py-12 md:py-24">
    <div class="relative max-w-5xl mx-auto px-4 sm:px-6 text-center">
        {{-- Badge --}}
        <div class="inline-flex items-center gap-2 bg-white/15 border border-white/25 text-white text-xs font-bold px-4 py-2 rounded-full mb-6 backdrop-blur-sm">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            🇧🇩 Shop from Bangladesh
        </div>

        <h1 class="text-3xl md:text-5xl lg:text-6xl font-black text-white leading-tight mb-5">
            Want Something from<br class="hidden sm:block">
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-violet-300 to-pink-300">Bangladesh?</span>
        </h1>
        <p class="text-violet-200 text-base md:text-lg max-w-2xl mx-auto mb-10 leading-relaxed">
            Living abroad? We'll find it, buy it, and deliver it right to your door — anywhere in the world.
            Just tell us what you need.
        </p>

        {{-- 3-Step Process --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 max-w-2xl mx-auto mb-2">
            @foreach([
                ['num' => '1', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'title' => 'Submit Request', 'desc' => 'Fill the form below'],
                ['num' => '2', 'icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'title' => 'We Contact You', 'desc' => 'Via WhatsApp'],
                ['num' => '3', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'title' => 'We Ship It', 'desc' => 'To your destination'],
            ] as $step)
            <div class="bg-white/10 border border-white/20 rounded-2xl p-4 backdrop-blur-sm">
                <div class="w-9 h-9 rounded-xl bg-violet-500/40 border border-violet-400/40 flex items-center justify-center mx-auto mb-2.5">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] }}"/>
                    </svg>
                </div>
                <p class="text-white font-black text-xs sm:text-sm">{{ $step['title'] }}</p>
                <p class="text-violet-300 text-xs mt-0.5">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== FORM SECTION ===== --}}
<section class="max-w-3xl mx-auto px-4 sm:px-6 -mt-8 pb-20 sourcing-form-wrapper">
    <div class="form-card rounded-3xl shadow-2xl border border-white/10 overflow-hidden">

        {{-- Form Header --}}
        <div class="bg-gradient-to-r from-violet-600 to-blue-700 px-6 sm:px-8 py-6">
            <h2 class="text-lg sm:text-xl font-black text-white">Shop From Bangladesh Form</h2>
            <p class="text-violet-200 text-xs sm:text-sm mt-1">All fields marked * are required. We'll reply on WhatsApp within 24 hours.</p>
        </div>

        {{-- Success Flash --}}
        @if(session('success'))
        <div class="mx-6 sm:mx-8 mt-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
        <script>
            localStorage.removeItem('sourcing_form_data');
            localStorage.removeItem('sourcing_cart');
        </script>
        @endif

        <form action="{{ $storeRoute ?? route('customer.sourcing-requests.store') }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-7" id="sourcing-form">
            @csrf

            {{-- ─── SECTION: Your Info ─── --}}
            <div>
                @if(isset($isAdmin) && $isAdmin)
                <h3 class="text-xs font-black text-violet-600 uppercase tracking-widest mb-5 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-violet-100 text-violet-600 flex items-center justify-center text-[10px] font-black">1</span>
                    Customer Contact Information
                </h3>
                
                {{-- Business Contact Toggle --}}
                <div class="flex items-center gap-3 mb-5">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="customer_is_business" value="1" id="customer_is_business_check"
                            class="w-4 h-4 rounded border-gray-300 text-violet-600 bg-white focus:ring-violet-500"
                            onchange="document.getElementById('customer_business_section').classList.toggle('hidden', !this.checked)"
                            {{ old('customer_is_business') ? 'checked' : '' }}>
                        <span class="text-sm font-semibold text-gray-700">Business Contact</span>
                    </label>
                </div>

                {{-- Name --}}
                <div class="mb-5">
                    <label class="form-label">Customer Name *</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" placeholder="Full name" class="input-field @error('customer_name') border-red-400 @enderror" required>
                    @error('customer_name') <p class="error-msg mt-1.5">{{ $message }}</p> @enderror
                </div>

                {{-- Company --}}
                <div id="customer_business_section" class="{{ old('customer_is_business') ? '' : 'hidden' }} mb-5">
                    <label class="form-label">Company</label>
                    <input type="text" name="customer_company" value="{{ old('customer_company') }}" placeholder="Company name" class="input-field @error('customer_company') border-red-400 @enderror">
                    @error('customer_company') <p class="error-msg mt-1.5">{{ $message }}</p> @enderror
                </div>
                @else
                {{-- Customer: editable contact info pre-filled from their profile --}}
                @php $authUser = auth()->user(); @endphp
                <h3 class="text-xs font-black text-violet-600 uppercase tracking-widest mb-5 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-violet-100 text-violet-600 flex items-center justify-center text-[10px] font-black">1</span>
                    Your Contact Details
                </h3>

                {{-- Full Name --}}
                <div class="mb-5">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name', $authUser->name ?? '') }}" placeholder="Full name" class="input-field @error('customer_name') border-red-400 @enderror" required>
                    @error('customer_name') <p class="error-msg mt-1.5">{{ $message }}</p> @enderror
                </div>

                {{-- Email --}}
                <div class="mb-5">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="customer_email" value="{{ old('customer_email', $authUser->email ?? '') }}" placeholder="email@example.com" class="input-field @error('customer_email') border-red-400 @enderror">
                    @error('customer_email') <p class="error-msg mt-1.5">{{ $message }}</p> @enderror
                </div>

                {{-- Phone Type + WhatsApp Number --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                    <div class="md:col-span-1">
                        <label class="form-label">Phone Type</label>
                        <select name="customer_phone_type" class="input-field">
                            <option @selected(old('customer_phone_type') === 'Office')>Office</option>
                            <option @selected(old('customer_phone_type', 'Mobile') === 'Mobile')>Mobile</option>
                            <option @selected(old('customer_phone_type') === 'Home')>Home</option>
                            <option @selected(old('customer_phone_type') === 'Fax')>Fax</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="form-label">Phone Number *</label>
                        <div
                            x-data="{
                                openPhone: false, qPhone: '', hiPhone: -1,
                                selectedCode: '{{ old('whatsapp_country_code', $authUser->phone_country_code ?? '+880') }}',
                                get allCodes() {
                                    return [
                                        {iso:'BD', c:'+880',n:'Bangladesh',f:'🇧🇩'}, {iso:'US', c:'+1',n:'United States/Canada',f:'🇺🇸'},
                                        {iso:'GB', c:'+44',n:'United Kingdom',f:'🇬🇧'}, {iso:'AU', c:'+61',n:'Australia',f:'🇦🇺'},
                                        {iso:'DE', c:'+49',n:'Germany',f:'🇩🇪'}, {iso:'FR', c:'+33',n:'France',f:'🇫🇷'},
                                        {iso:'IT', c:'+39',n:'Italy',f:'🇮🇹'}, {iso:'ES', c:'+34',n:'Spain',f:'🇪🇸'},
                                        {iso:'NL', c:'+31',n:'Netherlands',f:'🇳🇱'}, {iso:'SE', c:'+46',n:'Sweden',f:'🇸🇪'},
                                        {iso:'NO', c:'+47',n:'Norway',f:'🇳🇴'}, {iso:'DK', c:'+45',n:'Denmark',f:'🇩🇰'},
                                        {iso:'CH', c:'+41',n:'Switzerland',f:'🇨🇭'}, {iso:'AE', c:'+971',n:'UAE',f:'🇦🇪'},
                                        {iso:'QA', c:'+974',n:'Qatar',f:'🇶🇦'}, {iso:'SA', c:'+966',n:'Saudi Arabia',f:'🇸🇦'},
                                        {iso:'SG', c:'+65',n:'Singapore',f:'🇸🇬'}, {iso:'MY', c:'+60',n:'Malaysia',f:'🇲🇾'},
                                        {iso:'JP', c:'+81',n:'Japan',f:'🇯🇵'}, {iso:'KR', c:'+82',n:'South Korea',f:'🇰🇷'},
                                        {iso:'IN', c:'+91',n:'India',f:'🇮🇳'}, {iso:'PK', c:'+92',n:'Pakistan',f:'🇵🇰'},
                                        {iso:'LK', c:'+94',n:'Sri Lanka',f:'🇱🇰'}, {iso:'NZ', c:'+64',n:'New Zealand',f:'🇳🇿'},
                                        {iso:'ZA', c:'+27',n:'South Africa',f:'🇿🇦'}, {iso:'BR', c:'+55',n:'Brazil',f:'🇧🇷'},
                                        {iso:'MX', c:'+52',n:'Mexico',f:'🇲🇽'}, {iso:'CA', c:'+1',n:'Canada',f:'🇨🇦'},
                                        {iso:'RU', c:'+7',n:'Russia/Kazakhstan',f:'🇷🇺'}, {iso:'TR', c:'+90',n:'Turkey',f:'🇹🇷'},
                                        {iso:'EG', c:'+20',n:'Egypt',f:'🇪🇬'}, {iso:'NG', c:'+234',n:'Nigeria',f:'🇳🇬'},
                                        {iso:'KE', c:'+254',n:'Kenya',f:'🇰🇪'}, {iso:'MM', c:'+95',n:'Myanmar',f:'🇲🇲'},
                                        {iso:'PH', c:'+63',n:'Philippines',f:'🇵🇭'}, {iso:'ID', c:'+62',n:'Indonesia',f:'🇮🇩'},
                                        {iso:'VN', c:'+84',n:'Vietnam',f:'🇻🇳'}, {iso:'TH', c:'+66',n:'Thailand',f:'🇹🇭'},
                                        {iso:'HK', c:'+852',n:'Hong Kong',f:'🇭🇰'}, {iso:'KW', c:'+965',n:'Kuwait',f:'🇰🇼'},
                                        {iso:'OM', c:'+968',n:'Oman',f:'🇴🇲'}, {iso:'BH', c:'+973',n:'Bahrain',f:'🇧🇭'}
                                    ];
                                },
                                get opts() {
                                    if (!this.qPhone) return this.allCodes;
                                    const s = this.qPhone.toLowerCase();
                                    return this.allCodes.filter(c => c.n.toLowerCase().includes(s) || c.c.includes(s));
                                },
                                pick(c) { this.selectedCode = c.c; this.qPhone = ''; this.openPhone = false; this.hiPhone = -1; },
                                getSelected() { return this.allCodes.find(c => c.c === this.selectedCode) || this.allCodes[0]; }
                            }"
                            @click.away="openPhone = false; qPhone = '';"
                            @update-phone-country.window="let match = allCodes.find(x => x.iso === $event.detail); if (match) selectedCode = match.c;"
                            class="relative flex gap-0 border-[1.5px] border-gray-200 rounded-2xl overflow-visible bg-gray-50 focus-within:bg-white focus-within:border-violet-500 focus-within:ring-4 focus-within:ring-violet-500/10 transition-all @error('whatsapp_number') border-red-400 @enderror"
                        >
                            {{-- Dropdown Trigger --}}
                            <div class="relative flex-shrink-0 border-r border-gray-200">
                                <button type="button" @click="openPhone = !openPhone" class="h-full px-4 flex items-center gap-2 font-semibold text-gray-700 hover:text-violet-600 transition-colors">
                                    <span x-text="getSelected().iso" class="text-xs font-bold text-slate-600"></span>
                                    <span x-text="getSelected().c" class="text-sm font-bold"></span>
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                {{-- Dropdown Menu --}}
                                <div x-show="openPhone" x-transition class="absolute top-full left-0 mt-2 w-64 bg-white border border-gray-200 rounded-2xl shadow-2xl z-50 overflow-hidden flex flex-col max-h-72">
                                    <div class="p-2 border-b border-gray-200 sticky top-0 bg-white z-10">
                                        <input type="text" x-model="qPhone" placeholder="Search country or code..." class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-violet-500 focus:border-transparent outline-none" @click.stop>
                                    </div>
                                    <div class="overflow-y-auto flex-1 p-1">
                                        <template x-for="(c, i) in opts" :key="c.c + c.n">
                                            <button type="button" @click="pick(c)" :class="c.c === selectedCode ? 'bg-violet-50 text-violet-700' : 'text-gray-700 hover:bg-gray-50'" class="w-full text-left flex items-center gap-3 px-3 py-2 rounded-xl text-sm transition-colors mb-0.5">
                                                <span x-text="c.f" class="text-base"></span>
                                                <span x-text="c.n" class="truncate flex-1"></span>
                                                <span x-text="c.c" class="font-mono text-xs text-slate-400"></span>
                                            </button>
                                        </template>
                                        <div x-show="opts.length === 0" class="px-4 py-3 text-sm text-slate-400 text-center">No countries found</div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="whatsapp_country_code" :value="selectedCode">
                            <input
                                type="tel"
                                id="whatsapp_number"
                                name="whatsapp_number"
                                value="{{ old('whatsapp_number', $authUser->phone ?? '') }}"
                                placeholder="1968524222"
                                class="w-full bg-transparent border-none px-4 py-3.5 text-sm text-gray-900 focus:ring-0 outline-none placeholder:text-slate-400"
                                required
                            >
                        </div>
                        @error('whatsapp_number') <p class="error-msg mt-1.5">{{ $message }}</p> @enderror
                        <p class="text-xs text-slate-400 mt-2 pl-1">We'll reach out to this number on WhatsApp.</p>
                    </div>
                </div>

                <h3 class="text-xs font-black text-violet-600 uppercase tracking-widest mb-5 flex items-center gap-2 pt-3 border-t border-gray-100">
                    <span class="w-5 h-5 rounded-full bg-violet-100 text-violet-600 flex items-center justify-center text-[10px] font-black">2</span>
                    Delivery Destination
                </h3>
                @endif

                {{-- Removed Address and Postal/City/State fields per request --}}

                {{-- Country --}}
                <div class="mb-5">
                    <div
                        x-data="{
                            open: false, q: '', hi: -1,
                            selectedCode: '{{ old('destination_country_code', '') }}',
                            selectedName: '{{ old('destination_country', '') }}',
                            get opts() {
                                const all = window.SOURCING_COUNTRIES || [];
                                if (!this.q) return all;
                                const s = this.q.toLowerCase().trim();
                                const byStart = [], byContains = [];
                                for (const c of all) {
                                    const name = c.name.toLowerCase();
                                    const code = c.code.toLowerCase();
                                    let matchesAlias = false;
                                    if (s === 'us' || s === 'usa') {
                                        if (code === 'us' || name === 'united states') matchesAlias = true;
                                    } else if (s === 'uk') {
                                        if (code === 'gb' || name === 'united kingdom') matchesAlias = true;
                                    } else if (s === 'uae') {
                                        if (code === 'ae' || name === 'united arab emirates') matchesAlias = true;
                                    }
                                    if (name.startsWith(s) || matchesAlias || code === s) { byStart.push(c); continue; }
                                    if (name.includes(s) || code.startsWith(s)) byContains.push(c);
                                }
                                return [...byStart, ...byContains];
                            },
                            pick(c) { 
                                this.selectedCode = c.code; 
                                this.selectedName = c.name; 
                                this.q = c.name; 
                                this.open = false; 
                                this.hi = -1; 
                                $dispatch('update-phone-country', c.code);
                            },
                            clear() { this.selectedCode = ''; this.selectedName = ''; this.q = ''; },
                            onFocus() { this.open = true; if (this.selectedCode) this.q = ''; },
                            onKey(e) {
                                if (!this.open) return;
                                if (e.key === 'ArrowDown') { e.preventDefault(); this.hi = Math.min(this.hi + 1, this.opts.length - 1); }
                                else if (e.key === 'ArrowUp') { e.preventDefault(); this.hi = Math.max(this.hi - 1, 0); }
                                else if (e.key === 'Enter') { e.preventDefault(); if (this.hi >= 0) this.pick(this.opts[this.hi]); }
                                else if (e.key === 'Escape') { this.open = false; }
                            }
                        }"
                        @click.away="open = false; if (!selectedCode) q = ''; else q = selectedName;"
                        class="relative"
                    >
                        <label class="form-label">Destination Country *</label>
                        <div class="relative">
                            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input
                                type="text"
                                x-model="q"
                                :placeholder="selectedName || 'Search your country...'"
                                @focus="onFocus()"
                                @keydown="onKey($event)"
                                autocomplete="off"
                                class="input-field input-with-icon-left input-with-icon-right @error('destination_country') border-red-400 @enderror"
                                id="destination_country_search"
                                required
                            >
                            <button x-show="selectedCode" type="button" @click="clear()" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-500 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div x-show="open" x-transition class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-2xl shadow-2xl z-50 max-h-56 overflow-y-auto">
                            <template x-for="(c, i) in opts" :key="c.code">
                                <div @click="pick(c)" :class="hi === i ? 'bg-violet-50 text-violet-700' : 'text-gray-700 hover:bg-gray-50'"
                                    class="flex items-center justify-between px-4 py-3 text-sm cursor-pointer transition-colors min-h-[44px]">
                                    <span x-text="c.name"></span>
                                    <span x-text="c.code" class="text-xs font-mono text-slate-400 ml-2 shrink-0"></span>
                                </div>
                            </template>
                            <div x-show="opts.length === 0" class="px-4 py-3 text-sm text-slate-400 italic">No countries found</div>
                        </div>

                        {{-- Hidden inputs for form submission --}}
                        <input type="hidden" name="destination_country" :value="selectedName">
                        <input type="hidden" name="destination_country_code" :value="selectedCode">
                        
                        @error('destination_country')
                            <p class="error-msg mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>


                    {{-- Address Details --}}
                    <div class="mb-4">
                        <label for="destination_address" class="form-label">Address *</label>
                        <input type="text" id="destination_address" name="destination_address" value="{{ old('destination_address') }}" placeholder="Street address" class="input-field @error('destination_address') border-red-400 @enderror" required>
                        @error('destination_address') <p class="error-msg mt-1.5">{{ $message }}</p> @enderror
                    </div>
                    <div class="mb-4">
                        <input type="text" name="destination_address2" value="{{ old('destination_address2') }}" placeholder="Address line 2 (optional)" class="input-field">
                    </div>
                    <div class="mb-5">
                        <input type="text" name="destination_address3" value="{{ old('destination_address3') }}" placeholder="Address line 3 (optional)" class="input-field">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label for="destination_postal_code" class="form-label">Postal Code</label>
                            <input type="text" id="destination_postal_code" name="destination_postal_code" value="{{ old('destination_postal_code') }}" placeholder="ZIP / Postal Code" class="input-field @error('destination_postal_code') border-red-400 @enderror">
                            @error('destination_postal_code') <p class="error-msg mt-1.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="destination_city" class="form-label">City *</label>
                            <input type="text" id="destination_city" name="destination_city" value="{{ old('destination_city') }}" placeholder="City" class="input-field @error('destination_city') border-red-400 @enderror" required>
                            @error('destination_city') <p class="error-msg mt-1.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="destination_state" class="form-label">State</label>
                            <input type="text" id="destination_state" name="destination_state" value="{{ old('destination_state') }}" placeholder="State / Province" class="input-field @error('destination_state') border-red-400 @enderror">
                            @error('destination_state') <p class="error-msg mt-1.5">{{ $message }}</p> @enderror
                        </div>
                    </div>

                @if(isset($isAdmin) && $isAdmin)
                {{-- Email --}}
                <div class="mb-5">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="customer_email" value="{{ old('customer_email') }}" placeholder="email@example.com" class="input-field @error('customer_email') border-red-400 @enderror">
                    @error('customer_email') <p class="error-msg mt-1.5">{{ $message }}</p> @enderror
                </div>

                {{-- Phone --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                    <div class="md:col-span-1">
                        <label class="form-label">Phone Type</label>
                        <select name="customer_phone_type" class="input-field">
                            <option @selected(old('customer_phone_type') === 'Office')>Office</option>
                            <option @selected(old('customer_phone_type', 'Mobile') === 'Mobile')>Mobile</option>
                            <option @selected(old('customer_phone_type') === 'Home')>Home</option>
                            <option @selected(old('customer_phone_type') === 'Fax')>Fax</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="form-label">WhatsApp Number *</label>
                        <div
                            x-data="{
                                openPhone: false, qPhone: '', hiPhone: -1,
                                selectedCode: '{{ old('whatsapp_country_code', '+880') }}',
                                get allCodes() {
                                    return [
                                        {iso:'BD', c:'+880',n:'Bangladesh',f:'🇧🇩'}, {iso:'US', c:'+1',n:'United States/Canada',f:'🇺🇸'},
                                        {iso:'GB', c:'+44',n:'United Kingdom',f:'🇬🇧'}, {iso:'AU', c:'+61',n:'Australia',f:'🇦🇺'},
                                        {iso:'DE', c:'+49',n:'Germany',f:'🇩🇪'}, {iso:'FR', c:'+33',n:'France',f:'🇫🇷'},
                                        {iso:'IT', c:'+39',n:'Italy',f:'🇮🇹'}, {iso:'ES', c:'+34',n:'Spain',f:'🇪🇸'},
                                        {iso:'NL', c:'+31',n:'Netherlands',f:'🇳🇱'}, {iso:'SE', c:'+46',n:'Sweden',f:'🇸🇪'},
                                        {iso:'NO', c:'+47',n:'Norway',f:'🇳🇴'}, {iso:'DK', c:'+45',n:'Denmark',f:'🇩🇰'},
                                        {iso:'CH', c:'+41',n:'Switzerland',f:'🇨🇭'}, {iso:'AE', c:'+971',n:'UAE',f:'🇦🇪'},
                                        {iso:'QA', c:'+974',n:'Qatar',f:'🇶🇦'}, {iso:'SA', c:'+966',n:'Saudi Arabia',f:'🇸🇦'},
                                        {iso:'SG', c:'+65',n:'Singapore',f:'🇸🇬'}, {iso:'MY', c:'+60',n:'Malaysia',f:'🇲🇾'},
                                        {iso:'JP', c:'+81',n:'Japan',f:'🇯🇵'}, {iso:'KR', c:'+82',n:'South Korea',f:'🇰🇷'},
                                        {iso:'IN', c:'+91',n:'India',f:'🇮🇳'}, {iso:'PK', c:'+92',n:'Pakistan',f:'🇵🇰'},
                                        {iso:'LK', c:'+94',n:'Sri Lanka',f:'🇱🇰'}, {iso:'NZ', c:'+64',n:'New Zealand',f:'🇳🇿'},
                                        {iso:'ZA', c:'+27',n:'South Africa',f:'🇿🇦'}, {iso:'BR', c:'+55',n:'Brazil',f:'🇧🇷'},
                                        {iso:'MX', c:'+52',n:'Mexico',f:'🇲🇽'}, {iso:'AR', c:'+54',n:'Argentina',f:'🇦🇷'},
                                        {iso:'CL', c:'+56',n:'Chile',f:'🇨🇱'}, {iso:'CO', c:'+57',n:'Colombia',f:'🇨🇴'},
                                        {iso:'PE', c:'+51',n:'Peru',f:'🇵🇪'}, {iso:'VE', c:'+58',n:'Venezuela',f:'🇻🇪'},
                                        {iso:'PT', c:'+351',n:'Portugal',f:'🇵🇹'}, {iso:'BE', c:'+32',n:'Belgium',f:'🇧🇪'},
                                        {iso:'AT', c:'+43',n:'Austria',f:'🇦🇹'}, {iso:'IE', c:'+353',n:'Ireland',f:'🇮🇪'},
                                        {iso:'FI', c:'+358',n:'Finland',f:'🇫🇮'}, {iso:'PL', c:'+48',n:'Poland',f:'🇵🇱'},
                                        {iso:'CZ', c:'+420',n:'Czech Republic',f:'🇨🇿'}, {iso:'HU', c:'+36',n:'Hungary',f:'🇭🇺'},
                                        {iso:'RO', c:'+40',n:'Romania',f:'🇷🇴'}, {iso:'BG', c:'+359',n:'Bulgaria',f:'🇧🇬'},
                                        {iso:'GR', c:'+30',n:'Greece',f:'🇬🇷'}, {iso:'TR', c:'+90',n:'Turkey',f:'🇹🇷'},
                                        {iso:'EG', c:'+20',n:'Egypt',f:'🇪🇬'}, {iso:'MA', c:'+212',n:'Morocco',f:'🇲🇦'},
                                        {iso:'NG', c:'+234',n:'Nigeria',f:'🇳🇬'}, {iso:'KE', c:'+254',n:'Kenya',f:'🇰🇪'},
                                        {iso:'GH', c:'+233',n:'Ghana',f:'🇬🇭'}, {iso:'TZ', c:'+255',n:'Tanzania',f:'🇹🇿'},
                                        {iso:'UG', c:'+256',n:'Uganda',f:'🇺🇬'}, {iso:'CN', c:'+86',n:'China',f:'🇨🇳'},
                                        {iso:'HK', c:'+852',n:'Hong Kong',f:'🇭🇰'}, {iso:'TW', c:'+886',n:'Taiwan',f:'🇹🇼'},
                                        {iso:'TH', c:'+66',n:'Thailand',f:'🇹🇭'}, {iso:'VN', c:'+84',n:'Vietnam',f:'🇻🇳'},
                                        {iso:'ID', c:'+62',n:'Indonesia',f:'🇮🇩'}, {iso:'PH', c:'+63',n:'Philippines',f:'🇵🇭'},
                                        {iso:'MM', c:'+95',n:'Myanmar',f:'🇲🇲'}, {iso:'KH', c:'+855',n:'Cambodia',f:'🇰🇭'},
                                        {iso:'IL', c:'+972',n:'Israel',f:'🇮🇱'}, {iso:'JO', c:'+962',n:'Jordan',f:'🇯🇴'},
                                        {iso:'LB', c:'+961',n:'Lebanon',f:'🇱🇧'}, {iso:'KW', c:'+965',n:'Kuwait',f:'🇰🇼'},
                                        {iso:'OM', c:'+968',n:'Oman',f:'🇴🇲'}, {iso:'BH', c:'+973',n:'Bahrain',f:'🇧🇭'},
                                        {iso:'RU', c:'+7',n:'Russia/Kazakhstan',f:'🇷🇺'}, {iso:'CA', c:'+1',n:'Canada',f:'🇨🇦'}
                                    ];
                                },
                                get opts() {
                                    if (!this.qPhone) return this.allCodes;
                                    const s = this.qPhone.toLowerCase();
                                    return this.allCodes.filter(c => c.n.toLowerCase().includes(s) || c.c.includes(s));
                                },
                                pick(c) { this.selectedCode = c.c; this.qPhone = ''; this.openPhone = false; this.hiPhone = -1; },
                                getSelected() { return this.allCodes.find(c => c.c === this.selectedCode) || this.allCodes[0]; }
                            }"
                            @click.away="openPhone = false; qPhone = '';"
                            @update-phone-country.window="
                                let match = allCodes.find(x => x.iso === $event.detail);
                                if (match) selectedCode = match.c;
                            "
                            class="relative flex gap-0 border-[1.5px] border-gray-200 rounded-2xl overflow-visible bg-gray-50 focus-within:bg-white focus-within:border-violet-500 focus-within:ring-4 focus-within:ring-violet-500/10 transition-all @error('whatsapp_number') border-red-400 @enderror"
                        >
                            {{-- Dropdown Trigger --}}
                            <div class="relative flex-shrink-0 border-r border-gray-200">
                                <button type="button" @click="openPhone = !openPhone" class="h-full px-4 flex items-center gap-2 font-semibold text-gray-700 hover:text-violet-600 transition-colors">
                                    <span x-text="getSelected().f" class="text-lg"></span>
                                    <span x-text="getSelected().c" class="text-sm"></span>
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                
                                {{-- Dropdown Menu --}}
                                <div x-show="openPhone" x-transition class="absolute top-full left-0 mt-2 w-64 bg-white border border-gray-200 rounded-2xl shadow-2xl z-50 overflow-hidden flex flex-col max-h-72">
                                    <div class="p-2 border-b border-gray-200 sticky top-0 bg-white z-10">
                                        <input type="text" x-model="qPhone" placeholder="Search country or code..." class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-violet-500 focus:border-transparent outline-none" @click.stop>
                                    </div>
                                    <div class="overflow-y-auto flex-1 p-1">
                                        <template x-for="(c, i) in opts" :key="c.c + c.n">
                                            <button type="button" @click="pick(c)" :class="c.c === selectedCode ? 'bg-violet-50 text-violet-700' : 'text-gray-700 hover:bg-gray-50'" class="w-full text-left flex items-center gap-3 px-3 py-2 rounded-xl text-sm transition-colors mb-0.5">
                                                <span x-text="c.f" class="text-base"></span>
                                                <span x-text="c.n" class="truncate flex-1"></span>
                                                <span x-text="c.c" class="font-mono text-xs text-slate-400"></span>
                                            </button>
                                        </template>
                                        <div x-show="opts.length === 0" class="px-4 py-3 text-sm text-slate-400 text-center">No countries found</div>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="whatsapp_country_code" :value="selectedCode">
                            
                            <input
                                type="tel"
                                id="whatsapp_number"
                                name="whatsapp_number"
                                value="{{ old('whatsapp_number') }}"
                                placeholder="1968524222"
                                class="w-full bg-transparent border-none px-4 py-3.5 text-sm text-gray-900 focus:ring-0 outline-none placeholder:text-slate-400"
                                required
                            >
                        </div>
                        @error('whatsapp_number') <p class="error-msg mt-1.5">{{ $message }}</p> @enderror
                        <p class="text-xs text-slate-400 mt-2 pl-1">We'll reach out to this number on WhatsApp.</p>
                    </div>
                </div>
                @endif
            </div>

            <hr class="border-white/10">

            {{-- ─── SECTION: Product Details ─── --}}
            <div>
                @php
                    $oldProducts = old('products', [['description' => '', 'link' => '']]);
                    if (!is_array($oldProducts)) { $oldProducts = [['description' => '', 'link' => '']]; }
                    $mappedProducts = array_map(function($p) {
                        return [
                            'id' => uniqid(),
                            'desc' => $p['description'] ?? '',
                            'link' => $p['link'] ?? '',
                            'imagePreview' => null
                        ];
                    }, $oldProducts);
                @endphp
                <div x-data="productCart()">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-xs font-black text-violet-600 uppercase tracking-widest flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-violet-100 text-violet-600 flex items-center justify-center text-[10px] font-black">3</span>
                            Product Details
                        </h3>
                    </div>

                    @if($errors->has('products.*') || $errors->has('products'))
                        <div class="mb-4 text-red-500 text-sm font-semibold bg-red-50 p-3 rounded-xl border border-red-100">
                            <ul class="list-disc list-inside">
                                @foreach($errors->get('products.*') as $messages)
                                    @foreach($messages as $message)
                                        <li>{{ $message }}</li>
                                    @endforeach
                                @endforeach
                                @foreach($errors->get('products') as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="space-y-4">
                        <template x-for="(product, index) in products" :key="product.id">
                            <div class="bg-white border border-gray-200 p-5 rounded-2xl relative shadow-sm hover:shadow-md transition-shadow">
                                
                                <!-- SUMMARY VIEW (Collapsed) -->
                                <div x-show="editingId !== product.id" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="flex-1 flex items-center gap-3 sm:gap-4 text-sm text-slate-700 min-w-0">
                                        <div class="font-bold text-slate-400 whitespace-nowrap self-start mt-1 sm:mt-0" x-text="(index + 1) + '.'"></div>
                                        
                                        {{-- Image Thumbnail --}}
                                        <div class="w-14 h-14 sm:w-16 sm:h-16 flex-shrink-0 bg-gray-50 rounded-xl border border-gray-200 flex items-center justify-center overflow-hidden">
                                            <template x-if="product.imagePreview">
                                                <img :src="product.imagePreview" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!product.imagePreview">
                                                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            </template>
                                        </div>

                                        {{-- Product Details --}}
                                        <div class="flex flex-col min-w-0 flex-1">
                                            <span class="font-bold text-slate-900 line-clamp-2" x-text="product.desc || 'Unnamed Product'"></span>
                                            <template x-if="product.link">
                                                <a :href="product.link" target="_blank" class="text-xs text-violet-600 hover:text-violet-700 hover:underline truncate mt-1 inline-flex items-center gap-1" title="View product link">
                                                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                    <span x-text="product.link" class="truncate"></span>
                                                </a>
                                            </template>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center gap-2 self-start sm:self-auto shrink-0 border-t sm:border-t-0 border-gray-100 pt-3 sm:pt-0 w-full sm:w-auto justify-end">
                                        <button type="button" @click="editProduct(product.id)" class="text-xs font-semibold text-violet-600 hover:text-violet-700 bg-violet-50 hover:bg-violet-100 px-3 py-2 rounded-lg transition-colors shadow-sm">Edit</button>
                                        <button type="button" @click="copyProduct(product)" class="text-xs font-semibold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-2 rounded-lg transition-colors shadow-sm">Copy</button>
                                        <button type="button" x-show="products.length > 1" @click="removeProduct(product.id)" class="text-xs font-semibold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-2 rounded-lg transition-colors shadow-sm">Remove</button>
                                    </div>
                                </div>

                                <!-- EDIT VIEW (Expanded) -->
                                <div x-show="editingId === product.id" x-transition.opacity>
                                    <div class="mb-4 flex items-center justify-between">
                                        <div class="flex items-center gap-2 text-sm font-black text-slate-400 uppercase tracking-widest">
                                            Product <span x-text="index + 1"></span>
                                        </div>
                                    </div>

                                    {{-- Description --}}
                                    <div class="mb-4">
                                        <label class="form-label">Product Description *</label>
                                        <textarea
                                            :name="'products['+index+'][description]'"
                                            x-model="product.desc"
                                            rows="2"
                                            placeholder="Be as specific as possible. E.g: 1 piece Aarong kantha stitch saree (red and white)..."
                                            class="input-field resize-none bg-gray-50 focus:bg-white"
                                        ></textarea>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        {{-- Product Link --}}
                                        <div>
                                            <label class="form-label">Product Link <span class="text-slate-400 font-normal normal-case">(optional)</span></label>
                                            <div class="relative">
                                                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                                <input
                                                    type="text"
                                                    :name="'products['+index+'][link]'"
                                                    x-model="product.link"
                                                    placeholder="https://..."
                                                    class="input-field input-with-icon-left bg-gray-50 focus:bg-white"
                                                >
                                            </div>
                                        </div>

                                        {{-- Image Upload --}}
                                        <div>
                                            <label class="form-label">Product Image <span class="text-slate-400 font-normal normal-case">(optional)</span></label>
                                            <div
                                                class="upload-zone relative p-3 text-center border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-violet-500 hover:bg-violet-50 transition-all bg-gray-50"
                                                @click="document.getElementById('product_image_'+index).click()"
                                            >
                                                <div :id="'upload-placeholder-'+index">
                                                    <p class="text-xs font-semibold text-gray-600 flex items-center justify-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> Click to upload image</p>
                                                </div>
                                                <div :id="'upload-preview-'+index" class="hidden flex items-center justify-between gap-3 text-left">
                                                    <img :id="'preview-img-'+index" src="" alt="Preview" class="w-10 h-10 rounded-md object-cover border border-gray-200">
                                                    <p :id="'preview-name-'+index" class="text-[10px] text-slate-500 font-medium truncate flex-1"></p>
                                                    <button type="button" @click.stop="clearImg(index)" class="text-xs text-red-500 font-bold hover:bg-red-50 p-1.5 rounded-md transition-colors">Clear</button>
                                                </div>
                                            </div>
                                            <input type="file" :id="'product_image_'+index" :name="'products['+index+'][image]'" accept="image/*" class="hidden" @change="previewImg($event, index)">
                                        </div>
                                    </div>
                                    
                                    <div class="mt-5 flex items-center justify-between border-t border-gray-100 pt-4">
                                        <button type="button" x-show="products.length > 1" @click="removeProduct(product.id)" class="text-xs font-semibold text-red-600 hover:text-red-700 flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Remove Product
                                        </button>
                                        <div x-show="products.length <= 1"></div>

                                        <button type="button" @click="closeProduct()" class="text-sm font-bold text-white bg-violet-600 hover:bg-violet-700 px-5 py-2 rounded-xl transition-colors shadow-lg shadow-violet-600/20">
                                            Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <button type="button" @click="addProduct()" class="flex items-center gap-2 text-sm font-bold text-violet-600 hover:text-violet-700 bg-violet-50 hover:bg-violet-100 border border-violet-200 rounded-xl px-5 py-2.5 transition-all shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Another Item to Cart
                        </button>
                    </div>
                </div>
            </div>

            {{-- ─── SUBMIT ─── --}}
            <div class="pt-2">
                <button type="submit" id="submit-btn" class="submit-btn">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Confirm Order
                </button>
                <p class="text-center text-xs text-slate-400 mt-3">
                    By submitting, you agree that ExpressPeek may contact you on WhatsApp to discuss your order.
                </p>
            </div>
        </form>
    </div>

    {{-- Info Box --}}
    <div class="mt-8 mx-4 sm:mx-0 bg-amber-50 border border-amber-200 rounded-2xl p-6">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h4 class="font-bold text-amber-900 mb-1">How it works</h4>
                <ul class="text-sm text-amber-800 space-y-1 list-disc list-inside">
                    <li>We review your request and search for the product in Bangladesh</li>
                    <li>We contact you on WhatsApp within 24 hours with availability & price</li>
                    <li>After you confirm and pay, we purchase and ship directly to you</li>
                    <li>You get full tracking updates until delivery</li>
                </ul>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
window.productCart = function () {
    return {
        products: @json($mappedProducts),
        editingId: null,
        init() {
            const hasOld = {{ count(old('products', [])) > 0 ? 'true' : 'false' }};
            const startFresh = {{ request()->boolean('new') ? 'true' : 'false' }};
            if (startFresh) {
                localStorage.removeItem('sourcing_form_data');
                localStorage.removeItem('sourcing_cart');
            }

            if (!hasOld && !startFresh) {
                const saved = localStorage.getItem('sourcing_cart');
                if (saved) {
                    try {
                        this.products = JSON.parse(saved);
                    } catch (e) {}
                }
            }

            if (this.products.length > 0 && !this.products[0].desc && !hasOld) {
                this.editingId = this.products[0].id;
            }

            this.$watch('products', value => {
                try {
                    const toSave = value.map(product => ({
                        id: product.id,
                        desc: product.desc,
                        link: product.link,
                        imagePreview: null
                    }));
                    localStorage.setItem('sourcing_cart', JSON.stringify(toSave));
                } catch (e) {}
            }, { deep: true });
        },
        addProduct() {
            const id = Date.now().toString();
            this.products.push({ id, desc: '', link: '', imagePreview: null });
            this.editingId = id;
        },
        editProduct(id) {
            this.editingId = id;
        },
        removeProduct(id) {
            if (this.products.length > 1) {
                this.products = this.products.filter(product => product.id !== id);
            }
        },
        copyProduct(product) {
            const id = Date.now().toString();
            this.products.push({ id, desc: product.desc, link: product.link, imagePreview: null });
        },
        closeProduct() {
            this.editingId = null;
        },
        previewImg(event, index) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (result) => {
                    document.getElementById('upload-placeholder-' + index).classList.add('hidden');
                    document.getElementById('upload-preview-' + index).classList.remove('hidden');
                    document.getElementById('preview-img-' + index).src = result.target.result;
                    document.getElementById('preview-name-' + index).textContent = file.name;
                    this.products[index].imagePreview = result.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        clearImg(index) {
            document.getElementById('product_image_' + index).value = '';
            document.getElementById('upload-placeholder-' + index).classList.remove('hidden');
            document.getElementById('upload-preview-' + index).classList.add('hidden');
            this.products[index].imagePreview = null;
        }
    };
};

// --- Auto-save Regular Form Fields ---
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('sourcing-form');
    if (!form) return;

    const startFresh = {{ request()->boolean('new') ? 'true' : 'false' }};
    if (startFresh) {
        localStorage.removeItem('sourcing_form_data');
        localStorage.removeItem('sourcing_cart');
    }

    const hasOld = {{ count(old()) > 0 ? 'true' : 'false' }};
    if (!hasOld && !startFresh) {
        const saved = localStorage.getItem('sourcing_form_data');
        if (saved) {
            try {
                const data = JSON.parse(saved);
                for (const key in data) {
                    const field = form.querySelector(`[name="${key}"]`);
                    if (field && field.type !== 'file' && field.type !== 'hidden') {
                        if (field.type === 'checkbox' || field.type === 'radio') {
                            field.checked = data[key];
                            field.dispatchEvent(new Event('change', { bubbles: true }));
                        } else {
                            field.value = data[key];
                            field.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                    }
                }
            } catch (e) {}
        }
    }

    let draftTimeout;
    let draftRequestController;
    let isSubmitting = false;
    function saveForm() {
        if (isSubmitting) return;

        const formData = new FormData(form);
        const data = {};
        for (let [key, value] of formData.entries()) {
            if (typeof value === 'string' && key.indexOf('[') === -1 && key !== '_token') {
                data[key] = value;
            }
        }
        localStorage.setItem('sourcing_form_data', JSON.stringify(data));

        @if(auth()->check())
        clearTimeout(draftTimeout);
        draftTimeout = setTimeout(() => {
            draftRequestController = new AbortController();
            fetch("{{ route('customer.sourcing-requests.draft.store') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: new FormData(form),
                signal: draftRequestController.signal
            })
            .then(res => res.json())
            .then(res => {
                if(res.sourcing_request_id) {
                    let idInput = document.getElementById('sourcing_request_id_input');
                    if(!idInput) {
                        idInput = document.createElement('input');
                        idInput.type = 'hidden';
                        idInput.id = 'sourcing_request_id_input';
                        idInput.name = 'sourcing_request_id';
                        form.appendChild(idInput);
                    }
                    idInput.value = res.sourcing_request_id;
                }
            })
            .catch(err => {
                if (err.name !== 'AbortError') console.error('Draft save error:', err);
            });
        }, 1500);
        @endif
    }

    form.addEventListener('input', saveForm);
    form.addEventListener('change', saveForm);
    form.addEventListener('submit', () => {
        isSubmitting = true;
        clearTimeout(draftTimeout);
        if (draftRequestController) draftRequestController.abort();
        localStorage.removeItem('sourcing_form_data');
        localStorage.removeItem('sourcing_cart');
    });
});

window.SOURCING_COUNTRIES = @json($quoteCountries->map(fn($c) => ['name' => $c->country_name, 'code' => $c->country_code])->values());

function previewImage(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('upload-placeholder').classList.add('hidden');
            document.getElementById('upload-preview').classList.remove('hidden');
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('preview-name').textContent = file.name;
        };
        reader.readAsDataURL(file);
    }
}

function clearUpload(event) {
    event.stopPropagation();
    document.getElementById('product_image').value = '';
    document.getElementById('upload-placeholder').classList.remove('hidden');
    document.getElementById('upload-preview').classList.add('hidden');
}

function handleDrop(event) {
    event.preventDefault();
    document.getElementById('upload-zone').classList.remove('dragover');
    const files = event.dataTransfer.files;
    if (files.length > 0) {
        const input = document.getElementById('product_image');
        const dt = new DataTransfer();
        dt.items.add(files[0]);
        input.files = dt.files;
        previewImage(input);
    }
}

// Show loading state on submit
document.getElementById('sourcing-form').addEventListener('submit', function() {
    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.innerHTML = `<svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Submitting...`;
});
</script>
@endpush

@endsection
