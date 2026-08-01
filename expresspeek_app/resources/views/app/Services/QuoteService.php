<?php

namespace App\Services;

use App\Models\Carrier;
use App\Models\CountryZone;
use App\Models\Rate;
use Illuminate\Support\Collection;

class QuoteService
{
    protected const DATASET_CACHE_TTL_SECONDS = 900;

    private static ?array $memoryCache = null;

    protected const EXCHANGE_RATE = 122.73;

    protected const UPS_MIN_WEIGHT_KG = 25.0;
    protected const TGE_MIN_WEIGHT_KG = 5.0;

    protected const CARRIER_DISPLAY_NAMES = [
        'Master' => 'EXPRESS PEEK',
    ];

    public static function invalidatePricingDatasetCache(): void
    {
        $cacheFile = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'expresspeek_quote_dataset.php';

        self::$memoryCache = null;

        if (is_file($cacheFile)) {
            @unlink($cacheFile);
        }
    }

    protected const ABOVE_30_BANDS = [
        [
            'from' => 30.1,
            'to' => 70.0,
            'rates' => [1 => 1050.0, 2 => 1150.0, 3 => 1520.0, 4 => 1180.0, 5 => 1800.0, 6 => 1990.0, 7 => 2580.0],
        ],
        [
            'from' => 70.1,
            'to' => 300.0,
            'rates' => [1 => 1050.0, 2 => 1150.0, 3 => 1520.0, 4 => 1180.0, 5 => 1800.0, 6 => 1990.0, 7 => 2580.0],
        ],
    ];

    /**
     * Calculate shipping quotes for a given country and weight.
     *
     * @param string $countryCode ISO 2-letter country code
     * @param float $weight Weight in KG
     * @return array
     */
    public function getQuotes(string $countryCode, float $weight, string $shipmentType = 'non_document'): array
    {
        $normalizedWeight = $this->normalizeWeight($weight);
        [$carriers, $ratesByCarrier, $zoneMap] = $this->loadPricingDataset();

        $options = collect();
        $dhlBangladeshOption = null;

        foreach ($carriers as $carrier) {
            if (strtoupper(trim($carrier->name)) === 'S-DHL') {
                continue;
            }

            $carrierRates = $ratesByCarrier->get($carrier->id, collect());
            $carrierZone = $this->resolveZoneForCarrier($carrier, $countryCode, $zoneMap);
            $price = $this->calculateCarrierPrice($carrier, $carrierRates, $countryCode, $carrierZone, $normalizedWeight, $shipmentType);

            if ($price !== null && $price > 0) {
                $basePrice = $price;
                $fuelSurcharge = 0.0;
                $margin = 0.0;
                $finalPrice = $basePrice;

                $finalPriceBDT = ($carrier->currency === 'USD')
                    ? $finalPrice * self::EXCHANGE_RATE
                    : $finalPrice;

                $option = [
                    'carrier_id'     => $carrier->id,
                    'carrier'        => $this->displayName($carrier->name),
                    'shipment_type'  => $shipmentType,
                    'zone'           => $carrierZone,
                    'weight'         => $normalizedWeight,
                    'base_price'     => round($basePrice, 2),
                    'fuel_surcharge' => round($fuelSurcharge, 2),
                    'margin'         => round($margin, 2),
                    'total_price'    => round($finalPrice, 2),
                    'total_price_bdt'=> round($finalPriceBDT, 2),
                    'currency'       => $carrier->currency,
                ];

                $options->push($option);

                if ($dhlBangladeshOption === null && $this->isDhlBangladeshCarrier($carrier)) {
                    $dhlBangladeshOption = $option;
                }
            }
        }

        $sortedOptions = $options->sortBy('total_price_bdt')->values();
        $cheapest = $sortedOptions->first();
        $recommended = $dhlBangladeshOption ?? $cheapest;

        return [
            'recommended' => $recommended,
            'cheapest'    => $cheapest,
            'options'     => $sortedOptions->toArray(),
        ];
    }

