{{-- Contact form partial. Requires: $prefix ('sender' or 'receiver') --}}

{{-- Business Contact Toggle --}}
<div class="flex items-center gap-3 mb-4">
    <label class="flex items-center gap-2 cursor-pointer">
        <input type="checkbox" name="{{ $prefix }}_is_business" value="1" id="{{ $prefix }}_is_business_check"
            class="w-4 h-4 rounded border-gray-600 text-violet-600 bg-gray-800 focus:ring-violet-500"
            onchange="document.getElementById('{{ $prefix }}_business_section').classList.toggle('hidden', !this.checked)">
        <span class="text-sm text-slate-400">Business Contact</span>
    </label>
</div>

{{-- Name --}}
<div class="mb-4">
    <div class="flex items-center justify-between gap-3 mb-1.5">
        <label class="block text-xs font-medium text-slate-400">Full Name *</label>
        @if(!empty($showAddressBookPicker))
            <button type="button" onclick="openAddressBookPicker('{{ $prefix }}')"
                class="inline-flex items-center gap-1.5 rounded-lg border border-violet-500/30 bg-violet-500/10 px-2.5 py-1 text-[11px] font-medium text-violet-300 transition-colors hover:border-violet-400 hover:bg-violet-500/15 hover:text-violet-200">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v11.494m0 0A11.953 11.953 0 017.5 20.5a11.953 11.953 0 01-4.5-1.753V5.253a11.953 11.953 0 004.5 1.753A11.953 11.953 0 0012 5.253m0 12.494a11.953 11.953 0 014.5 1.753A11.953 11.953 0 0021 16.747V5.253a11.953 11.953 0 00-4.5 1.753A11.953 11.953 0 0012 5.253m0 0V4.5"/></svg>
                Address Book
            </button>
        @endif
    </div>
    <input type="text" name="{{ $prefix }}_name" value="{{ old($prefix . '_name', $prefix === 'sender' && auth()->check() ? auth()->user()->name : '') }}" placeholder="Full name"
        class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500 transition-colors">
</div>

{{-- Company (hidden until business checked) --}}
<div id="{{ $prefix }}_business_section" class="hidden mb-4">
    <label class="block text-xs font-medium text-slate-400 mb-1.5">Company</label>
    <input type="text" name="{{ $prefix }}_company" placeholder="Company name"
        class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500 transition-colors">
</div>

{{-- Country --}}
<div class="mb-4">
    <label class="block text-xs font-medium text-slate-400 mb-1.5">Country / Territory *</label>
    <div class="relative country-search-wrapper">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="{{ $prefix }}_country" id="{{ $prefix }}_country_input" value="{{ old($prefix . '_country', $prefix === 'receiver' ? request('country') : '') }}" placeholder="Search country..."
                autocomplete="off"
                oninput="countryAutocomplete(this,'{{ $prefix }}')"
                onfocus="countryInputFocus(this,'{{ $prefix }}')"
                onkeydown="countryKeydown(event,'{{ $prefix }}')"
                class="w-full bg-gray-800 border border-gray-700 rounded-xl pl-9 pr-9 py-3 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-violet-500 transition-colors">
            <button type="button" id="{{ $prefix }}_country_clear" onclick="clearCountryField('{{ $prefix }}')"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-300 transition-colors hidden">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <input type="hidden" name="{{ $prefix }}_country_code" id="{{ $prefix }}_country_code">
        <div id="{{ $prefix }}_country_dropdown" class="absolute top-full left-0 right-0 mt-1 bg-gray-800 border border-gray-700 rounded-xl shadow-xl z-30 hidden max-h-56 overflow-y-auto"></div>
    </div>
</div>

{{-- Address --}}
<div class="mb-3">
    <label class="block text-xs font-medium text-slate-400 mb-1.5">Address *</label>
    <input type="text" name="{{ $prefix }}_address" placeholder="Street address"
        class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500 transition-colors">
</div>
<div class="mb-3">
    <input type="text" name="{{ $prefix }}_address2" placeholder="Address line 2 (optional)"
        class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500 transition-colors">
</div>
<div class="mb-4">
    <input type="text" name="{{ $prefix }}_address3" placeholder="Address line 3 (optional)"
        class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500 transition-colors">
</div>

{{-- Postal / City / State --}}
<div class="grid grid-cols-3 gap-3 mb-4">
    <div>
        <label class="block text-xs font-medium text-slate-400 mb-1.5">Postal Code</label>
        <input type="text" name="{{ $prefix }}_postal_code" placeholder="1000"
            class="w-full bg-gray-800 border border-gray-700 rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-400 mb-1.5">City *</label>
        <input type="text" name="{{ $prefix }}_city" placeholder="City"
            class="w-full bg-gray-800 border border-gray-700 rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-400 mb-1.5">State</label>
        <input type="text" name="{{ $prefix }}_state" placeholder="State"
            class="w-full bg-gray-800 border border-gray-700 rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500">
    </div>
</div>

{{-- Email --}}
<div class="mb-4">
    <label class="block text-xs font-medium text-slate-400 mb-1.5">Email Address</label>
    <input type="email" name="{{ $prefix }}_email" placeholder="email@example.com"
        class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500 transition-colors">
</div>

