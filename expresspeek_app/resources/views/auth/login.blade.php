<x-guest-layout>
    {{-- Header --}}
    <div style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.625rem; font-weight: 800; color: #0f172a; letter-spacing: -0.025em; margin: 0;">
            Welcome back
        </h2>
        <p style="margin-top: 0.5rem; font-size: 0.9375rem; color: #64748b; font-weight: 400; margin-bottom: 0;">
            Sign in to your account to continue.
        </p>
    </div>

    {{-- Session Status --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login', request()->only('next')) }}" autocomplete="on">
        @csrf

        {{-- Email --}}
        <div style="margin-bottom: 1.25rem;">
            <label for="email" style="display: block; font-size: 0.8125rem; font-weight: 600; color: #334155; margin-bottom: 0.5rem;">
                Email address
            </label>
            <div style="position: relative;">
                <div style="position: absolute; inset: 0; right: auto; width: 2.75rem; display: flex; align-items: center; justify-content: center; pointer-events: none;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="4" width="20" height="16" rx="3"/>
                        <path d="M22 7l-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                    </svg>
                </div>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="you@example.com"
                    style="display: block; width: 100%; padding: 0.75rem 0.875rem 0.75rem 2.75rem; font-size: 0.9375rem; font-weight: 500; color: #0f172a; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 0.75rem; outline: none; transition: all 0.2s ease; font-family: inherit;"
                    onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.1)'; this.style.background='#ffffff';"
                    onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'; this.style.background='#f8fafc';"
                />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        {{-- Password --}}
        <div style="margin-bottom: 1.25rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                <label for="password" style="font-size: 0.8125rem; font-weight: 600; color: #334155;">
                    Password
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" style="font-size: 0.8125rem; font-weight: 600; color: #6366f1; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#4f46e5'" onmouseout="this.style.color='#6366f1'">
                        Forgot password?
                    </a>
                @endif
            </div>
            <div style="position: relative;">
                <div style="position: absolute; inset: 0; right: auto; width: 2.75rem; display: flex; align-items: center; justify-content: center; pointer-events: none;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="Enter your password"
                    style="display: block; width: 100%; padding: 0.75rem 0.875rem 0.75rem 2.75rem; font-size: 0.9375rem; font-weight: 500; color: #0f172a; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 0.75rem; outline: none; transition: all 0.2s ease; font-family: inherit;"
                    onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.1)'; this.style.background='#ffffff';"
                    onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'; this.style.background='#f8fafc';"
                />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        {{-- Remember Me --}}
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem;">
            <input
                id="remember_me"
                type="checkbox"
                name="remember"
                style="width: 1rem; height: 1rem; border-radius: 0.25rem; border: 1.5px solid #cbd5e1; accent-color: #6366f1; cursor: pointer;"
            />
            <label for="remember_me" style="font-size: 0.8125rem; font-weight: 500; color: #64748b; cursor: pointer; user-select: none;">
                Remember me
            </label>
        </div>

        {{-- Sign In Button --}}
        <button
            type="submit"
            style="width: 100%; padding: 0.8125rem 1.5rem; font-size: 0.9375rem; font-weight: 700; color: #ffffff; background: linear-gradient(135deg, #6366f1 0%, #7c3aed 100%); border: none; border-radius: 0.75rem; cursor: pointer; transition: all 0.25s ease; font-family: inherit; letter-spacing: 0.01em; box-shadow: 0 1px 3px rgba(99, 102, 241, 0.3), 0 8px 24px rgba(99, 102, 241, 0.15);"
            onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(99,102,241,0.35), 0 12px 32px rgba(99,102,241,0.2)'"
            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 1px 3px rgba(99,102,241,0.3), 0 8px 24px rgba(99,102,241,0.15)'"
        >
            Sign in
        </button>
    </form>

    {{-- Divider --}}
    <div style="display: flex; align-items: center; gap: 1rem; margin: 1.75rem 0;">
        <div style="flex: 1; height: 1px; background: #e2e8f0;"></div>
        <span style="font-size: 0.75rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.08em;">or</span>
        <div style="flex: 1; height: 1px; background: #e2e8f0;"></div>
    </div>

    {{-- Guest Shipment CTA --}}
    <a
        href="{{ route('shipment.guest.create', ['next' => request('next')]) }}"
        style="display: flex; align-items: center; gap: 0.875rem; padding: 1rem 1.125rem; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 0.75rem; text-decoration: none; transition: all 0.25s ease; cursor: pointer;"
        onmouseover="this.style.borderColor='#c7d2fe'; this.style.background='#eef2ff'; this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 16px rgba(99,102,241,0.08)'"
        onmouseout="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc'; this.style.transform='translateY(0)'; this.style.boxShadow='none'"
    >
        <div style="flex-shrink: 0; width: 2.5rem; height: 2.5rem; border-radius: 0.625rem; background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%); display: flex; align-items: center; justify-content: center;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                <line x1="12" y1="22.08" x2="12" y2="12"/>
            </svg>
        </div>
        <div style="flex: 1; min-width: 0;">
            <div style="font-size: 0.875rem; font-weight: 700; color: #1e293b;">Create shipment instantly</div>
            <div style="font-size: 0.75rem; font-weight: 400; color: #64748b; margin-top: 0.125rem;">No account needed — get started in seconds</div>
        </div>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"/>
        </svg>
    </a>

    {{-- Sign Up Link --}}
    <p style="margin-top: 1.5rem; text-align: center; font-size: 0.875rem; color: #64748b; font-weight: 400;">
        Don't have an account?
        <a href="{{ route('register', request()->only('next')) }}" style="font-weight: 700; color: #6366f1; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#4f46e5'" onmouseout="this.style.color='#6366f1'">
            Sign up for free
        </a>
    </p>
</x-guest-layout>
