@extends('layouts.customer')

@section('seo_title', 'Privacy Policy | ExpressPeek Bangladesh Courier & Logistics')
@section('seo_description', 'ExpressPeek Privacy Policy. Learn how we collect, use, and protect your personal information when you use our courier, shipping, and sourcing services from Bangladesh.')

@section('content')
<main class="min-h-screen bg-white">
    {{-- Hero --}}
    <section class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 py-16 md:py-24">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <h1 class="text-4xl md:text-5xl font-black text-white mb-4">Privacy Policy</h1>
            <p class="text-slate-400 text-base max-w-2xl mx-auto">Last updated: {{ date('F j, Y') }}</p>
        </div>
    </section>

    {{-- Content --}}
    <section class="max-w-4xl mx-auto px-6 py-16">
        <div class="prose prose-slate max-w-none prose-headings:font-black prose-h2:text-2xl prose-h2:mt-12 prose-h2:mb-4 prose-p:leading-relaxed prose-p:text-slate-600 prose-li:text-slate-600">

            <h2>1. Information We Collect</h2>
            <p>When you use ExpressPeek's international courier and sourcing services from Bangladesh, we may collect the following information:</p>
            <ul>
                <li><strong>Personal Information:</strong> Name, email address, phone/WhatsApp number, physical address (both sender and receiver).</li>
                <li><strong>Shipment Details:</strong> Package descriptions, weights, values, tracking numbers, destination countries.</li>
                <li><strong>Payment Information:</strong> Transaction records, payment method details (we do not store credit card numbers directly).</li>
                <li><strong>Sourcing Requests:</strong> Product descriptions, images, links, and delivery preferences submitted through our "Shop from Bangladesh" service.</li>
                <li><strong>Usage Data:</strong> IP address, browser type, pages visited, referring URLs, collected via cookies and analytics tools.</li>
            </ul>

            <h2>2. How We Use Your Information</h2>
            <p>We use your information to:</p>
            <ul>
                <li>Process and fulfil your shipments and sourcing requests</li>
                <li>Provide tracking updates and delivery notifications</li>
                <li>Contact you via WhatsApp regarding sourcing orders and quotes</li>
                <li>Calculate shipping quotes and compare carrier rates</li>
                <li>Comply with customs declarations and export regulations in Bangladesh</li>
                <li>Improve our website and services</li>
                <li>Prevent fraud and ensure security</li>
            </ul>

            <h2>3. Information Sharing</h2>
            <p>We share your information only as necessary to provide our services:</p>
            <ul>
                <li><strong>Shipping Carriers:</strong> Name, address, and package details are shared with our carrier partners (DHL, FedEx, Aramex, and others) to fulfil deliveries.</li>
                <li><strong>Customs Authorities:</strong> Shipment details may be shared with Bangladesh Customs and destination country customs as required by law.</li>
                <li><strong>Legal Compliance:</strong> We may disclose information if required by law, court order, or governmental request.</li>
            </ul>
            <p>We do <strong>not</strong> sell, rent, or share your personal information with third parties for marketing purposes.</p>

            <h2>4. Data Security</h2>
            <p>We implement industry-standard security measures to protect your personal information, including SSL encryption on our website, secure server infrastructure, and access controls for employee data handling.</p>

            <h2>5. Cookies</h2>
            <p>Our website uses cookies to maintain session state, remember your preferences, and collect analytics data. You can control cookie settings through your browser. Disabling cookies may affect the functionality of our website.</p>

            <h2>6. Data Retention</h2>
            <p>We retain your shipment records for a minimum of 3 years to comply with Bangladesh trade and customs regulations. Account information is retained for as long as your account is active. You may request deletion of your account and personal data by contacting us.</p>

            <h2>7. Your Rights</h2>
            <p>You have the right to:</p>
            <ul>
                <li>Access the personal information we hold about you</li>
                <li>Request correction of inaccurate information</li>
                <li>Request deletion of your personal data (subject to legal retention requirements)</li>
                <li>Withdraw consent for WhatsApp communications at any time</li>
            </ul>

            <h2>8. International Data Transfers</h2>
            <p>As an international shipping service, your data may be transferred to and processed in countries outside Bangladesh (including carrier systems in the destination country). By using our services, you consent to such transfers as necessary to fulfil your shipments.</p>

            <h2>9. Children's Privacy</h2>
            <p>Our services are not directed to individuals under the age of 18. We do not knowingly collect personal information from children.</p>

            <h2>10. Changes to This Policy</h2>
            <p>We may update this Privacy Policy from time to time. Changes will be posted on this page with an updated date. We encourage you to review this page periodically.</p>

            <h2>11. Contact Us</h2>
            <p>For privacy-related inquiries, please contact us:</p>
            <ul>
                <li><strong>Phone/WhatsApp:</strong> <a href="tel:+8801400659902" class="text-violet-600 hover:text-violet-800 font-semibold">+880 1400-659902</a></li>
                <li><strong>Address:</strong> 62-63 Shena Kollayn Business Centre, Motijheel Rd, Dhaka 1000, Bangladesh</li>
            </ul>
        </div>
    </section>
</main>
@endsection
