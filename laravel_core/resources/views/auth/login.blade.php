<x-auth-layout>
    <div class="text-center mb-10">
        <!-- Mobile Logo (hidden on desktop) -->
        <a href="/" class="inline-block lg:hidden mb-6">
            <x-application-logo class="w-16 h-16 text-brand-primary drop-shadow-md mx-auto" />
        </a>
        <h2 class="text-3xl font-bold text-gray-900 font-heading">Welcome Back</h2>
        <p class="text-gray-500 mt-2 text-sm">Sign in to continue your journey</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 shadow-sm animate-fade-in">
                <div class="flex items-center mb-2">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                    <span class="font-bold">Login failed.</span>
                </div>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 shadow-sm animate-fade-in">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                    <span class="font-bold">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Email or Phone -->
        <div class="mb-5">
            <label for="login" class="block font-medium text-sm text-gray-700 mb-1">{{ __('Email or Phone') }}</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-user text-gray-400"></i>
                </div>
                <input id="login" class="block w-full pl-10 pr-3 py-3 border-gray-300 rounded-xl shadow-sm focus:border-brand-primary focus:ring-brand-primary transition duration-200 bg-white" type="text" name="login" :value="old('login')" required autofocus autocomplete="username" placeholder="john@example.com" />
            </div>
        </div>

        <!-- Password -->
        <div class="mb-5">
            <label for="password" class="block font-medium text-sm text-gray-700 mb-1">{{ __('Password') }}</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400"></i>
                </div>
                <input id="password" class="block w-full pl-10 pr-3 py-3 border-gray-300 rounded-xl shadow-sm focus:border-brand-primary focus:ring-brand-primary transition duration-200 bg-white" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            </div>
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between mb-8">
            <label for="remember_me" class="inline-flex items-center group cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-brand-primary shadow-sm focus:ring-brand-primary cursor-pointer w-4 h-4" name="remember">
                <span class="ms-2 text-sm text-gray-600 group-hover:text-gray-900 transition duration-150">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-semibold text-brand-primary hover:text-brand-secondary transition duration-150 ease-in-out" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <div>
            <button type="submit" class="w-full flex justify-center items-center px-4 py-3.5 bg-brand-primary border border-transparent rounded-xl font-bold text-sm text-white uppercase tracking-wider hover:bg-brand-secondary focus:bg-brand-secondary active:bg-brand-primary focus:outline-none focus:ring-2 focus:ring-brand-primary focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg shadow-brand-primary/30 transform hover:-translate-y-0.5">
                {{ __('Log in') }} <i class="fas fa-arrow-right ml-2"></i>
            </button>
        </div>

        <div class="mt-6">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">Or continue with</span>
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('google.redirect', ['role' => 'customer']) }}" class="w-full flex justify-center items-center px-4 py-3 border border-gray-300 rounded-xl shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-primary transition duration-150">
                    <img class="h-5 w-5 mr-2" src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google logo">
                    Sign in with Google
                </a>
            </div>
        </div>
        
        <div class="mt-8 text-center text-sm">
            <span class="text-gray-500">Don't have an account?</span>
            <a class="font-bold text-brand-primary hover:text-brand-secondary transition duration-150 ease-in-out ml-1" href="{{ route('register') }}">
                {{ __('Sign up') }}
            </a>
        </div>

        <div class="mt-10 pt-6 border-t border-gray-200 text-center text-xs">
            <a class="text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out flex items-center justify-center gap-1" href="{{ route('admin.login') }}">
                <i class="fas fa-shield-halved"></i> Staff Login
            </a>
        </div>
    </form>
</x-auth-layout>
