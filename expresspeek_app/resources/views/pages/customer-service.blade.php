@extends('layouts.customer')

@section('seo_title', 'Customer Service | Contact ExpressPeek Bangladesh Courier')
@section('seo_description', 'Contact ExpressPeek customer service for help with shipping from Bangladesh, tracking parcels, sourcing products, or scheduling a pickup. Available via WhatsApp, phone, or visit our Dhaka office.')

@section('content')
<main class="min-h-screen bg-slate-50">
    {{-- Hero --}}
    <section class="bg-gradient-to-br from-slate-900 via-violet-950 to-slate-900 py-16 md:py-24">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <h1 class="text-4xl md:text-5xl font-black text-white mb-4">Customer Service</h1>
            <p class="text-slate-300 text-base max-w-2xl mx-auto leading-relaxed">We're here to help with your international shipping from Bangladesh. Reach us via WhatsApp, phone, or visit our office in Dhaka.</p>
        </div>
    </section>

    {{-- Contact Cards --}}
    <section class="max-w-5xl mx-auto px-6 -mt-10 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- WhatsApp --}}
            <a href="https://wa.me/8801400659902" target="_blank" rel="noopener noreferrer"
               class="bg-white rounded-2xl p-8 border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all text-center group" id="contact-whatsapp">
                <div class="w-14 h-14 rounded-2xl bg-emerald-100 group-hover:bg-emerald-200 flex items-center justify-center mx-auto mb-5 transition-colors">
                    <svg class="w-7 h-7 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                </div>
                <h3 class="font-bold text-slate-900 text-lg mb-2">WhatsApp</h3>
                <p class="text-slate-500 text-sm mb-3">Fastest response — usually within minutes</p>
                <span class="text-emerald-600 font-bold text-sm">Chat Now →</span>
            </a>

            {{-- Phone --}}
            <a href="tel:+8801400659902"
               class="bg-white rounded-2xl p-8 border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all text-center group" id="contact-phone">
                <div class="w-14 h-14 rounded-2xl bg-blue-100 group-hover:bg-blue-200 flex items-center justify-center mx-auto mb-5 transition-colors">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                </div>
                <h3 class="font-bold text-slate-900 text-lg mb-2">Phone</h3>
                <p class="text-slate-500 text-sm mb-3">+880 1400-659902</p>
                <span class="text-blue-600 font-bold text-sm">Call Now →</span>
            </a>

            {{-- Office --}}
            <div class="bg-white rounded-2xl p-8 border border-slate-100 shadow-sm text-center" id="contact-office">
                <div class="w-14 h-14 rounded-2xl bg-violet-100 flex items-center justify-center mx-auto mb-5">
                    <svg class="w-7 h-7 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h3 class="font-bold text-slate-900 text-lg mb-2">Visit Our Office</h3>
                <p class="text-slate-500 text-sm mb-1">62-63 Shena Kollayn Business Centre</p>
                <p class="text-slate-500 text-sm mb-3">Motijheel Rd, Dhaka 1000</p>
                <p class="text-violet-600 font-bold text-xs">Sat–Thu: 9AM–9PM</p>
            </div>
        </div>
    </section>

    {{-- Schedule Pickup Section --}}
    <section class="max-w-5xl mx-auto px-6 py-16" id="schedule-pickup">
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-violet-600 to-blue-700 px-8 py-6 text-white">
                <h2 class="text-xl font-black">Schedule a Pickup</h2>
                <p class="text-violet-200 text-sm mt-1">Want us to collect your parcel? Contact us to arrange a pickup from your location in Dhaka, Sylhet, or Chittagong.</p>
            </div>
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="font-bold text-slate-900 mb-4">Pickup Areas</h3>
                        <ul class="space-y-3">
                            @foreach([
                                ['city' => 'Dhaka', 'areas' => 'Motijheel, Gulshan, Dhanmondi, Uttara, Mirpur, Banani, Bashundhara'],
                                ['city' => 'Sylhet', 'areas' => 'Zindabazar, Ambarkhana, Subid Bazar, Airport area'],
                                ['city' => 'Chittagong', 'areas' => 'Agrabad, Nasirabad, GEC Circle, Halishahar'],
                            ] as $area)
                            <li class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-violet-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900 text-sm">{{ $area['city'] }}</p>
                                    <p class="text-slate-500 text-xs">{{ $area['areas'] }}</p>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 mb-4">How to Schedule</h3>
                        <div class="space-y-4">
                            @foreach([
                                ['step' => '1', 'text' => 'Message us on WhatsApp with your address and preferred pickup time'],
                                ['step' => '2', 'text' => 'We confirm the pickup slot (same-day available in Dhaka)'],
                                ['step' => '3', 'text' => 'Our agent arrives to collect your parcel — no extra charge for standard pickups'],
                            ] as $step)
                            <div class="flex items-start gap-3">
                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-violet-500 to-blue-600 flex items-center justify-center text-white text-xs font-black flex-shrink-0">{{ $step['step'] }}</div>
                                <p class="text-slate-600 text-sm leading-relaxed pt-0.5">{{ $step['text'] }}</p>
                            </div>
                            @endforeach
                        </div>
                        <a href="https://wa.me/8801400659902?text=Hi%2C%20I%20want%20to%20schedule%20a%20pickup" target="_blank" rel="noopener noreferrer"
                           class="mt-6 inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-violet-600 to-blue-700 text-white font-bold text-sm hover:opacity-90 transition-opacity shadow-lg shadow-violet-500/20">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                            Schedule Pickup on WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Map --}}
    <section class="max-w-5xl mx-auto px-6 pb-16">
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden p-2">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3652.5543161683204!2d90.41777451151938!3d23.727604678597146!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755b900404a03eb%3A0xfdba21f7353b5cb3!2sEXPRESS%20PEEK!5e0!3m2!1sen!2sbd!4v1782905765854!5m2!1sen!2sbd"
                    width="100%" height="350" style="border:0; border-radius: 1.25rem;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"
                    title="ExpressPeek Office Location - Motijheel, Dhaka"></iframe>
        </div>
    </section>
</main>
@endsection
