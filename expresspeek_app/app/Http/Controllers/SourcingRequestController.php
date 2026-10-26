<?php

namespace App\Http\Controllers;

use App\Helpers\CountryHelper;
use App\Models\CountryZone;
use App\Models\Shipment;
use App\Models\SourcingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $countries = CountryZone::query()
            ->select('country_code', DB::raw('MAX(country_name) as original_name'))
            ->whereNotNull('country_code')
            ->groupBy('country_code')
            ->get()
            ->map(fn ($country) => (object) [
                'country_code' => $country->country_code,
                'country_name' => CountryHelper::getCanonicalName($country->country_code),
            ])
            ->filter(fn ($country) => $country->country_name !== '')
            ->sortBy('country_name')
            ->values();

        return view('sourcing.create', compact('countries'));
    }

    /**
     * Store a new sourcing request.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_is_business'   => 'nullable|boolean',
            'customer_company'       => 'nullable|string|max:191',
            'customer_name'          => 'required|string|max:191',
            'customer_country'       => 'nullable|string|max:191',
            'customer_country_code'  => 'nullable|string|max:10',
            'customer_address'       => 'nullable|string|max:500',
            'customer_address2'      => 'nullable|string|max:500',
            'customer_address3'      => 'nullable|string|max:500',
            'customer_city'          => 'nullable|string|max:100',
            'customer_state'         => 'nullable|string|max:100',
            'customer_postal_code'   => 'nullable|string|max:50',
            'customer_email'         => 'nullable|email|max:191',
            'customer_phone_type'    => 'nullable|string|max:50',
            'whatsapp_country_code'  => 'required|string|max:10',
            'whatsapp_number'        => 'required|string|max:30',
            'destination_country'    => 'required|string|max:191',
            'destination_country_code' => 'nullable|string|max:10',
            'destination_address'    => 'required|string|max:500',
            'destination_address2'   => 'nullable|string|max:500',
            'destination_address3'   => 'nullable|string|max:500',
            'destination_city'       => 'required|string|max:100',
            'destination_state'      => 'nullable|string|max:100',
            'destination_postal_code'=> 'nullable|string|max:20',
            'products'               => 'required|array|min:1',
            'products.*.description' => 'required|string|max:2000',
            'products.*.link'        => 'nullable|url|max:1000',
            'products.*.image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'products.*.link.url' => 'Please enter a valid URL for the product link (make sure it starts with http:// or https://).'
        ]);

        $sourcing = null;
        if (auth()->check() && $request->filled('sourcing_request_id')) {
            $sourcing = SourcingRequest::where('id', $request->sourcing_request_id)
                ->where('user_id', auth()->id())
                ->where('status', SourcingRequest::STATUS_DRAFT)
                ->first();
        }

        if ($sourcing) {
            $sourcing->update([
                'customer_is_business'   => $data['customer_is_business'] ?? false,
                'customer_company'       => $data['customer_company'] ?? null,
                'customer_name'          => $data['customer_name'],
                'customer_country'       => $data['customer_country'] ?? null,
                'customer_country_code'  => $data['customer_country_code'] ?? null,
                'customer_address'       => $data['customer_address'] ?? null,
                'customer_address2'      => $data['customer_address2'] ?? null,
                'customer_address3'      => $data['customer_address3'] ?? null,
                'customer_city'          => $data['customer_city'] ?? null,
                'customer_state'         => $data['customer_state'] ?? null,
                'customer_postal_code'   => $data['customer_postal_code'] ?? null,
                'customer_email'         => $data['customer_email'] ?? null,
                'customer_phone_type'    => $data['customer_phone_type'] ?? null,
                'whatsapp_country_code'  => $data['whatsapp_country_code'],
                'whatsapp_number'        => $data['whatsapp_number'],
                'destination_country'    => $data['destination_country'],
                'destination_country_code' => $data['destination_country_code'] ?? null,
                'destination_address'    => $data['destination_address'],
                'destination_address2'   => $data['destination_address2'] ?? null,
                'destination_address3'   => $data['destination_address3'] ?? null,
                'destination_city'       => $data['destination_city'],
                'destination_state'      => $data['destination_state'] ?? null,
                'destination_postal_code'=> $data['destination_postal_code'] ?? null,
                'status'                 => SourcingRequest::STATUS_NEW,
            ]);
            // Clear existing draft items
            $sourcing->items()->delete();
        } else {
            $sourcing = SourcingRequest::create([
                'reference_number'       => SourcingRequest::generateReferenceNumber(),
                'tracking_number'        => Shipment::generateTrackingNumber(),
                'customer_is_business'   => $data['customer_is_business'] ?? false,
                'customer_company'       => $data['customer_company'] ?? null,
                'customer_name'          => $data['customer_name'],
                'customer_country'       => $data['customer_country'] ?? null,
                'customer_country_code'  => $data['customer_country_code'] ?? null,
                'customer_address'       => $data['customer_address'] ?? null,
                'customer_address2'      => $data['customer_address2'] ?? null,
                'customer_address3'      => $data['customer_address3'] ?? null,
                'customer_city'          => $data['customer_city'] ?? null,
                'customer_state'         => $data['customer_state'] ?? null,
                'customer_postal_code'   => $data['customer_postal_code'] ?? null,
                'customer_email'         => $data['customer_email'] ?? null,
                'customer_phone_type'    => $data['customer_phone_type'] ?? null,
                'whatsapp_country_code'  => $data['whatsapp_country_code'],
                'whatsapp_number'        => $data['whatsapp_number'],
                'destination_country'    => $data['destination_country'],
                'destination_country_code' => $data['destination_country_code'] ?? null,
                'destination_address'    => $data['destination_address'],
                'destination_address2'   => $data['destination_address2'] ?? null,
                'destination_address3'   => $data['destination_address3'] ?? null,
                'destination_city'       => $data['destination_city'],
                'destination_state'      => $data['destination_state'] ?? null,
                'destination_postal_code'=> $data['destination_postal_code'] ?? null,
                'status'                 => SourcingRequest::STATUS_NEW,
                'user_id'                => auth()->id(), // null if guest
            ]);
        }

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
