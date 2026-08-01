<x-guest-layout>
    <span class="auth-kicker">Customer onboarding</span>
    <h2 class="auth-heading">Create your logistics workspace</h2>
    <p class="auth-subtitle">One secure account for booking, tracking, quotes, and complete shipment visibility.</p>

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
                <div class="auth-input-wrap auth-code-wrap">
                    <select class="auth-input auth-code-select" id="phone_country_code" name="phone_country_code" required aria-label="Phone country code">
                        @foreach([
                            '+880' => '🇧🇩 +880', '+971' => '🇦🇪 +971', '+1' => '🇺🇸 +1',
                            '+44' => '🇬🇧 +44', '+61' => '🇦🇺 +61', '+91' => '🇮🇳 +91',
                            '+92' => '🇵🇰 +92', '+966' => '🇸🇦 +966', '+974' => '🇶🇦 +974',
                            '+65' => '🇸🇬 +65', '+60' => '🇲🇾 +60'
                        ] as $code => $label)
                            <option value="{{ $code }}" @selected(old('phone_country_code', '+880') === $code)>{{ $label }}</option>
                        @endforeach
                    </select>
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