{{-- Phone --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
    <div class="sm:col-span-1">
        <label class="block text-xs font-medium text-slate-400 mb-1.5">Phone Type</label>
        <select name="{{ $prefix }}_phone_type"
            class="w-full bg-gray-800 border border-gray-700 rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:border-violet-500">
            <option @selected(old($prefix . '_phone_type') === 'Office')>Office</option>
            <option @selected(old($prefix . '_phone_type', 'Mobile') === 'Mobile')>Mobile</option>
            <option @selected(old($prefix . '_phone_type') === 'Home')>Home</option>
            <option @selected(old($prefix . '_phone_type') === 'Fax')>Fax</option>
        </select>
    </div>
    <div class="sm:col-span-2">
        <label class="block text-xs font-medium text-slate-400 mb-1.5">Phone Number *</label>
        <div x-data="{
                openPhone: false, qPhone: '', hiPhone: -1,
                selectedCode: '{{ old($prefix . '_phone_code', '+880') }}',
                get allCodes() {
                    return [
    {c:'+93',n:'Afghanistan',f:'🇦🇫'}, {c:'+355',n:'Albania',f:'🇦🇱'}, {c:'+213',n:'Algeria',f:'🇩🇿'}, {c:'+1684',n:'American Samoa',f:'🇦🇸'},
    {c:'+376',n:'Andorra',f:'🇦🇩'}, {c:'+244',n:'Angola',f:'🇦🇴'}, {c:'+1264',n:'Anguilla',f:'🇦🇮'}, {c:'+1268',n:'Antigua and Barbuda',f:'🇦🇬'},
    {c:'+54',n:'Argentina',f:'🇦🇷'}, {c:'+374',n:'Armenia',f:'🇦🇲'}, {c:'+297',n:'Aruba',f:'🇦🇼'}, {c:'+61',n:'Australia',f:'🇦🇺'},
    {c:'+43',n:'Austria',f:'🇦🇹'}, {c:'+994',n:'Azerbaijan',f:'🇦🇿'}, {c:'+1242',n:'Bahamas',f:'🇧🇸'}, {c:'+973',n:'Bahrain',f:'🇧🇭'},
    {c:'+880',n:'Bangladesh',f:'🇧🇩'}, {c:'+1246',n:'Barbados',f:'🇧🇧'}, {c:'+375',n:'Belarus',f:'🇧🇾'}, {c:'+32',n:'Belgium',f:'🇧🇪'},
    {c:'+501',n:'Belize',f:'🇧🇿'}, {c:'+229',n:'Benin',f:'🇧🇯'}, {c:'+1441',n:'Bermuda',f:'🇧🇲'}, {c:'+975',n:'Bhutan',f:'🇧🇹'},
    {c:'+591',n:'Bolivia',f:'🇧🇴'}, {c:'+387',n:'Bosnia and Herzegovina',f:'🇧🇦'}, {c:'+267',n:'Botswana',f:'🇧🇼'}, {c:'+55',n:'Brazil',f:'🇧🇷'},
    {c:'+673',n:'Brunei',f:'🇧🇳'}, {c:'+359',n:'Bulgaria',f:'🇧🇬'}, {c:'+226',n:'Burkina Faso',f:'🇧🇫'}, {c:'+257',n:'Burundi',f:'🇧🇮'},
    {c:'+855',n:'Cambodia',f:'🇰🇭'}, {c:'+237',n:'Cameroon',f:'🇨🇲'}, {c:'+1',n:'Canada',f:'🇨🇦'}, {c:'+238',n:'Cape Verde',f:'🇨🇻'},
    {c:'+1345',n:'Cayman Islands',f:'🇰🇾'}, {c:'+236',n:'Central African Republic',f:'🇨🇫'}, {c:'+235',n:'Chad',f:'🇹🇩'}, {c:'+56',n:'Chile',f:'🇨🇱'},
    {c:'+86',n:'China',f:'🇨🇳'}, {c:'+57',n:'Colombia',f:'🇨🇴'}, {c:'+269',n:'Comoros',f:'🇰🇲'}, {c:'+242',n:'Congo',f:'🇨🇬'},
    {c:'+243',n:'DR Congo',f:'🇨🇩'}, {c:'+682',n:'Cook Islands',f:'🇨🇰'}, {c:'+506',n:'Costa Rica',f:'🇨🇷'}, {c:'+385',n:'Croatia',f:'🇭🇷'},
    {c:'+53',n:'Cuba',f:'🇨🇺'}, {c:'+357',n:'Cyprus',f:'🇨🇾'}, {c:'+420',n:'Czech Republic',f:'🇨🇿'}, {c:'+45',n:'Denmark',f:'🇩🇰'},
    {c:'+253',n:'Djibouti',f:'🇩🇯'}, {c:'+1767',n:'Dominica',f:'🇩🇲'}, {c:'+1809',n:'Dominican Republic',f:'🇩🇴'}, {c:'+593',n:'Ecuador',f:'🇪🇨'},
    {c:'+20',n:'Egypt',f:'🇪🇬'}, {c:'+503',n:'El Salvador',f:'🇸🇻'}, {c:'+240',n:'Equatorial Guinea',f:'🇬🇶'}, {c:'+291',n:'Eritrea',f:'🇪🇷'},
    {c:'+372',n:'Estonia',f:'🇪🇪'}, {c:'+268',n:'Eswatini',f:'🇸🇿'}, {c:'+251',n:'Ethiopia',f:'🇪🇹'}, {c:'+679',n:'Fiji',f:'🇫🇯'},
    {c:'+358',n:'Finland',f:'🇫🇮'}, {c:'+33',n:'France',f:'🇫🇷'}, {c:'+689',n:'French Polynesia',f:'🇵🇫'}, {c:'+241',n:'Gabon',f:'🇬🇦'},
    {c:'+220',n:'Gambia',f:'🇬🇲'}, {c:'+995',n:'Georgia',f:'🇬🇪'}, {c:'+49',n:'Germany',f:'🇩🇪'}, {c:'+233',n:'Ghana',f:'🇬🇭'},
    {c:'+350',n:'Gibraltar',f:'🇬🇮'}, {c:'+30',n:'Greece',f:'🇬🇷'}, {c:'+299',n:'Greenland',f:'🇬🇱'}, {c:'+1473',n:'Grenada',f:'🇬🇩'},
    {c:'+502',n:'Guatemala',f:'🇬🇹'}, {c:'+224',n:'Guinea',f:'🇬🇳'}, {c:'+245',n:'Guinea-Bissau',f:'🇬🇼'}, {c:'+592',n:'Guyana',f:'🇬🇾'},
    {c:'+509',n:'Haiti',f:'🇭🇹'}, {c:'+504',n:'Honduras',f:'🇭🇳'}, {c:'+852',n:'Hong Kong',f:'🇭🇰'}, {c:'+36',n:'Hungary',f:'🇭🇺'},
    {c:'+354',n:'Iceland',f:'🇮🇸'}, {c:'+91',n:'India',f:'🇮🇳'}, {c:'+62',n:'Indonesia',f:'🇮🇩'}, {c:'+98',n:'Iran',f:'🇮🇷'},
    {c:'+964',n:'Iraq',f:'🇮🇶'}, {c:'+353',n:'Ireland',f:'🇮🇪'}, {c:'+972',n:'Israel',f:'🇮🇱'}, {c:'+39',n:'Italy',f:'🇮🇹'},
    {c:'+225',n:'Ivory Coast',f:'🇨🇮'}, {c:'+1876',n:'Jamaica',f:'🇯🇲'}, {c:'+81',n:'Japan',f:'🇯🇵'}, {c:'+962',n:'Jordan',f:'🇯🇴'},
    {c:'+7',n:'Kazakhstan',f:'🇰🇿'}, {c:'+254',n:'Kenya',f:'🇰🇪'}, {c:'+965',n:'Kuwait',f:'🇰🇼'}, {c:'+996',n:'Kyrgyzstan',f:'🇰🇬'},
    {c:'+856',n:'Laos',f:'🇱🇦'}, {c:'+371',n:'Latvia',f:'🇱🇻'}, {c:'+961',n:'Lebanon',f:'🇱🇧'}, {c:'+266',n:'Lesotho',f:'🇱🇸'},
    {c:'+231',n:'Liberia',f:'🇱🇷'}, {c:'+218',n:'Libya',f:'🇱🇾'}, {c:'+423',n:'Liechtenstein',f:'🇱🇮'}, {c:'+370',n:'Lithuania',f:'🇱🇹'},
    {c:'+352',n:'Luxembourg',f:'🇱🇺'}, {c:'+853',n:'Macau',f:'🇲🇴'}, {c:'+261',n:'Madagascar',f:'🇲🇬'}, {c:'+265',n:'Malawi',f:'🇲🇼'},
    {c:'+60',n:'Malaysia',f:'🇲🇾'}, {c:'+960',n:'Maldives',f:'🇲🇻'}, {c:'+223',n:'Mali',f:'🇲🇱'}, {c:'+356',n:'Malta',f:'🇲🇹'},
    {c:'+692',n:'Marshall Islands',f:'🇲🇭'}, {c:'+222',n:'Mauritania',f:'🇲🇷'}, {c:'+230',n:'Mauritius',f:'🇲🇺'}, {c:'+52',n:'Mexico',f:'🇲🇽'},
    {c:'+691',n:'Micronesia',f:'🇫🇲'}, {c:'+373',n:'Moldova',f:'🇲🇩'}, {c:'+377',n:'Monaco',f:'🇲🇨'}, {c:'+976',n:'Mongolia',f:'🇲🇳'},
    {c:'+382',n:'Montenegro',f:'🇲🇪'}, {c:'+212',n:'Morocco',f:'🇲🇦'}, {c:'+258',n:'Mozambique',f:'🇲🇿'}, {c:'+95',n:'Myanmar',f:'🇲🇲'},
    {c:'+264',n:'Namibia',f:'🇳🇦'}, {c:'+977',n:'Nepal',f:'🇳🇵'}, {c:'+31',n:'Netherlands',f:'🇳🇱'}, {c:'+64',n:'New Zealand',f:'🇳🇿'},
    {c:'+505',n:'Nicaragua',f:'🇳🇮'}, {c:'+227',n:'Niger',f:'🇳🇪'}, {c:'+234',n:'Nigeria',f:'🇳🇬'}, {c:'+850',n:'North Korea',f:'🇰🇵'},
    {c:'+389',n:'North Macedonia',f:'🇲🇰'}, {c:'+47',n:'Norway',f:'🇳🇴'}, {c:'+968',n:'Oman',f:'🇴🇲'}, {c:'+92',n:'Pakistan',f:'🇵🇰'},
    {c:'+970',n:'Palestine',f:'🇵🇸'}, {c:'+507',n:'Panama',f:'🇵🇦'}, {c:'+675',n:'Papua New Guinea',f:'🇵🇬'}, {c:'+595',n:'Paraguay',f:'🇵🇾'},
    {c:'+51',n:'Peru',f:'🇵🇪'}, {c:'+63',n:'Philippines',f:'🇵🇭'}, {c:'+48',n:'Poland',f:'🇵🇱'}, {c:'+351',n:'Portugal',f:'🇵🇹'},
    {c:'+1',n:'Puerto Rico',f:'🇵🇷'}, {c:'+974',n:'Qatar',f:'🇶🇦'}, {c:'+40',n:'Romania',f:'🇷🇴'}, {c:'+7',n:'Russia',f:'🇷🇺'},
    {c:'+250',n:'Rwanda',f:'🇷🇼'}, {c:'+1758',n:'Saint Lucia',f:'🇱🇨'}, {c:'+685',n:'Samoa',f:'🇼🇸'}, {c:'+378',n:'San Marino',f:'🇸🇲'},
    {c:'+966',n:'Saudi Arabia',f:'🇸🇦'}, {c:'+221',n:'Senegal',f:'🇸🇳'}, {c:'+381',n:'Serbia',f:'🇷🇸'}, {c:'+248',n:'Seychelles',f:'🇸🇨'},
    {c:'+232',n:'Sierra Leone',f:'🇸🇱'}, {c:'+65',n:'Singapore',f:'🇸🇬'}, {c:'+421',n:'Slovakia',f:'🇸🇰'}, {c:'+386',n:'Slovenia',f:'🇸🇮'},
    {c:'+677',n:'Solomon Islands',f:'🇸🇧'}, {c:'+252',n:'Somalia',f:'🇸🇴'}, {c:'+27',n:'South Africa',f:'🇿🇦'}, {c:'+82',n:'South Korea',f:'🇰🇷'},
    {c:'+211',n:'South Sudan',f:'🇸🇸'}, {c:'+34',n:'Spain',f:'🇪🇸'}, {c:'+94',n:'Sri Lanka',f:'🇱🇰'}, {c:'+249',n:'Sudan',f:'🇸🇩'},
    {c:'+597',n:'Suriname',f:'🇸🇷'}, {c:'+46',n:'Sweden',f:'🇸🇪'}, {c:'+41',n:'Switzerland',f:'🇨🇭'}, {c:'+963',n:'Syria',f:'🇸🇾'},
    {c:'+886',n:'Taiwan',f:'🇹🇼'}, {c:'+992',n:'Tajikistan',f:'🇹🇯'}, {c:'+255',n:'Tanzania',f:'🇹🇿'}, {c:'+66',n:'Thailand',f:'🇹🇭'},
    {c:'+670',n:'Timor-Leste',f:'🇹🇱'}, {c:'+228',n:'Togo',f:'🇹🇬'}, {c:'+676',n:'Tonga',f:'🇹🇴'}, {c:'+1868',n:'Trinidad and Tobago',f:'🇹🇹'},
    {c:'+216',n:'Tunisia',f:'🇹🇳'}, {c:'+90',n:'Turkey',f:'🇹🇷'}, {c:'+993',n:'Turkmenistan',f:'🇹🇲'}, {c:'+256',n:'Uganda',f:'🇺🇬'},
    {c:'+380',n:'Ukraine',f:'🇺🇦'}, {c:'+971',n:'United Arab Emirates',f:'🇦🇪'}, {c:'+44',n:'United Kingdom',f:'🇬🇧'}, {c:'+1',n:'United States',f:'🇺🇸'},
    {c:'+598',n:'Uruguay',f:'🇺🇾'}, {c:'+998',n:'Uzbekistan',f:'🇺🇿'}, {c:'+678',n:'Vanuatu',f:'🇻🇺'}, {c:'+379',n:'Vatican City',f:'🇻🇦'},
    {c:'+58',n:'Venezuela',f:'🇻🇪'}, {c:'+84',n:'Vietnam',f:'🇻🇳'}, {c:'+967',n:'Yemen',f:'🇾🇪'}, {c:'+260',n:'Zambia',f:'🇿🇲'},
    {c:'+263',n:'Zimbabwe',f:'🇿🇼'}
];
                },
                get opts() {
                    if (!this.qPhone) return this.allCodes;
                    const s = this.qPhone.toLowerCase();
                    return this.allCodes.filter(c => c.n.toLowerCase().includes(s) || c.c.includes(s));
                },
                pick(c) { 
                    this.selectedCode = c.c; 
                    this.qPhone = ''; 
                    this.openPhone = false; 
                    this.hiPhone = -1; 
                },
                getSelected() { return this.allCodes.find(c => c.c === this.selectedCode) || this.allCodes[0]; }
            }"
            @phone-code-changed.window="if ($event.detail.prefix === '{{ $prefix }}') { selectedCode = $event.detail.dial; }"
            @click.away="openPhone = false; qPhone = '';"
            class="relative flex gap-0 w-full bg-gray-800 border border-gray-700 rounded-xl overflow-visible focus-within:border-violet-500 transition-colors"
        >
            <div class="relative flex-shrink-0 border-r border-gray-700">
                <button type="button" @click="openPhone = !openPhone" class="h-full px-3 py-2 flex items-center gap-2 font-semibold text-white hover:text-violet-400 transition-colors">
                    <span x-text="getSelected().f" class="text-base"></span>
                    <span x-text="getSelected().c" class="text-sm"></span>
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </button>
                
                <div x-show="openPhone" x-transition x-cloak class="absolute top-full left-0 mt-2 w-64 bg-gray-800 border border-gray-700 rounded-xl shadow-2xl z-50 overflow-hidden flex flex-col max-h-72">
                    <div class="p-2 border-b border-gray-700 sticky top-0 bg-gray-800 z-10">
                        <input type="text" x-model="qPhone" placeholder="Search country or code..." class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-violet-500" @click.stop>
                    </div>
                    <div class="overflow-y-auto flex-1 p-1" style="scrollbar-width: thin; scrollbar-color: #4b5563 transparent;">
                        <template x-for="(c, i) in opts" :key="c.c + c.n">
                            <button type="button" @click="pick(c)" :class="c.c === selectedCode ? 'bg-gray-700 text-white' : 'text-slate-300 hover:bg-gray-700/80'" class="w-full text-left flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors mb-0.5">
                                <span x-text="c.f" class="text-base"></span>
                                <span x-text="c.n" class="truncate flex-1"></span>
                                <span x-text="c.c" class="font-mono text-xs text-slate-400"></span>
                            </button>
                        </template>
                        <div x-show="opts.length === 0" class="px-4 py-3 text-sm text-slate-400 text-center">No countries found</div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="{{ $prefix }}_phone_code" x-model="selectedCode">
            
            <input type="tel" name="{{ $prefix }}_phone" value="{{ old($prefix . '_phone') }}" placeholder="1968524222" required
                class="w-full bg-transparent border-none px-3 py-2 text-sm text-white focus:ring-0 outline-none placeholder-gray-600">
        </div>
    </div>
</div>

{{-- Save to Address Book --}}
@if(!($guestMode ?? false))
<div class="pt-3 border-t border-white/10 space-y-2">
    <button type="button" onclick="saveToAddressBook('{{ $prefix }}')"
        data-save-address-book-button="{{ $prefix }}"
        class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-300 transition-all hover:border-emerald-400 hover:bg-emerald-500/15 hover:text-emerald-200 disabled:cursor-not-allowed disabled:opacity-60">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
        <span>Save to Address Book</span>
    </button>
    <p id="{{ $prefix }}_save_status" class="hidden text-xs leading-5 text-slate-400"></p>
</div>
@endif

@once
@push('scripts')
<script>
window.COUNTRIES = [
    {name:'Afghanistan',code:'AF'},{name:'Albania',code:'AL'},{name:'Algeria',code:'DZ'},
    {name:'American Samoa',code:'AS'},{name:'Andorra',code:'AD'},{name:'Angola',code:'AO'},
    {name:'Anguilla',code:'AI'},{name:'Antigua and Barbuda',code:'AG'},{name:'Argentina',code:'AR'},
    {name:'Armenia',code:'AM'},{name:'Aruba',code:'AW'},{name:'Australia',code:'AU'},
    {name:'Austria',code:'AT'},{name:'Azerbaijan',code:'AZ'},{name:'Bahamas',code:'BS'},
    {name:'Bahrain',code:'BH'},{name:'Bangladesh',code:'BD'},{name:'Barbados',code:'BB'},
    {name:'Belarus',code:'BY'},{name:'Belgium',code:'BE'},{name:'Belize',code:'BZ'},
    {name:'Benin',code:'BJ'},{name:'Bermuda',code:'BM'},{name:'Bhutan',code:'BT'},
    {name:'Bolivia',code:'BO'},{name:'Bosnia and Herzegovina',code:'BA'},{name:'Botswana',code:'BW'},
    {name:'Brazil',code:'BR'},{name:'Brunei',code:'BN'},{name:'Bulgaria',code:'BG'},
    {name:'Burkina Faso',code:'BF'},{name:'Burundi',code:'BI'},{name:'Cambodia',code:'KH'},
    {name:'Cameroon',code:'CM'},{name:'Canada',code:'CA'},{name:'Cape Verde',code:'CV'},
    {name:'Cayman Islands',code:'KY'},{name:'Central African Republic',code:'CF'},{name:'Chad',code:'TD'},
    {name:'Chile',code:'CL'},{name:'China',code:'CN'},{name:'Colombia',code:'CO'},
    {name:'Comoros',code:'KM'},{name:'Congo',code:'CG'},{name:'DR Congo',code:'CD'},
    {name:'Cook Islands',code:'CK'},{name:'Costa Rica',code:'CR'},{name:'Croatia',code:'HR'},
    {name:'Cuba',code:'CU'},{name:'Cyprus',code:'CY'},{name:'Czech Republic',code:'CZ'},
    {name:'Denmark',code:'DK'},{name:'Djibouti',code:'DJ'},{name:'Dominica',code:'DM'},
    {name:'Dominican Republic',code:'DO'},{name:'Ecuador',code:'EC'},{name:'Egypt',code:'EG'},
    {name:'El Salvador',code:'SV'},{name:'Equatorial Guinea',code:'GQ'},{name:'Eritrea',code:'ER'},
    {name:'Estonia',code:'EE'},{name:'Eswatini',code:'SZ'},{name:'Ethiopia',code:'ET'},
    {name:'Fiji',code:'FJ'},{name:'Finland',code:'FI'},{name:'France',code:'FR'},
    {name:'French Polynesia',code:'PF'},{name:'Gabon',code:'GA'},{name:'Gambia',code:'GM'},
    {name:'Georgia',code:'GE'},{name:'Germany',code:'DE'},{name:'Ghana',code:'GH'},
    {name:'Gibraltar',code:'GI'},{name:'Greece',code:'GR'},{name:'Greenland',code:'GL'},
    {name:'Grenada',code:'GD'},{name:'Guatemala',code:'GT'},{name:'Guinea',code:'GN'},
    {name:'Guinea-Bissau',code:'GW'},{name:'Guyana',code:'GY'},{name:'Haiti',code:'HT'},
    {name:'Honduras',code:'HN'},{name:'Hong Kong',code:'HK'},{name:'Hungary',code:'HU'},
    {name:'Iceland',code:'IS'},{name:'India',code:'IN'},{name:'Indonesia',code:'ID'},
    {name:'Iran',code:'IR'},{name:'Iraq',code:'IQ'},{name:'Ireland',code:'IE'},
    {name:'Israel',code:'IL'},{name:'Italy',code:'IT'},{name:'Ivory Coast',code:'CI'},
    {name:'Jamaica',code:'JM'},{name:'Japan',code:'JP'},{name:'Jordan',code:'JO'},
    {name:'Kazakhstan',code:'KZ'},{name:'Kenya',code:'KE'},{name:'Kuwait',code:'KW'},
    {name:'Kyrgyzstan',code:'KG'},{name:'Laos',code:'LA'},{name:'Latvia',code:'LV'},
    {name:'Lebanon',code:'LB'},{name:'Lesotho',code:'LS'},{name:'Liberia',code:'LR'},
    {name:'Libya',code:'LY'},{name:'Liechtenstein',code:'LI'},{name:'Lithuania',code:'LT'},
    {name:'Luxembourg',code:'LU'},{name:'Macau',code:'MO'},{name:'Madagascar',code:'MG'},
    {name:'Malawi',code:'MW'},{name:'Malaysia',code:'MY'},{name:'Maldives',code:'MV'},
    {name:'Mali',code:'ML'},{name:'Malta',code:'MT'},{name:'Marshall Islands',code:'MH'},
    {name:'Mauritania',code:'MR'},{name:'Mauritius',code:'MU'},{name:'Mexico',code:'MX'},
    {name:'Micronesia',code:'FM'},{name:'Moldova',code:'MD'},{name:'Monaco',code:'MC'},
    {name:'Mongolia',code:'MN'},{name:'Montenegro',code:'ME'},{name:'Morocco',code:'MA'},
    {name:'Mozambique',code:'MZ'},{name:'Myanmar',code:'MM'},{name:'Namibia',code:'NA'},
    {name:'Nepal',code:'NP'},{name:'Netherlands',code:'NL'},{name:'New Zealand',code:'NZ'},
    {name:'Nicaragua',code:'NI'},{name:'Niger',code:'NE'},{name:'Nigeria',code:'NG'},
    {name:'North Korea',code:'KP'},{name:'North Macedonia',code:'MK'},{name:'Norway',code:'NO'},
    {name:'Oman',code:'OM'},{name:'Pakistan',code:'PK'},{name:'Palestine',code:'PS'},
    {name:'Panama',code:'PA'},{name:'Papua New Guinea',code:'PG'},{name:'Paraguay',code:'PY'},
    {name:'Peru',code:'PE'},{name:'Philippines',code:'PH'},{name:'Poland',code:'PL'},
    {name:'Portugal',code:'PT'},{name:'Puerto Rico',code:'PR'},{name:'Qatar',code:'QA'},
    {name:'Romania',code:'RO'},{name:'Russia',code:'RU'},{name:'Rwanda',code:'RW'},
    {name:'Saint Lucia',code:'LC'},{name:'Samoa',code:'WS'},{name:'San Marino',code:'SM'},
    {name:'Saudi Arabia',code:'SA'},{name:'Senegal',code:'SN'},{name:'Serbia',code:'RS'},
    {name:'Seychelles',code:'SC'},{name:'Sierra Leone',code:'SL'},{name:'Singapore',code:'SG'},
    {name:'Slovakia',code:'SK'},{name:'Slovenia',code:'SI'},{name:'Solomon Islands',code:'SB'},
    {name:'Somalia',code:'SO'},{name:'South Africa',code:'ZA'},{name:'South Korea',code:'KR'},
    {name:'South Sudan',code:'SS'},{name:'Spain',code:'ES'},{name:'Sri Lanka',code:'LK'},
    {name:'Sudan',code:'SD'},{name:'Suriname',code:'SR'},{name:'Sweden',code:'SE'},
    {name:'Switzerland',code:'CH'},{name:'Syria',code:'SY'},{name:'Taiwan',code:'TW'},
    {name:'Tajikistan',code:'TJ'},{name:'Tanzania',code:'TZ'},{name:'Thailand',code:'TH'},
    {name:'Timor-Leste',code:'TL'},{name:'Togo',code:'TG'},{name:'Tonga',code:'TO'},
    {name:'Trinidad and Tobago',code:'TT'},{name:'Tunisia',code:'TN'},{name:'Turkey',code:'TR'},
    {name:'Turkmenistan',code:'TM'},{name:'Uganda',code:'UG'},{name:'Ukraine',code:'UA'},
    {name:'United Arab Emirates',code:'AE'},{name:'United Kingdom',code:'GB'},
    {name:'United States',code:'US'},{name:'Uruguay',code:'UY'},{name:'Uzbekistan',code:'UZ'},
    {name:'Vanuatu',code:'VU'},{name:'Vatican City',code:'VA'},{name:'Venezuela',code:'VE'},
    {name:'Vietnam',code:'VN'},{name:'Yemen',code:'YE'},{name:'Zambia',code:'ZM'},
    {name:'Zimbabwe',code:'ZW'},
];

const _ctryIdx = {};

function _buildCountryDropdown(prefix, results, q) {
    const dd = document.getElementById(prefix + '_country_dropdown');
    if (!results.length) {
        dd.innerHTML = '<div class="px-4 py-3 text-sm text-slate-400 italic">No countries found</div>';
        dd.classList.remove('hidden');
        return;
    }
    const esc = s => s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    dd.innerHTML = results.map((c, i) => {
        const label = q ? c.name.replace(new RegExp('(' + esc(q) + ')', 'gi'), '<strong class="text-white font-semibold">$1</strong>') : c.name;
        return `<div class="country-option flex items-center justify-between px-4 py-3 text-sm text-slate-300 hover:bg-gray-700/80 cursor-pointer transition-colors min-h-[44px]" data-idx="${i}" ontouchend="event.preventDefault();selectCountry('${prefix}','${c.name.replace(/'/g,"\\'")}','${c.code}')" onclick="selectCountry('${prefix}','${c.name.replace(/'/g,"\\'")}','${c.code}')"><span>${label}</span><span class="text-xs font-mono text-slate-400 ml-2 shrink-0">${c.code}</span></div>`;
    }).join('');
    dd.classList.remove('hidden');
    _ctryIdx[prefix] = -1;
}

function countryAutocomplete(input, prefix) {
    const q = input.value.trim().toLowerCase();
    const clearBtn = document.getElementById(prefix + '_country_clear');
    if (clearBtn) clearBtn.classList.toggle('hidden', !input.value.trim());
    let results;
    if (q.length === 0) {
        results = window.COUNTRIES.slice(0, 60);
    } else {
        results = window.COUNTRIES.filter(c => {
            const name = c.name.toLowerCase();
            const code = c.code.toLowerCase();
            let matchesAlias = false;
            if (q === 'us' || q === 'usa') {
                if (code === 'us' || name === 'united states') matchesAlias = true;
            } else if (q === 'uk') {
                if (code === 'gb' || name === 'united kingdom') matchesAlias = true;
            } else if (q === 'uae') {
                if (code === 'ae' || name === 'united arab emirates') matchesAlias = true;
            }
            return name.includes(q) || code === q || code.startsWith(q) || matchesAlias;
        }).slice(0, 12);
    }
    _buildCountryDropdown(prefix, results, q);
}

function countryInputFocus(input, prefix) {
    countryAutocomplete(input, prefix);
}

function countryKeydown(e, prefix) {
    const dd = document.getElementById(prefix + '_country_dropdown');
    const opts = dd.querySelectorAll('.country-option');
    if (!opts.length || dd.classList.contains('hidden')) return;
    if (!_ctryIdx[prefix]) _ctryIdx[prefix] = -1;
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        _ctryIdx[prefix] = Math.min(_ctryIdx[prefix] + 1, opts.length - 1);
        opts.forEach((o, i) => o.classList.toggle('bg-gray-700', i === _ctryIdx[prefix]));
        opts[_ctryIdx[prefix]].scrollIntoView({ block: 'nearest' });
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        _ctryIdx[prefix] = Math.max(_ctryIdx[prefix] - 1, 0);
        opts.forEach((o, i) => o.classList.toggle('bg-gray-700', i === _ctryIdx[prefix]));
        opts[_ctryIdx[prefix]].scrollIntoView({ block: 'nearest' });
    } else if (e.key === 'Enter') {
        e.preventDefault();
        if (_ctryIdx[prefix] >= 0) opts[_ctryIdx[prefix]].click();
    } else if (e.key === 'Escape') {
        dd.classList.add('hidden');
    }
}

