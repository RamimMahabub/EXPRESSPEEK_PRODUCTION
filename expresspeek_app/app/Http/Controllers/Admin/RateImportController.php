<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MonthlyRateImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class RateImportController extends Controller
{
    public function __construct(private MonthlyRateImportService $rateImportService)
    {
    }

    public function create(): View
    {
        return view('admin.rates.import');
    }

    public function store(Request $request): RedirectResponse
    {
        $carrier = $request->input('carrier', 'all');

        try {
            $summary = match ($carrier) {
                'dhl'    => $this->storeDhl($request),
                'master' => $this->storeMaster($request),
                'aramex' => $this->storeAramex($request),
                'ups'    => $this->storeUps($request),
                'ocs'    => $this->storeOcs($request),
                'tge'    => $this->storeTge($request),
                'fedex'  => $this->storeFedex($request),
                default  => $this->storeAll($request),
            };
        } catch (Throwable $e) {
            report($e);
            return back()->withInput()->with('error', 'Rate import failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Rates imported successfully.')->with('import_summary', $summary);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Per-carrier handlers
    // ─────────────────────────────────────────────────────────────────────────

    private function storeDhl(Request $request): array
    {
        $request->validate([
            'dhl_file'      => ['required', 'file', 'mimes:xlsx'],
            'dhl_zone_file' => ['nullable', 'file', 'mimes:xlsx'],
        ]);

        $dhlPath     = $request->file('dhl_file')->storeAs('rate-import/uploads', 'dhl_' . time() . '.xlsx');
        $dhlZonePath = $request->hasFile('dhl_zone_file')
            ? $request->file('dhl_zone_file')->storeAs('rate-import/uploads', 'dhl_zone_' . time() . '.xlsx')
            : null;

        return $this->rateImportService->importDhlCarrier(
            storage_path('app/private/' . $dhlPath),
            $dhlZonePath ? storage_path('app/private/' . $dhlZonePath) : null
        );
    }

    private function storeMaster(Request $request): array
    {
        $request->validate([
            'master_file' => ['required', 'file', 'mimes:xlsx'],
        ]);

        $masterPath = $request->file('master_file')->storeAs('rate-import/uploads', 'master_' . time() . '.xlsx');

        return $this->rateImportService->importMasterCarrier(
            storage_path('app/private/' . $masterPath)
        );
    }

    private function storeAramex(Request $request): array
    {
        $request->validate([
            'aramex_zone_file'           => ['nullable', 'file', 'mimes:xlsx'],
            'aramex_rates_up_to_10_file' => ['nullable', 'file', 'mimes:xlsx'],
            'aramex_rates_above_10_file' => ['nullable', 'file', 'mimes:xlsx'],
        ]);

        $aramexZonePath    = $request->hasFile('aramex_zone_file')
            ? $request->file('aramex_zone_file')->storeAs('rate-import/uploads', 'aramex_zone_' . time() . '.xlsx')
            : null;
        $aramexUpTo10Path  = $request->hasFile('aramex_rates_up_to_10_file')
            ? $request->file('aramex_rates_up_to_10_file')->storeAs('rate-import/uploads', 'aramex_upto10_' . time() . '.xlsx')
            : null;
        $aramexAbove10Path = $request->hasFile('aramex_rates_above_10_file')
            ? $request->file('aramex_rates_above_10_file')->storeAs('rate-import/uploads', 'aramex_above10_' . time() . '.xlsx')
            : null;

        return $this->rateImportService->importAramexCarrier(
            $aramexZonePath    ? storage_path('app/private/' . $aramexZonePath)    : null,
            $aramexUpTo10Path  ? storage_path('app/private/' . $aramexUpTo10Path)  : null,
            $aramexAbove10Path ? storage_path('app/private/' . $aramexAbove10Path) : null
        );
    }

    private function storeUps(Request $request): array
    {
        $request->validate([
            'ups_file' => ['required', 'file', 'mimes:xlsx'],
        ]);

        $upsPath = $request->file('ups_file')->storeAs('rate-import/uploads', 'ups_' . time() . '.xlsx');

        return $this->rateImportService->importUpsCarrier(
            storage_path('app/private/' . $upsPath)
        );
    }

    private function storeOcs(Request $request): array
    {
        $request->validate([
            'ocs_file' => ['required', 'file', 'mimes:xlsx'],
        ]);

        $ocsPath = $request->file('ocs_file')->storeAs('rate-import/uploads', 'ocs_' . time() . '.xlsx');

        return $this->rateImportService->importOcsCarrier(
            storage_path('app/private/' . $ocsPath)
        );
    }

    private function storeTge(Request $request): array
    {
        $request->validate([
            'tge_file' => ['required', 'file', 'mimes:xlsx'],
        ]);

        $tgePath = $request->file('tge_file')->storeAs('rate-import/uploads', 'tge_' . time() . '.xlsx');

        return $this->rateImportService->importTgeCarrier(
            storage_path('app/private/' . $tgePath)
        );
    }

    private function storeFedex(Request $request): array
    {
        $request->validate([
            'fedex_zone_file'  => ['nullable', 'file', 'mimes:xlsx'],
            'fedex_rates_file' => ['nullable', 'file', 'mimes:xlsx'],
        ]);

        $fedexZonePath  = $request->hasFile('fedex_zone_file')
            ? $request->file('fedex_zone_file')->storeAs('rate-import/uploads', 'fedex_zone_' . time() . '.xlsx')
            : null;
        $fedexRatesPath = $request->hasFile('fedex_rates_file')
            ? $request->file('fedex_rates_file')->storeAs('rate-import/uploads', 'fedex_rates_' . time() . '.xlsx')
            : null;

        return $this->rateImportService->importFedexCarrier(
            $fedexZonePath  ? storage_path('app/private/' . $fedexZonePath)  : null,
            $fedexRatesPath ? storage_path('app/private/' . $fedexRatesPath) : null
        );
    }

    private function storeAll(Request $request): array
    {
        $request->validate([
            'dhl_file'                   => ['required', 'file', 'mimes:xlsx'],
            'dhl_zone_file'              => ['required', 'file', 'mimes:xlsx'],
            'master_file'                => ['required', 'file', 'mimes:xlsx'],
            'aramex_zone_file'           => ['nullable', 'file', 'mimes:xlsx'],
            'aramex_rates_up_to_10_file' => ['nullable', 'file', 'mimes:xlsx'],
            'aramex_rates_above_10_file' => ['nullable', 'file', 'mimes:xlsx'],
            'ups_file'                   => ['nullable', 'file', 'mimes:xlsx'],
            'ocs_file'                   => ['nullable', 'file', 'mimes:xlsx'],
            'tge_file'                   => ['nullable', 'file', 'mimes:xlsx'],
            'fedex_zone_file'            => ['nullable', 'file', 'mimes:xlsx'],
            'fedex_rates_file'           => ['nullable', 'file', 'mimes:xlsx'],
        ]);

        $dhlPath      = $request->file('dhl_file')->storeAs('rate-import/uploads', 'dhl_' . time() . '.xlsx');
        $dhlZonePath  = $request->file('dhl_zone_file')->storeAs('rate-import/uploads', 'dhl_zone_' . time() . '.xlsx');
        $masterPath   = $request->file('master_file')->storeAs('rate-import/uploads', 'master_' . time() . '.xlsx');

        $aramexZonePath    = $request->hasFile('aramex_zone_file')
            ? $request->file('aramex_zone_file')->storeAs('rate-import/uploads', 'aramex_zone_' . time() . '.xlsx')
            : null;
        $aramexUpTo10Path  = $request->hasFile('aramex_rates_up_to_10_file')
            ? $request->file('aramex_rates_up_to_10_file')->storeAs('rate-import/uploads', 'aramex_upto10_' . time() . '.xlsx')
            : null;
        $aramexAbove10Path = $request->hasFile('aramex_rates_above_10_file')
            ? $request->file('aramex_rates_above_10_file')->storeAs('rate-import/uploads', 'aramex_above10_' . time() . '.xlsx')
            : null;
        $upsPath           = $request->hasFile('ups_file')
            ? $request->file('ups_file')->storeAs('rate-import/uploads', 'ups_' . time() . '.xlsx')
            : null;
        $ocsPath           = $request->hasFile('ocs_file')
            ? $request->file('ocs_file')->storeAs('rate-import/uploads', 'ocs_' . time() . '.xlsx')
            : null;
        $tgePath           = $request->hasFile('tge_file')
            ? $request->file('tge_file')->storeAs('rate-import/uploads', 'tge_' . time() . '.xlsx')
            : null;
        $fedexZonePath     = $request->hasFile('fedex_zone_file')
            ? $request->file('fedex_zone_file')->storeAs('rate-import/uploads', 'fedex_zone_' . time() . '.xlsx')
            : null;
        $fedexRatesPath    = $request->hasFile('fedex_rates_file')
            ? $request->file('fedex_rates_file')->storeAs('rate-import/uploads', 'fedex_rates_' . time() . '.xlsx')
            : null;

        return $this->rateImportService->importFromExcelFiles(
            storage_path('app/private/' . $dhlPath),
            storage_path('app/private/' . $dhlZonePath),
            storage_path('app/private/' . $masterPath),
            $aramexZonePath    ? storage_path('app/private/' . $aramexZonePath)    : null,
            $aramexUpTo10Path  ? storage_path('app/private/' . $aramexUpTo10Path)  : null,
            $aramexAbove10Path ? storage_path('app/private/' . $aramexAbove10Path) : null,
            $upsPath           ? storage_path('app/private/' . $upsPath)           : null,
            $ocsPath           ? storage_path('app/private/' . $ocsPath)           : null,
            $tgePath           ? storage_path('app/private/' . $tgePath)           : null,
            $fedexZonePath     ? storage_path('app/private/' . $fedexZonePath)     : null,
            $fedexRatesPath    ? storage_path('app/private/' . $fedexRatesPath)    : null
        );
    }
}
