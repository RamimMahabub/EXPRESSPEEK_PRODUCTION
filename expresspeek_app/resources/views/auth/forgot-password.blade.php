<x-guest-layout>
    <span class="auth-kicker">Account recovery</span>
    <h2 class="auth-heading">Reset your password</h2>
    <p class="auth-subtitle">Enter the email connected to your account. If it matches an account, we’ll send a secure reset link.</p>

    @if(session('status'))
        <div class="auth-status" role="status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="auth-field" style="margin-top:0">
            <div class="auth-label-row"><label class="auth-label" for="email">Account email</label></div>
            <div class="auth-input-wrap">
                <svg class="auth-field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="m4 7 8 6 8-6"/></svg>
                <input class="auth-input" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" inputmode="email" placeholder="you@example.com">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <button class="auth-primary" type="submit">Send secure reset link <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button>
    </form>

    <div class="auth-security-note">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3 20 6v6c0 5-3.4 8-8 9-4.6-1-8-4-8-9V6l8-3Z"/><path d="m8 12 2.5 2.5L16 9"/></svg>
        <span>For privacy, this page does not reveal whether an email is registered. Reset links expire after 60 minutes and can only be used once.</span>
    </div>

    <p class="auth-switch">Remembered your password? <a class="auth-link" href="{{ route('login') }}">Return to sign in</a></p>
</x-guest-layout>
