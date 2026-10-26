@php
    $displayPhone = old('phone', $user->phone);
    if (!old('phone') && $displayPhone && $user->phone_country_code && str_starts_with($displayPhone, $user->phone_country_code)) {
        $displayPhone = substr($displayPhone, strlen($user->phone_country_code));
        if ($user->phone_country_code === '+880') $displayPhone = '0'.$displayPhone;
    }
@endphp

<form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

<form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="profile-form-grid" x-data="{ preview: null }">
    @csrf
    @method('patch')

    <div class="profile-field full">
        <label class="profile-label" for="avatar">Profile photo <span>Optimized automatically</span></label>
        <div class="profile-photo-box">
            <div class="profile-photo-preview">
                <img x-cloak x-show="preview" :src="preview" alt="Selected profile photo preview">
                @if($user->avatarUrl())
                    <img x-show="!preview" src="{{ $user->avatarUrl() }}" alt="Current profile photo">
                @else
                    <span x-show="!preview">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                @endif
            </div>
            <div class="profile-photo-copy">
                <strong>Choose a clear portrait</strong>
                <span>JPG, PNG, or WebP up to 8 MB. We securely crop it to 512×512 and save an optimized WebP copy.</span>
                <label class="profile-upload-button" for="avatar">Choose photo</label>
                <input class="profile-file-input" id="avatar" type="file" name="avatar" accept="image/jpeg,image/png,image/webp" @change="const file=$event.target.files[0]; if(file){ if(preview) URL.revokeObjectURL(preview); preview=URL.createObjectURL(file) }">
            </div>
        </div>
        <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
    </div>

    <div class="profile-field">
        <label class="profile-label" for="name">Full name</label>
        <div class="profile-input-wrap"><svg class="profile-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg><input class="profile-input" id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autocomplete="name"></div>
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div class="profile-field">
        <label class="profile-label" for="email">Email address</label>
        <div class="profile-input-wrap"><svg class="profile-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="m4 7 8 6 8-6"/></svg><input class="profile-input" id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username"></div>
        <x-input-error class="mt-2" :messages="$errors->get('email')" />
    </div>

    <div class="profile-field full">
        <label class="profile-label" for="phone">Mobile number <span>Used for account contact</span></label>
        <div class="profile-phone">
            <select class="profile-code" id="phone_country_code" name="phone_country_code" aria-label="Phone country code">
                @foreach([
                    '+880' => '🇧🇩 +880', '+1' => '🇺🇸/🇨🇦 +1', '+44' => '🇬🇧 +44', '+61' => '🇦🇺 +61',
                    '+49' => '🇩🇪 +49', '+33' => '🇫🇷 +33', '+39' => '🇮🇹 +39', '+34' => '🇪🇸 +34',
                    '+31' => '🇳🇱 +31', '+46' => '🇸🇪 +46', '+47' => '🇳🇴 +47', '+45' => '🇩🇰 +45',
                    '+41' => '🇨🇭 +41', '+971' => '🇦🇪 +971', '+974' => '🇶🇦 +974', '+966' => '🇸🇦 +966',
                    '+65' => '🇸🇬 +65', '+60' => '🇲🇾 +60', '+81' => '🇯🇵 +81', '+82' => '🇰🇷 +82',
                    '+91' => '🇮🇳 +91', '+92' => '🇵🇰 +92', '+94' => '🇱🇰 +94', '+64' => '🇳🇿 +64',
                    '+27' => '🇿🇦 +27', '+55' => '🇧🇷 +55', '+52' => '🇲🇽 +52', '+54' => '🇦🇷 +54',
                    '+56' => '🇨🇱 +56', '+57' => '🇨🇴 +57', '+51' => '🇵🇪 +51', '+58' => '🇻🇪 +58',
                    '+351' => '🇵🇹 +351', '+32' => '🇧🇪 +32', '+43' => '🇦🇹 +43', '+353' => '🇮🇪 +353',
                    '+358' => '🇫🇮 +358', '+48' => '🇵🇱 +48', '+420' => '🇨🇿 +420', '+36' => '🇭🇺 +36',
                    '+40' => '🇷🇴 +40', '+359' => '🇧🇬 +359', '+30' => '🇬🇷 +30', '+90' => '🇹🇷 +90',
                    '+20' => '🇪🇬 +20', '+212' => '🇲🇦 +212', '+234' => '🇳🇬 +234', '+254' => '🇰🇪 +254',
                    '+233' => '🇬🇭 +233', '+255' => '🇹🇿 +255', '+256' => '🇺🇬 +256', '+86' => '🇨🇳 +86',
                    '+852' => '🇭🇰 +852', '+886' => '🇹🇼 +886', '+66' => '🇹🇭 +66', '+84' => '🇻🇳 +84',
                    '+62' => '🇮🇩 +62', '+63' => '🇵🇭 +63', '+95' => '🇲🇲 +95', '+855' => '🇰🇭 +855',
                    '+972' => '🇮🇱 +972', '+962' => '🇯🇴 +962', '+961' => '🇱🇧 +961', '+965' => '🇰🇼 +965',
                    '+968' => '🇴🇲 +968', '+973' => '🇧🇭 +973', '+7' => '🇷🇺 +7'
                ] as $code => $label)
                    <option value="{{ $code }}" @selected(old('phone_country_code', $user->phone_country_code ?: '+880') === $code)>{{ $label }}</option>
                @endforeach
            </select>
            <div class="profile-input-wrap"><svg class="profile-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 16.4v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.3 19.3 0 0 1-6-6A19.8 19.8 0 0 1 1.1 3.7 2 2 0 0 1 3.1 1.5h3A2 2 0 0 1 8 3.2c.1 1 .4 2 .7 2.9a2 2 0 0 1-.4 2.1L7 9.5a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.9.6 2.9.7a2 2 0 0 1 1.7 1.9Z"/></svg><input class="profile-input" id="phone" name="phone" type="tel" value="{{ $displayPhone }}" inputmode="tel" autocomplete="tel-national" placeholder="01712 345678"></div>
        </div>
        <x-input-error class="mt-2" :messages="$errors->get('phone_country_code')" />
        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
    </div>

    <div class="profile-field full flex items-center gap-3 pt-1">
        <button class="profile-save" type="submit"><svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 4 4L19 6"/></svg>Save profile changes</button>
        @if(session('status') === 'profile-updated')<span class="text-xs font-semibold text-emerald-600" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,3000)" x-transition>Saved successfully</span>@endif
    </div>
</form>
