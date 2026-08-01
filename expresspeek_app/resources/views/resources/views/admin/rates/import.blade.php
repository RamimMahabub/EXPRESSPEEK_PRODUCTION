@extends('layouts.dashboard')

@section('title', 'Rate Import')
@section('page-title', 'Rate Upload')
@section('page-subtitle', 'Upload rates for any carrier independently — no need to upload all at once')

@section('content')
<div class="max-w-3xl mx-auto space-y-6" x-data="{ tab: '{{ old('carrier', 'dhl') }}' }">

    {{-- Carrier Tabs --}}
    <div class="neon-card rounded-2xl overflow-hidden">
        {{-- Tab nav --}}
        <div class="flex overflow-x-auto border-b border-white/10 scrollbar-none">
            @foreach([
                ['key' => 'dhl',    'label' => 'DHL',    'color' => 'text-yellow-400'],
                ['key' => 'master', 'label' => 'Master', 'color' => 'text-blue-400'],
                ['key' => 'aramex', 'label' => 'Aramex', 'color' => 'text-orange-400'],
                ['key' => 'ups',    'label' => 'UPS',    'color' => 'text-amber-400'],
                ['key' => 'ocs',    'label' => 'OCS',    'color' => 'text-cyan-400'],
                ['key' => 'tge',    'label' => 'TGE',    'color' => 'text-rose-400'],
                ['key' => 'fedex',  'label' => 'FedEx',  'color' => 'text-purple-400'],
            ] as $t)
            <button
                type="button"
                id="tab-{{ $t['key'] }}"
                @click="tab = '{{ $t['key'] }}'"
                :class="tab === '{{ $t['key'] }}' ? 'border-b-2 border-white {{ $t['color'] }} bg-gray-800/50' : 'text-slate-400 hover:text-slate-300'"
                class="whitespace-nowrap px-5 py-3.5 text-sm font-semibold transition-colors focus:outline-none"
            >{{ $t['label'] }}</button>
            @endforeach
        </div>

        {{-- ── DHL ── --}}
        <div x-show="tab === 'dhl'" x-cloak class="p-6 space-y-4">
            <div>
                <h2 class="text-base font-semibold text-white">DHL Bangladesh</h2>
                <p class="mt-1 text-sm text-slate-400">Upload the DHL rate sheet. Optionally include the zone list to refresh country → zone mappings.</p>
            </div>
            <form action="{{ route('admin.rates.import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="carrier" value="dhl">
                <x-rate-file-input id="dhl_file" name="dhl_file" label="DHL Rate File" required color="yellow" />
                <x-rate-file-input id="dhl_zone_file" name="dhl_zone_file" label="DHL Zone List (optional)" color="yellow" />
                <x-import-button />
            </form>
        </div>

        {{-- ── Master ── --}}
        <div x-show="tab === 'master'" x-cloak class="p-6 space-y-4">
            <div>
                <h2 class="text-base font-semibold text-white">Master Air Agent</h2>
                <p class="mt-1 text-sm text-slate-400">Upload the Master Air Agent monthly rate sheet to refresh all Master/Singapore/Dubai provider rates.</p>
            </div>
            <form action="{{ route('admin.rates.import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="carrier" value="master">
                <x-rate-file-input id="master_file" name="master_file" label="Master Rate File" required color="blue" />
                <x-import-button />
            </form>
        </div>

        {{-- ── Aramex ── --}}
        <div x-show="tab === 'aramex'" x-cloak class="p-6 space-y-4">
            <div>
                <h2 class="text-base font-semibold text-white">Aramex</h2>
                <p class="mt-1 text-sm text-slate-400">Upload any combination of Aramex files. You can upload just the zone list, or just the rate files — whatever changed this month.</p>
            </div>
            <form action="{{ route('admin.rates.import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="carrier" value="aramex">
                <x-rate-file-input id="aramex_zone_file" name="aramex_zone_file" label="Zone List (optional)" color="orange" />
                <x-rate-file-input id="aramex_rates_up_to_10_file" name="aramex_rates_up_to_10_file" label="Rates Up to 10 kg (optional)" color="orange" />
                <x-rate-file-input id="aramex_rates_above_10_file" name="aramex_rates_above_10_file" label="Rates 10 kg+ (optional)" color="orange" />
                <x-import-button />
            </form>
        </div>

        {{-- ── UPS ── --}}
        <div x-show="tab === 'ups'" x-cloak class="p-6 space-y-4">
            <div>
                <h2 class="text-base font-semibold text-white">UPS Bangladesh</h2>
                <p class="mt-1 text-sm text-slate-400">Upload the UPS Bangladesh rate file (25 kg+ country-based per-kg rates).</p>
            </div>
            <form action="{{ route('admin.rates.import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="carrier" value="ups">
                <x-rate-file-input id="ups_file" name="ups_file" label="UPS Rate File (25 kg+)" required color="amber" />
                <x-import-button />
            </form>
        </div>

        {{-- ── OCS ── --}}
        <div x-show="tab === 'ocs'" x-cloak class="p-6 space-y-4">
            <div>
                <h2 class="text-base font-semibold text-white">OCS Japan</h2>
                <p class="mt-1 text-sm text-slate-400">Upload the OCS Japan rate file.</p>
            </div>
            <form action="{{ route('admin.rates.import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="carrier" value="ocs">
                <x-rate-file-input id="ocs_file" name="ocs_file" label="OCS Japan Rate File" required color="cyan" />
                <x-import-button />
            </form>
        </div>

        {{-- ── TGE ── --}}
        <div x-show="tab === 'tge'" x-cloak class="p-6 space-y-4">
            <div>
                <h2 class="text-base font-semibold text-white">TGE (Team Global Express)</h2>
                <p class="mt-1 text-sm text-slate-400">Upload the TGE Australia rate file (5 kg+ per-kg rates for Australia).</p>
            </div>
            <form action="{{ route('admin.rates.import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="carrier" value="tge">
                <x-rate-file-input id="tge_file" name="tge_file" label="TGE Rate File" required color="rose" />
                <x-import-button />
            </form>
        </div>

        {{-- ── FedEx ── --}}
        <div x-show="tab === 'fedex'" x-cloak class="p-6 space-y-4">
            <div>
                <h2 class="text-base font-semibold text-white">FedEx Bangladesh</h2>
                <p class="mt-1 text-sm text-slate-400">Upload either or both FedEx files. You can refresh just the zone list or just the rates independently.</p>
            </div>
            <form action="{{ route('admin.rates.import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="carrier" value="fedex">
                <x-rate-file-input id="fedex_zone_file" name="fedex_zone_file" label="Zone List (optional)" color="purple" />
                <x-rate-file-input id="fedex_rates_file" name="fedex_rates_file" label="Rate File — Doc + Non-Doc + 10 kg+ (optional)" color="purple" />
                <x-import-button />
            </form>
        </div>
    </div>

    {{-- Import Summary --}}
    @php
        $importSummary = session('import_summary');
        $importStats   = [];
        if (is_array($importSummary)) {
            $importStats = array_filter([
                'DHL Zones inserted'    => $importSummary['dhl_zones']['inserted']  ?? null,
                'DHL Zones deleted'     => ($importSummary['dhl_zones']['deleted']  ?? 0) ?: null,
                'DHL Rates inserted'    => $importSummary['dhl']['inserted']         ?? null,
                'Master Rates inserted' => $importSummary['master']['inserted']      ?? null,
                'Master Rates deleted'  => ($importSummary['master']['deleted']      ?? 0) ?: null,
                'Aramex Zones'          => $importSummary['aramex_zones']['inserted'] ?? null,
                'Aramex Rates'          => $importSummary['aramex']['inserted']       ?? null,
                'UPS Rates'             => $importSummary['ups']['inserted']          ?? null,
                'OCS Rates'             => $importSummary['ocs']['inserted']          ?? null,
                'TGE Rates'             => $importSummary['tge']['inserted']          ?? null,
                'FedEx Zones'           => $importSummary['fedex_zones']['inserted']  ?? null,
                'FedEx Zones skipped'   => ($importSummary['fedex_zones']['skipped']  ?? 0) ?: null,
                'FedEx Rates'           => $importSummary['fedex']['inserted']         ?? null,
            ], fn($v) => $v !== null);
        }
    @endphp
    @if(!empty($importStats))
        <div class="neon-card rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white uppercase tracking-wide mb-4">Import Summary</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                @foreach($importStats as $label => $value)
                    <div class="rounded-xl border border-white/10 bg-gray-950/50 p-3">
                        <p class="text-slate-400 text-xs">{{ $label }}</p>
                        <p class="text-lg font-bold {{ str_contains($label, 'skipped') ? 'text-yellow-400' : 'text-white' }} mt-0.5">{{ $value }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>

<style>
[x-cloak] { display: none !important; }
.scrollbar-none { scrollbar-width: none; }
.scrollbar-none::-webkit-scrollbar { display: none; }
</style>
@endsection
