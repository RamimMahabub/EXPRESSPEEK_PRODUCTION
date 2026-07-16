@extends('layouts.customer')

@section('seo_title', 'Help Center & FAQ | ExpressPeek Bangladesh Shipping Support')
@section('seo_description', 'Find answers to frequently asked questions about shipping from Bangladesh, tracking parcels, customs rules, sourcing products, and getting quotes. ExpressPeek help center.')

@section('content')
<main class="min-h-screen bg-slate-50">
    {{-- Hero --}}
    <section class="bg-gradient-to-br from-slate-900 via-violet-950 to-slate-900 py-16 md:py-24">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <div class="inline-flex items-center gap-2 bg-white/10 border border-white/20 text-white text-xs font-bold px-4 py-2 rounded-full mb-6">
                <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                24/7 Support Available
            </div>
            <h1 class="text-4xl md:text-5xl font-black text-white mb-4">Help Center</h1>
            <p class="text-slate-300 text-base max-w-2xl mx-auto leading-relaxed">Find answers to common questions about shipping from Bangladesh, tracking your parcels, customs requirements, and our sourcing service.</p>
        </div>
    </section>

    {{-- FAQ Section --}}
    <section class="max-w-4xl mx-auto px-6 py-16">
        <h2 class="text-2xl font-black text-slate-900 mb-8">Frequently Asked Questions</h2>

        <div x-data="{ open: null }" class="space-y-4">
            @foreach([
                ['q' => 'How do I ship a parcel from Bangladesh?', 'a' => 'Create a free account on ExpressPeek, enter your package details (destination, weight, type), compare carrier rates, and book your shipment. We handle pickup from your location in Dhaka, Sylhet, or Chittagong and deliver to 220+ countries worldwide.'],
                ['q' => 'What are your shipping rates from Bangladesh?', 'a' => 'Rates depend on the destination country, package weight, and shipment type (document or non-document). Use our <a href="' . route('quote') . '" class="text-violet-600 font-semibold hover:text-violet-800">instant quote calculator</a> to compare rates from multiple carriers in seconds. Fuel surcharges and standard handling fees are included.'],
                ['q' => 'How long does shipping from Bangladesh take?', 'a' => 'Transit times vary by destination: UK/USA/Canada: 5-8 business days, Middle East (UAE, Saudi Arabia): 4-6 business days, Australia/Europe: 6-10 business days. Express services may be faster. Track your shipment in real-time on our <a href="' . route('track') . '" class="text-violet-600 font-semibold hover:text-violet-800">tracking page</a>.'],
                ['q' => 'What items can I ship from Bangladesh?', 'a' => 'You can ship most legal goods including documents, clothing, textiles, dry foods, handicrafts, electronics, and commercial samples. Prohibited items include narcotics, firearms, hazardous chemicals, and perishable food (unless specially arranged). Each destination country may have additional restrictions.'],
                ['q' => 'How does the "Shop from Bangladesh" sourcing service work?', 'a' => 'If you live abroad (UK, USA, Australia, Europe, etc.) and want products from Bangladesh, simply <a href="' . route('sourcing.create') . '" class="text-violet-600 font-semibold hover:text-violet-800">submit a sourcing request</a>. Tell us what you need — traditional clothing, dry foods, local goods — and we\'ll find it, quote you on WhatsApp, and ship it to your door after payment.'],
                ['q' => 'How do I track my shipment?', 'a' => 'Enter your ExpressPeek tracking number on our <a href="' . route('track') . '" class="text-violet-600 font-semibold hover:text-violet-800">tracking page</a>. You\'ll see real-time status updates, delivery milestones, and estimated arrival. No account required.'],
                ['q' => 'Do you offer pickup from my location in Bangladesh?', 'a' => 'Yes! We offer pickup services in Dhaka (including Motijheel, Gulshan, Dhanmondi, Uttara), Sylhet, and Chittagong. You can also drop off packages at our office: 62-63 Shena Kollayn Business Centre, Motijheel Rd, Dhaka 1000.'],
                ['q' => 'What payment methods do you accept?', 'a' => 'We accept cash, mobile banking (bKash, Nagad), and bank transfer. Payment is required before shipment pickup.'],
                ['q' => 'Are shipments insured?', 'a' => 'Basic carrier liability is included with every shipment. For high-value items, we recommend purchasing additional insurance. Contact us for details on coverage options.'],
                ['q' => 'I\'m a Bangladeshi expat — can you send gifts to my family in Bangladesh?', 'a' => 'Yes! While our primary service is shipping FROM Bangladesh, we also help with gift delivery within Bangladesh for diaspora customers. Contact us via WhatsApp to arrange.'],
            ] as $i => $faq)
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden" id="faq-{{ $i }}">
                <button @click="open = open === {{ $i }} ? null : {{ $i }}" class="w-full flex items-center justify-between p-6 text-left group">
                    <h3 class="font-bold text-slate-900 text-base pr-4 group-hover:text-violet-700 transition-colors">{{ $faq['q'] }}</h3>
                    <svg class="w-5 h-5 text-slate-400 flex-shrink-0 transition-transform duration-200" :class="open === {{ $i }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open === {{ $i }}" x-collapse x-cloak class="px-6 pb-6">
                    <p class="text-slate-600 text-sm leading-relaxed">{!! $faq['a'] !!}</p>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    {{-- Contact CTA --}}
    <section class="max-w-4xl mx-auto px-6 pb-16">
        <div class="bg-gradient-to-br from-violet-600 to-blue-700 rounded-3xl p-8 md:p-12 text-center relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 right-0 w-64 h-64 rounded-full bg-white translate-x-1/2 -translate-y-1/2"></div>
            </div>
            <div class="relative">
                <h2 class="text-2xl md:text-3xl font-black text-white mb-3">Still Need Help?</h2>
                <p class="text-violet-200 max-w-lg mx-auto mb-6 text-sm leading-relaxed">Our customer service team is available to assist you with any questions about shipping from Bangladesh.</p>
                <div class="flex flex-wrap items-center justify-center gap-4">
                    <a href="https://wa.me/8801400659902" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white text-violet-700 font-bold text-sm hover:bg-violet-50 transition-colors shadow-lg">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.625.846 5.059 2.284 7.034L.789 23.492l4.571-1.461C7.177 23.362 9.525 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818c-2.234 0-4.308-.637-6.078-1.738l-.436-.26-2.713.866.871-2.642-.283-.451A9.776 9.776 0 012.182 12c0-5.418 4.4-9.818 9.818-9.818S21.818 6.582 21.818 12s-4.4 9.818-9.818 9.818z"/></svg>
                        Chat on WhatsApp
                    </a>
                    <a href="tel:+8801400659902"
                       class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border-2 border-white/30 text-white font-bold text-sm hover:border-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        Call +880 1400-659902
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

