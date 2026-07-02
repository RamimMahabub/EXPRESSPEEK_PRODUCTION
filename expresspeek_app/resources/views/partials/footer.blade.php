<footer class="bg-gray-900 text-slate-400 border-t border-gray-800">
    <div class="max-w-7xl mx-auto px-6 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-10 mb-10">
            {{-- Brand --}}
            <div>
                <div class="flex items-center gap-2.5 mb-6">
                    <div class="bg-white rounded-2xl p-4 shadow-lg inline-block">
                        <img src="/images/express-peek-logo.webp" alt="Express Peek" class="w-48 h-auto object-contain">
                    </div>
                </div>
                <p class="text-sm leading-relaxed text-slate-400 max-w-sm">
                    Your intelligent logistics partner. Fast, reliable, and trackable deliveries worldwide. We bridge the gap between you and your destinations.
                </p>
            </div>
            
            {{-- Quick Links & Support (Middle) --}}
            <div class="grid grid-cols-2 gap-4 pt-2">
                <div>
                    <h4 class="text-white text-base font-bold mb-6">Quick Links</h4>
                    <ul class="space-y-3.5 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-violet-400 transition-colors flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-gray-700"></span>Home</a></li>
                        @auth
                            @if(auth()->user()->isCustomer())
                                <li><a href="{{ route('customer.shipments.create') }}" class="hover:text-violet-400 transition-colors flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-gray-700"></span>Ship Now</a></li>
                                <li><a href="{{ route('customer.shipments.index') }}" class="hover:text-violet-400 transition-colors flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-gray-700"></span>My Shipments</a></li>
                            @elseif(auth()->user()->isAgent())
                                <li><a href="{{ route('agent.shipments.create') }}" class="hover:text-violet-400 transition-colors flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-gray-700"></span>Ship Now</a></li>
                                <li><a href="{{ route('agent.shipments.index') }}" class="hover:text-violet-400 transition-colors flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-gray-700"></span>My Shipments</a></li>
                            @elseif(auth()->user()->isAdmin())
                                <li><a href="{{ route('admin.shipments.create') }}" class="hover:text-violet-400 transition-colors flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-gray-700"></span>Ship Now</a></li>
                            @endif
                        @else
                            <li><a href="{{ route('login') }}" class="hover:text-violet-400 transition-colors flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-gray-700"></span>Ship Now</a></li>
                        @endauth
                        <li><a href="{{ route('quote') }}" class="hover:text-violet-400 transition-colors flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-gray-700"></span>Get a Quote</a></li>
                        <li><a href="{{ route('track') }}" class="hover:text-violet-400 transition-colors flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-gray-700"></span>Track a Shipment</a></li>
                        <li><a href="{{ route('sourcing.create') }}" class="text-amber-500 hover:text-amber-400 transition-colors font-semibold flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-amber-500/50"></span>Shop from Bangladesh</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white text-base font-bold mb-6">Support</h4>
                    <ul class="space-y-3.5 text-sm">
                        <li><a href="#" class="hover:text-white transition-colors flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-gray-700"></span>Customer Service</a></li>
                        <li><a href="#" class="hover:text-white transition-colors flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-gray-700"></span>Help Center</a></li>
                        <li><a href="#" class="hover:text-white transition-colors flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-gray-700"></span>Schedule Pickup</a></li>
                        <li><a href="#" class="hover:text-white transition-colors flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-gray-700"></span>Terms of Service</a></li>
                        <li><a href="#" class="hover:text-white transition-colors flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-gray-700"></span>Privacy Policy</a></li>
                    </ul>
                </div>
            </div>

            {{-- Contact Info (Top Right) --}}
            <div class="pt-2">
                <h4 class="text-white text-base font-bold mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Contact Us
                </h4>
                <ul class="space-y-6 text-sm text-slate-400">
                    <li class="flex items-start gap-4 group">
                        <div class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center shrink-0 group-hover:bg-violet-900/50 group-hover:text-violet-400 transition-colors border border-gray-700">
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-violet-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div class="pt-1">
                            <strong class="block text-slate-200 font-bold mb-1">EXPRESS PEEK</strong>
                            <span class="leading-relaxed block mb-1">62, 63 SHENA KOLLAYN BUSINESS CENTRE,<br>Motijheel Rd, Dhaka</span>
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-gray-800 text-gray-400 uppercase tracking-wider border border-gray-700">Located in: Amin Court</span>
                        </div>
                    </li>
                    <li class="flex items-center gap-4 group">
                        <div class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center shrink-0 group-hover:bg-blue-900/50 group-hover:text-blue-400 transition-colors border border-gray-700">
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <a href="tel:01400659902" class="hover:text-white transition-colors font-medium text-base">01400-659902</a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Map Frame (Full Width at the end) --}}
        <div class="mb-12">
            <h4 class="text-white text-base font-bold mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Find Us
            </h4>
            <div class="bg-gray-800/50 p-1.5 rounded-2xl border border-gray-700/50 shadow-2xl relative group overflow-hidden h-[300px] w-full">
                <div class="absolute inset-0 bg-gradient-to-tr from-violet-600/20 to-blue-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3652.5543161683204!2d90.41777451151938!3d23.727604678597146!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755b900404a03eb%3A0xfdba21f7353b5cb3!2sEXPRESS%20PEEK!5e0!3m2!1sen!2sbd!4v1782905765854!5m2!1sen!2sbd" width="100%" height="100%" style="border:0; border-radius: 0.85rem;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
            </div>
        </div>
        
        <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="text-sm font-medium">© {{ date('Y') }} ExpressPeek Logistics. All rights reserved.</p>
            <div class="flex items-center gap-5">
                <a href="https://www.facebook.com/share/1FPx4tcpH3/?mibextid=wwXIfr" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-[#1877F2] hover:text-white transition-all text-slate-400 shadow-sm hover:shadow-blue-500/30">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>
                <a href="https://www.instagram.com/expresspeek?igsh=bmwwbWNlc2Z2dTc2" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-[#E1306C] hover:text-white transition-all text-slate-400 shadow-sm hover:shadow-pink-500/30">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                </a>
            </div>
        </div>
    </div>
</footer>
