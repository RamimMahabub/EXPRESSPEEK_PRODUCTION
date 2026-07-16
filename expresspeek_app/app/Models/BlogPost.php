<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class BlogPost extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'seo_title',
        'seo_description',
        'tags',
        'author',
        'featured_image',
        'published_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'published_at' => 'datetime',
    ];

    /**
     * Scope: only published posts.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    /**
     * Scope: posts with a specific tag.
     */
    public function scopeTagged(Builder $query, string $tag): Builder
    {
        return $query->whereJsonContains('tags', $tag);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the SEO title, falling back to post title.
     */
    public function getSeoTitleAttribute(): string
    {
        return $this->attributes['seo_title'] ?: $this->title . ' | ExpressPeek Resources';
    }

    /**
     * Get the SEO description, falling back to excerpt.
     */
    public function getSeoDescriptionAttribute(): string
    {
        return $this->attributes['seo_description'] ?: ($this->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($this->body), 160));
    }
}
