<?php

namespace App\Services;

use App\Models\Carrier;
use App\Models\CountryZone;
use App\Models\Rate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Symfony\Component\Process\Process;

class MonthlyRateImportService
{
    /**
     * Parse both monthly Excel files and import rates in one transaction.
     *
     * @return array<string, mixed>
     */
    // ─────────────────────────────────────────────────────────────────────────
    // Convenience: import ALL carriers at once (legacy / one-click flow)
    // ─────────────────────────────────────────────────────────────────────────

    public function importFromExcelFiles(
        string $dhlFilePath, 
        ?string $dhlZoneFilePath, 
        string $masterFilePath,
        ?string $aramexZoneFilePath = null,
        ?string $aramexUpTo10FilePath = null,
        ?string $aramexAbove10FilePath = null,
        ?string $upsFilePath = null,
        ?string $ocsFilePath = null,
        ?string $tgeFilePath = null,
        ?string $fedexZoneFilePath = null,
        ?string $fedexRatesFilePath = null
    ): array
    {
        $parsed = $this->runParser([
            'dhl_rate'      => $dhlFilePath,
            'dhl_zone'      => $dhlZoneFilePath,
            'master'        => $masterFilePath,
            'aramex_zone'   => $aramexZoneFilePath,
            'aramex_upto10' => $aramexUpTo10FilePath,
            'aramex_above10'=> $aramexAbove10FilePath,
            'ups'           => $upsFilePath,
            'ocs'           => $ocsFilePath,
            'tge'           => $tgeFilePath,
            'fedex_zone'    => $fedexZoneFilePath,
            'fedex_rates'   => $fedexRatesFilePath,
        ]);

        $summary = DB::transaction(function () use ($parsed, $dhlZoneFilePath, $ocsFilePath, $tgeFilePath, $fedexZoneFilePath, $fedexRatesFilePath) {
            $summary = $this->blankSummary();

            if ($dhlZoneFilePath !== null) {
                $summary['dhl_zones'] = $this->importDhlZones($parsed['dhl_zones'] ?? []);
            } else {
                $summary['dhl_zones']['skipped'] = 1;
            }
            $summary['dhl']    = $this->importDhlRates($parsed['dhl'] ?? []);
            $summary['master'] = $this->importMasterRates($parsed['master'] ?? []);

            if (isset($parsed['aramex_zones'])) {
                $summary['aramex_zones'] = $this->importAramexZones($parsed['aramex_zones']);
            }
            if (isset($parsed['aramex_upto10']) || isset($parsed['aramex_above10'])) {
                $summary['aramex'] = $this->importAramexRates($parsed['aramex_upto10'] ?? [], $parsed['aramex_above10'] ?? []);
            }
            if (isset($parsed['ups'])) {
                $summary['ups'] = $this->importUpsRates($parsed['ups']);
            }
            if ($ocsFilePath !== null && !empty($parsed['ocs']['rates'] ?? [])) {
                $summary['ocs'] = $this->importOcsRates($parsed['ocs']);
            }
            if ($tgeFilePath !== null && !empty($parsed['tge'] ?? [])) {
                $summary['tge'] = $this->importTgeRates($parsed['tge']);
            }
            if ($fedexZoneFilePath !== null && !empty($parsed['fedex_zones']['zones'] ?? [])) {
                $summary['fedex_zones'] = $this->importFedexZones($parsed['fedex_zones']);
            }
            if ($fedexRatesFilePath !== null && (!empty($parsed['fedex']['document'] ?? []) || !empty($parsed['fedex']['non_document'] ?? []))) {
                $summary['fedex'] = $this->importFedexRates($parsed['fedex']);
            }

            Log::info('Monthly rate import (all carriers) completed.', ['summary' => $summary]);

            return $summary;
        });

        QuoteService::invalidatePricingDatasetCache();

        return $summary;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Individual carrier import methods (one carrier at a time)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Import DHL Bangladesh rates (and optionally zones) in isolation.
     */
    public function importDhlCarrier(string $dhlRateFilePath, ?string $dhlZoneFilePath): array
    {
        $parsed = $this->runParser([
            'dhl_rate' => $dhlRateFilePath,
            'dhl_zone' => $dhlZoneFilePath,
        ]);

        $summary = DB::transaction(function () use ($parsed, $dhlZoneFilePath) {
            $summary = $this->blankSummary();

            if ($dhlZoneFilePath !== null) {
                $summary['dhl_zones'] = $this->importDhlZones($parsed['dhl_zones'] ?? []);
            }
            $summary['dhl'] = $this->importDhlRates($parsed['dhl'] ?? []);

            Log::info('DHL rate import completed.', ['summary' => $summary]);
            return $summary;
        });

        QuoteService::invalidatePricingDatasetCache();

        return $summary;
    }

    /**
     * Import Master Air Agent rates in isolation.
     */
    public function importMasterCarrier(string $masterFilePath): array
    {
        $parsed = $this->runParser([
            'master' => $masterFilePath,
        ]);

        $summary = DB::transaction(function () use ($parsed) {
            $summary = $this->blankSummary();
            $summary['master'] = $this->importMasterRates($parsed['master'] ?? []);

            Log::info('Master rate import completed.', ['summary' => $summary]);
            return $summary;
        });

        QuoteService::invalidatePricingDatasetCache();

        return $summary;
    }

    /**
     * Import Aramex rates (zones + up-to-10 + above-10) in isolation.
     */
    public function importAramexCarrier(
        ?string $aramexZoneFilePath,
        ?string $aramexUpTo10FilePath,
        ?string $aramexAbove10FilePath
    ): array {
        $parsed = $this->runParser([
            'aramex_zone'    => $aramexZoneFilePath,
            'aramex_upto10'  => $aramexUpTo10FilePath,
            'aramex_above10' => $aramexAbove10FilePath,
        ]);

        $summary = DB::transaction(function () use ($parsed) {
            $summary = $this->blankSummary();

            if (!empty($parsed['aramex_zones'])) {
                $summary['aramex_zones'] = $this->importAramexZones($parsed['aramex_zones']);
            }
            if (!empty($parsed['aramex_upto10']) || !empty($parsed['aramex_above10'])) {
                $summary['aramex'] = $this->importAramexRates($parsed['aramex_upto10'] ?? [], $parsed['aramex_above10'] ?? []);
            }

            Log::info('Aramex rate import completed.', ['summary' => $summary]);
            return $summary;
        });

        QuoteService::invalidatePricingDatasetCache();

        return $summary;
    }

    /**
     * Import UPS Bangladesh rates in isolation.
     */
    public function importUpsCarrier(string $upsFilePath): array
    {
        $parsed = $this->runParser(['ups' => $upsFilePath]);

        $summary = DB::transaction(function () use ($parsed) {
            $summary = $this->blankSummary();
            $summary['ups'] = $this->importUpsRates($parsed['ups'] ?? []);

            Log::info('UPS rate import completed.', ['summary' => $summary]);
            return $summary;
        });

        QuoteService::invalidatePricingDatasetCache();

        return $summary;
    }

    /**
     * Import OCS Japan rates in isolation.
     */
    public function importOcsCarrier(string $ocsFilePath): array
    {
        $parsed = $this->runParser(['ocs' => $ocsFilePath]);

        $summary = DB::transaction(function () use ($parsed) {
            $summary = $this->blankSummary();
            if (!empty($parsed['ocs']['rates'] ?? [])) {
                $summary['ocs'] = $this->importOcsRates($parsed['ocs']);
            }

            Log::info('OCS rate import completed.', ['summary' => $summary]);
            return $summary;
        });

        QuoteService::invalidatePricingDatasetCache();

        return $summary;
    }

    /**
     * Import TGE Australia rates in isolation.
     */
    public function importTgeCarrier(string $tgeFilePath): array
    {
        $parsed = $this->runParser(['tge' => $tgeFilePath]);

        $summary = DB::transaction(function () use ($parsed) {
            $summary = $this->blankSummary();
            if (!empty($parsed['tge'] ?? [])) {
                $summary['tge'] = $this->importTgeRates($parsed['tge']);
            }

            Log::info('TGE rate import completed.', ['summary' => $summary]);
            return $summary;
        });

        QuoteService::invalidatePricingDatasetCache();

        return $summary;
    }

    /**
     * Import FedEx Bangladesh rates (zones + rates) in isolation.
     */
    public function importFedexCarrier(?string $fedexZoneFilePath, ?string $fedexRatesFilePath): array
    {
        $parsed = $this->runParser([
            'fedex_zone'  => $fedexZoneFilePath,
            'fedex_rates' => $fedexRatesFilePath,
        ]);

        $summary = DB::transaction(function () use ($parsed, $fedexZoneFilePath, $fedexRatesFilePath) {
            $summary = $this->blankSummary();

            if ($fedexZoneFilePath !== null && !empty($parsed['fedex_zones']['zones'] ?? [])) {
                $summary['fedex_zones'] = $this->importFedexZones($parsed['fedex_zones']);
            }
            if ($fedexRatesFilePath !== null && (!empty($parsed['fedex']['document'] ?? []) || !empty($parsed['fedex']['non_document'] ?? []))) {
                $summary['fedex'] = $this->importFedexRates($parsed['fedex']);
            }

            Log::info('FedEx rate import completed.', ['summary' => $summary]);
            return $summary;
        });

        QuoteService::invalidatePricingDatasetCache();

        return $summary;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Internal helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Run the Python parser with only the specified file paths.
     * Keys map to CLI flags: dhl_rate → --dhl-rate, fedex_zone → --fedex-zone, etc.
     *
     * @param  array<string, string|null> $files
     * @return array<string, mixed>
     */
    private function runParser(array $files): array
    {
        $tempOutputPath = storage_path('app/rate-import/latest_parsed_rates.json');
        $outputDir = dirname($tempOutputPath);

        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        $pythonBinary = $this->resolvePythonBinary();
        $scriptPath   = base_path('parse_monthly_rates.py');

        if (!file_exists($scriptPath)) {
            throw new RuntimeException('Rate parser script not found: parse_monthly_rates.py');
        }

        // Map internal keys to CLI flags
        $flagMap = [
            'dhl_rate'       => '--dhl-rate',
            'dhl_zone'       => '--dhl-zone',
            'master'         => '--master',
            'aramex_zone'    => '--aramex-zone',
            'aramex_upto10'  => '--aramex-upto10',
            'aramex_above10' => '--aramex-above10',
            'ups'            => '--ups',
            'ocs'            => '--ocs',
            'tge'            => '--tge',
            'fedex_zone'     => '--fedex-zone',
            'fedex_rates'    => '--fedex-rates',
        ];

        $processArgs = [$pythonBinary, $scriptPath];

        foreach ($flagMap as $key => $flag) {
            if (!empty($files[$key])) {
                $processArgs[] = $flag;
                $processArgs[] = $files[$key];
            }
        }

        $processArgs[] = '--output';
        $processArgs[] = $tempOutputPath;

        $process = new Process($processArgs);
        $process->setTimeout(120);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException('Excel parsing failed: ' . trim($process->getErrorOutput() ?: $process->getOutput()));
        }

        $parsed = json_decode((string) file_get_contents($tempOutputPath), true);

        if (!is_array($parsed)) {
            throw new RuntimeException('Parser returned invalid JSON structure.');
        }

        return $parsed;
    }

    /**
     * Return a zeroed-out summary array so callers don't have to repeat the structure.
     *
     * @return array<string, array<string, int>>
     */
    private function blankSummary(): array
    {
        return [
            'dhl'         => ['deleted' => 0, 'inserted' => 0, 'zones' => 0, 'weights' => 0, 'per_kg_bands' => 0],
            'dhl_zones'   => ['deleted' => 0, 'inserted' => 0, 'countries' => 0, 'skipped' => 0],
            'master'      => ['deleted' => 0, 'inserted' => 0, 'providers' => 0, 'countries' => 0, 'skipped_countries' => 0],
            'aramex_zones'=> ['deleted' => 0, 'inserted' => 0, 'skipped' => 0],
            'aramex'      => ['deleted' => 0, 'inserted' => 0],
            'ups'         => ['deleted' => 0, 'inserted' => 0],
            'ocs'         => ['deleted' => 0, 'inserted' => 0],
            'tge'         => ['deleted' => 0, 'inserted' => 0],
            'fedex_zones' => ['deleted' => 0, 'inserted' => 0, 'skipped' => 0],
            'fedex'       => ['deleted' => 0, 'inserted' => 0],
        ];
    }

    private function importAramexZones(array $zoneData): array
    {
        $carrier = Carrier::firstOrCreate(
            ['name' => 'ARAMEX'],
            [
                'fuel_surcharge_percent' => 0,
                'profit_margin_percent' => 0,
                'currency' => 'USD',
            ]
        );

        $deleted = CountryZone::where('carrier_id', $carrier->id)->delete();
        $inserted = 0;
        $skipped = 0;

        $countryMap = $this->countryCodeMap();

        foreach (($zoneData['zones'] ?? []) as $zoneRow) {
            $countryName = trim((string) ($zoneRow['country_name'] ?? ''));
            $zone = isset($zoneRow['zone']) ? (int) $zoneRow['zone'] : null;

            if ($countryName === '' || $zone === null) {
                continue;
            }

            $normalizedKey = $this->normalizeCountryName($countryName);
            $countryCode = $countryMap[$normalizedKey] ?? null;

            if ($countryCode === null) {
                $skipped++;
                continue;
            }

            CountryZone::updateOrCreate([
'country_code' => $countryCode,
'carrier_id' => $carrier->id
], [
'country_name' => $countryName,
'zone' => $zone
]);
            $inserted++;
        }

        return [
            'deleted' => $deleted,
            'inserted' => $inserted,
            'skipped' => $skipped,
        ];
    }

    private function importAramexRates(array $upTo10Data, array $above10Data): array
    {
        $carrier = Carrier::firstOrCreate(
            ['name' => 'ARAMEX'],
            [
                'fuel_surcharge_percent' => 0,
                'profit_margin_percent' => 0,
                'currency' => 'BDT',
            ]
        );

        $deleted = Rate::where('carrier_id', $carrier->id)->delete();
        $inserted = 0;

        // Process Up To 10 (Zone-based)
        $docRows = $upTo10Data['document'] ?? [];
        $docIncrements = null;
        
        foreach ($docRows as $row) {
            $weight = $row['weight'] ?? 0;
            $zones = $row['zones'] ?? [];
            if ($weight === 'ADD 0.5') {
                $docIncrements = $zones;
                continue;
            }
            foreach ($zones as $zone => $price) {
                Rate::create([
                    'carrier_id' => $carrier->id,
                    'zone' => (int) $zone,
                    'shipment_type' => 'document',
                    'weight_slab' => (float) $weight,
                    'price' => (float) $price,
                ]);
                $inserted++;
            }
        }

        // Apply ADD 0.5 up to 2.0kg (from 0.5kg base)
        if ($docIncrements !== null) {
            $baseRow = collect($docRows)->firstWhere('weight', 0.5);
            if ($baseRow) {
                for ($w = 1.0; $w <= 2.0; $w += 0.5) {
                    $increments = ($w - 0.5) / 0.5;
                    foreach ($docIncrements as $zone => $incPrice) {
                        $basePrice = $baseRow['zones'][$zone] ?? 0;
                        if ($basePrice > 0) {
                            Rate::create([
                                'carrier_id' => $carrier->id,
                                'zone' => (int) $zone,
                                'shipment_type' => 'document',
                                'weight_slab' => (float) $w,
                                'price' => $basePrice + ($increments * $incPrice),
                            ]);
                            $inserted++;
                        }
                    }
                }
            }
        }

        $nonDocRows = $upTo10Data['non_document'] ?? [];
        foreach ($nonDocRows as $row) {
            $weight = (float) ($row['weight'] ?? 0);
            $zones = $row['zones'] ?? [];
            foreach ($zones as $zone => $price) {
                Rate::create([
                    'carrier_id' => $carrier->id,
                    'zone' => (int) $zone,
                    'shipment_type' => 'non_document',
                    'weight_slab' => $weight,
                    'price' => (float) $price,
                ]);
                $inserted++;
            }
        }

        // Process Above 10 (Country-based per-kg rates)
        $countryMap = $this->countryCodeMap();
        foreach ($above10Data as $bandData) {
            $weightBand = (float) ($bandData['weight_band'] ?? 0);
            $rates = $bandData['rates'] ?? [];
            
            foreach ($rates as $countryName => $price) {
                $normalizedKey = $this->normalizeCountryName($countryName);
                $countryCode = $countryMap[$normalizedKey] ?? null;
                
                if ($countryCode === null) {
                    continue;
                }
                
                Rate::create([
                    'carrier_id' => $carrier->id,
                    'zone' => null,
                    'country_code' => $countryCode,
                    'country_name' => $countryName,
                    'shipment_type' => 'non_document',
                    'weight_slab' => $weightBand,
                    'price' => 0,
                    'per_kg_rate' => (float) $price,
                    'rate_type' => 'per_kg',
                ]);
                $inserted++;
            }
        }

        return [
            'deleted' => $deleted,
            'inserted' => $inserted,
        ];
    }

    private function importUpsRates(array $upsData): array
    {
        $carrier = Carrier::firstOrCreate(
            ['name' => 'UPS Bangladesh'],
            [
                'fuel_surcharge_percent' => 0,
                'profit_margin_percent' => 0,
                'currency' => 'BDT',
            ]
        );

        $deleted = Rate::where('carrier_id', $carrier->id)->delete();
        $inserted = 0;

        $countryMap = $this->countryCodeMap();

        foreach ($upsData as $bandData) {
            $weightBand = (float) ($bandData['weight_band'] ?? 0);
            $rates = $bandData['rates'] ?? [];
            
            foreach ($rates as $countryName => $price) {
                $normalizedKey = $this->normalizeCountryName($countryName);
                $countryCode = $countryMap[$normalizedKey] ?? null;
                
                // Support 2-letter ISO codes directly
                if ($countryCode === null && strlen($normalizedKey) === 2 && preg_match('/^[A-Z]{2}$/', $normalizedKey)) {
                    $countryCode = $normalizedKey;
                }
                
                if ($countryCode === null) {
                    continue;
                }
                
                Rate::create([
                    'carrier_id' => $carrier->id,
                    'zone' => null,
                    'country_code' => $countryCode,
                    'country_name' => $countryName,
                    'shipment_type' => 'non_document',
                    'weight_slab' => $weightBand,
                    'price' => 0,
                    'per_kg_rate' => (float) $price,
                    'rate_type' => 'per_kg',
                ]);
                $inserted++;
            }
        }

        return [
            'deleted' => $deleted,
            'inserted' => $inserted,
        ];
    }

    private function importFedexZones(array $zoneData): array
    {
        $carrier = Carrier::firstOrCreate(
            ['name' => 'FedEx Bangladesh'],
            [
                'fuel_surcharge_percent' => 0,
                'profit_margin_percent' => 0,
                'currency' => 'BDT',
            ]
        );

        $deleted = CountryZone::where('carrier_id', $carrier->id)->delete();
        $inserted = 0;
        $skipped = 0;

        $countryMap = $this->countryCodeMap();

        foreach (($zoneData['zones'] ?? []) as $zoneRow) {
            $countryName = trim((string) ($zoneRow['country_name'] ?? ''));
            $zone = isset($zoneRow['zone']) ? (int) $zoneRow['zone'] : null;

            if ($countryName === '' || $zone === null) {
                continue;
            }

            $normalizedKey = $this->normalizeCountryName($countryName);
            $countryCode = $countryMap[$normalizedKey] ?? null;

            // Also try matching common FedEx-specific name variants
            if ($countryCode === null) {
                // Try with 'Is.' suffix removal or common abbreviations
                $simplifiedKey = $this->normalizeCountryName(
                    preg_replace('/\bIs\.$/i', 'Islands', $countryName)
                );
                $countryCode = $countryMap[$simplifiedKey] ?? null;
            }

            if ($countryCode === null) {
                Log::debug('FedEx zone: no ISO code for country', ['name' => $countryName]);
                $skipped++;
                continue;
            }

            CountryZone::updateOrCreate(
                ['country_code' => $countryCode, 'carrier_id' => $carrier->id],
                ['country_name' => $countryName, 'zone' => $zone]
            );
            $inserted++;
        }

        return [
            'deleted' => $deleted,
            'inserted' => $inserted,
            'skipped' => $skipped,
        ];
    }

    private function importFedexRates(array $fedexData): array
    {
        $carrier = Carrier::firstOrCreate(
            ['name' => 'FedEx Bangladesh'],
            [
                'fuel_surcharge_percent' => 0,
                'profit_margin_percent' => 0,
                'currency' => 'BDT',
            ]
        );

        $deleted = Rate::where('carrier_id', $carrier->id)->delete();
        $inserted = 0;

        // Document slabs
        foreach (($fedexData['document'] ?? []) as $row) {
            $weight = (float) ($row['weight'] ?? 0);
            foreach (($row['zones'] ?? []) as $zone => $price) {
                if ($price === null) {
                    continue;
                }
                Rate::create([
                    'carrier_id' => $carrier->id,
                    'zone'       => (int) $zone,
                    'country_code' => null,
                    'country_name' => null,
                    'shipment_type' => 'document',
                    'weight_slab'   => $weight,
                    'price'         => (float) $price,
                    'per_kg_rate'   => null,
                    'rate_type'     => null,
                ]);
                $inserted++;
            }
        }

        // Non-document slabs
        foreach (($fedexData['non_document'] ?? []) as $row) {
            $weight = (float) ($row['weight'] ?? 0);
            foreach (($row['zones'] ?? []) as $zone => $price) {
                if ($price === null) {
                    continue;
                }
                Rate::create([
                    'carrier_id' => $carrier->id,
                    'zone'       => (int) $zone,
                    'country_code' => null,
                    'country_name' => null,
                    'shipment_type' => 'non_document',
                    'weight_slab'   => $weight,
                    'price'         => (float) $price,
                    'per_kg_rate'   => null,
                    'rate_type'     => null,
                ]);
                $inserted++;
            }
        }

        // Per-kg bands (10kg+)
        foreach (($fedexData['per_kg_bands'] ?? []) as $band) {
            $fromWeight = (float) ($band['from_weight'] ?? 0);
            foreach (($band['zones'] ?? []) as $zone => $perKgRate) {
                if ($perKgRate === null) {
                    continue;
                }
                Rate::create([
                    'carrier_id' => $carrier->id,
                    'zone'       => (int) $zone,
                    'country_code' => null,
                    'country_name' => null,
                    'shipment_type' => 'non_document',
                    'weight_slab'   => $fromWeight,
                    'price'         => 0,
                    'per_kg_rate'   => (float) $perKgRate,
                    'rate_type'     => 'per_kg',
                ]);
                $inserted++;
            }
        }

        return [
            'deleted' => $deleted,
            'inserted' => $inserted,
        ];
    }

    private function importOcsRates(array $ocsData): array
    {
        $carrier = Carrier::firstOrCreate(
            ['name' => 'OCS'],
            [
                'fuel_surcharge_percent' => 0,
                'profit_margin_percent' => 0,
                'currency' => 'BDT',
            ]
        );

        $deleted = Rate::where('carrier_id', $carrier->id)->delete();
        $inserted = 0;

        $countryCode = strtoupper((string) ($ocsData['country_code'] ?? 'JP'));
        $countryName = trim((string) ($ocsData['country_name'] ?? 'Japan')) ?: 'Japan';

        foreach (($ocsData['rates'] ?? []) as $row) {
            $weight = isset($row['weight']) ? (float) $row['weight'] : null;
            $price = isset($row['price']) ? (float) $row['price'] : null;

            if ($weight === null || $price === null) {
                continue;
            }

            Rate::create([
                'carrier_id' => $carrier->id,
                'zone' => null,
                'country_code' => $countryCode,
                'country_name' => $countryName,
                'shipment_type' => 'non_document',
                'weight_slab' => $weight,
                'price' => $price,
                'per_kg_rate' => null,
                'rate_type' => null,
            ]);
            $inserted++;
        }

        if (!empty($ocsData['add_0_5'])) {
            Rate::create([
                'carrier_id' => $carrier->id,
                'zone' => null,
                'country_code' => $countryCode,
                'country_name' => $countryName,
                'shipment_type' => 'non_document',
                'weight_slab' => 10.5,
                'price' => 0,
                'per_kg_rate' => (float) $ocsData['add_0_5'],
                'rate_type' => 'per_0_5_kg',
            ]);
            $inserted++;
        }

        if (!empty($ocsData['per_21_kg'])) {
            Rate::create([
                'carrier_id' => $carrier->id,
                'zone' => null,
                'country_code' => $countryCode,
                'country_name' => $countryName,
                'shipment_type' => 'non_document',
                'weight_slab' => 21.0,
                'price' => 0,
                'per_kg_rate' => (float) $ocsData['per_21_kg'],
                'rate_type' => 'per_kg',
            ]);
            $inserted++;
        }

        if (!empty($ocsData['per_31_kg'])) {
            Rate::create([
                'carrier_id' => $carrier->id,
                'zone' => null,
                'country_code' => $countryCode,
                'country_name' => $countryName,
                'shipment_type' => 'non_document',
                'weight_slab' => 31.0,
                'price' => 0,
                'per_kg_rate' => (float) $ocsData['per_31_kg'],
                'rate_type' => 'per_kg',
            ]);
            $inserted++;
        }

        return [
            'deleted' => $deleted,
            'inserted' => $inserted,
        ];
    }

    private function importTgeRates(array $tgeData): array
    {
        $carrier = Carrier::firstOrCreate(
            ['name' => 'TGE'],
            [
                'fuel_surcharge_percent' => 0,
                'profit_margin_percent' => 0,
                'currency' => 'BDT',
            ]
        );

        $deleted = Rate::where('carrier_id', $carrier->id)->delete();
        $inserted = 0;

        foreach ($tgeData as $bandData) {
            $weightBand = (float) ($bandData['weight_band'] ?? 0);
            $rates = $bandData['rates'] ?? [];
            $price = isset($rates['Australia']) ? (float) $rates['Australia'] : null;

            if ($weightBand <= 0 || $price === null) {
                continue;
            }

            Rate::create([
                'carrier_id' => $carrier->id,
                'zone' => null,
                'country_code' => 'AU',
                'country_name' => 'Australia',
                'shipment_type' => 'non_document',
                'weight_slab' => $weightBand,
                'price' => 0,
                'per_kg_rate' => $price,
                'rate_type' => 'per_kg',
            ]);
            $inserted++;
        }

        return [
            'deleted' => $deleted,
            'inserted' => $inserted,
        ];
    }

    /**
     * @param array<string, mixed> $zoneData
     * @return array<string, int>
     */
    private function importDhlZones(array $zoneData): array
    {
        $carrier = Carrier::firstOrCreate(
            ['name' => 'DHL-Bangladesh'],
            [
                'fuel_surcharge_percent' => 0,
                'profit_margin_percent' => 0,
                'currency' => 'BDT',
            ]
        );

        // Keep only the latest DHL-specific zone map.
        $deleted = CountryZone::where('carrier_id', $carrier->id)->delete();

        $inserted = 0;
        foreach (($zoneData['zones'] ?? []) as $zoneRow) {
            $countryCode = strtoupper((string) ($zoneRow['country_code'] ?? ''));
            $countryName = trim((string) ($zoneRow['country_name'] ?? ''));
            $zone = isset($zoneRow['zone']) ? (int) $zoneRow['zone'] : null;

            if ($countryCode === '' || $countryName === '' || $zone === null) {
                continue;
            }

            CountryZone::updateOrCreate(
                [
                    'country_code' => $countryCode,
                    'carrier_id' => $carrier->id,
                ],
                [
                    'country_name' => $countryName,
                    'zone' => $zone,
                ]
            );
            $inserted++;
        }

        return [
            'deleted' => $deleted,
            'inserted' => $inserted,
            'countries' => $inserted,
        ];
    }

    /**
     * @param array<string, mixed> $dhlData
     * @return array<string, int>
     */
    private function importDhlRates(array $dhlData): array
    {
        $carrier = Carrier::firstOrCreate(
            ['name' => 'DHL-Bangladesh'],
            [
                'fuel_surcharge_percent' => 0,
                'profit_margin_percent' => 0,
                'currency' => 'BDT',
            ]
        );

        $deleted = Rate::where('carrier_id', $carrier->id)
            ->whereNotNull('zone')
            ->delete();

        $inserted = 0;

        $documentRows = $dhlData['document'] ?? [];
        foreach ($documentRows as $row) {
            $weight = (float) ($row['weight'] ?? 0);
            $zones = $row['zones'] ?? [];

            foreach ($zones as $zone => $price) {
                if ($price === null) {
                    continue;
                }

                Rate::create([
                    'carrier_id' => $carrier->id,
                    'zone' => (int) $zone,
                    'country_code' => null,
                    'country_name' => null,
                    'shipment_type' => 'document',
                    'weight_slab' => $weight,
                    'price' => (float) $price,
                    'per_kg_rate' => null,
                    'rate_type' => null,
                ]);
                $inserted++;
            }
        }

        $nonDocumentRows = $dhlData['non_document'] ?? [];
        foreach ($nonDocumentRows as $row) {
            $weight = (float) ($row['weight'] ?? 0);
            $zones = $row['zones'] ?? [];

            foreach ($zones as $zone => $price) {
                if ($price === null) {
                    continue;
                }

                Rate::create([
                    'carrier_id' => $carrier->id,
                    'zone' => (int) $zone,
                    'country_code' => null,
                    'country_name' => null,
                    'shipment_type' => 'non_document',
                    'weight_slab' => $weight,
                    'price' => (float) $price,
                    // The 30kg slab stores the default per-kg rate fallback if present.
                    'per_kg_rate' => ($weight === 30.0)
                        ? $this->firstPerKgBandRateForZone($dhlData['per_kg_bands'] ?? [], (int) $zone)
                        : null,
                    'rate_type' => null,
                ]);
                $inserted++;
            }
        }

        foreach (($dhlData['per_kg_bands'] ?? []) as $band) {
            $from = isset($band['from']) ? (float) $band['from'] : null;
            $zones = $band['zones'] ?? [];

            if ($from === null) {
                continue;
            }

            foreach ($zones as $zone => $perKgRate) {
                if ($perKgRate === null) {
                    continue;
                }

                Rate::create([
                    'carrier_id' => $carrier->id,
                    'zone' => (int) $zone,
                    'country_code' => null,
                    'country_name' => null,
                    'shipment_type' => 'non_document',
                    'weight_slab' => $from,
                    'price' => 0,
                    'per_kg_rate' => (float) $perKgRate,
                    'rate_type' => 'per_kg',
                ]);
                $inserted++;
            }
        }

        return [
            'deleted' => $deleted,
            'inserted' => $inserted,
            'zones' => count(array_keys($documentRows[0]['zones'] ?? ($nonDocumentRows[0]['zones'] ?? []))),
            'weights' => count($documentRows) + count($nonDocumentRows),
            'per_kg_bands' => count($dhlData['per_kg_bands'] ?? []),
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $bands
     */
    private function firstPerKgBandRateForZone(array $bands, int $zone): ?float
    {
        if (empty($bands)) {
            return null;
        }

        $firstBand = $bands[0];
        $zones = $firstBand['zones'] ?? [];

        if (!isset($zones[$zone])) {
            return null;
        }

        return (float) $zones[$zone];
    }

    /**
     * @param array<string, mixed> $masterData
     * @return array<string, int>
     */
    private function importMasterRates(array $masterData): array
    {
        $providerMap = [
            'Singapore-DHL' => 'Singapore-DHL',
            'Singapore-UPS' => 'Singapore-UPS',
            'DUBAI-DHL' => 'DUBAI-DHL',
            'DUBAI-UPS' => 'DUBAI-UPS',
            'Singapore-FedEx' => 'Singapore-FedEx',
            'DUBAI-FedEx' => 'DUBAI-FedEx',
            'Master' => 'Master',
            'Master-SF' => 'Master-SF',
            'Master-Nice' => 'Master-Nice',
            'DUBAI-DHL/Risk' => 'DUBAI-DHL/Risk',
        ];

        $countryMap = $this->countryCodeMap();
        $ignoredCountryNames = $this->ignoredCountryNames();

        $inserted = 0;
        $deleted = 0;
        $countries = 0;
        $skippedCountries = 0;

        $providers = $masterData['providers'] ?? [];

        foreach ($providers as $providerName => $providerData) {
            if (!isset($providerMap[$providerName])) {
                continue;
            }

            $carrier = Carrier::updateOrCreate(
                ['name' => $providerMap[$providerName]],
                [
                    'fuel_surcharge_percent' => 0,
                    'profit_margin_percent' => 0,
                    'currency' => 'BDT',
                ]
            );

            // Latest-only semantics: replace all existing rates for this provider.
            // Master-file carriers are always country-based (zone IS NULL), so deleting all
            // rates (including any stale zone-based rows) guarantees a clean import.
            $deleted += Rate::where('carrier_id', $carrier->id)
                ->delete();

            foreach (($providerData['countries'] ?? []) as $countryName => $countryData) {
                $normalizedCountryName = trim((string) $countryName);
                $normalizedKey = $this->normalizeCountryName($normalizedCountryName);

                if (in_array($normalizedKey, $ignoredCountryNames, true)) {
                    $skippedCountries++;
                    continue;
                }

                $countryCode = $countryMap[$normalizedKey] ?? null;
                $countries++;

                $inserted += $this->insertMasterCountryRates($carrier->id, $normalizedCountryName, $countryCode, is_array($countryData) ? $countryData : []);
            }
        }

        return [
            'deleted' => $deleted,
            'inserted' => $inserted,
            'providers' => count($providers),
            'countries' => $countries,
            'skipped_countries' => $skippedCountries,
        ];
    }

    /**
     * @param array<string, mixed> $countryData
     */
    private function insertMasterCountryRates(int $carrierId, string $countryName, ?string $countryCode, array $countryData): int
    {
        $inserted = 0;

        $document = is_array($countryData['document'] ?? null) ? $countryData['document'] : [];
        $doc_0_5 = isset($document['0.5']) ? (float) $document['0.5'] : null;
        $doc_1_0 = isset($document['1.0']) ? (float) $document['1.0'] : null;
        $doc_add_0_5 = isset($document['add_0.5']) ? (float) $document['add_0.5'] : null;

        if ($doc_0_5 !== null && $doc_0_5 > 0) {
            Rate::create([
                'carrier_id' => $carrierId,
                'zone' => null,
                'country_code' => $countryCode,
                'country_name' => $countryName,
                'shipment_type' => 'document',
                'weight_slab' => 0.5,
                'price' => $doc_0_5,
                'per_kg_rate' => null,
                'rate_type' => null,
            ]);
            $inserted++;
        }

        if ($doc_1_0 !== null && $doc_1_0 > 0) {
            Rate::create([
                'carrier_id' => $carrierId,
                'zone' => null,
                'country_code' => $countryCode,
                'country_name' => $countryName,
                'shipment_type' => 'document',
                'weight_slab' => 1.0,
                'price' => $doc_1_0,
                'per_kg_rate' => null,
                'rate_type' => null,
            ]);
            $inserted++;

            if ($doc_add_0_5 !== null && $doc_add_0_5 > 0) {
                for ($increments = 1; $increments <= 8; $increments++) {
                    $weight = 1.0 + ($increments * 0.5);
                    if ($weight > 5.0) {
                        break;
                    }

                    Rate::create([
                        'carrier_id' => $carrierId,
                        'zone' => null,
                        'country_code' => $countryCode,
                        'country_name' => $countryName,
                        'shipment_type' => 'document',
                        'weight_slab' => $weight,
                        'price' => $doc_1_0 + ($increments * $doc_add_0_5),
                        'per_kg_rate' => null,
                        'rate_type' => null,
                    ]);
                    $inserted++;
                }
            }
        }

        foreach (($countryData['parcel'] ?? []) as $weight => $price) {
            if ($price === null || (float) $price <= 0) {
                continue;
            }

            Rate::create([
                'carrier_id' => $carrierId,
                'zone' => null,
                'country_code' => $countryCode,
                'country_name' => $countryName,
                'shipment_type' => 'non_document',
                'weight_slab' => (float) $weight,
                'price' => (float) $price,
                'per_kg_rate' => null,
                'rate_type' => null,
            ]);
            $inserted++;
        }

        if (!empty($countryData['per_0_5_kg'])) {
            Rate::create([
                'carrier_id' => $carrierId,
                'zone' => null,
                'country_code' => $countryCode,
                'country_name' => $countryName,
                'shipment_type' => 'non_document',
                'weight_slab' => 10.5,
                'price' => 0,
                'per_kg_rate' => (float) $countryData['per_0_5_kg'],
                'rate_type' => 'per_0_5_kg',
            ]);
            $inserted++;
        }

        if (!empty($countryData['per_21_kg'])) {
            Rate::create([
                'carrier_id' => $carrierId,
                'zone' => null,
                'country_code' => $countryCode,
                'country_name' => $countryName,
                'shipment_type' => 'non_document',
                'weight_slab' => 21.0,
                'price' => 0,
                'per_kg_rate' => (float) $countryData['per_21_kg'],
                'rate_type' => 'per_kg',
            ]);
            $inserted++;
        }

        if (!empty($countryData['per_31_kg'])) {
            Rate::create([
                'carrier_id' => $carrierId,
                'zone' => null,
                'country_code' => $countryCode,
                'country_name' => $countryName,
                'shipment_type' => 'non_document',
                'weight_slab' => 31.0,
                'price' => 0,
                'per_kg_rate' => (float) $countryData['per_31_kg'],
                'rate_type' => 'per_kg',
            ]);
            $inserted++;
        }

        return $inserted;
    }

    private function normalizeCountryName(string $name): string
    {
        return mb_strtoupper(preg_replace('/\s+/', ' ', trim($name)));
    }

    private function countryCodeMap(): array
    {
        $raw = [
            'Australia' => 'AU',
            'Brunei' => 'BN',
            'China' => 'CN',
            'Cambodia' => 'KH',
            'Indonesia' => 'ID',
            'Japan' => 'JP',
            'Macau' => 'MO',
            'Macau SAR China' => 'MO',
            'Malaysia' => 'MY',
            'Myanmar' => 'MM',
            'Newzeland' => 'NZ',
            'New Zealand' => 'NZ',
            'Pakistan' => 'PK',
            'Phillipines' => 'PH',
            'Philippines' => 'PH',
            'Philippines, The' => 'PH',
            'Singapore' => 'SG',
            'South Korea' => 'KR',
            'Korea' => 'KR',
            'Korea, Rep. Of' => 'KR',
            'Korea, D.P.R Of' => 'KP',
            'Thailand' => 'TH',
            'Taiwan' => 'TW',
            'Vietnam' => 'VN',
            'Bahrain' => 'BH',
            'Egypt' => 'EG',
            'India' => 'IN',
            'Iran' => 'IR',
            'Iraq' => 'IQ',
            'Iraq Republic' => 'IQ',
            'Israel' => 'IL',
            'Jordan' => 'JO',
            'Kuwait' => 'KW',
            'Lebanon' => 'LB',
            'Oman' => 'OM',
            'Qatar' => 'QA',
            'Saudi Arabia' => 'SA',
            'U.A.E' => 'AE',
            'UAE' => 'AE',
            'UAE Others' => 'AE',
            'United Arab Emirates' => 'AE',
            'Dubai,Sharjah' => 'AE',
            'Yemen' => 'YE',
            'Yemen, Rep. Of' => 'YE',
            'Austria' => 'AT',
            'Belgium' => 'BE',
            'Bulgaria' => 'BG',
            'Bosnia' => 'BA',
            'Bosnia & Herzegovina' => 'BA',
            'Czech Republic' => 'CZ',
            'Czech Rep., The' => 'CZ',
            'Cyprus' => 'CY',
            'Croatia' => 'HR',
            'Denmark' => 'DK',
            'Estonia' => 'EE',
            'Finland' => 'FI',
            'France' => 'FR',
            'Germany' => 'DE',
            'GREECE' => 'GR',
            'Greece' => 'GR',
            'Hungary' => 'HU',
            'Ireland' => 'IE',
            'Ireland, Rep. Of' => 'IE',
            'Iceland' => 'IS',
            'Italy' => 'IT',
            'Latvia' => 'LV',
            'Lithuania' => 'LT',
            'Luxembourg' => 'LU',
            'Malta' => 'MT',
            'Netherlands' => 'NL',
            'Netherlands, The' => 'NL',
            'Norway' => 'NO',
            'Poland' => 'PL',
            'Portugal' => 'PT',
            'Romania' => 'RO',
            'Russia' => 'RU',
            'Russian Federation' => 'RU',
            'Slovakia' => 'SK',
            'Slovenia' => 'SI',
            'Spain' => 'ES',
            'Sweden' => 'SE',
            'Switzerland' => 'CH',
            'Turkey' => 'TR',
            'U.K' => 'GB',
            'UK' => 'GB',
            'UNITED KINGDOM' => 'GB',
            'United Kingdom' => 'GB',
            'Ukraine' => 'UA',
            'Canada' => 'CA',
            'United States' => 'US',
            'United States of America' => 'US',
            'U.S.A' => 'US',
            'USA' => 'US',
            'USA -N.Y/ N.J/CA' => 'US',
            'Mexico' => 'MX',
            'Brazil' => 'BR',
            'Argentina' => 'AR',
            'Chile' => 'CL',
            'Colombia' => 'CO',
            'Ecuador' => 'EC',
            'Peru' => 'PE',
            'South Africa' => 'ZA',
            'Monaco' => 'MC',
            'Nepal' => 'NP',
            'Serbia' => 'RS',
            'Azerbaijan' => 'AZ',
            'Cameroon' => 'CM',
            'Ethiopia' => 'ET',
            'Kenya' => 'KE',
            'Mauritius' => 'MU',
            'Morocco' => 'MA',
            'Mozambique' => 'MZ',
            'Nigeria' => 'NG',
            'Srilanka' => 'LK',
            'Sri Lanka' => 'LK',
            'Maldives' => 'MV',
            'Tanzania' => 'TZ',
            'Tunisia' => 'TN',
            'Uganda' => 'UG',
            'Uzbekistan' => 'UZ',
            'Zambia' => 'ZM',
            'Zimbabwe' => 'ZW',
            'Uruguay' => 'UY',
            'Hong Kong' => 'HK',
            'Hong Kong SAR China' => 'HK',
            'Bhutan' => 'BT',
            'Seychelles' => 'SC',
            'Madagascar' => 'MG',
            'Afghanistan' => 'AF',
            'Malawi' => 'MW',
            'Albania' => 'AL',
            'Somalia' => 'SO',
            'Algeria' => 'DZ',
            'American Samoa' => 'AS',
            'Fiji' => 'FJ',
            'Mali' => 'ML',
            'Andorra' => 'AD',
            'Angola' => 'AO',
            'Marshall Islands' => 'MH',
            'Anguilla' => 'AI',
            'French Guiana' => 'GF',
            'Martinique' => 'MQ',
            'St Kitts Nevis' => 'KN',
            'St. Kitts' => 'KN',
            'Nevis' => 'KN',
            'Antigua' => 'AG',
            'French Polynesia' => 'PF',
            'Mauritania' => 'MR',
            'St Lucia' => 'LC',
            'St. Lucia' => 'LC',
            'St Vincent' => 'VC',
            'St. Vincent' => 'VC',
            'Armenia' => 'AM',
            'Sudan' => 'SD',
            'Aruba' => 'AW',
            'Gabon' => 'GA',
            'Micronesia' => 'FM',
            'Suriname' => 'SR',
            'Gambia' => 'GM',
            'Moldova' => 'MD',
            'Moldova, Rep. Of' => 'MD',
            'Swaziland' => 'SZ',
            'Georgia' => 'GE',
            'Mongolia' => 'MN',
            'Montenegro' => 'ME',
            'Montenegro, Rep Of' => 'ME',
            'Syria' => 'SY',
            'Gibraltar' => 'GI',
            'Montserrat' => 'MS',
            'Bahamas' => 'BS',
            'Grenada' => 'GD',
            'Barbados' => 'BB',
            'Guadeloupe' => 'GP',
            'Belarus' => 'BY',
            'Guatemala' => 'GT',
            'Togo' => 'TG',
            'Belize' => 'BZ',
            'Guinea' => 'GN',
            'Guinea Rep.' => 'GN',
            'Namibia' => 'NA',
            'Tonga' => 'TO',
            'Benin' => 'BJ',
            'Guinea Bissau' => 'GW',
            'Guinea-Bissau' => 'GW',
            'Trinidad Tobag' => 'TT',
            'Trinidad And Tobago' => 'TT',
            'Trinidad & Tobago' => 'TT',
            'Bermuda' => 'BM',
            'Guyana' => 'GY',
            'New Caledonia' => 'NC',
            'Bolivia' => 'BO',
            'Turkmenistan' => 'TM',
            'Haiti' => 'HT',
            'Nicaragua' => 'NI',
            'Turks Caicos I' => 'TC',
            'Turks & Caicos' => 'TC',
            'Turks and Caicos Islands' => 'TC',
            'Botswana' => 'BW',
            'Honduras' => 'HN',
            'Niger' => 'NE',
            'British Virgin Is' => 'VG',
            'Virgin Islands-British' => 'VG',
            'Burkina Faso' => 'BF',
            'Burundi' => 'BI',
            'Palau' => 'PW',
            'Cape Verde' => 'CV',
            'Palestine Authority' => 'PS',
            'Vanuatu' => 'VU',
            'Cayman Islands' => 'KY',
            'Ivory Coast' => 'CI',
            'Cote D Ivoire' => 'CI',
            'Panama' => 'PA',
            'Vatican City' => 'VA',
            'Cent Afr Rep' => 'CF',
            'Papua New Guinea' => 'PG',
            'Venezuela' => 'VE',
            'Chad' => 'TD',
            'Paraguay' => 'PY',
            'Jamaica' => 'JM',
            'Virgin Islands' => 'VI',
            'Virgin Islands-US' => 'VI',
            'Congo' => 'CG',
            'Congo, DPR' => 'CD',
            'Wallis Futuna' => 'WF',
            'Cook Islands' => 'CK',
            'Costa Rica' => 'CR',
            'Kazakhstan' => 'KZ',
            'Yugoslavia' => 'RS',
            'Kyrgyzstan' => 'KG',
            'Reunion Island' => 'RE',
            'Reunion, Island Of' => 'RE',
            'Zaire' => 'CD',
            'Democratic Republic O' => 'CD',
            'Laos' => 'LA',
            'Rwanda' => 'RW',
            'Djibouti' => 'DJ',
            'Dominica' => 'DM',
            'Dominican Republic' => 'DO',
            'Dominican Rep.' => 'DO',
            'Lesotho' => 'LS',
            'Liberia' => 'LR',
            'Libya' => 'LY',
            'Samoa' => 'WS',
            'Liechtenstein' => 'LI',
            'East Timor' => 'TL',
            'Senegal' => 'SN',
            'Serbia And Monteneg' => 'RS',
            'El Salvador' => 'SV',
            'Equatorial Guinea' => 'GQ',
            'Guinea-Equatorial' => 'GQ',
            'Sierra Leone' => 'SL',
            'Eritrea' => 'ER',
            'Macedonia' => 'MK',
            'North Macedonia' => 'MK',
            // FedEx Bangladesh-specific variants
            'Ghana' => 'GH',
            'Slovak Republic' => 'SK',
            'British Virgin Is.' => 'VG',
            'British Virgin Is' => 'VG',
            'St Kitts & Nevis' => 'KN',
            'St. Kitts & Nevis' => 'KN',
            'Nl. Antilles' => 'AN',
            'NL Antilles' => 'AN',
            'Netherlands Antilles' => 'AN',
            'Trinidad & Tobag' => 'TT',
            'Iraq Republic' => 'IQ',
            'St Lucia' => 'LC',
            'St Vincent' => 'VC',
            'Taiwan' => 'TW',
            'Tanzania' => 'TZ',
            'Turks & Caicos I' => 'TC',
        ];

        $mapped = [];
        foreach ($raw as $countryName => $code) {
            $mapped[$this->normalizeCountryName($countryName)] = $code;
        }

        return $mapped;
    }

    /**
     * @return array<int, string>
     */
    private function ignoredCountryNames(): array
    {
        return [
            'DHL RISK FEE APPLY',
            'IRAQ-RISK FEE APPLY',
            'LIBYA-RISK FEE APPLY',
            'REST OF THE WORLD',
            'SOMALIA-RISK FEE APP',
            'SUDAN-RISK FEE APPL',
            'SYRIA - RISK FEE APPLY',
            'YEMEN-RISK FEE APPLY',
            'LEBANON -RISK FEE',
            'UPS EXTRA $4.6 APPLY',
            'IRAN-KARA EXPRESS',
            'SOMALILAND, REP OF',
            'CANARY ISLANDS',
            'SAIPAN',
            'TAHITI',
            'GUAM',
            'NL ANTILLES',
        ];
    }

    private function resolvePythonBinary(): string
    {
        $candidates = array_values(array_filter([
            env('PYTHON_BINARY'),
            'python',
            'py',
            'C:/Users/Admin/AppData/Local/Programs/Python/Python311/python.exe',
        ]));

        foreach ($candidates as $candidate) {
            $process = new Process([$candidate, '--version']);
            $process->setTimeout(10);
            $process->run();

            if ($process->isSuccessful()) {
                return $candidate;
            }
        }

        throw new RuntimeException('Python executable not found. Set PYTHON_BINARY in .env.');
    }
}
