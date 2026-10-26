<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ExpressPeek for Business | Premium Corporate Courier Services</title>
    @include('partials.favicon')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'Inter', sans-serif; }
        .business-hero {
            background: linear-gradient(135deg, #064e3b 0%, #047857 50%, #059669 100%);
            position: relative;
            overflow: hidden;
        }
        .business-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
            border-radius: 50%;
            pointer-events: none;
        }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-slide-up { animation: slideUp 0.6s ease forwards; opacity: 0; }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        
        .attraction-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .attraction-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px -10px rgba(4, 120, 87, 0.15);
            border-color: #10b981;
        }
    </style>
</head>
<body class="bg-gray-50 text-slate-900 antialiased min-h-screen flex flex-col">

@include('partials.header')

{{-- Hero Section --}}
<section class="business-hero py-24 px-6 text-center">
    <div class="max-w-4xl mx-auto relative z-10">
        <span class="inline-block py-1.5 px-4 rounded-full bg-emerald-900/40 text-emerald-100 text-sm font-bold uppercase tracking-widest mb-6 border border-emerald-400/20 animate-slide-up">Corporate Solutions</span>
        <h1 class="text-4xl md:text-6xl font-black text-white leading-tight mb-6 animate-slide-up delay-100">
            Scale Your Deliveries with <br/>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-300 to-green-100">ExpressPeek for Business</span>
        </h1>
        <p class="text-emerald-50 md:text-lg max-w-2xl mx-auto mb-10 animate-slide-up delay-200 opacity-90 leading-relaxed">
            Exclusive rates, priority support, and seamless logistics designed for high-volume shippers. Join the network of top businesses trusting ExpressPeek.
        </p>
    </div>
</section>

