<x-guest-layout>
    <span class="auth-kicker">Customer onboarding</span>
    <h2 class="auth-heading">Create your logistics workspace</h2>
    <p class="auth-subtitle">One secure account for booking, tracking, check delivery costs, and complete shipment visibility.</p>

    <form method="POST" action="{{ route('register', request()->only('next')) }}" autocomplete="on">
        @csrf

        <div class="auth-field" style="margin-top:0">
            <div class="auth-label-row"><label class="auth-label" for="name">Full name</label></div>
            <div class="auth-input-wrap">
                <svg class="auth-field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
                <input class="auth-input" id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Your full name">
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="auth-field flex items-center gap-2" style="margin-bottom: 1rem; flex-direction: row;">
            <input type="checkbox" name="is_business" value="1" id="is_business_check" @checked(old('is_business'))
                class="w-4 h-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500"
                onchange="document.getElementById('business_section').style.display = this.checked ? 'block' : 'none'">
            <label for="is_business_check" class="auth-label cursor-pointer text-sm font-medium" style="margin-bottom: 0;">Business Contact</label>
        </div>

        <div id="business_section" class="auth-field" style="display: {{ old('is_business') ? 'block' : 'none' }};">
            <div class="auth-label-row"><label class="auth-label" for="company_name">Company name</label></div>
            <div class="auth-input-wrap">
                <svg class="auth-field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 21h18M5 21V7l8-4v18M13 21V3l8 4v14M7 11h2M7 15h2M15 11h2M15 15h2"/></svg>
                <input class="auth-input" id="company_name" type="text" name="company_name" value="{{ old('company_name') }}" placeholder="Your company name">
            </div>
            <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
        </div>

        <div class="auth-field">
            <div class="auth-label-row"><label class="auth-label" for="email">Email address</label></div>
            <div class="auth-input-wrap">
                <svg class="auth-field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="m4 7 8 6 8-6"/></svg>
                <input class="auth-input" id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" inputmode="email" placeholder="you@example.com">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="auth-field">
            <div class="auth-label-row"><label class="auth-label" for="phone">Mobile number</label><span class="auth-required-note">Required for account support</span></div>
            <div class="auth-phone-grid">
                <div class="auth-input-wrap auth-code-wrap" x-data="{
                    openPhone: false, qPhone: '', hiPhone: -1,
                    selectedCode: '{{ old('phone_country_code', '+880') }}',
                    get allCodes() {
                        return [
                            {c:'+880',n:'Bangladesh',f:'🇧🇩'}, {c:'+1',n:'United States/Canada',f:'🇺🇸'},
                            {c:'+44',n:'United Kingdom',f:'🇬🇧'}, {c:'+61',n:'Australia',f:'🇦🇺'},
                            {c:'+49',n:'Germany',f:'🇩🇪'}, {c:'+33',n:'France',f:'🇫🇷'},
                            {c:'+39',n:'Italy',f:'🇮🇹'}, {c:'+34',n:'Spain',f:'🇪🇸'},
                            {c:'+31',n:'Netherlands',f:'🇳🇱'}, {c:'+46',n:'Sweden',f:'🇸🇪'},
                            {c:'+47',n:'Norway',f:'🇳🇴'}, {c:'+45',n:'Denmark',f:'🇩🇰'},
                            {c:'+41',n:'Switzerland',f:'🇨🇭'}, {c:'+971',n:'UAE',f:'🇦🇪'},
                            {c:'+974',n:'Qatar',f:'🇶🇦'}, {c:'+966',n:'Saudi Arabia',f:'🇸🇦'},
                            {c:'+65',n:'Singapore',f:'🇸🇬'}, {c:'+60',n:'Malaysia',f:'🇲🇾'},
                            {c:'+81',n:'Japan',f:'🇯🇵'}, {c:'+82',n:'South Korea',f:'🇰🇷'},
                            {c:'+91',n:'India',f:'🇮🇳'}, {c:'+92',n:'Pakistan',f:'🇵🇰'},
                            {c:'+94',n:'Sri Lanka',f:'🇱🇰'}, {c:'+64',n:'New Zealand',f:'🇳🇿'},
                            {c:'+27',n:'South Africa',f:'🇿🇦'}, {c:'+55',n:'Brazil',f:'🇧🇷'},
                            {c:'+52',n:'Mexico',f:'🇲🇽'}, {c:'+54',n:'Argentina',f:'🇦🇷'},
                            {c:'+56',n:'Chile',f:'🇨🇱'}, {c:'+57',n:'Colombia',f:'🇨🇴'},
                            {c:'+51',n:'Peru',f:'🇵🇪'}, {c:'+58',n:'Venezuela',f:'🇻🇪'},
                            {c:'+351',n:'Portugal',f:'🇵🇹'}, {c:'+32',n:'Belgium',f:'🇧🇪'},
                            {c:'+43',n:'Austria',f:'🇦🇹'}, {c:'+353',n:'Ireland',f:'🇮🇪'},
                            {c:'+358',n:'Finland',f:'🇫🇮'}, {c:'+48',n:'Poland',f:'🇵🇱'},
                            {c:'+420',n:'Czech Republic',f:'🇨🇿'}, {c:'+36',n:'Hungary',f:'🇭🇺'},
                            {c:'+40',n:'Romania',f:'🇷🇴'}, {c:'+359',n:'Bulgaria',f:'🇧🇬'},
                            {c:'+30',n:'Greece',f:'🇬🇷'}, {c:'+90',n:'Turkey',f:'🇹🇷'},
                            {c:'+20',n:'Egypt',f:'🇪🇬'}, {c:'+212',n:'Morocco',f:'🇲🇦'},
                            {c:'+234',n:'Nigeria',f:'🇳🇬'}, {c:'+254',n:'Kenya',f:'🇰🇪'},
                            {c:'+233',n:'Ghana',f:'🇬🇭'}, {c:'+255',n:'Tanzania',f:'🇹🇿'},
                            {c:'+256',n:'Uganda',f:'🇺🇬'}, {c:'+86',n:'China',f:'🇨🇳'},
                            {c:'+852',n:'Hong Kong',f:'🇭🇰'}, {c:'+886',n:'Taiwan',f:'🇹🇼'},
                            {c:'+66',n:'Thailand',f:'🇹🇭'}, {c:'+84',n:'Vietnam',f:'🇻🇳'},
                            {c:'+62',n:'Indonesia',f:'🇮🇩'}, {c:'+63',n:'Philippines',f:'🇵🇭'},
                            {c:'+95',n:'Myanmar',f:'🇲🇲'}, {c:'+855',n:'Cambodia',f:'🇰🇭'},
                            {c:'+972',n:'Israel',f:'🇮🇱'}, {c:'+962',n:'Jordan',f:'🇯🇴'},
                            {c:'+961',n:'Lebanon',f:'🇱🇧'}, {c:'+965',n:'Kuwait',f:'🇰🇼'},
                            {c:'+968',n:'Oman',f:'🇴🇲'}, {c:'+973',n:'Bahrain',f:'🇧🇭'},
                            {c:'+7',n:'Russia/Kazakhstan',f:'🇷🇺'}
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
                style="overflow: visible; position: relative; z-index: 50;">
                    
                    <button type="button" @click="openPhone = !openPhone" class="auth-input auth-code-select flex items-center justify-between w-full h-full bg-transparent border-none outline-none cursor-pointer" style="padding: 0 0.75rem;">
                        <span class="flex items-center gap-1.5 text-sm font-semibold text-gray-900">
                            <span x-text="getSelected().f"></span>
                            <span x-text="getSelected().c"></span>
                        </span>
                        <svg class="w-3.5 h-3.5 text-slate-400 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    
                    <div x-show="openPhone" x-transition x-cloak class="absolute top-[calc(100%+6px)] left-0 w-64 bg-white border border-gray-200 rounded-2xl shadow-[0_12px_40px_-12px_rgba(0,0,0,0.15)] z-[100] overflow-hidden flex flex-col max-h-[300px]">
                        <div class="p-2 border-b border-gray-100 sticky top-0 bg-white z-10">
                            <input type="text" x-model="qPhone" placeholder="Search country or code..." class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 outline-none transition-all" @click.stop>
                        </div>
                        <div class="overflow-y-auto flex-1 p-1.5" style="scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;">
                            <template x-for="(c, i) in opts" :key="c.c + c.n">
                                <button type="button" @click="pick(c)" :class="c.c === selectedCode ? 'bg-violet-50 text-violet-700 font-medium' : 'text-gray-600 hover:bg-gray-50'" class="w-full text-left flex items-center gap-3 px-3 py-2 rounded-xl text-sm transition-colors mb-0.5 cursor-pointer border-none outline-none">
                                    <span x-text="c.f" class="text-base leading-none"></span>
                                    <span x-text="c.n" class="truncate flex-1"></span>
                                    <span x-text="c.c" class="font-mono text-xs text-slate-400"></span>
                                </button>
                            </template>
                            <div x-show="opts.length === 0" class="px-4 py-3 text-sm text-slate-400 text-center bg-gray-50/50 rounded-xl">No countries found</div>
                        </div>
                    </div>
                    
                    <input type="hidden" name="phone_country_code" :value="selectedCode">
                </div>
                <div class="auth-input-wrap">
                    <svg class="auth-field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 16.4v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.3 19.3 0 0 1-6-6A19.8 19.8 0 0 1 1.1 3.7 2 2 0 0 1 3.1 1.5h3A2 2 0 0 1 8 3.2c.1 1 .4 2 .7 2.9a2 2 0 0 1-.4 2.1L7 9.5a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.9.6 2.9.7a2 2 0 0 1 1.7 1.9Z"/></svg>
                    <input class="auth-input" id="phone" type="tel" name="phone" value="{{ old('phone') }}" required autocomplete="tel-national" inputmode="tel" placeholder="01712 345678">
                </div>
            </div>
            <x-input-error :messages="$errors->get('phone_country_code')" class="mt-2" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <div class="auth-field" x-data="{ visible: false }">
            <div class="auth-label-row"><label class="auth-label" for="password">Create password</label></div>
            <div class="auth-input-wrap">
                <svg class="auth-field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                <input class="auth-input" id="password" :type="visible ? 'text' : 'password'" name="password" required autocomplete="new-password" placeholder="Create a strong password">
                <button class="auth-password-toggle" type="button" @click="visible = !visible" :aria-label="visible ? 'Hide password' : 'Show password'">
                    <svg x-show="!visible" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                    <svg x-cloak x-show="visible" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m3 3 18 18M10.6 6.2A11.6 11.6 0 0 1 12 6c6.5 0 10 6 10 6M2 12s3.5 6 10 6a11 11 0 0 0 4.2-.8"/></svg>
                </button>
            </div>
            <p class="auth-help">Use at least 8 characters with uppercase, lowercase, and a number.</p>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="auth-field" x-data="{ visible: false }">
            <div class="auth-label-row"><label class="auth-label" for="password_confirmation">Confirm password</label></div>
            <div class="auth-input-wrap">
                <svg class="auth-field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3 20 6v6c0 5-3.4 8-8 9-4.6-1-8-4-8-9V6l8-3Z"/><path d="m8 12 2.5 2.5L16 9"/></svg>
                <input class="auth-input" id="password_confirmation" :type="visible ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat your password">
                <button class="auth-password-toggle" type="button" @click="visible = !visible" :aria-label="visible ? 'Hide password confirmation' : 'Show password confirmation'">
                    <svg x-show="!visible" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                    <svg x-cloak x-show="visible" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m3 3 18 18M10.6 6.2A11.6 11.6 0 0 1 12 6c6.5 0 10 6 10 6M2 12s3.5 6 10 6a11 11 0 0 0 4.2-.8"/></svg>
                </button>
            </div>
        </div>

        <label class="auth-terms" for="terms">
            <input id="terms" type="checkbox" name="terms" value="1" required @checked(old('terms'))>
            <span>I agree to the <a class="auth-link" href="{{ route('terms') }}">Terms of Service</a> and <a class="auth-link" href="{{ route('privacy') }}">Privacy Policy</a>.</span>
        </label>
        <x-input-error :messages="$errors->get('terms')" class="mt-2" />

        <button class="auth-primary" type="submit">Create secure account <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button>
    </form>

    <p class="auth-switch">Already have an account? <a class="auth-link" href="{{ route('login', request()->only('next')) }}">Sign in</a></p>
</x-guest-layout>
