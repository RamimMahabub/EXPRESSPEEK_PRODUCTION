<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShippingRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Zone Mappings
        $zones = [
            1 => ['Bahrain', 'Hong Kong', 'India', 'Jordan', 'Kuwait', 'Oman', 'Qatar', 'Saudi Arabia', 'Singapore', 'Thailand', 'UAE'],
            2 => ['Bhutan', 'Brunei', 'Cambodia', 'China', 'Indonesia', 'Korea (South)', 'Laos', 'Macau', 'Malaysia', 'Maldives', 'Myanmar', 'Nepal', 'Pakistan', 'Philippines', 'Sri Lanka', 'Taiwan', 'Vietnam'],
            3 => ['Australia', 'Japan', 'New Zealand'],
            4 => ['United Kingdom', 'Germany', 'France', 'Italy', 'Spain', 'Netherlands', 'Belgium', 'Austria', 'Denmark', 'Finland', 'Greece', 'Ireland', 'Luxembourg', 'Portugal', 'Sweden', 'Switzerland'],
            5 => ['Canada', 'Mexico', 'USA'],
            6 => ['Bosnia', 'Gibraltar', 'Guernsey', 'Israel', 'Jersey', 'Montenegro', 'Norway', 'Turkey'],
            7 => ['Afghanistan', 'South Africa', 'Brazil', 'Argentina', 'Chile', 'Colombia', 'Egypt', 'Nigeria', 'Kenya'],
        ];

        foreach ($zones as $zone => $countries) {
            foreach ($countries as $country) {
                \App\Models\CountryZone::updateOrCreate(
                    [
                        'country_code' => $this->getCountryCode($country),
                        'zone' => $zone,
                    ],
                    [
                        'country_name' => $country,
                    ]
                );
            }
        }

        // 2. Create Carriers
        $dhlBD = \App\Models\Carrier::updateOrCreate(
            ['name' => 'DHL-Bangladesh'],
            [
                'fuel_surcharge_percent' => 39.00,
                'profit_margin_percent' => 10.00,
                'currency' => 'BDT',
            ]
        );

        $sDhl = \App\Models\Carrier::updateOrCreate(
            ['name' => 'S-DHL'],
            [
                'fuel_surcharge_percent' => 15.00,
                'profit_margin_percent' => 15.00,
                'currency' => 'USD',
            ]
        );

        $sUps = \App\Models\Carrier::updateOrCreate(
            ['name' => 'S-UPS'],
            [
                'fuel_surcharge_percent' => 15.00,
                'profit_margin_percent' => 15.00,
                'currency' => 'USD',
            ]
        );

        // 3. Create Rates for DHL-Bangladesh (Zone-based, from provided rate sheet)
        $dhlRateRows = [
            ['weight_slab' => 0.5, 'zones' => [1 => 3640, 2 => 4910, 3 => 4970, 4 => 4420, 5 => 4420, 6 => 6150, 7 => 6360]],
            ['weight_slab' => 1.0, 'zones' => [1 => 4530, 2 => 6090, 3 => 6180, 4 => 5330, 5 => 5360, 6 => 7800, 7 => 8250]],
            ['weight_slab' => 1.5, 'zones' => [1 => 5410, 2 => 7280, 3 => 7390, 4 => 6240, 5 => 6300, 6 => 9460, 7 => 10140]],
            ['weight_slab' => 2.0, 'zones' => [1 => 6290, 2 => 8460, 3 => 8590, 4 => 7150, 5 => 7230, 6 => 11110, 7 => 12020]],
            ['weight_slab' => 2.5, 'zones' => [1 => 7030, 2 => 9410, 3 => 9660, 4 => 7920, 5 => 8010, 6 => 12570, 7 => 13860]],
            ['weight_slab' => 3.0, 'zones' => [1 => 7770, 2 => 10310, 3 => 10620, 4 => 8680, 5 => 8790, 6 => 14020, 7 => 15690]],
            ['weight_slab' => 3.5, 'zones' => [1 => 8520, 2 => 11200, 3 => 11590, 4 => 9450, 5 => 9570, 6 => 15480, 7 => 17530]],
            ['weight_slab' => 4.0, 'zones' => [1 => 9260, 2 => 12100, 3 => 12550, 4 => 10220, 5 => 10350, 6 => 16940, 7 => 19370]],
            ['weight_slab' => 4.5, 'zones' => [1 => 10000, 2 => 13000, 3 => 13520, 4 => 10990, 5 => 11120, 6 => 18390, 7 => 21210]],
            ['weight_slab' => 5.0, 'zones' => [1 => 10750, 2 => 13900, 3 => 14480, 4 => 11750, 5 => 11900, 6 => 19850, 7 => 23050]],
            ['weight_slab' => 5.5, 'zones' => [1 => 11380, 2 => 14800, 3 => 15450, 4 => 12520, 5 => 12680, 6 => 21310, 7 => 24880]],
            ['weight_slab' => 6.0, 'zones' => [1 => 12020, 2 => 15690, 3 => 16410, 4 => 13290, 5 => 13460, 6 => 22760, 7 => 26720]],
            ['weight_slab' => 6.5, 'zones' => [1 => 12660, 2 => 16590, 3 => 17380, 4 => 14050, 5 => 14240, 6 => 24220, 7 => 28550]],
            ['weight_slab' => 7.0, 'zones' => [1 => 13300, 2 => 17480, 3 => 18340, 4 => 14820, 5 => 15010, 6 => 25670, 7 => 30390]],
            ['weight_slab' => 7.5, 'zones' => [1 => 13930, 2 => 18380, 3 => 19300, 4 => 15580, 5 => 15790, 6 => 27130, 7 => 32220]],
            ['weight_slab' => 8.0, 'zones' => [1 => 14570, 2 => 19280, 3 => 20270, 4 => 16350, 5 => 16570, 6 => 28580, 7 => 34060]],
            ['weight_slab' => 8.5, 'zones' => [1 => 15210, 2 => 20170, 3 => 21230, 4 => 17120, 5 => 17350, 6 => 30040, 7 => 35990]],
            ['weight_slab' => 9.0, 'zones' => [1 => 15850, 2 => 21070, 3 => 22200, 4 => 17880, 5 => 18130, 6 => 31490, 7 => 37730]],
            ['weight_slab' => 9.5, 'zones' => [1 => 16480, 2 => 21960, 3 => 23160, 4 => 18650, 5 => 18910, 6 => 32950, 7 => 39570]],
            ['weight_slab' => 10.0, 'zones' => [1 => 17120, 2 => 22860, 3 => 24130, 4 => 19410, 5 => 19680, 6 => 34400, 7 => 41400]],
            ['weight_slab' => 10.5, 'zones' => [1 => 17690, 2 => 23700, 3 => 25170, 4 => 20100, 5 => 20500, 6 => 35810, 7 => 43090]],
            ['weight_slab' => 11.0, 'zones' => [1 => 18250, 2 => 24540, 3 => 26210, 4 => 20790, 5 => 21310, 6 => 37220, 7 => 44770]],
            ['weight_slab' => 11.5, 'zones' => [1 => 18820, 2 => 25380, 3 => 27250, 4 => 21490, 5 => 22120, 6 => 38630, 7 => 46460]],
            ['weight_slab' => 12.0, 'zones' => [1 => 19380, 2 => 26220, 3 => 28290, 4 => 22180, 5 => 22940, 6 => 40040, 7 => 48140]],
            ['weight_slab' => 12.5, 'zones' => [1 => 19950, 2 => 27060, 3 => 29330, 4 => 22870, 5 => 23750, 6 => 41450, 7 => 49830]],
            ['weight_slab' => 13.0, 'zones' => [1 => 20510, 2 => 27900, 3 => 30370, 4 => 23560, 5 => 24560, 6 => 42860, 7 => 51510]],
            ['weight_slab' => 13.5, 'zones' => [1 => 21080, 2 => 28740, 3 => 31410, 4 => 24250, 5 => 25380, 6 => 44270, 7 => 53200]],
            ['weight_slab' => 14.0, 'zones' => [1 => 21640, 2 => 29580, 3 => 32460, 4 => 24940, 5 => 26190, 6 => 45680, 7 => 54880]],
            ['weight_slab' => 14.5, 'zones' => [1 => 22210, 2 => 30420, 3 => 33500, 4 => 25630, 5 => 27000, 6 => 47090, 7 => 56570]],
            ['weight_slab' => 15.0, 'zones' => [1 => 22770, 2 => 31260, 3 => 34540, 4 => 26320, 5 => 27820, 6 => 48500, 7 => 58250]],
            ['weight_slab' => 15.5, 'zones' => [1 => 23340, 2 => 32100, 3 => 35580, 4 => 27020, 5 => 28630, 6 => 49910, 7 => 59940]],
            ['weight_slab' => 16.0, 'zones' => [1 => 23900, 2 => 32940, 3 => 36620, 4 => 27710, 5 => 29440, 6 => 51310, 7 => 61620]],
            ['weight_slab' => 16.5, 'zones' => [1 => 24470, 2 => 33780, 3 => 37660, 4 => 28400, 5 => 30260, 6 => 52720, 7 => 63300]],
            ['weight_slab' => 17.0, 'zones' => [1 => 25030, 2 => 34620, 3 => 38700, 4 => 29090, 5 => 31070, 6 => 54130, 7 => 64990]],
            ['weight_slab' => 17.5, 'zones' => [1 => 25600, 2 => 35460, 3 => 39740, 4 => 29780, 5 => 31880, 6 => 55540, 7 => 66670]],
            ['weight_slab' => 18.0, 'zones' => [1 => 26160, 2 => 36310, 3 => 40780, 4 => 30470, 5 => 32700, 6 => 56950, 7 => 68360]],
            ['weight_slab' => 18.5, 'zones' => [1 => 26730, 2 => 37150, 3 => 41830, 4 => 31160, 5 => 33510, 6 => 58360, 7 => 70040]],
            ['weight_slab' => 19.0, 'zones' => [1 => 27290, 2 => 37990, 3 => 42870, 4 => 31860, 5 => 34320, 6 => 59770, 7 => 71730]],
            ['weight_slab' => 19.5, 'zones' => [1 => 27860, 2 => 38830, 3 => 43910, 4 => 32550, 5 => 35140, 6 => 61180, 7 => 73410]],
            ['weight_slab' => 20.0, 'zones' => [1 => 28420, 2 => 39670, 3 => 44950, 4 => 33240, 5 => 35950, 6 => 62590, 7 => 75100]],
            ['weight_slab' => 20.5, 'zones' => [1 => 28990, 2 => 40510, 3 => 45980, 4 => 33930, 5 => 36760, 6 => 64000, 7 => 76790]],
            ['weight_slab' => 21.0, 'zones' => [1 => 29560, 2 => 41350, 3 => 47020, 4 => 34620, 5 => 37580, 6 => 65410, 7 => 78480]],
            ['weight_slab' => 21.5, 'zones' => [1 => 30120, 2 => 42190, 3 => 48050, 4 => 35320, 5 => 38390, 6 => 66810, 7 => 80160]],
            ['weight_slab' => 22.0, 'zones' => [1 => 30690, 2 => 43030, 3 => 49090, 4 => 36010, 5 => 39200, 6 => 68220, 7 => 81850]],
            ['weight_slab' => 22.5, 'zones' => [1 => 31260, 2 => 43870, 3 => 50120, 4 => 36700, 5 => 40020, 6 => 69630, 7 => 83540]],
            ['weight_slab' => 23.0, 'zones' => [1 => 31820, 2 => 44710, 3 => 51160, 4 => 37400, 5 => 40830, 6 => 71040, 7 => 85230]],
            ['weight_slab' => 23.5, 'zones' => [1 => 32390, 2 => 45550, 3 => 52190, 4 => 38090, 5 => 41640, 6 => 72440, 7 => 86920]],
            ['weight_slab' => 24.0, 'zones' => [1 => 32960, 2 => 46390, 3 => 53230, 4 => 38780, 5 => 42460, 6 => 73850, 7 => 88610]],
            ['weight_slab' => 24.5, 'zones' => [1 => 33530, 2 => 47230, 3 => 54260, 4 => 39480, 5 => 43270, 6 => 75260, 7 => 90300]],
            ['weight_slab' => 25.0, 'zones' => [1 => 34090, 2 => 48070, 3 => 55300, 4 => 40170, 5 => 44080, 6 => 76660, 7 => 91990]],
            ['weight_slab' => 25.5, 'zones' => [1 => 34660, 2 => 48910, 3 => 56330, 4 => 40860, 5 => 44900, 6 => 78070, 7 => 93680]],
            ['weight_slab' => 26.0, 'zones' => [1 => 35230, 2 => 49750, 3 => 57370, 4 => 41560, 5 => 45710, 6 => 79480, 7 => 95360]],
            ['weight_slab' => 26.5, 'zones' => [1 => 35790, 2 => 50590, 3 => 58400, 4 => 42250, 5 => 46520, 6 => 80890, 7 => 97050]],
            ['weight_slab' => 27.0, 'zones' => [1 => 36360, 2 => 51430, 3 => 59440, 4 => 42940, 5 => 47340, 6 => 82290, 7 => 98740]],
            ['weight_slab' => 27.5, 'zones' => [1 => 36930, 2 => 52270, 3 => 60470, 4 => 43640, 5 => 48150, 6 => 83700, 7 => 100430]],
            ['weight_slab' => 28.0, 'zones' => [1 => 37490, 2 => 53110, 3 => 61510, 4 => 44330, 5 => 48970, 6 => 85110, 7 => 102120]],
            ['weight_slab' => 28.5, 'zones' => [1 => 38060, 2 => 53950, 3 => 62540, 4 => 45020, 5 => 49780, 6 => 86520, 7 => 103810]],
            ['weight_slab' => 29.0, 'zones' => [1 => 38630, 2 => 54790, 3 => 63580, 4 => 45720, 5 => 50590, 6 => 87920, 7 => 105500]],
            ['weight_slab' => 29.5, 'zones' => [1 => 39200, 2 => 55630, 3 => 64610, 4 => 46410, 5 => 51410, 6 => 89330, 7 => 107190]],
            ['weight_slab' => 30.0, 'zones' => [1 => 39760, 2 => 56470, 3 => 65650, 4 => 47100, 5 => 52220, 6 => 90740, 7 => 108880]],
        ];

        $perKgRates = [
            1 => 1050, 2 => 1150, 3 => 1520, 4 => 1180, 5 => 1800, 6 => 1990, 7 => 2580
        ];

        foreach ($dhlRateRows as $row) {
            foreach ($row['zones'] as $zone => $price) {
                $weight = $row['weight_slab'];
                \App\Models\Rate::updateOrCreate(
                    [
                        'carrier_id' => $dhlBD->id,
                        'zone' => $zone,
                        'shipment_type' => 'non_document',
                        'weight_slab' => $weight,
                    ],
                    [
                        'price' => $price,
                        'per_kg_rate' => ($weight == 30.0) ? $perKgRates[$zone] : null,
                    ]
                );
            }
        }

        // 3b. Create DHL-Bangladesh document rates (up to 2.0 KG)
        $dhlDocumentRateRows = [
            ['weight_slab' => 0.5, 'zones' => [1 => 2440, 2 => 4160, 3 => 4200, 4 => 3900, 5 => 3990, 6 => 6080, 7 => 6200]],
            ['weight_slab' => 1.0, 'zones' => [1 => 3330, 2 => 5480, 3 => 5460, 4 => 4840, 5 => 4960, 6 => 7980, 7 => 8090]],
            ['weight_slab' => 1.5, 'zones' => [1 => 4220, 2 => 6810, 3 => 6730, 4 => 5780, 5 => 5930, 6 => 9870, 7 => 9970]],
            ['weight_slab' => 2.0, 'zones' => [1 => 5110, 2 => 8130, 3 => 7990, 4 => 6720, 5 => 6900, 6 => 11760, 7 => 11860]],
        ];

        foreach ($dhlDocumentRateRows as $row) {
            foreach ($row['zones'] as $zone => $price) {
                \App\Models\Rate::updateOrCreate(
                    [
                        'carrier_id' => $dhlBD->id,
                        'zone' => $zone,
                        'shipment_type' => 'document',
                        'weight_slab' => $row['weight_slab'],
                    ],
                    [
                        'price' => $price,
                        'per_kg_rate' => null,
                    ]
                );
            }
        }

        // 4. Create Rates for S-DHL / S-UPS (Country-based)
        // Example: Australia
        \App\Models\Rate::updateOrCreate(
            [
                'carrier_id' => $sDhl->id,
                'country_code' => 'AU',
                'shipment_type' => 'non_document',
                'weight_slab' => 21.0,
            ],
            [
                'price' => 7.76, // This is a per-kg rate technically, but we store it
                'per_kg_rate' => 7.76,
            ]
        );
        \App\Models\Rate::updateOrCreate(
            [
                'carrier_id' => $sDhl->id,
                'country_code' => 'AU',
                'shipment_type' => 'non_document',
                'weight_slab' => 31.0,
            ],
            [
                'price' => 7.48,
                'per_kg_rate' => 7.48,
            ]
        );
    }

    private function getCountryCode($name)
    {
        $codes = [
            'Bahrain' => 'BH', 'Hong Kong' => 'HK', 'India' => 'IN', 'Jordan' => 'JO', 'Kuwait' => 'KW', 'Oman' => 'OM', 'Qatar' => 'QA', 'Saudi Arabia' => 'SA', 'Singapore' => 'SG', 'Thailand' => 'TH', 'UAE' => 'AE',
            'Bhutan' => 'BT', 'Brunei' => 'BN', 'Cambodia' => 'KH', 'China' => 'CN', 'Indonesia' => 'ID', 'Korea (South)' => 'KR', 'Laos' => 'LA', 'Macau' => 'MO', 'Malaysia' => 'MY', 'Maldives' => 'MV', 'Myanmar' => 'MM', 'Nepal' => 'NP', 'Pakistan' => 'PK', 'Philippines' => 'PH', 'Sri Lanka' => 'LK', 'Taiwan' => 'TW', 'Vietnam' => 'VN',
            'Australia' => 'AU', 'Japan' => 'JP', 'New Zealand' => 'NZ',
            'United Kingdom' => 'GB', 'Germany' => 'DE', 'France' => 'FR', 'Italy' => 'IT', 'Spain' => 'ES', 'Netherlands' => 'NL', 'Belgium' => 'BE', 'Austria' => 'AT', 'Denmark' => 'DK', 'Finland' => 'FI', 'Greece' => 'GR', 'Ireland' => 'IE', 'Luxembourg' => 'LU', 'Portugal' => 'PT', 'Sweden' => 'SE', 'Switzerland' => 'CH',
            'Canada' => 'CA', 'Mexico' => 'MX', 'USA' => 'US',
            'Bosnia' => 'BA', 'Gibraltar' => 'GI', 'Guernsey' => 'GG', 'Israel' => 'IL', 'Jersey' => 'JE', 'Montenegro' => 'ME', 'Norway' => 'NO', 'Turkey' => 'TR',
            'Afghanistan' => 'AF', 'South Africa' => 'ZA', 'Brazil' => 'BR', 'Argentina' => 'AR', 'Chile' => 'CL', 'Colombia' => 'CO', 'Egypt' => 'EG', 'Nigeria' => 'NG', 'Kenya' => 'KE',
        ];

        return $codes[$name] ?? 'XX';
    }
}
