<?php

namespace App\Http\Controllers;

class CityPageController extends Controller
{
    public function show(string $city)
    {
        $cities = config('seo.city-data', []);

        if (!isset($cities[$city])) {
            abort(404);
        }

        $data = $cities[$city];

        // Get country pages for internal linking
        $countries = collect(config('seo.country-data', []))
            ->map(fn($c, $slug) => ['slug' => $slug, 'name' => $c['name'], 'transit_days' => $c['transit_days']])
            ->values();

        // Other cities for internal linking
        $otherCities = collect($cities)
            ->except($city)
            ->map(fn($c, $slug) => ['slug' => $slug, 'name' => $c['name']])
            ->values();

        return view('pages.ship-from', compact('data', 'city', 'countries', 'otherCities'));
    }
}
