<?php

namespace App\Console\Commands;

use App\Services\MonthlyRateImportService;
use Illuminate\Console\Command;
use RuntimeException;

class ImportMonthlyRates extends Command
{
    protected $signature = 'rates:import-monthly {--dhl= : Path to DHL rate Excel file} {--dhl-zone= : Path to DHL zone Excel file} {--master= : Path to Master Excel file} {--ocs= : Path to OCS Japan rate file} {--tge= : Path to TGE Australia rate file}';

    protected $description = 'Import monthly DHL + Master rates from Excel files in one step';

    public function __construct(private MonthlyRateImportService $rateImportService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $dhlPath = $this->option('dhl') ?: base_path('DHL BANGLADESH RATES.xlsx');
        $dhlZonePath = $this->option('dhl-zone') ?: base_path('DHL BANGLADESH ZONELIST.xlsx');
        $masterPath = $this->option('master') ?: base_path('MASTER AIR AGENT RATE 01ST July.2025.xlsx');
        $ocsPath = $this->option('ocs') ?: base_path('rates/OCS.xlsx');
        $tgePath = $this->option('tge') ?: base_path('rates/TGE.xlsx');

        if (!file_exists($dhlPath)) {
            $this->error('DHL file not found: ' . $dhlPath);
            return self::FAILURE;
        }

        if ($dhlZonePath && !file_exists($dhlZonePath)) {
            $this->error('DHL zone file not found: ' . $dhlZonePath);
            return self::FAILURE;
        }

        if (!file_exists($masterPath)) {
            $this->error('Master file not found: ' . $masterPath);
            return self::FAILURE;
        }

        if ($ocsPath && !file_exists($ocsPath)) {
            $this->error('OCS file not found: ' . $ocsPath);
            return self::FAILURE;
        }

        if ($tgePath && !file_exists($tgePath)) {
            $this->error('TGE file not found: ' . $tgePath);
            return self::FAILURE;
        }

        try {
            $summary = $this->rateImportService->importFromExcelFiles($dhlPath, $dhlZonePath, $masterPath, null, null, null, null, $ocsPath, $tgePath);
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }

        $this->info('Monthly rates imported successfully.');
        $this->table(['Source', 'Deleted', 'Inserted'], [
            ['DHL Zones', $summary['dhl_zones']['deleted'] ?? 0, $summary['dhl_zones']['inserted'] ?? 0],
            ['DHL', $summary['dhl']['deleted'] ?? 0, $summary['dhl']['inserted'] ?? 0],
            ['Master', $summary['master']['deleted'] ?? 0, $summary['master']['inserted'] ?? 0],
            ['OCS', $summary['ocs']['deleted'] ?? 0, $summary['ocs']['inserted'] ?? 0],
            ['TGE', $summary['tge']['deleted'] ?? 0, $summary['tge']['inserted'] ?? 0],
        ]);

        return self::SUCCESS;
    }
}
