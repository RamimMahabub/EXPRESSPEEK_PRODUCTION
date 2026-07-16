<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'title' => 'How to Send Food & Gifts to Bangladeshi Family Abroad',
                'slug' => 'how-to-send-food-gifts-to-bangladeshi-family-abroad',
                'excerpt' => 'A complete guide for Bangladeshi families who want to send traditional food, clothing, and gifts to relatives living in the UK, USA, Australia, and other countries.',
                'body' => '<h2>Why Send Gifts from Bangladesh?</h2>
<p>For millions of Bangladeshi families, sending a parcel abroad is more than logistics — it\'s a way to stay connected. Whether it\'s homemade pitha for Eid, a jamdani saree for a wedding, or dried shutki for someone who misses the taste of home, parcels from Bangladesh carry love across borders.</p>

<h2>What Can You Send?</h2>
<p>Most non-perishable Bangladeshi products can be shipped internationally. Popular items include:</p>
<ul>
<li><strong>Traditional clothing:</strong> Jamdani, muslin, Aarong products, panjabis, kameez sets</li>
<li><strong>Dry foods:</strong> Shutki (dried fish), muri, chanachur, spices, tea</li>
<li><strong>Handicrafts:</strong> Nakshi Kantha, pottery, brass items</li>
<li><strong>Personal items:</strong> Documents, certificates, photos, books</li>
</ul>

<h2>What to Avoid</h2>
<p>Every destination country has its own customs restrictions. Some common items to be careful with:</p>
<ul>
<li>Fresh or perishable food (most countries prohibit this)</li>
<li>Liquids over certain volumes</li>
<li>Soil, plants, or seeds (especially strict in Australia)</li>
<li>Medicines without proper documentation</li>
</ul>

<h2>How ExpressPeek Makes It Easy</h2>
<p>At ExpressPeek, we specialise in shipping from Bangladesh. Our team understands what Bangladeshi families typically send abroad and can advise on packaging, customs declarations, and restricted items for each destination. Simply get a quote on our website, book your shipment, and we\'ll handle the rest — from pickup in Dhaka, Sylhet, or Chittagong to doorstep delivery worldwide.</p>

