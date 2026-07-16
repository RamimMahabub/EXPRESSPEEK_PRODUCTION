@extends('layouts.customer')

@section('seo_title', 'Terms of Service | ExpressPeek Bangladesh Courier & Logistics')
@section('seo_description', 'Read the Terms of Service for ExpressPeek international courier and logistics services from Bangladesh. Covers shipping policies, liability, prohibited items, and payment terms.')

@section('content')
<main class="min-h-screen bg-white">
    {{-- Hero --}}
    <section class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 py-16 md:py-24">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <h1 class="text-4xl md:text-5xl font-black text-white mb-4">Terms of Service</h1>
            <p class="text-slate-400 text-base max-w-2xl mx-auto">Last updated: {{ date('F j, Y') }}</p>
        </div>
    </section>

    {{-- Content --}}
    <section class="max-w-4xl mx-auto px-6 py-16">
        <div class="prose prose-slate max-w-none prose-headings:font-black prose-h2:text-2xl prose-h2:mt-12 prose-h2:mb-4 prose-p:leading-relaxed prose-p:text-slate-600 prose-li:text-slate-600">

            <h2>1. Introduction</h2>
            <p>Welcome to ExpressPeek ("we", "us", "our"). These Terms of Service govern your use of our website <strong>expresspeek.com</strong> and the international courier, parcel, freight, and sourcing services we provide from Bangladesh. By accessing our website or using our services, you agree to these terms in full.</p>
            <p>ExpressPeek is operated from our registered office at 62-63 Shena Kollayn Business Centre, Motijheel Rd, Dhaka 1000, Bangladesh.</p>

            <h2>2. Services</h2>
            <p>ExpressPeek provides the following services from Bangladesh:</p>
            <ul>
                <li><strong>International Courier & Parcel Delivery</strong> — shipping documents, parcels, and packages to 220+ countries worldwide via our carrier network.</li>
                <li><strong>Freight & Cargo Shipping</strong> — heavy cargo, pallets, and bulk freight for commercial use.</li>
                <li><strong>Product Sourcing ("Shop from Bangladesh")</strong> — we source products from Bangladeshi shops and ship them to customers living abroad (UK, USA, Australia, Europe, and beyond).</li>
            </ul>

            <h2>3. Shipping & Delivery</h2>
            <p>All shipments originate from Bangladesh. Transit times are estimates and may vary based on destination country customs clearance, carrier performance, weather, and other factors beyond our control. ExpressPeek is not liable for delays caused by third-party carriers or customs authorities.</p>
            <p>Risk of loss for shipments passes to the recipient upon handover to the destination carrier or delivery agent.</p>

            <h2>4. Prohibited & Restricted Items</h2>
            <p>You may not ship items that are illegal under Bangladeshi law, international law, or the laws of the destination country. Prohibited items include but are not limited to:</p>
            <ul>
                <li>Narcotics and illegal drugs</li>
                <li>Firearms and explosives</li>
                <li>Hazardous chemicals and radioactive materials</li>
                <li>Counterfeit goods and pirated materials</li>
                <li>Live animals (unless pre-approved)</li>
                <li>Perishable food items (unless specifically arranged)</li>
            </ul>
            <p>ExpressPeek reserves the right to refuse, return, or destroy any shipment containing prohibited items, at the sender's expense.</p>

            <h2>5. Pricing & Payment</h2>
            <p>All quotes provided through our website are estimates. Final pricing may vary based on actual weight, volumetric weight, destination-specific surcharges, and customs duties. Fuel surcharges and standard handling fees are included in our quoted prices.</p>
            <p>Payment is due before shipment pickup. We accept cash, mobile banking (bKash, Nagad), and bank transfer.</p>

            <h2>6. Liability & Insurance</h2>
            <p>ExpressPeek's liability for lost or damaged shipments is limited to the declared value or the maximum carrier liability, whichever is lower. We recommend purchasing additional insurance for high-value items.</p>

            <h2>7. Sourcing Service Terms</h2>
            <p>For our "Shop from Bangladesh" sourcing service: we act as your purchasing agent in Bangladesh. We will provide product availability and pricing via WhatsApp before any purchase is made. Payment for the product plus shipping is required before we proceed with purchase and shipment.</p>
            <p>Product returns may not be possible once shipped internationally. Refund eligibility depends on the original seller's return policy.</p>

            <h2>8. Privacy</h2>
            <p>Your personal information is handled in accordance with our <a href="{{ route('privacy') }}" class="text-violet-600 hover:text-violet-800 font-semibold">Privacy Policy</a>.</p>

            <h2>9. Governing Law</h2>
            <p>These Terms are governed by the laws of the People's Republic of Bangladesh. Any disputes arising from these terms shall be resolved in the courts of Dhaka, Bangladesh.</p>

            <h2>10. Changes to Terms</h2>
            <p>We reserve the right to update these Terms at any time. Changes will be posted on this page with an updated "Last updated" date. Continued use of our services after changes constitutes acceptance of the new terms.</p>

            <h2>11. Contact Us</h2>
            <p>If you have any questions about these Terms of Service, please contact us:</p>
            <ul>
                <li><strong>Phone/WhatsApp:</strong> <a href="tel:+8801400659902" class="text-violet-600 hover:text-violet-800 font-semibold">+880 1400-659902</a></li>
                <li><strong>Address:</strong> 62-63 Shena Kollayn Business Centre, Motijheel Rd, Dhaka 1000, Bangladesh</li>
            </ul>
        </div>
    </section>
</main>
@endsection
