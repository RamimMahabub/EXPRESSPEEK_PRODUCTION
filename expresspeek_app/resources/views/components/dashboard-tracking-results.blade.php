@props([
    'shipment' => null,
    'error' => null,
    'lookupUnavailable' => false,
])

@if($error)
    <section class="ep-card mt-6" style="background:#fff2f4; border-color:#ffd8df; padding: 24px; margin-top: 24px; border-radius: 12px; border: 1px solid;">
        <div class="flex items-start gap-4">
            <div class="flex-none flex items-center justify-center w-12 h-12 rounded-xl {{ $lookupUnavailable ? 'bg-amber-100 text-amber-600' : 'bg-red-100 text-red-600' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.73-3L13.73 4a2 2 0 00-3.46 0L3.34 16a2 2 0 001.73 3z"/></svg>
            </div>
            <div>
                <h3 class="text-lg font-bold" style="color:#c8203e;">{{ $lookupUnavailable ? 'Tracking service unavailable' : 'Shipment not found' }}</h3>
                <p class="text-sm mt-1" style="color:#e05b73;">{{ $error }}</p>
            </div>
        </div>
    </section>
@elseif($shipment)
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6" id="dashboard-tracking-result" style="margin-top: 32px;">
        <div class="lg:col-span-2 neon-card rounded-2xl p-6" style="background: rgba(26, 31, 55, 0.6); border: 1px solid rgba(255, 255, 255, 0.1);">
            <div class="flex items-start justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-xl font-bold text-white">Tracking Timeline</h2>
                    <p class="text-sm text-slate-400 mt-1">Latest updates for {{ $shipment->tracking_number }}</p>
                </div>
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-gray-800 text-gray-300 border border-gray-700">
                    {{ $shipment->status_label }}
                </span>
            </div>

            <div class="space-y-4">
                @forelse($shipment->trackingEvents as $event)
                    <div class="flex gap-4">
                        <div class="w-3.5 h-3.5 rounded-full mt-1.5 bg-violet-500 flex-shrink-0 shadow-[0_0_10px_rgba(139,92,246,0.6)]"></div>
                        <div class="flex-1 pb-4 border-b border-white/10 last:border-b-0 last:pb-0">
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mb-1">
                                <p class="text-sm font-semibold text-white">{{ \App\Models\Shipment::statuses()[$event->status] ?? ucwords(str_replace('_', ' ', $event->status)) }}</p>
                                <p class="text-xs text-slate-400">{{ $event->occurred_at?->format('M d, Y h:i A') ?? $event->created_at?->format('M d, Y h:i A') }}</p>
                            </div>
                            <p class="text-sm text-slate-300">{{ $event->notes ?: 'No additional notes provided.' }}</p>
                            @if($event->location)
                                <p class="text-xs text-slate-500 mt-1">{{ $event->location }}</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-gray-700 p-8 text-center text-slate-400">
                        No tracking updates have been recorded for this shipment yet.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            <div class="neon-card rounded-2xl p-6" style="background: rgba(26, 31, 55, 0.6); border: 1px solid rgba(255, 255, 255, 0.1);">
                <h2 class="text-lg font-bold text-white mb-4">Shipment Summary</h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between gap-4 border-b border-white/5 pb-2">
                        <dt class="text-slate-400">Tracking Number</dt>
                        <dd class="font-mono font-semibold text-violet-300 text-right break-all">{{ $shipment->tracking_number }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-white/5 pb-2">
                        <dt class="text-slate-400">Receiver</dt>
                        <dd class="font-semibold text-white text-right">{{ $shipment->receiver_name }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-white/5 pb-2">
                        <dt class="text-slate-400">Destination</dt>
                        <dd class="font-semibold text-white text-right">{{ $shipment->receiver_city }}, {{ $shipment->receiver_country }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-white/5 pb-2">
                        <dt class="text-slate-400">Weight</dt>
                        <dd class="font-semibold text-white text-right">{{ $shipment->weight }} kg</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-white/5 pb-2">
                        <dt class="text-slate-400">Created</dt>
                        <dd class="font-semibold text-white text-right">{{ $shipment->created_at?->format('M d, Y') }}</dd>
                    </div>
                    @if($shipment->estimated_delivery)
                        <div class="flex justify-between gap-4 pb-2">
                            <dt class="text-slate-400">Estimated Delivery</dt>
                            <dd class="font-semibold text-emerald-400 text-right">{{ $shipment->estimated_delivery->format('M d, Y') }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <div class="neon-card rounded-2xl p-6" style="background: rgba(26, 31, 55, 0.6); border: 1px solid rgba(255, 255, 255, 0.1);">
                <h2 class="text-lg font-bold text-white mb-4">Carrier Tracking</h2>
                @if(!empty($shipment->carrier_tracking_number))
                    <p class="text-sm text-slate-400 mb-2">{{ $shipment->carrier_name ?: 'Carrier' }} reference</p>
                    <a href="{{ $shipment->carrier_tracking_url }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 text-sm font-semibold text-violet-400 hover:text-violet-300 transition-colors">
                        Open carrier tracking
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14"/>
                        </svg>
                    </a>
                    <p class="text-xs text-slate-400 mt-2 font-mono break-all bg-gray-900 rounded p-2 border border-gray-700">{{ $shipment->carrier_tracking_number }}</p>
                @else
                    <p class="text-sm text-slate-400 bg-gray-900 rounded p-3 border border-gray-700 text-center">Carrier tracking has not been assigned yet.</p>
                @endif
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const results = document.getElementById('dashboard-tracking-result');
            if (results) {
                results.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    </script>
@endif
