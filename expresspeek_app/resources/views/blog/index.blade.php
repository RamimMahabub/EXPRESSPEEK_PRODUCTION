@extends('layouts.customer')

@section('seo_title', 'Shipping Resources & Guides | ExpressPeek Bangladesh')
@section('seo_description', 'Expert guides on shipping from Bangladesh — customs rules, packaging tips, courier comparisons, and advice for Bangladeshi expats sending parcels home or abroad.')

@section('content')
<main class="min-h-screen bg-slate-50">
    {{-- Hero --}}
    <section class="bg-gradient-to-br from-slate-900 via-violet-950 to-slate-900 py-16 md:py-24">
        <div class="max-w-6xl mx-auto px-6 text-center">
            <div class="inline-flex items-center gap-2 bg-white/10 border border-white/20 text-white text-xs font-bold px-4 py-2 rounded-full mb-6">
                <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                ExpressPeek Resources
            </div>
            <h1 class="text-4xl md:text-5xl font-black text-white mb-4">Shipping Guides & Resources</h1>
            <p class="text-slate-300 text-base max-w-2xl mx-auto leading-relaxed">Expert advice on international shipping from Bangladesh — customs rules, packaging tips, and guides for the Bangladeshi diaspora.</p>
        </div>
    </section>

    {{-- Posts Grid --}}
    <section class="max-w-6xl mx-auto px-6 py-16">
        @if($posts->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($posts as $post)
            <article class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all group">
                @if($post->featured_image)
                <div class="h-48 overflow-hidden">
                    <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                </div>
                @else
                <div class="h-48 bg-gradient-to-br from-violet-100 to-blue-100 flex items-center justify-center">
                    <svg class="w-12 h-12 text-violet-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                @endif
                <div class="p-6">
                    @if($post->tags)
                    <div class="flex flex-wrap gap-2 mb-3">
                        @foreach(array_slice($post->tags, 0, 2) as $tag)
                        <span class="bg-violet-50 text-violet-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ $tag }}</span>
                        @endforeach
                    </div>
                    @endif
                    <h2 class="font-bold text-slate-900 text-lg mb-2 group-hover:text-violet-700 transition-colors line-clamp-2">
                        <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                    </h2>
                    <p class="text-slate-500 text-sm leading-relaxed line-clamp-3 mb-4">{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->body), 120) }}</p>
                    <div class="flex items-center justify-between text-xs text-slate-400">
                        <span>{{ $post->published_at->format('M d, Y') }}</span>
                        <a href="{{ route('blog.show', $post->slug) }}" class="text-violet-600 font-bold hover:text-violet-800 transition-colors">Read more →</a>
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        <div class="mt-12">
            {{ $posts->links() }}
        </div>
        @else
        <div class="text-center py-20">
            <div class="w-16 h-16 rounded-2xl bg-violet-100 flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <h2 class="text-2xl font-black text-slate-900 mb-3">Resources Coming Soon</h2>
            <p class="text-slate-500 max-w-md mx-auto">We're working on expert shipping guides, customs information, and tips for Bangladeshi expats. Check back soon!</p>
        </div>
        @endif
    </section>

    {{-- Country Pages Sidebar --}}
    <section class="max-w-6xl mx-auto px-6 pb-16">
        <h3 class="text-lg font-bold text-slate-900 mb-6">Popular Shipping Routes from Bangladesh</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
            @foreach($countries as $c)
            <a href="{{ route('ship-to', $c['slug']) }}" class="bg-white rounded-xl px-4 py-3 border border-slate-100 hover:border-violet-200 shadow-sm hover:shadow-md transition-all text-center text-sm font-medium text-slate-700 hover:text-violet-700">
                {{ $c['name'] }}
            </a>
            @endforeach
        </div>
    </section>
</main>
@endsection