    /**
     * Price each product independently and sum totals per carrier.
     *
     * @param array<int, array{country:string,type:string,weight:float}> $products
     */
    public function getQuotesForProducts(array $products): array
    {
        if (empty($products)) {
            return ['cheapest' => null, 'options' => []];
        }

        [$carriers, $ratesByCarrier, $zoneMap] = $this->loadPricingDataset();
        $options = collect();

        foreach ($carriers as $carrier) {
            if (strtoupper(trim($carrier->name)) === 'S-DHL') {
                continue;
            }

            $carrierRates = $ratesByCarrier->get($carrier->id, collect());
            $perProduct = [];
            $totalBasePrice = 0.0;
            $totalFinalPrice = 0.0;

            foreach ($products as $index => $product) {
                $countryCode = strtoupper($product['country']);
                $shipmentType = $product['type'];
                $normalizedWeight = $this->normalizeWeight((float) $product['weight']);

                $carrierZone = $this->resolveZoneForCarrier($carrier, $countryCode, $zoneMap);
                $price = $this->calculateCarrierPrice($carrier, $carrierRates, $countryCode, $carrierZone, $normalizedWeight, $shipmentType);

                if ($price === null || $price <= 0) {
                    continue 2;
                }

                $perProduct[] = [
                    'product_no' => $index + 1,
                    'country' => $countryCode,
                    'shipment_type' => $shipmentType,
                    'weight' => $normalizedWeight,
                    'price' => round($price, 2),
                ];

                $totalBasePrice += $price;
                $totalFinalPrice += $price;
            }

            $totalFinalPriceBDT = ($carrier->currency === 'USD')
                ? $totalFinalPrice * self::EXCHANGE_RATE
                : $totalFinalPrice;

            $options->push([
                'carrier_id'      => $carrier->id,
                'carrier'         => $this->displayName($carrier->name),
                'base_price'      => round($totalBasePrice, 2),
                'fuel_surcharge'  => 0.0,
                'margin'          => 0.0,
                'total_price'     => round($totalFinalPrice, 2),
                'total_price_bdt' => round($totalFinalPriceBDT, 2),
                'currency'        => $carrier->currency,
                'per_product'     => $perProduct,
            ]);
        }

        $sortedOptions = $options->sortBy('total_price_bdt')->values();

        return [
            'cheapest' => $sortedOptions->first(),
            'options' => $sortedOptions->toArray(),
            'summary' => [
                'product_count' => count($products),
                'total_input_weight' => round(array_sum(array_map(fn ($p) => (float) $p['weight'], $products)), 2),
            ],
        ];
    }

    /**
     * Normalize weight to the nearest 0.5kg.
     */
    protected function normalizeWeight(float $weight): float
    {
        return ceil($weight * 2) / 2;
    }

    /**
     * @return array{0:Collection<int,Carrier>,1:Collection<int,Collection<int,Rate>>,2:array<int,array<string,int>>}
     */
    protected function loadPricingDataset(): array
    {
        if (is_array(self::$memoryCache)) {
            return self::$memoryCache;
        }

        $cacheFile = $this->pricingDatasetCacheFile();

        if ($this->isPricingDatasetCacheFresh($cacheFile)) {
            $cachedPayload = require $cacheFile;

            if (is_array($cachedPayload)) {
                self::$memoryCache = $this->hydratePricingDataset($cachedPayload);

                return self::$memoryCache;
            }
        }

        $carriers = Carrier::query()->get(['id', 'name', 'currency']);
        $carrierIds = $carriers->pluck('id')->all();

        $ratesByCarrier = Rate::query()
            ->whereIn('carrier_id', $carrierIds)
            ->get([
                'carrier_id',
                'zone',
                'country_code',
                'country_name',
                'shipment_type',
                'weight_slab',
                'price',
                'per_kg_rate',
                'rate_type',
            ])
            ->groupBy('carrier_id');

        $zoneMap = [];

        $zones = CountryZone::query()
            ->whereIn('carrier_id', $carrierIds)
            ->get(['carrier_id', 'country_code', 'zone']);

        foreach ($zones as $zone) {
            $code = strtoupper((string) $zone->country_code);
            if ($code === '') {
                continue;
            }

            $zoneMap[$zone->carrier_id][$code] = (int) $zone->zone;
        }

        self::$memoryCache = [$carriers, $ratesByCarrier, $zoneMap];

        $this->storePricingDatasetCache($cacheFile, [
            'carriers' => $carriers->toArray(),
            'rates' => $ratesByCarrier->map(fn (Collection $group): array => $group->toArray())->all(),
            'zones' => $zoneMap,
        ]);

        return self::$memoryCache;
    }