function clearCountryField(prefix) {
    const inp = document.getElementById(prefix + '_country_input');
    inp.value = '';
    document.getElementById(prefix + '_country_code').value = '';
    document.getElementById(prefix + '_country_clear').classList.add('hidden');
    inp.focus();
    countryAutocomplete(inp, prefix);
}

function selectCountry(prefix, name, code) {
    document.querySelector(`[name="${prefix}_country"]`).value = name;
    document.getElementById(prefix + '_country_code').value = code;
    document.getElementById(prefix + '_country_dropdown').classList.add('hidden');
    const clearBtn = document.getElementById(prefix + '_country_clear');
    if (clearBtn) clearBtn.classList.remove('hidden');
    const codeEl = document.querySelector(`[name="${prefix}_phone_code"]`);
    const cObj = window.PHONE_CODES.find(p => p.code === code);
    if (codeEl && cObj) {
        codeEl.value = cObj.dial;
        codeEl.dispatchEvent(new Event('change', { bubbles: true }));
        window.dispatchEvent(new CustomEvent('phone-code-changed', {
            detail: { prefix: prefix, dial: cObj.dial }
        }));
    }
}

document.addEventListener('click', (e) => {
    if (!e.target.closest('.country-search-wrapper')) {
        document.querySelectorAll('[id$="_country_dropdown"]').forEach(d => d.classList.add('hidden'));
    }
    if (!e.target.closest('.phone-code-search-wrapper')) {
        document.querySelectorAll('[id$="_phone_code_dropdown"]').forEach(d => d.classList.add('hidden'));
    }
});