{{-- FAQPage Schema --}}
<?php
$faqs = [
    ['q' => 'How do I ship a parcel from Bangladesh?', 'a' => 'Create a free account on ExpressPeek, enter your package details, compare carrier rates, and book your shipment. We handle pickup from Dhaka, Sylhet, or Chittagong and deliver to 220+ countries.'],
    ['q' => 'What are your shipping rates from Bangladesh?', 'a' => 'Rates depend on destination, weight, and shipment type. Use our instant quote calculator to compare rates from multiple carriers in seconds.'],
    ['q' => 'How long does shipping from Bangladesh take?', 'a' => 'UK/USA/Canada: 5-8 business days. Middle East: 4-6 business days. Australia/Europe: 6-10 business days. Express services may be faster.'],
    ['q' => 'How does the Shop from Bangladesh sourcing service work?', 'a' => 'Submit a sourcing request with what you need. We find the product in Bangladesh, quote you on WhatsApp, and ship it to your door after payment.'],
    ['q' => 'How do I track my shipment?', 'a' => 'Enter your ExpressPeek tracking number on our tracking page for real-time status updates and delivery milestones. No account required.'],
];
?>
@push('schema')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        <?php foreach($faqs as $i => $faq): ?>
        {
            "@type": "Question",
            "name": "<?php echo $faq['q']; ?>",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "<?php echo $faq['a']; ?>"
            }
        }<?php echo ($i < count($faqs) - 1) ? ',' : ''; ?>
        <?php endforeach; ?>
    ]
}
</script>
@endpush
@endsection
