<x-guest-layout>
    <span class="auth-kicker">Secure recovery</span>
    <h2 class="auth-heading">Choose a new password</h2>
    <p class="auth-subtitle">Create a strong password you haven’t used for this account before.</p>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="auth-field" style="margin-top:0">
            <div class="auth-label-row"><label class="auth-label" for="email">Account email</label></div>
            <div class="auth-input-wrap">
                <svg class="auth-field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="m4 7 8 6 8-6"/></svg>
                <input class="auth-input" id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" inputmode="email">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="auth-field" x-data="{ visible: false }">
            <div class="auth-label-row"><label class="auth-label" for="password">New password</label></div>
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
            <div class="auth-label-row"><label class="auth-label" for="password_confirmation">Confirm new password</label></div>
            <div class="auth-input-wrap">
                <svg class="auth-field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3 20 6v6c0 5-3.4 8-8 9-4.6-1-8-4-8-9V6l8-3Z"/><path d="m8 12 2.5 2.5L16 9"/></svg>
                <input class="auth-input" id="password_confirmation" :type="visible ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat your new password">
                <button class="auth-password-toggle" type="button" @click="visible = !visible" :aria-label="visible ? 'Hide password confirmation' : 'Show password confirmation'">
                    <svg x-show="!visible" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                    <svg x-cloak x-show="visible" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m3 3 18 18M10.6 6.2A11.6 11.6 0 0 1 12 6c6.5 0 10 6 10 6M2 12s3.5 6 10 6a11 11 0 0 0 4.2-.8"/></svg>
                </button>
            </div>
        </div>

        <button class="auth-primary" type="submit">Update password securely <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button>
    </form>

    <div class="auth-security-note"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3 20 6v6c0 5-3.4 8-8 9-4.6-1-8-4-8-9V6l8-3Z"/></svg><span>After the password changes, the reset token becomes invalid and your account receives a new remember token.</span></div>
</x-guest-layout>