window.PHONE_CODES = [
    {code:'AF',dial:'+93'},{code:'AL',dial:'+355'},{code:'DZ',dial:'+213'},{code:'AS',dial:'+1684'},
    {code:'AD',dial:'+376'},{code:'AO',dial:'+244'},{code:'AI',dial:'+1264'},{code:'AG',dial:'+1268'},
    {code:'AR',dial:'+54'},{code:'AM',dial:'+374'},{code:'AW',dial:'+297'},{code:'AU',dial:'+61'},
    {code:'AT',dial:'+43'},{code:'AZ',dial:'+994'},{code:'BS',dial:'+1242'},{code:'BH',dial:'+973'},
    {code:'BD',dial:'+880'},{code:'BB',dial:'+1246'},{code:'BY',dial:'+375'},{code:'BE',dial:'+32'},
    {code:'BZ',dial:'+501'},{code:'BJ',dial:'+229'},{code:'BM',dial:'+1441'},{code:'BT',dial:'+975'},
    {code:'BO',dial:'+591'},{code:'BA',dial:'+387'},{code:'BW',dial:'+267'},{code:'BR',dial:'+55'},
    {code:'BN',dial:'+673'},{code:'BG',dial:'+359'},{code:'BF',dial:'+226'},{code:'BI',dial:'+257'},
    {code:'KH',dial:'+855'},{code:'CM',dial:'+237'},{code:'CA',dial:'+1'},{code:'CV',dial:'+238'},
    {code:'KY',dial:'+1345'},{code:'CF',dial:'+236'},{code:'TD',dial:'+235'},{code:'CL',dial:'+56'},
    {code:'CN',dial:'+86'},{code:'CO',dial:'+57'},{code:'KM',dial:'+269'},{code:'CG',dial:'+242'},
    {code:'CD',dial:'+243'},{code:'CK',dial:'+682'},{code:'CR',dial:'+506'},{code:'HR',dial:'+385'},
    {code:'CU',dial:'+53'},{code:'CY',dial:'+357'},{code:'CZ',dial:'+420'},{code:'DK',dial:'+45'},
    {code:'DJ',dial:'+253'},{code:'DM',dial:'+1767'},{code:'DO',dial:'+1809'},{code:'EC',dial:'+593'},
    {code:'EG',dial:'+20'},{code:'SV',dial:'+503'},{code:'GQ',dial:'+240'},{code:'ER',dial:'+291'},
    {code:'EE',dial:'+372'},{code:'SZ',dial:'+268'},{code:'ET',dial:'+251'},{code:'FJ',dial:'+679'},
    {code:'FI',dial:'+358'},{code:'FR',dial:'+33'},{code:'PF',dial:'+689'},{code:'GA',dial:'+241'},
    {code:'GM',dial:'+220'},{code:'GE',dial:'+995'},{code:'DE',dial:'+49'},{code:'GH',dial:'+233'},
    {code:'GI',dial:'+350'},{code:'GR',dial:'+30'},{code:'GL',dial:'+299'},{code:'GD',dial:'+1473'},
    {code:'GT',dial:'+502'},{code:'GN',dial:'+224'},{code:'GW',dial:'+245'},{code:'GY',dial:'+592'},
    {code:'HT',dial:'+509'},{code:'HN',dial:'+504'},{code:'HK',dial:'+852'},{code:'HU',dial:'+36'},
    {code:'IS',dial:'+354'},{code:'IN',dial:'+91'},{code:'ID',dial:'+62'},{code:'IR',dial:'+98'},
    {code:'IQ',dial:'+964'},{code:'IE',dial:'+353'},{code:'IL',dial:'+972'},{code:'IT',dial:'+39'},
    {code:'CI',dial:'+225'},{code:'JM',dial:'+1876'},{code:'JP',dial:'+81'},{code:'JO',dial:'+962'},
    {code:'KZ',dial:'+7'},{code:'KE',dial:'+254'},{code:'KW',dial:'+965'},{code:'KG',dial:'+996'},
    {code:'LA',dial:'+856'},{code:'LV',dial:'+371'},{code:'LB',dial:'+961'},{code:'LS',dial:'+266'},
    {code:'LR',dial:'+231'},{code:'LY',dial:'+218'},{code:'LI',dial:'+423'},{code:'LT',dial:'+370'},
    {code:'LU',dial:'+352'},{code:'MO',dial:'+853'},{code:'MG',dial:'+261'},{code:'MW',dial:'+265'},
    {code:'MY',dial:'+60'},{code:'MV',dial:'+960'},{code:'ML',dial:'+223'},{code:'MT',dial:'+356'},
    {code:'MH',dial:'+692'},{code:'MR',dial:'+222'},{code:'MU',dial:'+230'},{code:'MX',dial:'+52'},
    {code:'FM',dial:'+691'},{code:'MD',dial:'+373'},{code:'MC',dial:'+377'},{code:'MN',dial:'+976'},
    {code:'ME',dial:'+382'},{code:'MA',dial:'+212'},{code:'MZ',dial:'+258'},{code:'MM',dial:'+95'},
    {code:'NA',dial:'+264'},{code:'NP',dial:'+977'},{code:'NL',dial:'+31'},{code:'NZ',dial:'+64'},
    {code:'NI',dial:'+505'},{code:'NE',dial:'+227'},{code:'NG',dial:'+234'},{code:'KP',dial:'+850'},
    {code:'MK',dial:'+389'},{code:'NO',dial:'+47'},{code:'OM',dial:'+968'},{code:'PK',dial:'+92'},
    {code:'PS',dial:'+970'},{code:'PA',dial:'+507'},{code:'PG',dial:'+675'},{code:'PY',dial:'+595'},
    {code:'PE',dial:'+51'},{code:'PH',dial:'+63'},{code:'PL',dial:'+48'},{code:'PT',dial:'+351'},
    {code:'PR',dial:'+1'},{code:'QA',dial:'+974'},{code:'RO',dial:'+40'},{code:'RU',dial:'+7'},
    {code:'RW',dial:'+250'},{code:'LC',dial:'+1758'},{code:'WS',dial:'+685'},{code:'SM',dial:'+378'},
    {code:'SA',dial:'+966'},{code:'SN',dial:'+221'},{code:'RS',dial:'+381'},{code:'SC',dial:'+248'},
    {code:'SL',dial:'+232'},{code:'SG',dial:'+65'},{code:'SK',dial:'+421'},{code:'SI',dial:'+386'},
    {code:'SB',dial:'+677'},{code:'SO',dial:'+252'},{code:'ZA',dial:'+27'},{code:'KR',dial:'+82'},
    {code:'SS',dial:'+211'},{code:'ES',dial:'+34'},{code:'LK',dial:'+94'},{code:'SD',dial:'+249'},
    {code:'SR',dial:'+597'},{code:'SE',dial:'+46'},{code:'CH',dial:'+41'},{code:'SY',dial:'+963'},
    {code:'TW',dial:'+886'},{code:'TJ',dial:'+992'},{code:'TZ',dial:'+255'},{code:'TH',dial:'+66'},
    {code:'TL',dial:'+670'},{code:'TG',dial:'+228'},{code:'TO',dial:'+676'},{code:'TT',dial:'+1868'},
    {code:'TN',dial:'+216'},{code:'TR',dial:'+90'},{code:'TM',dial:'+993'},{code:'UG',dial:'+256'},
    {code:'UA',dial:'+380'},{code:'AE',dial:'+971'},{code:'GB',dial:'+44'},{code:'US',dial:'+1'},
    {code:'UY',dial:'+598'},{code:'UZ',dial:'+998'},{code:'VU',dial:'+678'},{code:'VA',dial:'+379'},
    {code:'VE',dial:'+58'},{code:'VN',dial:'+84'},{code:'YE',dial:'+967'},{code:'ZM',dial:'+260'},
    {code:'ZW',dial:'+263'}
];