    protected function pricingDatasetCacheFile(): string
    {
        return rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'expresspeek_quote_dataset.php';
    }

    protected function isPricingDatasetCacheFresh(string $cacheFile): bool
    {
        return is_file($cacheFile) && (time() - (int) filemtime($cacheFile) < self::DATASET_CACHE_TTL_SECONDS);
    }

    /**
     * @param array{carriers:array<int,array<string,mixed>>,rates:array<int|string,array<int,array<string,mixed>>>,zones:array<int,array<string,int>>} $payload
     * @return array{0:Collection<int,Carrier>,1:Collection<int,Collection<int,Rate>>,2:array<int,array<string,int>>}
     */
    protected function hydratePricingDataset(array $payload): array
    {
        $carriers = Carrier::hydrate($payload['carriers'] ?? []);
        $ratesByCarrier = collect($payload['rates'] ?? [])
            ->map(fn (array $rows): Collection => Rate::hydrate($rows));
        $zoneMap = is_array($payload['zones'] ?? null) ? $payload['zones'] : [];

        return [$carriers, $ratesByCarrier, $zoneMap];
    }

    /**
     * @param array<string,mixed> $payload
     */
    protected function storePricingDatasetCache(string $cacheFile, array $payload): void
    {
        $directory = dirname($cacheFile);

        if (!is_dir($directory)) {
            return;
        }

        $serialized = '<?php return ' . var_export($payload, true) . ';';

        @file_put_contents($cacheFile, $serialized, LOCK_EX);
    }

    /**
     * Resolve country zone for a specific carrier.
     */
    protected function resolveZoneForCarrier(Carrier $carrier, string $countryCode, array $zoneMap): ?int
    {
        if (!($this->isDhlBangladeshCarrier($carrier) || $this->isAramexCarrier($carrier) || $this->isFedexBangladeshCarrier($carrier))) {
            return null;
        }

        $normalizedCountryCode = strtoupper(trim($countryCode));
        $carrierZones = $zoneMap[$carrier->id] ?? [];

        $zone = $carrierZones[$normalizedCountryCode] ?? null;

        if ($zone === null && $this->isFedexBangladeshCarrier($carrier) && $normalizedCountryCode === 'UK') {
            $zone = $carrierZones['GB'] ?? null;
        }

        return $zone;
    }

