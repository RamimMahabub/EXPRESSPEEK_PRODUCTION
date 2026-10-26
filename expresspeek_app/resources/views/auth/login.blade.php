<x-guest-layout>
    <span class="auth-kicker">Welcome back</span>
    <h2 class="auth-heading">Access your command center</h2>
    <p class="auth-subtitle">Sign in securely to manage shipments, check delivery costs, and live delivery updates.</p>

    @if(session('status'))
        <div class="auth-status" role="status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login', request()->only('next')) }}" autocomplete="on">
        @csrf

        <div class="auth-field" style="margin-top:0">
            <div class="auth-label-row"><label class="auth-label" for="identifier">Email address or phone number</label></div>
            <div class="auth-input-wrap">
                <svg class="auth-field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 16.4v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.3 19.3 0 0 1-6-6A19.8 19.8 0 0 1 1.1 3.7 2 2 0 0 1 3.1 1.5h3A2 2 0 0 1 8 3.2c.1 1 .4 2 .7 2.9a2 2 0 0 1-.4 2.1L7 9.5a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.9.6 2.9.7a2 2 0 0 1 1.7 1.9Z"/><path d="M14.5 4.5h6M17.5 1.5v6"/></svg>
                <input class="auth-input" id="identifier" type="text" name="identifier" value="{{ old('identifier') }}" required autofocus autocomplete="username" placeholder="Email or +880 mobile number">
            </div>
            <x-input-error :messages="$errors->get('identifier')" class="mt-2" />
        </div>

        <div class="auth-field" x-data="{ visible: false }">
            <div class="auth-label-row">
                <label class="auth-label" for="password">Password</label>
                <a class="auth-link" href="{{ route('password.request') }}">Forgot password?</a>
            </div>
            <div class="auth-input-wrap">
                <svg class="auth-field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                <input class="auth-input" id="password" :type="visible ? 'text' : 'password'" name="password" required autocomplete="current-password" placeholder="Enter your password">
                <button class="auth-password-toggle" type="button" @click="visible = !visible" :aria-label="visible ? 'Hide password' : 'Show password'" :aria-pressed="visible.toString()">
                    <svg x-show="!visible" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                    <svg x-cloak x-show="visible" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m3 3 18 18M10.6 6.2A11.6 11.6 0 0 1 12 6c6.5 0 10 6 10 6a16.7 16.7 0 0 1-2.1 2.8M6.2 6.2C3.5 8 2 12 2 12s3.5 6 10 6a10.7 10.7 0 0 0 4.2-.8M10.7 10.7a2 2 0 0 0 2.6 2.6"/></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="auth-check-row">
            <label class="auth-check" for="remember_me"><input id="remember_me" type="checkbox" name="remember">Keep me signed in</label>
            <span class="auth-session-note"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3 20 6v6c0 5-3.4 8-8 9-4.6-1-8-4-8-9V6l8-3Z"/><path d="m8 12 2.5 2.5L16 9"/></svg>Protected session</span>
        </div>

        <button class="auth-primary" type="submit">Sign in securely <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button>
    </form>

    <div class="auth-divider">or continue without an account</div>
    <a class="auth-secondary" href="{{ route('shipment.guest.create', ['next' => request('next')]) }}">
        <span class="auth-secondary-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/><path d="m4.5 7.5 7.5 4 7.5-4M12 11.5V21"/></svg></span>
        <span><strong>Create a guest shipment</strong><span>Book a delivery without creating an account</span></span>
    </a>

    <p class="auth-switch">New to Express Peek? <a class="auth-link" href="{{ route('register', request()->only('next')) }}">Create your account</a></p>
</x-guest-layout>