const _phoneCodeIdx = {};

function _buildPhoneCodeDropdown(prefix, results, q) {
    const dd = document.getElementById(prefix + '_phone_code_dropdown');
    if (!results.length) {
        dd.innerHTML = '<div class="px-4 py-3 text-sm text-slate-400 italic">No codes found</div>';
        dd.classList.remove('hidden');
        return;
    }
    const esc = s => s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    dd.innerHTML = results.map((c, i) => {
        const labelStr = c.code + ' ' + c.dial;
        const label = q ? labelStr.replace(new RegExp('(' + esc(q) + ')', 'gi'), '<strong class="text-white font-semibold">$1</strong>') : labelStr;
        return `<div class="phone-code-option flex items-center justify-between px-3 py-2 text-sm text-slate-300 hover:bg-gray-700/80 cursor-pointer transition-colors" data-idx="${i}" ontouchend="event.preventDefault();selectPhoneCode('${prefix}','${c.dial}')" onclick="selectPhoneCode('${prefix}','${c.dial}')"><span>${label}</span></div>`;
    }).join('');
    dd.classList.remove('hidden');
    _phoneCodeIdx[prefix] = -1;
}

function phoneCodeAutocomplete(input, prefix) {
    const q = input.value.trim().toLowerCase();
    const results = q.length === 0
        ? window.PHONE_CODES.slice(0, 50)
        : window.PHONE_CODES.filter(c => c.code.toLowerCase().includes(q) || c.dial.includes(q)).slice(0, 12);
    _buildPhoneCodeDropdown(prefix, results, q);
}

