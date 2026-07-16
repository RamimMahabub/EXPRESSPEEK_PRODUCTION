<?php

namespace App\Http\Controllers;

use App\Models\CountryZone;
use App\Models\SourcingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class SourcingRequestController extends Controller
{
    /**
     * Show the public sourcing request form.
     */
    public function create()
    {
        $countries = CountryZone::select('country_name', 'country_code')
            ->distinct()
            ->orderBy('country_name')
            ->get();

        return view('sourcing.create', compact('countries'));
    }

    /**
     * Store a new sourcing request.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name'          => 'required|string|max:191',
            'whatsapp_country_code'  => 'required|string|max:10',
            'whatsapp_number'        => 'required|string|max:30',
            'destination_country'    => 'required|string|max:191',
            'destination_country_code' => 'nullable|string|max:10',
            'destination_address'    => 'required|string|max:500',
            'destination_city'       => 'required|string|max:100',
            'destination_state'      => 'nullable|string|max:100',
            'destination_postal_code'=> 'nullable|string|max:20',
            'products'               => 'required|array|min:1',
            'products.*.description' => 'required|string|max:2000',
            'products.*.link'        => 'nullable|url|max:1000',
            'products.*.image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $sourcing = SourcingRequest::create([
            'reference_number'       => SourcingRequest::generateReferenceNumber(),
            'tracking_number'        => 'EP' . strtoupper(Str::random(2)) . now()->format('ymd') . rand(1000, 9999),
            'customer_name'          => $data['customer_name'],
            'whatsapp_country_code'  => $data['whatsapp_country_code'],
            'whatsapp_number'        => $data['whatsapp_number'],
            'destination_country'    => $data['destination_country'],
            'destination_country_code' => $data['destination_country_code'] ?? null,
            'destination_address'    => $data['destination_address'],
            'destination_city'       => $data['destination_city'],
            'destination_state'      => $data['destination_state'] ?? null,
            'destination_postal_code'=> $data['destination_postal_code'] ?? null,
            'status'                 => SourcingRequest::STATUS_NEW,
            'user_id'                => auth()->id(), // null if guest
        ]);

        $manager = new ImageManager(new Driver());

        foreach ($request->file('products', []) as $index => $files) {
            if (isset($files['image']) && $files['image']->isValid()) {
                $file = $files['image'];
                
                // Read and optimize the image
                $image = $manager->read($file->getPathname());
                $image->scaleDown(width: 800);
                $encoded = $image->toWebp(quality: 80);
                
                // Store using Intervention's encoded data
                $filename = 'sourcing-images/' . Str::uuid() . '.webp';
                Storage::disk('public')->put($filename, $encoded->toString());
                
                $data['products'][$index]['image_path'] = $filename;
            }
        }

        foreach ($data['products'] as $productData) {
            $sourcing->items()->create([
                'product_description' => $productData['description'],
                'product_link'        => $productData['link'] ?? null,
                'product_image'       => $productData['image_path'] ?? null,
            ]);
        }

        return redirect()->route('sourcing.success', ['ref' => $sourcing->reference_number]);
    }

    /**
     * Show the success page after form submission.
     */
    public function success(Request $request)
    {
        $ref = $request->query('ref');
        return view('sourcing.success', compact('ref'));
    }
}
