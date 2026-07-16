<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\BlogPost;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the XML sitemap for ExpressPeek';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating sitemap...');

        $sitemap = Sitemap::create();

        // 1. Static Pages
        $staticPages = [
            '/', '/track', '/quote', '/sourcing',
            '/terms', '/privacy', '/help', '/customer-service',
            '/resources'
        ];

        foreach ($staticPages as $page) {
            $sitemap->add(Url::create($page)
                ->setPriority($page === '/' ? 1.0 : 0.8)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY));
        }

        // 2. Country Landing Pages
        $countries = array_keys(config('seo.country-data', []));
        foreach ($countries as $country) {
            $sitemap->add(Url::create("/ship-to/{$country}")
                ->setPriority(0.9)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY));
        }

        // 3. City Landing Pages
        $cities = array_keys(config('seo.city-data', []));
        foreach ($cities as $city) {
            $sitemap->add(Url::create("/ship-from/{$city}")
                ->setPriority(0.8)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY));
        }

        // 4. Blog Posts
        $posts = BlogPost::published()->get();
        foreach ($posts as $post) {
            $sitemap->add(Url::create("/resources/{$post->slug}")
                ->setLastModificationDate($post->updated_at)
                ->setPriority(0.7)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY));
        }

        $path = public_path('sitemap.xml');
        $sitemap->writeToFile($path);

        $this->info("Sitemap generated successfully at {$path}");
    }
}