{{-- Main Content --}}
<main class="flex-1 -mt-10 relative z-20 pb-24">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-12 gap-10">
        
        {{-- Left Column: Attractions --}}
        <div class="lg:col-span-5 space-y-6">
            
            <div class="bg-white rounded-3xl p-8 shadow-xl shadow-emerald-900/5 border border-gray-100">
                <h2 class="text-2xl font-black text-slate-900 mb-6 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    Premium Benefits
                </h2>
                
                <div class="space-y-4">
                    {{-- Attraction 1 --}}
                    <div class="attraction-card bg-gray-50 border border-gray-100 rounded-2xl p-5 flex gap-4 items-start">
                        <div class="bg-emerald-500 text-white p-2.5 rounded-xl shadow-md">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 mb-1">Discounted Corporate Rates</h3>
                            <p class="text-sm text-slate-500 leading-relaxed">Save more on every shipment with our specialized volume-based pricing structure.</p>
                        </div>
                    </div>

                    {{-- Attraction 2 --}}
                    <div class="attraction-card bg-gray-50 border border-gray-100 rounded-2xl p-5 flex gap-4 items-start">
                        <div class="bg-blue-500 text-white p-2.5 rounded-xl shadow-md">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 mb-1">Dedicated Account Manager</h3>
                            <p class="text-sm text-slate-500 leading-relaxed">Get personalized support and priority assistance for all your logistics needs.</p>
                        </div>
                    </div>

                    {{-- Attraction 3 --}}
                    <div class="attraction-card bg-gray-50 border border-gray-100 rounded-2xl p-5 flex gap-4 items-start">
                        <div class="bg-violet-500 text-white p-2.5 rounded-xl shadow-md">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 mb-1">Advanced Analytics & APIs</h3>
                            <p class="text-sm text-slate-500 leading-relaxed">Integrate smoothly and track shipments in bulk with robust reporting tools.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Contact Box --}}
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <h3 class="text-lg font-bold mb-2">Need immediate assistance?</h3>
                <p class="text-slate-300 text-sm mb-4">Our corporate team is ready to discuss custom solutions.</p>
                <a href="{{ route('customer-service') }}" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 px-5 py-2.5 rounded-xl text-sm font-semibold transition-colors border border-white/10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    Contact Sales
                </a>
            </div>

        </div>

        {{-- Right Column: Application Form --}}
        <div class="lg:col-span-7">
            <div class="bg-white rounded-3xl p-8 md:p-10 shadow-xl shadow-slate-200/50 border border-gray-100">
                
                @if(session('success'))
                    <div class="mb-8 bg-emerald-50 border border-emerald-200 rounded-2xl p-5 flex items-start gap-4">
                        <div class="bg-emerald-500 rounded-full p-1 text-white flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-emerald-800 text-base">Application Received!</h3>
                            <p class="text-emerald-700 text-sm mt-1">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                <div class="mb-8">
                    <h2 class="text-2xl font-black text-slate-900 mb-2">Apply for a Business Account</h2>
                    <p class="text-slate-500 text-sm">Fill out the form below and our partnership team will reach out to you within 24 hours.</p>
                </div>

                <form action="{{ route('business.store') }}" method="POST" class="space-y-8">
                    @csrf
                    
                    {{-- Business Info Section --}}
                    <div>
                        <div class="flex items-center gap-2 mb-5 pb-2 border-b border-gray-100">
                            <span class="w-6 h-6 rounded bg-slate-900 text-white flex items-center justify-center text-xs font-bold">1</span>
                            <h3 class="font-bold text-slate-800">BUSINESS INFO</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-2">Name (Proprietor) <span class="text-red-500">*</span></label>
                                <input type="text" name="proprietor_name" required value="{{ old('proprietor_name') }}"
                                    class="w-full bg-gray-50 border border-gray-200 text-slate-900 text-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500 block p-3.5 transition-colors">
                                @error('proprietor_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-2">NID No (Proprietor) <span class="text-red-500">*</span></label>
                                <input type="text" name="nid_no" required value="{{ old('nid_no') }}"
                                    class="w-full bg-gray-50 border border-gray-200 text-slate-900 text-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500 block p-3.5 transition-colors">
                                @error('nid_no') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-2">Name (Company) <span class="text-red-500">*</span></label>
                                <input type="text" name="company_name" required value="{{ old('company_name') }}"
                                    class="w-full bg-gray-50 border border-gray-200 text-slate-900 text-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500 block p-3.5 transition-colors">
                                @error('company_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-2">Trade License (If Existed)</label>
                                <input type="text" name="trade_license" value="{{ old('trade_license') }}"
                                    class="w-full bg-gray-50 border border-gray-200 text-slate-900 text-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500 block p-3.5 transition-colors placeholder-gray-400" placeholder="Optional">
                                @error('trade_license') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Contact Info Section --}}
                    <div>
                        <div class="flex items-center gap-2 mb-5 pb-2 border-b border-gray-100">
                            <span class="w-6 h-6 rounded bg-slate-900 text-white flex items-center justify-center text-xs font-bold">2</span>
                            <h3 class="font-bold text-slate-800">CONTACT INFO</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-2">Address <span class="text-red-500">*</span></label>
                                <textarea name="address" rows="3" required
                                    class="w-full bg-gray-50 border border-gray-200 text-slate-900 text-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500 block p-3.5 transition-colors">{{ old('address') }}</textarea>
                                @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-2">Mobile No (Alternative One) <span class="text-red-500">*</span></label>
                                <input type="text" name="mobile_no" required value="{{ old('mobile_no') }}"
                                    class="w-full bg-gray-50 border border-gray-200 text-slate-900 text-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500 block p-3.5 transition-colors">
                                @error('mobile_no') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-2">Email <span class="text-red-500">*</span></label>
                                <input type="email" name="email" required value="{{ old('email') }}"
                                    class="w-full bg-gray-50 border border-gray-200 text-slate-900 text-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500 block p-3.5 transition-colors">
                                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white font-bold py-4 px-8 rounded-xl shadow-lg shadow-emerald-600/20 transition-all transform hover:-translate-y-1 focus:ring-4 focus:ring-emerald-500/50">
                            Submit Business Application
                        </button>
                        <p class="text-center text-xs text-slate-400 mt-4">By submitting this form, you agree to our <a href="{{ route('terms') }}" class="underline hover:text-emerald-600">Terms of Service</a>.</p>
                    </div>

                </form>
            </div>
        </div>
        
    </div>
</main>

@include('partials.footer')
</body>
</html>
