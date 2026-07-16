@extends('layouts.customer')

@section('seo_title', $post->seo_title)
@section('seo_description', $post->seo_description)
@section('og_type', 'article')

@section('content')
<main class="min-h-screen bg-slate-50">
    {{-- Hero --}}
    <section class="bg-gradient-to-br from-slate-900 via-violet-950 to-slate-900 py-16 md:py-20">
        <div class="max-w-4xl mx-auto px-6">
            <div class="flex items-center gap-2 text-sm text-slate-400 mb-6">
                <a href="{{ route('blog.index') }}" class="hover:text-white transition-colors">Resources</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-slate-300">Article</span>
            </div>
            @if($post->tags)
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach($post->tags as $tag)
                <span class="bg-white/10 border border-white/20 text-white text-xs font-bold px-3 py-1 rounded-full">{{ $tag }}</span>
                @endforeach
            </div>
            @endif
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-black text-white leading-tight mb-5">{{ $post->title }}</h1>
            <div class="flex items-center gap-4 text-sm text-slate-400">
                <span>By {{ $post->author }}</span>
                <span>•</span>
                <time datetime="{{ $post->published_at->toISOString() }}">{{ $post->published_at->format('F j, Y') }}</time>
            </div>
        </div>
    </section>

    {{-- Article Content --}}
    <section class="max-w-4xl mx-auto px-6 py-16">
        <article class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8 md:p-12">
            @if($post->featured_image)
            <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="w-full rounded-2xl mb-8 shadow-sm" loading="lazy">
            @endif
            <div class="prose prose-slate max-w-none prose-headings:font-black prose-h2:text-2xl prose-h2:mt-10 prose-h2:mb-4 prose-p:leading-relaxed prose-p:text-slate-600 prose-li:text-slate-600 prose-a:text-violet-600 prose-a:font-semibold hover:prose-a:text-violet-800 prose-img:rounded-xl">
                {!! $post->body !!}
            </div>
        </article>

        {{-- Share + CTA --}}
        <div class="mt-8 bg-gradient-to-r from-violet-600 to-blue-700 rounded-3xl p-8 text-center">
            <h2 class="text-xl font-black text-white mb-3">Need to Ship from Bangladesh?</h2>
            <p class="text-violet-200 text-sm mb-5">Get an instant quote and compare carriers for your international shipment.</p>
            <div class="flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('quote') }}" class="px-6 py-3 rounded-xl bg-white text-violet-700 font-bold text-sm hover:bg-violet-50 transition-colors shadow-lg">Get a Quote →</a>
                <a href="{{ route('sourcing.create') }}" class="px-6 py-3 rounded-xl border-2 border-white/30 text-white font-bold text-sm hover:border-white transition-colors">🛒 Shop from Bangladesh</a>
            </div>
        </div>
    </section>

    {{-- Related Posts --}}
    @if($related->count() > 0)
    <section class="max-w-4xl mx-auto px-6 pb-16">
        <h3 class="text-xl font-black text-slate-900 mb-6">Related Articles</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($related as $r)
            <a href="{{ route('blog.show', $r->slug) }}" class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all group">
                <h4 class="font-bold text-slate-900 text-base mb-2 group-hover:text-violet-700 transition-colors line-clamp-2">{{ $r->title }}</h4>
                <p class="text-sm text-slate-500 line-clamp-2">{{ $r->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($r->body), 80) }}</p>
                <span class="text-xs text-violet-600 font-bold mt-3 block">Read more →</span>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Country Links --}}
    <section class="max-w-4xl mx-auto px-6 pb-16">
        <h3 class="text-lg font-bold text-slate-900 mb-4">Ship from Bangladesh to</h3>
        <div class="flex flex-wrap gap-2">
            @foreach($countries as $c)
            <a href="{{ route('ship-to', $c['slug']) }}" class="bg-white rounded-lg px-4 py-2 border border-slate-100 text-sm text-slate-600 hover:text-violet-700 hover:border-violet-200 transition-all">{{ $c['name'] }}</a>
            @endforeach
        </div>
    </section>
</main>

{{-- Article Schema --}}
@push('schema')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": "{{ $post->title }}",
    "description": "{{ $post->seo_description }}",
    "datePublished": "{{ $post->published_at->toISOString() }}",
    "dateModified": "{{ $post->updated_at->toISOString() }}",
    "author": {
        "@type": "Organization",
        "name": "{{ $post->author }}",
        "url": "{{ url('/') }}"
    },
    "publisher": {
        "@type": "Organization",
        "name": "ExpressPeek",
        "logo": {
            "@type": "ImageObject",
            "url": "{{ asset('images/express-peek-logo.webp') }}"
        }
    },
    "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "{{ route('blog.show', $post->slug) }}"
    }
}
</script>
@endpush
@endsection
