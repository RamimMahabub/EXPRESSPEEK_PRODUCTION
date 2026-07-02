<x-guest-layout>
    <div class="mb-8 text-center">
        <p class="text-xs font-bold uppercase tracking-[0.35em]" style="color: #67e8f9;">ExpressPeek Access</p>
        <h2 class="mt-3 text-3xl font-black" style="color: #ffffff;">Log in to continue</h2>
        <p class="mt-3 text-sm leading-6" style="color: #94a3b8;">Use your account, create a new one, or keep going without login.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4 rounded-2xl border px-4 py-3" style="border-color: rgba(16,185,129,0.3); background: rgba(16,185,129,0.1); color: #d1fae5;" :status="session('status')" />

    <form method="POST" action="{{ route('login', request()->only('next')) }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-slate-300" style="color:#cbd5e1;" />
            <x-text-input id="email" class="block mt-1 w-full rounded-xl border-white/10 text-white placeholder-slate-500 focus:ring-2" style="background: rgba(15, 23, 42, 0.75); border-color: rgba(255,255,255,0.10); color: #ffffff;" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" style="color:#fda4af;" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" class="text-slate-300" style="color:#cbd5e1;" />

            <x-text-input id="password" class="block mt-1 w-full rounded-xl border-white/10 text-white placeholder-slate-500 focus:ring-2"
                            style="background: rgba(15, 23, 42, 0.75); border-color: rgba(255,255,255,0.10); color: #ffffff;"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" style="color:#fda4af;" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center gap-2">
                <input id="remember_me" type="checkbox" class="rounded border-white/20 bg-slate-950 text-cyan-500 shadow-sm" name="remember">
                <span class="text-sm" style="color:#94a3b8;">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between gap-3 mt-4">
            @if (Route::has('password.request'))
                <a class="text-sm transition-colors" style="color:#94a3b8;" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-auto rounded-xl px-5 py-3 text-sm font-bold" style="background:#ffffff; color:#0f172a;">
                {{ __('Log in') }}
            </x-primary-button>
        </div>

        <div class="mt-6 grid gap-3 sm:grid-cols-2">
            <a href="{{ route('register', request()->only('next')) }}" class="inline-flex items-center justify-center rounded-2xl border px-4 py-3 text-sm font-semibold transition-colors" style="border-color: rgba(255,255,255,0.10); background: rgba(255,255,255,0.04); color: #c0f2ff;">
                Create an account
            </a>
            <a href="{{ route('shipment.guest.create', ['next' => request('next')]) }}" class="inline-flex items-center justify-center rounded-2xl border px-4 py-3 text-sm font-semibold transition-colors" style="border-color: rgba(255,255,255,0.10); background: rgba(255,255,255,0.04); color: #e2e8f0;">
                Continue without login
            </a>
        </div>
    </form>
</x-guest-layout>
