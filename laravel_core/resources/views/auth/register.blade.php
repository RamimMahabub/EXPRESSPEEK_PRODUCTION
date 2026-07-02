<x-auth-layout>
    <div x-data="{ role: 'customer' }">
        <div class="text-center mb-8">
            <!-- Mobile Logo (hidden on desktop) -->
            <a href="/" class="inline-block lg:hidden mb-4">
                <x-application-logo class="w-16 h-16 text-brand-primary drop-shadow-md mx-auto" />
            </a>
            <h2 class="text-3xl font-bold text-gray-900 font-heading">Create an Account</h2>
            <p class="text-gray-500 mt-2 text-sm">Join us and start your journey today</p>
        </div>

        <!-- Role Selection Visual Cards -->
        <div class="grid grid-cols-2 gap-4 mb-8">
            <!-- Customer Card -->
            <button 
                type="button"
                @click="role = 'customer'"
                :class="role === 'customer' ? 'border-brand-primary ring-2 ring-brand-primary/20 bg-brand-primary/5' : 'border-gray-200 hover:border-gray-300 bg-white'"
                class="relative flex flex-col items-center justify-center p-4 border rounded-2xl transition-all duration-200 text-center group"
            >
                <div :class="role === 'customer' ? 'text-brand-primary bg-white' : 'text-gray-400 bg-gray-50 group-hover:text-gray-600'" class="w-12 h-12 rounded-full flex items-center justify-center mb-3 shadow-sm transition-colors">
                    <i class="fas fa-suitcase-rolling text-xl"></i>
                </div>
                <span :class="role === 'customer' ? 'text-brand-primary font-bold' : 'text-gray-600 font-medium'" class="text-sm">Traveler</span>
                <div x-show="role === 'customer'" x-transition class="absolute top-2 right-2 text-brand-primary">
                    <i class="fas fa-check-circle"></i>
                </div>
            </button>

            <!-- Property Owner Card -->
            <button 
                type="button"
                @click="role = 'property_owner'"
                :class="role === 'property_owner' ? 'border-brand-primary ring-2 ring-brand-primary/20 bg-brand-primary/5' : 'border-gray-200 hover:border-gray-300 bg-white'"
                class="relative flex flex-col items-center justify-center p-4 border rounded-2xl transition-all duration-200 text-center group"
            >
                <div :class="role === 'property_owner' ? 'text-brand-primary bg-white' : 'text-gray-400 bg-gray-50 group-hover:text-gray-600'" class="w-12 h-12 rounded-full flex items-center justify-center mb-3 shadow-sm transition-colors">
                    <i class="fas fa-building text-xl"></i>
                </div>
                <span :class="role === 'property_owner' ? 'text-brand-primary font-bold' : 'text-gray-600 font-medium'" class="text-sm">Property Owner</span>
                <div x-show="role === 'property_owner'" x-transition class="absolute top-2 right-2 text-brand-primary">
                    <i class="fas fa-check-circle"></i>
                </div>
            </button>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf
            
            <input type="hidden" name="role" x-model="role">

            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 shadow-sm animate-fade-in">
                    <div class="flex items-center mb-2">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                        <span class="font-bold">Whoops! Something went wrong.</span>
                    </div>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Name -->
            <div class="mb-4">
                <label for="name" class="block font-medium text-sm text-gray-700 mb-1">{{ __('Full Name') }}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-id-card text-gray-400"></i>
                    </div>
                    <input id="name" class="block w-full pl-10 pr-3 py-2.5 border-gray-300 rounded-xl shadow-sm focus:border-brand-primary focus:ring-brand-primary transition duration-200 bg-white" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="John Doe" />
                </div>
            </div>

            <!-- Email Address -->
            <div class="mb-4">
                <label for="email" class="block font-medium text-sm text-gray-700 mb-1">{{ __('Email Address') }}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-envelope text-gray-400"></i>
                    </div>
                    <input id="email" class="block w-full pl-10 pr-3 py-2.5 border-gray-300 rounded-xl shadow-sm focus:border-brand-primary focus:ring-brand-primary transition duration-200 bg-white" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="john@example.com" />
                </div>
            </div>

            <!-- Phone Number -->
            <div class="mb-4">
                <label for="phone" class="block font-medium text-sm text-gray-700 mb-1">{{ __('Phone Number') }}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-phone text-gray-400"></i>
                    </div>
                    <input id="phone" class="block w-full pl-10 pr-3 py-2.5 border-gray-300 rounded-xl shadow-sm focus:border-brand-primary focus:ring-brand-primary transition duration-200 bg-white" type="text" name="phone" :value="old('phone')" required autocomplete="tel" placeholder="+1234567890" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                <!-- Password -->
                <div>
                    <label for="password" class="block font-medium text-sm text-gray-700 mb-1">{{ __('Password') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input id="password" class="block w-full pl-10 pr-3 py-2.5 border-gray-300 rounded-xl shadow-sm focus:border-brand-primary focus:ring-brand-primary transition duration-200 bg-white" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block font-medium text-sm text-gray-700 mb-1">{{ __('Confirm') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input id="password_confirmation" class="block w-full pl-10 pr-3 py-2.5 border-gray-300 rounded-xl shadow-sm focus:border-brand-primary focus:ring-brand-primary transition duration-200 bg-white" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                    </div>
                </div>
            </div>

            <div>
                <button type="submit" class="w-full flex justify-center items-center px-4 py-3.5 bg-brand-primary border border-transparent rounded-xl font-bold text-sm text-white uppercase tracking-wider hover:bg-brand-secondary focus:bg-brand-secondary active:bg-brand-primary focus:outline-none focus:ring-2 focus:ring-brand-primary focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg shadow-brand-primary/30 transform hover:-translate-y-0.5">
                    {{ __('Create Account') }} <i class="fas fa-user-plus ml-2"></i>
                </button>
            </div>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Or sign up with</span>
                    </div>
                </div>

                <div class="mt-6">
                    <a :href="'/auth/google/' + role + '/redirect'" class="w-full flex justify-center items-center px-4 py-3 border border-gray-300 rounded-xl shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-primary transition duration-150">
                        <img class="h-5 w-5 mr-2" src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google logo">
                        Sign up with Google
                    </a>
                </div>
            </div>

            <div class="mt-6 text-center text-sm">
                <span class="text-gray-500">Already have an account?</span>
                <a class="font-bold text-brand-primary hover:text-brand-secondary transition duration-150 ease-in-out ml-1" href="{{ route('login') }}">
                    {{ __('Log in') }}
                </a>
            </div>
            
            <div class="mt-8 pt-6 border-t border-gray-200 text-center text-xs">
                <a class="text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out flex items-center justify-center gap-1" href="{{ route('admin.login') }}">
                    <i class="fas fa-shield-halved"></i> Staff Login
                </a>
            </div>
        </form>
    </div>
    <!-- Include Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</x-auth-layout>
