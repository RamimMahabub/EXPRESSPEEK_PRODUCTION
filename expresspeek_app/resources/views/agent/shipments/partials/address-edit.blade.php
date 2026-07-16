{{-- Business Contact Toggle --}}
<div class="flex items-center gap-3 mb-4">
    <label class="flex items-center gap-2 cursor-pointer">
        <input type="checkbox" name="{{ $prefix }}_is_business" value="1" id="{{ $prefix }}_is_business_check"
            class="w-4 h-4 rounded border-gray-600 text-violet-600 bg-gray-800 focus:ring-violet-500"
            @checked((int) old($prefix . '_is_business', data_get($source, $prefix . '_is_business')) === 1)
            onchange="document.getElementById('{{ $prefix }}_business_section').classList.toggle('hidden', !this.checked)">
        <span class="text-sm text-slate-400">Business Contact</span>
    </label>
</div>

{{-- Name --}}
<div class="mb-4">
    <label class="block text-xs font-medium text-slate-400 mb-1.5">Full Name *</label>
    <input type="text" name="{{ $prefix }}_name" value="{{ old($prefix . '_name', data_get($source, $prefix . '_name')) }}"
        placeholder="Full name"
        class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500 transition-colors">
</div>

{{-- Company (hidden until business checked) --}}
<div id="{{ $prefix }}_business_section" class="{{ ((int) old($prefix . '_is_business', data_get($source, $prefix . '_is_business')) === 1) ? '' : 'hidden' }} mb-4">
    <label class="block text-xs font-medium text-slate-400 mb-1.5">Company</label>
    <input type="text" name="{{ $prefix }}_company" value="{{ old($prefix . '_company', data_get($source, $prefix . '_company')) }}"
        placeholder="Company name"
        class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500 transition-colors">
</div>

