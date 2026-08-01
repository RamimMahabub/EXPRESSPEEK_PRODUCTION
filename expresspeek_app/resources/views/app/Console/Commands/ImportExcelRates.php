<?php
namespace App\Console\Commands;

use App\Models\Carrier;
use App\Models\Rate;
use Illuminate\Console\Command;

class ImportExcelRates extends Command
{
    protected $signature = 'import:rates';
    protected $description = 'Imports rates from the excel_rates_parsed.json file into the database';

    public function handle()
    {
        $this->info('Reading parsed rates...');
        
        $jsonPath = base_path('excel_rates_parsed.json');
        if (!file_exists($jsonPath)) {
            $this->error('excel_rates_parsed.json not found. Run: python parse_excel_full.py');
            return;
        }

        $ratesData = json_decode(file_get_contents($jsonPath), true);

        if (!$ratesData || !isset($ratesData['providers'])) {
            $this->error('Invalid rates JSON structure');
            return;
        }

        // Map provider names
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

        $carrierModels = [];
        foreach ($providerMap as $name => $fullName) {
            $carrierModels[$name] = Carrier::firstOrCreate(
                ['name' => $fullName],
                [
                    'email' => 'contact@' . strtolower(str_replace([' ', '/'], '', $fullName)) . '.com',
                    'margin_percentage' => 0.0,
                    'currency' => 'USD',
                ]
            );
        }

        $ratesInserted = 0;
        $ratesUpdated = 0;

        foreach ($ratesData['providers'] as $providerName => $providerData) {
            if (!isset($providerMap[$providerName])) {
                $this->warn("Unknown provider: $providerName");
                continue;
            }

            $carrier = $carrierModels[$providerName];

            foreach ($providerData['countries'] as $countryName => $countryData) {
                // For Master Air providers, we use country_code (will add country mapping later)
                // For now, we'll store just the country name
                $this->importCountryRates($carrier, $countryName, $countryData, $ratesInserted, $ratesUpdated);
            }
        }

        $this->info("Import complete. Inserted: {$ratesInserted}, Updated: {$ratesUpdated}");
    }

    private function importCountryRates($carrier, $countryName, $countryData, &$inserted, &$updated)
    {
        // Import document rates (0.5kg, 1kg, 1.5kg, 2kg, 2.5kg, 3kg up to 5kg)
        if (isset($countryData['document'])) {
            $doc = $countryData['document'];
            $doc_0_5 = null;
            $doc_1_0 = null;
            $doc_add_0_5 = null;

            // Extract base values
            if (isset($doc['0.5'])) $doc_0_5 = floatval($doc['0.5']);
            if (isset($doc['1.0'])) $doc_1_0 = floatval($doc['1.0']);
            if (isset($doc['add_0.5'])) $doc_add_0_5 = floatval($doc['add_0.5']);

            // Store 0.5kg rate
            if ($doc_0_5) {
                $result = Rate::updateOrCreate([
                    'carrier_id' => $carrier->id,
                    'country_code' => null,
                    'country_name' => $countryName,
                    'weight_slab' => 0.5,
                    'shipment_type' => 'document'
                ], [
                    'price' => $doc_0_5,
                    'per_kg_rate' => null
                ]);
                $result->wasRecentlyCreated ? $inserted++ : $updated++;
            }

            // Store 1.0kg rate and calculated increments (1.5, 2.0, 2.5, 3.0, 3.5, 4.0, 4.5, 5.0kg)
            if ($doc_1_0 && $doc_add_0_5) {
                // 1.0kg base
                $result = Rate::updateOrCreate([
                    'carrier_id' => $carrier->id,
                    'country_code' => null,
                    'country_name' => $countryName,
                    'weight_slab' => 1.0,
                    'shipment_type' => 'document'
                ], [
                    'price' => $doc_1_0,
                    'per_kg_rate' => null
                ]);
                $result->wasRecentlyCreated ? $inserted++ : $updated++;

                // 1.5kg = 1.0 + 1×add_0.5
                // 2.0kg = 1.0 + 2×add_0.5
                // 2.5kg = 1.0 + 3×add_0.5
                // ... up to 5.0kg = 1.0 + 8×add_0.5
                for ($increments = 1; $increments <= 8; $increments++) {
                    $weight = 1.0 + ($increments * 0.5);
                    if ($weight > 5.0) break; // Only go up to 5kg for now

                    $price = $doc_1_0 + ($increments * $doc_add_0_5);

                    $result = Rate::updateOrCreate([
                        'carrier_id' => $carrier->id,
                        'country_code' => null,
                        'country_name' => $countryName,
                        'weight_slab' => $weight,
                        'shipment_type' => 'document'
                    ], [
                        'price' => $price,
                        'per_kg_rate' => null
                    ]);
                    $result->wasRecentlyCreated ? $inserted++ : $updated++;
                }
            }
        }

        // Import parcel/non-document rates (0.5kg - 10kg fixed slabs)
        if (isset($countryData['parcel'])) {
            foreach ($countryData['parcel'] as $weightStr => $price) {
                if ($price === null) continue;

                $weight = floatval($weightStr);
                $result = Rate::updateOrCreate([
                    'carrier_id' => $carrier->id,
                    'country_code' => null,
                    'country_name' => $countryName,
                    'weight_slab' => $weight,
                    'shipment_type' => 'non_document'
                ], [
                    'price' => floatval($price),
                    'per_kg_rate' => null
                ]);
                $result->wasRecentlyCreated ? $inserted++ : $updated++;
            }
        }

        // Import per-kg rates for 10-21kg, 21-31kg, 31+kg
        if (isset($countryData['per_0_5_kg']) && $countryData['per_0_5_kg']) {
            // Per 0.5kg increment for 10-21kg range
            $result = Rate::updateOrCreate([
                'carrier_id' => $carrier->id,
                'country_code' => null,
                'country_name' => $countryName,
                'weight_slab' => 10.5,
                'shipment_type' => 'non_document'
            ], [
                'price' => 0,
                'per_kg_rate' => floatval($countryData['per_0_5_kg']),
                'rate_type' => 'per_0_5_kg' // Store the type for interpretation
            ]);
            $result->wasRecentlyCreated ? $inserted++ : $updated++;
        }

        // Per-kg rates for 21+
        if (isset($countryData['per_21_kg']) && $countryData['per_21_kg']) {
            $result = Rate::updateOrCreate([
                'carrier_id' => $carrier->id,
                'country_code' => null,
                'country_name' => $countryName,
                'weight_slab' => 21.0,
                'shipment_type' => 'non_document'
            ], [
                'price' => 0,
                'per_kg_rate' => floatval($countryData['per_21_kg'])
            ]);
            $result->wasRecentlyCreated ? $inserted++ : $updated++;
        }

        // Per-kg rates for 31+
        if (isset($countryData['per_31_kg']) && $countryData['per_31_kg']) {
            $result = Rate::updateOrCreate([
                'carrier_id' => $carrier->id,
                'country_code' => null,
                'country_name' => $countryName,
                'weight_slab' => 31.0,
                'shipment_type' => 'non_document'
            ], [
                'price' => 0,
                'per_kg_rate' => floatval($countryData['per_31_kg'])
            ]);
            $result->wasRecentlyCreated ? $inserted++ : $updated++;
        }
    }
}
