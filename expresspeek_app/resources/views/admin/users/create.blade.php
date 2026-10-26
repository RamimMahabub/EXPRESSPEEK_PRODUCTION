@extends('layouts.operations-dashboard')

@section('title', 'Add User')
@section('page-title', 'Add New User')
@section('page-subtitle', 'Create a new platform user')

@section('content')

<div class="max-w-2xl">
    <div class="neon-card rounded-2xl p-6">
        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition-colors @error('name') border-red-500 @enderror">
                @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_business" value="1" id="is_business_check" @checked(old('is_business'))
                        class="w-4 h-4 rounded border-gray-700 text-violet-600 bg-gray-900 focus:ring-violet-500 focus:ring-offset-gray-800"
                        onchange="document.getElementById('business_section').classList.toggle('hidden', !this.checked)">
                    <span class="text-sm font-medium text-slate-300">Business Contact</span>
                </label>
            </div>

            <div id="business_section" class="{{ old('is_business') ? '' : 'hidden' }}">
                <label class="block text-sm font-medium text-slate-300 mb-2">Company Name</label>
                <input type="text" name="company_name" value="{{ old('company_name') }}" placeholder="Company name"
                    class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition-colors @error('company_name') border-red-500 @enderror">
                @error('company_name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition-colors @error('email') border-red-500 @enderror">
                @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Mobile Number</label>
                <div class="grid grid-cols-[140px_1fr] gap-3">
                    <select name="phone_country_code" required class="w-full bg-gray-800 border border-gray-700 rounded-xl px-3 py-2.5 text-white text-sm focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500">
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
                            <option value="{{ $code }}" @selected(old('phone_country_code', '+880') === $code)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required inputmode="tel" placeholder="01712 345678" class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500 @error('phone') border-red-500 @enderror">
                </div>
                @error('phone_country_code') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                @error('phone') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Password</label>
                <input type="password" name="password" required
                    class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition-colors @error('password') border-red-500 @enderror">
                @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Confirm Password</label>
                <input type="password" name="password_confirmation" required
                    class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition-colors">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Role</label>
                <select name="role" required
                    class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition-colors @error('role') border-red-500 @enderror">
                    <option value="">Select a role...</option>
                    <option value="admin"    {{ old('role') === 'admin'    ? 'selected' : '' }}>Admin</option>
                    <option value="customer" {{ old('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                    <option value="agent"    {{ old('role') === 'agent'    ? 'selected' : '' }}>Agent</option>
                </select>
                @error('role') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                    class="px-6 py-2.5 rounded-xl neon-button text-white text-sm font-medium transition-colors">
                    Create User
                </button>
                <a href="{{ route('admin.users.index') }}"
                   class="px-6 py-2.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-slate-300 text-sm font-medium transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