function phoneCodeKeydown(e, prefix) {
    const dd = document.getElementById(prefix + '_phone_code_dropdown');
    const opts = dd.querySelectorAll('.phone-code-option');
    if (!opts.length || dd.classList.contains('hidden')) return;
    if (!_phoneCodeIdx[prefix]) _phoneCodeIdx[prefix] = -1;
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        _phoneCodeIdx[prefix] = Math.min(_phoneCodeIdx[prefix] + 1, opts.length - 1);
        opts.forEach((o, i) => o.classList.toggle('bg-gray-700', i === _phoneCodeIdx[prefix]));
        opts[_phoneCodeIdx[prefix]].scrollIntoView({ block: 'nearest' });
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        _phoneCodeIdx[prefix] = Math.max(_phoneCodeIdx[prefix] - 1, 0);
        opts.forEach((o, i) => o.classList.toggle('bg-gray-700', i === _phoneCodeIdx[prefix]));
        opts[_phoneCodeIdx[prefix]].scrollIntoView({ block: 'nearest' });
    } else if (e.key === 'Enter') {
        e.preventDefault();
        if (_phoneCodeIdx[prefix] >= 0) opts[_phoneCodeIdx[prefix]].click();
    } else if (e.key === 'Escape') {
        dd.classList.add('hidden');
    }
}

function selectPhoneCode(prefix, dial) {
    document.getElementById(prefix + '_phone_code_input').value = dial;
    document.getElementById(prefix + '_phone_code_dropdown').classList.add('hidden');
}
</script>
@endpush
@endonce
