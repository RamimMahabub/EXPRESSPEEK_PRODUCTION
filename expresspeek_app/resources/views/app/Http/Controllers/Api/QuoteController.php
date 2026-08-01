<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\QuoteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuoteController extends Controller
{
    protected $quoteService;

    public function __construct(QuoteService $quoteService)
    {
        $this->quoteService = $quoteService;
    }

    /**
     * Get shipping quotes.
     * 
     * POST /api/quote
     * {
     *   "country": "UK",
     *   "weight": 2.5,
     *   "type": "non_document"
     * }
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country' => 'nullable|string|size:2',
            'weight' => 'nullable|numeric|min:0',
            'type' => 'nullable|string|in:document,non_document',
            'products' => 'nullable|array|min:1',
            'products.*.country' => 'required_with:products|string|size:2',
            'products.*.type' => 'required_with:products|string|in:document,non_document',
            'products.*.weight' => 'required_with:products|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $products = $request->input('products', []);

        if (empty($products)) {
            if (!$request->filled('country') || !$request->filled('type') || !$request->filled('weight')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please provide country, shipment type and weight.'
                ], 422);
            }

            $products = [[
                'country' => $this->normalizeCountryCode($request->input('country')),
                'type' => $request->input('type'),
                'weight' => (float) $request->input('weight'),
            ]];
        } else {
            $products = collect($products)->map(function ($product) {
                return [
                    'country' => $this->normalizeCountryCode($product['country']),
                    'type' => $product['type'],
                    'weight' => (float) $product['weight'],
                ];
            })->all();
        }

        foreach ($products as $product) {
            if ($product['type'] === 'non_document' && $product['weight'] > 300.0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non-document rates are available up to 300 KG only. Please contact support for higher weights.'
                ], 422);
            }
        }

        $result = count($products) === 1
            ? $this->quoteService->getQuotes($products[0]['country'], $products[0]['weight'], $products[0]['type'])
            : $this->quoteService->getQuotesForProducts($products);

        if (empty($result['options'])) {
            return response()->json([
                'success' => false,
                'message' => 'No shipping rates found for the specified destination and weight.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    private function normalizeCountryCode(string $countryCode): string
    {
        $code = strtoupper(trim($countryCode));

        // Legacy/typo alias support from historical country-zone mappings.
        $aliases = [
            'KO' => 'KR',
        ];

        return $aliases[$code] ?? $code;
    }
}
