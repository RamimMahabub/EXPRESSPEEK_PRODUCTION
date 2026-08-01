@if($error)
    <section class="mt-4" aria-live="polite">
        <div class="rounded-3xl border {{ $lookupUnavailable ? 'border-amber-100' : 'border-red-100' }} bg-white p-7 shadow-sm sm:flex sm:items-center sm:gap-5">
            <div class="mb-4 flex h-12 w-12 flex-none items-center justify-center rounded-2xl {{ $lookupUnavailable ? 'bg-amber-50 text-amber-600' : 'bg-red-50 text-red-600' }} sm:mb-0">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.73-3L13.73 4a2 2 0 00-3.46 0L3.34 16a2 2 0 001.73 3z"/></svg>
            </div>
            <div class="flex-1">
                <h2 class="text-lg font-bold text-slate-900">{{ $lookupUnavailable ? 'Tracking service unavailable' : 'Shipment not found' }}</h2>
                <p class="mt-1 text-sm leading-6 text-slate-600">{{ $error }}</p>
            </div>
        </div>
    </section>
@elseif($shipment)
    @php
        $events = $shipment->trackingEvents;
        $progressSteps = $shipment->progress_steps;
        $statusSteps = array_keys($progressSteps);
        $statusIndex = array_search($shipment->status, $statusSteps, true);
        $statusIndex = $statusIndex === false ? 0 : $statusIndex;
        $statusTheme = match($shipment->status) {
            'connected_to_carrier', 'delivered' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500'],
            'failed', 'returned' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'dot' => 'bg-red-500'],
            default => ['bg' => 'bg-violet-50', 'text' => 'text-violet-700', 'dot' => 'bg-violet-500'],
        };
    @endphp

    <section class="mt-4" aria-live="polite">
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl shadow-slate-200/50">
            <div class="border-b border-slate-100 p-6 sm:p-8">
                <div class="flex flex-col justify-between gap-6 lg:flex-row lg:items-center">
                    <div>
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="inline-flex items-center gap-2 rounded-full {{ $statusTheme['bg'] }} px-3 py-1.5 text-xs font-bold {{ $statusTheme['text'] }}">
                                <span class="h-2 w-2 rounded-full {{ $statusTheme['dot'] }}"></span>
                                {{ $shipment->status_label }}
                            </span>
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Last updated {{ optional($events->first()?->occurred_at)->diffForHumans() ?? 'recently' }}</span>
                        </div>
                        <h2 class="mt-4 text-2xl font-black tracking-tight text-slate-900 sm:text-3xl">Shipment {{ $shipment->tracking_number }}</h2>
                        <p class="mt-2 text-sm text-slate-500">{{ $shipment->carrier_name ?: 'Express Peek network' }} · {{ $shipment->shipment_type ? ucfirst($shipment->shipment_type) : 'International shipment' }}</p>
                    </div>
                    @if($shipment->carrier_tracking_number)
                        <a href="{{ $shipment->carrier_tracking_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex h-12 items-center justify-center gap-2 rounded-xl border border-violet-200 bg-violet-50 px-5 text-sm font-bold text-violet-700 transition hover:bg-violet-100">
                            View live carrier tracking
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7v7m0-7L10 14M5 7v12h12v-5"/></svg>
                        </a>
                    @endif
                </div>

                <div class="mt-9 grid gap-1" style="grid-template-columns: repeat({{ count($progressSteps) }}, minmax(0, 1fr));" aria-label="Shipment progress">
                    @foreach(array_values($progressSteps) as $index => $label)
                        <div class="relative text-center">
                            @if($index < count($progressSteps) - 1)
                                <div class="absolute left-1/2 top-3 h-1 w-full {{ $index < $statusIndex ? 'bg-violet-600' : 'bg-slate-200' }}"></div>
                            @endif
                            <div class="relative mx-auto flex h-7 w-7 items-center justify-center rounded-full border-4 border-white {{ $index <= $statusIndex ? 'bg-violet-600 text-white shadow-[0_0_0_1px_#7c3aed]' : 'bg-slate-200 text-slate-400 shadow-[0_0_0_1px_#e2e8f0]' }}">
                                @if($index < $statusIndex)
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                @else
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                @endif
                            </div>
                            <p class="mt-2 hidden text-[11px] font-semibold {{ $index <= $statusIndex ? 'text-slate-700' : 'text-slate-400' }} sm:block">{{ $label }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid lg:grid-cols-[minmax(0,1.35fr)_minmax(300px,.65fr)]">
                <div class="p-6 sm:p-8 lg:border-r lg:border-slate-100">
                    <h3 class="text-lg font-extrabold text-slate-900">Tracking history</h3>
                    @forelse($events as $event)
                        <div class="relative mt-7 flex gap-4 {{ !$loop->last ? 'pb-7' : '' }}">
                            @if(!$loop->last)<span class="absolute left-[9px] top-6 h-[calc(100%-8px)] w-px bg-slate-200"></span>@endif
                            <span class="relative mt-1.5 h-[19px] w-[19px] flex-none rounded-full border-4 {{ $loop->first ? 'border-violet-100 bg-violet-600' : 'border-slate-100 bg-slate-400' }}"></span>
                            <div class="min-w-0 flex-1 sm:flex sm:justify-between sm:gap-6">
                                <div>
                                    <p class="font-bold text-slate-900">{{ \App\Models\Shipment::statuses()[$event->status] ?? ucwords(str_replace('_', ' ', $event->status)) }}</p>
                                    <p class="mt-1 text-sm leading-6 text-slate-500">{{ $loop->last && $event->status === \App\Models\Shipment::STATUS_PENDING ? $shipment->creation_note : ($event->notes ?: 'Shipment status updated.') }}</p>
                                    @if($event->location)<p class="mt-1.5 text-xs font-semibold text-slate-400">{{ $event->location }}</p>@endif
                                </div>
                                <time class="mt-2 block flex-none text-xs font-semibold text-slate-400 sm:mt-0 sm:text-right">{{ optional($event->occurred_at)->format('M d, Y') }}<br class="hidden sm:block"> {{ optional($event->occurred_at)->format('g:i A') }}</time>
                            </div>
                        </div>
                    @empty
                        <div class="mt-6 rounded-2xl bg-slate-50 p-5 text-sm text-slate-500">Your shipment is registered. Tracking events will appear here as the package moves.</div>
                    @endforelse
                </div>

                <aside class="bg-slate-50/70 p-6 sm:p-8">
                    <h3 class="text-lg font-extrabold text-slate-900">Shipment details</h3>
                    <dl class="mt-6 space-y-5">
                        <div><dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Tracking number</dt><dd class="mt-1.5 font-mono text-sm font-bold text-slate-900">{{ $shipment->tracking_number }}</dd></div>
                        @if($shipment->carrier_tracking_number)<div><dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Carrier reference</dt><dd class="mt-1.5 font-mono text-sm font-bold text-slate-900">{{ $shipment->carrier_tracking_number }}</dd></div>@endif
                        <div><dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Route</dt><dd class="mt-1.5 text-sm font-semibold text-slate-900">{{ $shipment->sender_city ?: $shipment->sender_country ?: 'Origin' }} <span class="mx-1 text-slate-300">→</span> {{ $shipment->receiver_city ?: $shipment->receiver_country ?: 'Destination' }}</dd></div>
                        <div><dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Estimated delivery</dt><dd class="mt-1.5 text-sm font-semibold text-slate-900">{{ $shipment->estimated_delivery?->format('l, M d, Y') ?? 'To be confirmed' }}</dd></div>
                        <div><dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Package</dt><dd class="mt-1.5 text-sm font-semibold text-slate-900">{{ $shipment->total_packages ?: 1 }} {{ ($shipment->total_packages ?: 1) == 1 ? 'piece' : 'pieces' }}</dd></div>
                    </dl>
                </aside>
            </div>
        </div>
    </section>
@endif