{{-- Country --}}
<div class="mb-4">
    <label class="block text-xs font-medium text-slate-400 mb-1.5">Country / Territory *</label>
    <div class="relative country-search-wrapper">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="{{ $prefix }}_country" id="{{ $prefix }}_country_input"
                value="{{ old($prefix . '_country', data_get($source, $prefix . '_country')) }}"
                placeholder="Search country..."
                autocomplete="off"
                oninput="countryAutocomplete(this,'{{ $prefix }}')"
                onfocus="countryInputFocus(this,'{{ $prefix }}')"
                onkeydown="countryKeydown(event,'{{ $prefix }}')"
                class="w-full bg-gray-800 border border-gray-700 rounded-xl pl-9 pr-9 py-3 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-violet-500 transition-colors">
            <button type="button" id="{{ $prefix }}_country_clear" onclick="clearCountryField('{{ $prefix }}')"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-300 transition-colors {{ old($prefix . '_country', data_get($source, $prefix . '_country')) ? '' : 'hidden' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <input type="hidden" name="{{ $prefix }}_country_code" id="{{ $prefix }}_country_code"
            value="{{ old($prefix . '_country_code', data_get($source, $prefix . '_country_code')) }}">
        <div id="{{ $prefix }}_country_dropdown" class="absolute top-full left-0 right-0 mt-1 bg-gray-800 border border-gray-700 rounded-xl shadow-xl z-30 hidden max-h-56 overflow-y-auto"></div>
    </div>
</div>

@once
@push('scripts')
<script>
if (typeof window.countryAutocomplete === 'undefined') {
    window.COUNTRIES = window.COUNTRIES || [
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
        if (!results.length) { dd.innerHTML = '<div class="px-4 py-3 text-sm text-slate-400 italic">No countries found</div>'; dd.classList.remove('hidden'); return; }
        const esc = s => s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        dd.innerHTML = results.map((c, i) => {
            const label = q ? c.name.replace(new RegExp('(' + esc(q) + ')', 'gi'), '<strong class="text-white font-semibold">$1</strong>') : c.name;
            return `<div class="country-option flex items-center justify-between px-4 py-3 text-sm text-slate-300 hover:bg-gray-700/80 cursor-pointer transition-colors min-h-[44px]" data-idx="${i}" ontouchend="event.preventDefault();selectCountry('${prefix}','${c.name.replace(/'/g,"\\'")}','${c.code}')" onclick="selectCountry('${prefix}','${c.name.replace(/'/g,"\\'")}','${c.code}')"><span>${label}</span><span class="text-xs font-mono text-slate-400 ml-2 shrink-0">${c.code}</span></div>`;
        }).join('');
        dd.classList.remove('hidden'); _ctryIdx[prefix] = -1;
    }
    window.countryAutocomplete = function(input, prefix) {
        const q = input.value.trim().toLowerCase();
        const clearBtn = document.getElementById(prefix + '_country_clear');
        if (clearBtn) clearBtn.classList.toggle('hidden', !input.value.trim());
        const results = q.length === 0 ? window.COUNTRIES.slice(0, 60) : window.COUNTRIES.filter(c => c.name.toLowerCase().includes(q) || c.code.toLowerCase() === q).slice(0, 12);
        _buildCountryDropdown(prefix, results, q);
    };
    window.countryInputFocus = function(input, prefix) { window.countryAutocomplete(input, prefix); };
    window.countryKeydown = function(e, prefix) {
        const dd = document.getElementById(prefix + '_country_dropdown');
        const opts = dd.querySelectorAll('.country-option');
        if (!opts.length || dd.classList.contains('hidden')) return;
        if (!_ctryIdx[prefix]) _ctryIdx[prefix] = -1;
        if (e.key === 'ArrowDown') { e.preventDefault(); _ctryIdx[prefix] = Math.min(_ctryIdx[prefix]+1, opts.length-1); opts.forEach((o,i) => o.classList.toggle('bg-gray-700', i === _ctryIdx[prefix])); opts[_ctryIdx[prefix]].scrollIntoView({block:'nearest'}); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); _ctryIdx[prefix] = Math.max(_ctryIdx[prefix]-1, 0); opts.forEach((o,i) => o.classList.toggle('bg-gray-700', i === _ctryIdx[prefix])); opts[_ctryIdx[prefix]].scrollIntoView({block:'nearest'}); }
        else if (e.key === 'Enter') { e.preventDefault(); if (_ctryIdx[prefix] >= 0) opts[_ctryIdx[prefix]].click(); }
        else if (e.key === 'Escape') { dd.classList.add('hidden'); }
    };
    window.clearCountryField = function(prefix) {
        const inp = document.getElementById(prefix + '_country_input');
        inp.value = ''; document.getElementById(prefix + '_country_code').value = '';
        document.getElementById(prefix + '_country_clear').classList.add('hidden');
        inp.focus(); window.countryAutocomplete(inp, prefix);
    };
    window.selectCountry = function(prefix, name, code) {
        document.querySelector(`[name="${prefix}_country"]`).value = name;
        document.getElementById(prefix + '_country_code').value = code;
        document.getElementById(prefix + '_country_dropdown').classList.add('hidden');
        const clearBtn = document.getElementById(prefix + '_country_clear');
        if (clearBtn) clearBtn.classList.remove('hidden');
        const phoneCodes = {AE:'+971',BD:'+880',IN:'+91',PK:'+92',US:'+1',GB:'+44',SA:'+966',SG:'+65',MY:'+60',AU:'+61',CA:'+1',DE:'+49',FR:'+33',JP:'+81',CN:'+86',QA:'+974',KW:'+965',OM:'+968',BH:'+973',EG:'+20',TR:'+90',NL:'+31',LK:'+94',NP:'+977',MM:'+95',KH:'+855',VN:'+84',TH:'+66',ID:'+62',PH:'+63',HK:'+852',TW:'+886',KR:'+82',NG:'+234',ZA:'+27',KE:'+254'};
        const codeEl = document.querySelector(`[name="${prefix}_phone_code"]`);
        if (codeEl && !codeEl.value && phoneCodes[code]) codeEl.value = phoneCodes[code];
    };
    document.addEventListener('click', (e) => { if (!e.target.closest('.country-search-wrapper')) document.querySelectorAll('[id$="_country_dropdown"]').forEach(d => d.classList.add('hidden')); });
}
</script>
@endpush
@endonce

{{-- Address --}}
<div class="mb-3">
    <label class="block text-xs font-medium text-slate-400 mb-1.5">Address *</label>
    <input type="text" name="{{ $prefix }}_address" value="{{ old($prefix . '_address', data_get($source, $prefix . '_address')) }}"
        placeholder="Street address"
        class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500 transition-colors">
</div>
<div class="mb-3">
    <input type="text" name="{{ $prefix }}_address2" value="{{ old($prefix . '_address2', data_get($source, $prefix . '_address2')) }}"
        placeholder="Address line 2 (optional)"
        class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500 transition-colors">
</div>
<div class="mb-4">
    <input type="text" name="{{ $prefix }}_address3" value="{{ old($prefix . '_address3', data_get($source, $prefix . '_address3')) }}"
        placeholder="Address line 3 (optional)"
        class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500 transition-colors">
</div>

{{-- Postal / City / State --}}
<div class="grid grid-cols-3 gap-3 mb-4">
    <div>
        <label class="block text-xs font-medium text-slate-400 mb-1.5">Postal Code</label>
        <input type="text" name="{{ $prefix }}_postal_code" value="{{ old($prefix . '_postal_code', data_get($source, $prefix . '_postal_code')) }}"
            placeholder="1000"
            class="w-full bg-gray-800 border border-gray-700 rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-400 mb-1.5">City *</label>
        <input type="text" name="{{ $prefix }}_city" value="{{ old($prefix . '_city', data_get($source, $prefix . '_city')) }}"
            placeholder="City"
            class="w-full bg-gray-800 border border-gray-700 rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-400 mb-1.5">State</label>
        <input type="text" name="{{ $prefix }}_state" value="{{ old($prefix . '_state', data_get($source, $prefix . '_state')) }}"
            placeholder="State"
            class="w-full bg-gray-800 border border-gray-700 rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500">
    </div>
</div>

{{-- Email --}}
<div class="mb-4">
    <label class="block text-xs font-medium text-slate-400 mb-1.5">Email Address</label>
    <input type="email" name="{{ $prefix }}_email" value="{{ old($prefix . '_email', data_get($source, $prefix . '_email')) }}"
        placeholder="email@example.com"
        class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500 transition-colors">
</div>

{{-- Phone --}}
<div class="grid grid-cols-3 gap-3 mb-4">
    <div>
        <label class="block text-xs font-medium text-slate-400 mb-1.5">Phone Type</label>
        <select name="{{ $prefix }}_phone_type"
            class="w-full bg-gray-800 border border-gray-700 rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:border-violet-500">
            <option value="Office" @selected(old($prefix . '_phone_type', data_get($source, $prefix . '_phone_type')) === 'Office')>Office</option>
            <option value="Mobile" @selected(old($prefix . '_phone_type', data_get($source, $prefix . '_phone_type')) === 'Mobile')>Mobile</option>
            <option value="Home" @selected(old($prefix . '_phone_type', data_get($source, $prefix . '_phone_type')) === 'Home')>Home</option>
            <option value="Fax" @selected(old($prefix . '_phone_type', data_get($source, $prefix . '_phone_type')) === 'Fax')>Fax</option>
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-400 mb-1.5">Code</label>
        <input type="text" name="{{ $prefix }}_phone_code" value="{{ old($prefix . '_phone_code', data_get($source, $prefix . '_phone_code')) }}"
            placeholder="+880" maxlength="6"
            class="w-full bg-gray-800 border border-gray-700 rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-400 mb-1.5">Phone Number</label>
        <input type="text" name="{{ $prefix }}_phone" value="{{ old($prefix . '_phone', data_get($source, $prefix . '_phone')) }}"
            placeholder="01XXXXXXXXX"
            class="w-full bg-gray-800 border border-gray-700 rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500">
    </div>
</div>
