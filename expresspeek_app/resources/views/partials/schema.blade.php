{{-- Schema.org JSON-LD Structured Data --}}

{{-- LocalBusiness + Organization --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@type": "LocalBusiness",
    "@id": "{{ url('/') }}#organization",
    "name": "ExpressPeek",
    "alternateName": "Express Peek Logistics",
    "description": "International courier, parcel, and cargo shipping service from Bangladesh to 220+ countries. Sourcing and shipping for Bangladeshi expats worldwide.",
    "url": "{{ url('/') }}",
    "logo": "{{ asset('images/express-peek-logo.webp') }}",
    "image": "{{ asset('images/express-peek-logo.webp') }}",
    "telephone": "+8801400659902",
    "email": "info@expresspeek.com",
    "address": {
        "@type": "PostalAddress",
        "streetAddress": "62-63 Shena Kollayn Business Centre, Motijheel Rd",
        "addressLocality": "Dhaka",
        "addressRegion": "Dhaka Division",
        "postalCode": "1000",
        "addressCountry": "BD"
    },
    "geo": {
        "@type": "GeoCoordinates",
        "latitude": 23.7276,
        "longitude": 90.4178
    },
    "openingHoursSpecification": [
        {
            "@type": "OpeningHoursSpecification",
            "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Sunday"],
            "opens": "09:00",
            "closes": "21:00"
        },
        {
            "@type": "OpeningHoursSpecification",
            "dayOfWeek": "Saturday",
            "opens": "10:00",
            "closes": "18:00"
        }
    ],
    "priceRange": "৳৳",
    "currenciesAccepted": "BDT",
    "paymentAccepted": "Cash, Mobile Banking, Bank Transfer",
    "areaServed": {
        "@type": "GeoCircle",
        "geoMidpoint": {
            "@type": "GeoCoordinates",
            "latitude": 23.7276,
            "longitude": 90.4178
        },
        "geoRadius": "50000"
    },
    "sameAs": [
        "https://www.facebook.com/share/1FPx4tcpH3/?mibextid=wwXIfr",
        "https://www.instagram.com/expresspeek",
        "https://wa.me/8801400659902"
    ],
    "hasMap": "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3652.5543161683204!2d90.41777451151938!3d23.727604678597146!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755b900404a03eb%3A0xfdba21f7353b5cb3!2sEXPRESS%20PEEK!5e0!3m2!1sen!2sbd"
}
</script>

{{-- Service Schema: Courier --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@type": "Service",
    "serviceType": "International Courier Service",
    "name": "ExpressPeek International Courier & Parcel Delivery",
    "description": "Send parcels, documents, and packages from Bangladesh to 220+ countries worldwide. Express and standard delivery options with full tracking.",
    "provider": {
        "@type": "LocalBusiness",
        "@id": "{{ url('/') }}#organization"
    },
    "areaServed": {
        "@type": "Country",
        "name": "Bangladesh"
    },
    "availableChannel": {
        "@type": "ServiceChannel",
        "serviceUrl": "{{ url('/quote') }}",
        "servicePhone": "+8801400659902"
    },
    "offers": {
        "@type": "Offer",
        "priceCurrency": "BDT",
        "availability": "https://schema.org/InStock"
    }
}
</script>

{{-- Service Schema: Freight --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@type": "Service",
    "serviceType": "International Freight & Cargo Shipping",
    "name": "ExpressPeek Freight & Cargo Solutions",
    "description": "Heavy cargo, pallets, and bulk freight shipping from Bangladesh. Commercial samples and business shipments handled with precision.",
    "provider": {
        "@type": "LocalBusiness",
        "@id": "{{ url('/') }}#organization"
    },
    "areaServed": {
        "@type": "Country",
        "name": "Bangladesh"
    }
}
</script>

{{-- Service Schema: Sourcing --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@type": "Service",
    "serviceType": "Product Sourcing & Shopping from Bangladesh",
    "name": "Shop from Bangladesh — ExpressPeek Sourcing Service",
    "description": "We source any product from Bangladesh — traditional clothing, dry foods, local goods — and ship it to Bangladeshi expats living in the UK, USA, Australia, Europe, and 220+ countries.",
    "provider": {
        "@type": "LocalBusiness",
        "@id": "{{ url('/') }}#organization"
    },
    "areaServed": {
        "@type": "Country",
        "name": "Bangladesh"
    },
    "availableChannel": {
        "@type": "ServiceChannel",
        "serviceUrl": "{{ url('/sourcing') }}"
    }
}
</script>

{{-- BreadcrumbList (dynamic per route) --}}
@php
    $breadcrumbs = [['name' => 'Home', 'url' => url('/')]];

    if (request()->routeIs('track')) {
        $breadcrumbs[] = ['name' => 'Track Parcel', 'url' => url('/track')];
    } elseif (request()->routeIs('quote')) {
        $breadcrumbs[] = ['name' => 'Get a Quote', 'url' => url('/quote')];
    } elseif (request()->routeIs('sourcing.create')) {
        $breadcrumbs[] = ['name' => 'Shop from Bangladesh', 'url' => url('/sourcing')];
    } elseif (request()->routeIs('terms')) {
        $breadcrumbs[] = ['name' => 'Terms of Service', 'url' => url('/terms')];
    } elseif (request()->routeIs('privacy')) {
        $breadcrumbs[] = ['name' => 'Privacy Policy', 'url' => url('/privacy')];
    } elseif (request()->routeIs('help')) {
        $breadcrumbs[] = ['name' => 'Help Center', 'url' => url('/help')];
    } elseif (request()->routeIs('customer-service')) {
        $breadcrumbs[] = ['name' => 'Customer Service', 'url' => url('/customer-service')];
    } elseif (request()->routeIs('ship-to')) {
        $breadcrumbs[] = ['name' => 'Ship to ' . (request()->route('country') ?? ''), 'url' => url()->current()];
    } elseif (request()->routeIs('ship-from')) {
        $breadcrumbs[] = ['name' => 'Ship from ' . ucfirst(request()->route('city') ?? ''), 'url' => url()->current()];
    } elseif (request()->routeIs('blog.index')) {
        $breadcrumbs[] = ['name' => 'Resources', 'url' => url('/resources')];
    } elseif (request()->routeIs('blog.show')) {
        $breadcrumbs[] = ['name' => 'Resources', 'url' => url('/resources')];
        $breadcrumbs[] = ['name' => 'Article', 'url' => url()->current()];
    }
@endphp
@if(count($breadcrumbs) > 1)
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
        @foreach($breadcrumbs as $i => $crumb)
        {
            "@type": "ListItem",
            "position": {{ $i + 1 }},
            "name": "{{ $crumb['name'] }}",
            "item": "{{ $crumb['url'] }}"
        }{{ !$loop->last ? ',' : '' }}
        @endforeach
    ]
}
</script>
@endif