    /**
     * Calculate base price for a specific carrier.
     */
    protected function calculateCarrierPrice(Carrier $carrier, Collection $rates, string $countryCode, ?int $zone, float $weight, string $shipmentType): ?float
    {
        if ($this->isUpsBangladeshCarrier($carrier) && $weight < self::UPS_MIN_WEIGHT_KG) {
            return null;
        }

        if ($this->isTgeCarrier($carrier) && (!$this->isAustraliaCountry($countryCode) || $weight < self::TGE_MIN_WEIGHT_KG)) {
            return null;
        }

        $effectiveShipmentType = $shipmentType;

        if ($zone !== null) {
            if ($effectiveShipmentType === 'non_document' && $weight > 30.0) {
                $above30Rate = $this->getAbove30PerKgRate($carrier, $rates, $zone, $weight);

                if ($above30Rate !== null) {
                    return $weight * $above30Rate;
                }
            }

            $zoneRates = $rates->filter(function (Rate $rate) use ($effectiveShipmentType, $zone): bool {
                return $rate->shipment_type === $effectiveShipmentType && (int) $rate->zone === $zone;
            });

            $rate = $zoneRates
                ->filter(function (Rate $r) use ($weight): bool {
                    $slab = (float) $r->weight_slab;
                    if ($r->rate_type === 'per_kg' && in_array($slab, [5.0, 10.0, 15.0, 20.0, 21.0, 30.0, 31.0, 40.0, 50.0, 70.0], true)) {
                        return $slab < $weight;
                    }
                    return $slab <= $weight;
                })
                ->sortByDesc('weight_slab')
                ->first();

            if ($rate) {
                if ($rate->per_kg_rate !== null) {
                    return $weight * (float) $rate->per_kg_rate;
                }

                $exactSlab = $zoneRates
                    ->filter(fn (Rate $r): bool => (float) $r->weight_slab >= $weight)
                    ->sortBy('weight_slab')
                    ->first();

                if ($exactSlab) {
                    if ($exactSlab->price !== null && (float) $exactSlab->price > 0) {
                        return (float) $exactSlab->price;
                    }

                    if ($exactSlab->per_kg_rate !== null) {
                        return $weight * (float) $exactSlab->per_kg_rate;
                    }
                }
            }
        }

        if ($effectiveShipmentType === 'document' && $weight > 1.0) {
            $docPrice = $this->calculateDocumentWeightPrice($rates, $countryCode, $weight);
            if ($docPrice !== null) {
                return $docPrice;
            }
        }

        $countryRates = $rates->filter(function (Rate $rate) use ($effectiveShipmentType, $countryCode): bool {
            return $rate->zone === null
                && $rate->shipment_type === $effectiveShipmentType
                && $this->rateMatchesCountry($rate, $countryCode);
        });

        $countryRate = $countryRates
            ->filter(function (Rate $r) use ($weight): bool {
                $slab = (float) $r->weight_slab;
                if ($r->rate_type === 'per_kg' && in_array($slab, [5.0, 10.0, 15.0, 20.0, 21.0, 30.0, 31.0, 40.0, 50.0, 70.0], true)) {
                    return $slab < $weight;
                }
                return $slab <= $weight;
            })
            ->sortByDesc('weight_slab')
            ->first();

        if ($countryRate) {
            if ($countryRate->per_kg_rate !== null) {
                if ($countryRate->rate_type === 'per_0_5_kg' && $weight > 10.0) {
                    $baseRate = $countryRates
                        ->filter(function (Rate $r): bool {
                            return (float) $r->weight_slab <= 10.0
                                && $r->price !== null
                                && (float) $r->price > 0
                                && $r->per_kg_rate === null;
                        })
                        ->sortByDesc('weight_slab')
                        ->first();

                    if ($baseRate && (float) $baseRate->price > 0) {
                        $extraWeight = $weight - (float) $baseRate->weight_slab;
                        $increments = (int) ceil($extraWeight / 0.5);
                        return (float) $baseRate->price + ($increments * (float) $countryRate->per_kg_rate);
                    }

                    return null;
                }

                return $weight * (float) $countryRate->per_kg_rate;
            }

            $exactCountrySlab = $countryRates
                ->filter(fn (Rate $r): bool => (float) $r->weight_slab >= $weight)
                ->sortBy('weight_slab')
                ->first();

            if ($exactCountrySlab) {
                return (float) $exactCountrySlab->price;
            }
        }

        return null;
    }

    /**
     * Calculate document weight with increments (for weights > 1kg).
     */
    protected function calculateDocumentWeightPrice(Collection $rates, string $countryCode, float $weight): ?float
    {
        $documentRates = $rates->filter(function (Rate $rate) use ($countryCode): bool {
            return $rate->zone === null
                && $rate->shipment_type === 'document'
                && $this->rateMatchesCountry($rate, $countryCode);
        });

        $exactRate = $documentRates->first(fn (Rate $rate): bool => (float) $rate->weight_slab === $weight);

        if ($exactRate) {
            return (float) $exactRate->price;
        }

        $baseRate = $documentRates->first(fn (Rate $rate): bool => (float) $rate->weight_slab === 1.0);

        if (!$baseRate) {
            return null;
        }

        $basePrice = (float) $baseRate->price;
        $extraWeight = $weight - 1.0;
        $increments = (int) ceil($extraWeight / 0.5);

        $nextSlab = $documentRates
            ->filter(fn (Rate $r): bool => (float) $r->weight_slab > 1.0)
            ->sortBy('weight_slab')
            ->first();

        if ($nextSlab && (float) $nextSlab->weight_slab === 1.5) {
            $incrementRate = (float) $nextSlab->price - $basePrice;
            return $basePrice + ($increments * $incrementRate);
        }

        return $basePrice;
    }

