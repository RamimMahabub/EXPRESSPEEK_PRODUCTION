<?php

namespace App\Http\Controllers;

class CountryPageController extends Controller
{
    public function show(string $country)
    {
        $countries = config('seo.country-data', []);

        if (!isset($countries[$country])) {
            abort(404);
        }

        $data = $countries[$country];

        // Get related city pages for internal linking
        $cities = config('seo.city-data', []);

        // Get other country pages for internal linking
        $otherCountries = collect($countries)
            ->except($country)
            ->take(6)
            ->map(fn($c, $slug) => ['slug' => $slug, 'name' => $c['name']])
            ->values();

        return view('pages.ship-to', compact('data', 'country', 'cities', 'otherCountries'));
    }
}
