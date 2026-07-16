<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;

class BlogController extends Controller
{
    /**
     * Blog listing page — published posts, paginated.
     */
    public function index()
    {
        $posts = BlogPost::published()
            ->orderByDesc('published_at')
            ->paginate(12);

        // Country pages for sidebar internal linking
        $countries = collect(config('seo.country-data', []))
            ->map(fn($c, $slug) => ['slug' => $slug, 'name' => $c['name']])
            ->values();

        return view('blog.index', compact('posts', 'countries'));
    }

    /**
     * Single blog post page.
     */
    public function show(string $slug)
    {
        $post = BlogPost::published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Related posts by shared tags
        $related = collect();
        if (!empty($post->tags)) {
            $related = BlogPost::published()
                ->where('id', '!=', $post->id)
                ->where(function ($q) use ($post) {
                    foreach ($post->tags as $tag) {
                        $q->orWhereJsonContains('tags', $tag);
                    }
                })
                ->orderByDesc('published_at')
                ->take(3)
                ->get();
        }

        // Country pages for sidebar
        $countries = collect(config('seo.country-data', []))
            ->map(fn($c, $slug) => ['slug' => $slug, 'name' => $c['name']])
            ->values();

        return view('blog.show', compact('post', 'related', 'countries'));
    }
}