    protected function getAbove30PerKgRate(Carrier $carrier, Collection $rates, int $zone, float $weight): ?float
    {
        $dbRate = $rates
            ->filter(function (Rate $rate) use ($zone, $weight): bool {
                $slab = (float) $rate->weight_slab;
                $isApplicable = ($rate->rate_type === 'per_kg' && in_array($slab, [5.0, 10.0, 15.0, 20.0, 21.0, 30.0, 31.0, 40.0, 50.0, 70.0], true))
                    ? $slab < $weight
                    : $slab <= $weight;

                return $rate->shipment_type === 'non_document'
                    && (int) $rate->zone === $zone
                    && $rate->per_kg_rate !== null
                    && $isApplicable;
            })
            ->sortByDesc('weight_slab')
            ->first();

        if ($dbRate && $dbRate->per_kg_rate !== null) {
            return (float) $dbRate->per_kg_rate;
        }

        if ($this->isDhlBangladeshCarrier($carrier)) {
            foreach (self::ABOVE_30_BANDS as $band) {
                if ($weight >= $band['from'] && $weight <= $band['to']) {
                    return $band['rates'][$zone] ?? null;
                }
            }
        }

        return null;
    }

    protected function displayName(string $internalName): string
    {
        return self::CARRIER_DISPLAY_NAMES[$internalName] ?? $internalName;
    }

    protected function rateMatchesCountry(Rate $rate, string $countryCode): bool
    {
        $target = strtoupper(trim($countryCode));

        return strtoupper(trim((string) $rate->country_code)) === $target
            || strtoupper(trim((string) $rate->country_name)) === $target;
    }

    protected function isUpsBangladeshCarrier(Carrier $carrier): bool
    {
        $normalizedName = strtoupper(trim($carrier->name));

        return in_array($normalizedName, [
            'UPS',
            'UPS BANGLADESH',
            'UPS-BANGLADESH',
            'BANGLADESH UPS',
            'BANGLADESH-UPS',
            'BD UPS',
            'BD-UPS',
        ], true);
    }

    protected function isTgeCarrier(Carrier $carrier): bool
    {
        $normalizedName = strtoupper(trim($carrier->name));

        return in_array($normalizedName, [
            'TGE',
            'TEAM GLOBAL EXPRESS',
        ], true);
    }

    protected function isDhlBangladeshCarrier(Carrier $carrier): bool
    {
        $normalizedName = strtoupper(trim($carrier->name));

        return in_array($normalizedName, [
            'DHL-BANGLADESH',
            'DHL BANGLADESH',
            'BANGLADESH DHL',
            'BD DHL',
        ], true);
    }

    protected function isAramexCarrier(Carrier $carrier): bool
    {
        $normalizedName = strtoupper(trim($carrier->name));

        return in_array($normalizedName, [
            'ARAMEX',
            'ARAMEX BANGLADESH',
            'BANGLADESH ARAMEX',
        ], true);
    }

    protected function isFedexBangladeshCarrier(Carrier $carrier): bool
    {
        $normalizedName = strtoupper(trim($carrier->name));

        return in_array($normalizedName, [
            'FEDEX BANGLADESH',
            'FEDEX-BANGLADESH',
            'BANGLADESH FEDEX',
            'BD FEDEX',
        ], true);
    }

    protected function isAustraliaCountry(string $countryCode): bool
    {
        $normalizedCountry = strtoupper(trim($countryCode));

        return in_array($normalizedCountry, ['AU', 'AUSTRALIA'], true);
    }
}