<h2>Tips for Sending Food Items</h2>
<ol>
<li><strong>Use airtight packaging:</strong> Double-bag dry foods and vacuum-seal if possible</li>
<li><strong>Label everything clearly:</strong> Write contents in English on the outside</li>
<li><strong>Check destination rules:</strong> Australia and the USA have strict biosecurity — check before packing</li>
<li><strong>Choose reliable carriers:</strong> Use tracked services so you can monitor your parcel</li>
</ol>',
                'seo_title' => 'How to Send Food & Gifts to Bangladeshi Family Abroad | ExpressPeek Guide',
                'seo_description' => 'Complete guide to sending traditional Bangladeshi food, clothing, and gifts to family abroad. Covers packaging tips, customs rules, and shipping from Dhaka, Sylhet & Chittagong.',
                'tags' => ['gifts', 'food', 'probashi', 'guide'],
                'author' => 'ExpressPeek Team',
                'published_at' => now(),
            ],
            [
                'title' => 'Customs Rules for Sending Parcels from Bangladesh to the UK',
                'slug' => 'customs-rules-sending-parcels-bangladesh-to-uk',
                'excerpt' => 'Everything you need to know about UK customs when shipping parcels from Bangladesh — duty thresholds, restricted items, and how to fill out customs declarations correctly.',
                'body' => '<h2>Understanding UK Customs for Bangladesh Parcels</h2>
<p>The UK is the most popular destination for parcels from Bangladesh, driven by the large British-Bangladeshi community (particularly from Sylhet). Understanding UK customs rules helps ensure your parcel arrives smoothly without delays or extra charges.</p>

<h2>Duty & Tax Thresholds</h2>
<p>As of 2024, goods shipped to the UK are subject to:</p>
<ul>
<li><strong>No duty on gifts valued under £39:</strong> Genuine gifts between individuals may be exempt from customs duty if under this threshold</li>
<li><strong>VAT at 20%:</strong> Applicable on goods above the threshold</li>
<li><strong>Commercial shipments:</strong> All commercial goods are subject to duty based on the commodity code</li>
</ul>

<h2>Customs Declaration Tips</h2>
<ol>
<li>Always declare the true value of goods — undervaluation can lead to penalties</li>
<li>Describe items accurately in English (e.g., "cotton saree" not just "clothing")</li>
<li>Mark gift shipments clearly as "GIFT" with the relationship to the receiver</li>
<li>Include invoices for commercial shipments</li>
</ol>

<h2>Restricted Items for UK-Bound Parcels</h2>
<ul>
<li>Fresh meat and dairy products</li>
<li>Plants and soil without phytosanitary certificates</li>
<li>Counterfeit branded goods</li>
<li>Certain traditional medicines containing restricted substances</li>
</ul>

<h2>How ExpressPeek Helps</h2>
<p>We prepare your customs documentation as part of our service. Our team is experienced with UK-bound shipments from Bangladesh and will ensure your declarations are accurate, reducing the risk of customs delays. Get started with a <a href="/quote">free quote</a>.</p>',
                'seo_title' => 'Customs Rules: Shipping Parcels from Bangladesh to UK | ExpressPeek',
                'seo_description' => 'UK customs rules for parcels from Bangladesh. Learn about duty thresholds, VAT, restricted items, and how to fill customs declarations correctly. Guide for British-Bangladeshis.',
                'tags' => ['customs', 'UK', 'guide', 'regulations'],
                'author' => 'ExpressPeek Team',
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'Shipping Product Samples from Bangladesh: A Guide for Exporters',
                'slug' => 'shipping-product-samples-bangladesh-exporters-guide',
                'excerpt' => 'How Bangladeshi garment manufacturers and SMBs can ship product samples to international buyers efficiently and affordably.',
                'body' => '<h2>Why Sample Shipping Matters</h2>
<p>Bangladesh\'s ready-made garment (RMG) industry is the country\'s largest export sector. Before bulk orders are placed, international buyers need to see and feel samples. Fast, reliable sample delivery can make or break a deal.</p>

<h2>Common Sample Types from Bangladesh</h2>
<ul>
<li><strong>Garment samples:</strong> T-shirts, shirts, trousers, denim — the bread and butter of BD exports</li>
<li><strong>Textile swatches:</strong> Fabric samples for approval before production</li>
<li><strong>Handicraft samples:</strong> Jute products, leather goods, home textiles</li>
<li><strong>Food samples:</strong> Packaged tea, spices, frozen fish (requires cold chain)</li>
</ul>

<h2>Key Considerations for Exporters</h2>
<ol>
<li><strong>Speed is critical:</strong> Buyers expect samples within 5-7 days. Use express services</li>
<li><strong>Label as "samples — no commercial value":</strong> This can reduce customs complexity in some countries</li>
<li><strong>Include proper documentation:</strong> Commercial invoice, packing list, and any required certificates</li>
<li><strong>Track everything:</strong> Buyers expect tracking numbers immediately after dispatch</li>
</ol>

<h2>How ExpressPeek Serves Exporters</h2>
<p>We offer competitive rates for commercial sample shipping from Bangladesh to major buying markets including the USA, UK, Europe, Australia, and Japan. Our multi-carrier comparison ensures you get the best rate, and our team handles customs documentation. <a href="/quote">Get a quote now</a>.</p>',
                'seo_title' => 'Shipping Product Samples from Bangladesh: Exporter Guide | ExpressPeek',
                'seo_description' => 'Guide for Bangladeshi garment manufacturers and SMBs to ship product samples internationally. Tips on documentation, labeling, and fast delivery to buyers worldwide.',
                'tags' => ['business', 'exporters', 'RMG', 'samples', 'guide'],
                'author' => 'ExpressPeek Team',
                'published_at' => now()->subDays(7),
            ],
            [
                'title' => 'Sending Eid Gifts to Probashi Family Abroad — A Complete Guide',
                'slug' => 'sending-eid-gifts-probashi-family-abroad',
                'excerpt' => 'How to send Eid gifts from Bangladesh to family members living abroad in the UK, USA, Saudi Arabia, and other countries. Timing, packaging, and shipping tips.',
                'body' => '<h2>Eid and the Probashi Connection</h2>
<p>Eid ul-Fitr and Eid ul-Adha are the most important occasions for Bangladeshi families to connect across borders. For probashi (expatriate) Bangladeshis, receiving a parcel from home during Eid brings the celebration closer, no matter where in the world they live.</p>

<h2>When to Send</h2>
<p>Timing is everything for Eid parcels:</p>
<ul>
<li><strong>Start planning 2-3 weeks before Eid:</strong> This accounts for transit time and potential customs delays</li>
<li><strong>UK/USA/Canada:</strong> Ship at least 10-12 days before Eid for standard delivery</li>
<li><strong>Middle East (UAE, Saudi Arabia):</strong> 7-8 days is usually sufficient</li>
<li><strong>Express options:</strong> Available for last-minute parcels (5-day rush delivery)</li>
</ul>

<h2>Popular Eid Gift Ideas to Send from Bangladesh</h2>
<ul>
<li>New clothes — salwar kameez, panjabis, children\'s Eid outfits</li>
<li>Traditional sweets — mishti, sandesh, rosogolla (dried/preserved versions)</li>
<li>Dry fruits and dates</li>
<li>Perfume (attar)</li>
<li>Jewellery and accessories</li>
<li>Aarong gift items</li>
</ul>

<h2>Packaging Tips for Eid Parcels</h2>
<ol>
<li>Wrap clothing in plastic to protect from moisture</li>
<li>Pack sweets in airtight containers — use silica gel packets</li>
<li>Avoid sending fresh/perishable items internationally</li>
<li>Add padding to protect fragile items like jewellery or pottery</li>
</ol>

<h2>Ship Your Eid Parcel with ExpressPeek</h2>
<p>We understand the emotional significance of Eid parcels. That\'s why we offer pickup across Dhaka, Sylhet, and Chittagong — so you can focus on preparing the perfect gift while we handle delivery. <a href="/quote">Get a quote</a> or <a href="/sourcing">use our sourcing service</a> if you need us to shop for you.</p>',
                'seo_title' => 'Sending Eid Gifts to Probashi Family Abroad | ExpressPeek Guide',
                'seo_description' => 'Complete guide to sending Eid gifts from Bangladesh to probashi family in the UK, USA, Saudi Arabia & more. Timing tips, packaging advice, and shipping options.',
                'tags' => ['Eid', 'gifts', 'probashi', 'seasonal', 'guide'],
                'author' => 'ExpressPeek Team',
                'published_at' => now()->subDays(14),
            ],
        ];

        foreach ($posts as $post) {
            BlogPost::updateOrCreate(
                ['slug' => $post['slug']],
                $post
            );
        }
    }
}
