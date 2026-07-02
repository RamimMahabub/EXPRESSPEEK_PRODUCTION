@extends('layouts.customer')

@section('title', 'Track Your Shipment')

@push('head')
<style>
    .tracking-hero-art { object-position: 58% 50%; opacity: .52; transform: scale(1.02); }
    .tracking-hero-shade {
        background:
            radial-gradient(circle at 16% 40%, rgba(99, 102, 241, .2), transparent 34%),
            linear-gradient(90deg, rgba(2, 6, 23, .98) 0%, rgba(2, 6, 23, .9) 48%, rgba(2, 6, 23, .48) 100%),
            linear-gradient(180deg, rgba(2, 6, 23, .1), rgba(2, 6, 23, .78));
    }
    .tracking-search-input { padding-left: 3rem !important; }
    .track-results-layout { display: grid; }
    @media (min-width: 1024px) {
        .track-results-layout { grid-template-columns: minmax(0, 1.35fr) minmax(0, .65fr); }
    }
    @media (max-width: 767px) {
        .tracking-hero-art { object-position: 68% 50%; opacity: .3; }
        .tracking-hero-shade { background: linear-gradient(180deg, rgba(2, 6, 23, .92), rgba(2, 6, 23, .82)); }
    }
</style>
@endpush

@section('content')
<main class="min-h-screen bg-slate-50">
    <section class="relative isolate overflow-hidden bg-slate-950">
        <img
            src="{{ asset('images/tracking-route-hero.webp') }}"
            alt=""
            width="1896"
            height="830"
            fetchpriority="high"
            decoding="async"
            class="tracking-hero-art pointer-events-none absolute inset-0 h-full w-full object-cover"
            aria-hidden="true"
        >
        <div class="tracking-hero-shade pointer-events-none absolute inset-0" aria-hidden="true"></div>

        <div class="relative mx-auto max-w-7xl px-6 py-20 sm:py-24 lg:py-28">
            <div class="max-w-3xl">
                <div class="mb-6 inline-flex items-center gap-2.5 rounded-full border border-white/10 bg-white/[.06] px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-300 backdrop-blur-md">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-60"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-400"></span>
                    </span>
                    Live global tracking
                </div>
                <h1 class="text-5xl font-semibold leading-[.98] tracking-[-0.055em] text-white sm:text-6xl lg:text-7xl">
                    Every mile.<br><span class="bg-gradient-to-r from-violet-300 via-indigo-300 to-blue-400 bg-clip-text text-transparent">One clear view.</span>
                </h1>
                <p class="mt-7 max-w-xl text-base leading-7 text-slate-300 sm:text-lg">
                    Follow your shipment from pickup to doorstep with live events, clear milestones, and one secure tracking number.
                </p>

                <form action="{{ route('track') }}" method="GET" class="mt-10 max-w-3xl rounded-[1.6rem] border border-white/15 bg-white/[.08] p-2.5 shadow-2xl shadow-black/30 backdrop-blur-xl">
                    <div class="flex flex-col gap-2.5 sm:flex-row">
                        <label for="tracking" class="sr-only">Tracking number</label>
                        <div class="relative min-w-0 flex-1">
                            <svg class="pointer-events-none absolute left-5 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <input id="tracking" name="tracking" value="{{ $trackingNumber }}" required autofocus autocomplete="off" spellcheck="false"
                                class="track-input tracking-search-input h-16 w-full rounded-[1.1rem] border-0 bg-white px-5 pr-4 font-mono text-sm font-semibold uppercase tracking-wide text-slate-950 shadow-sm placeholder:font-sans placeholder:font-normal placeholder:normal-case placeholder:tracking-normal placeholder:text-slate-400 focus:ring-0"
                                placeholder="Enter your tracking number">
                        </div>
                        <button type="submit" class="group flex h-16 items-center justify-center gap-3 rounded-[1.1rem] bg-gradient-to-r from-violet-600 to-indigo-600 px-7 text-sm font-semibold text-white shadow-lg shadow-indigo-950/30 transition duration-200 hover:brightness-110 focus:outline-none focus:ring-4 focus:ring-violet-300/30 sm:min-w-[190px]">
                            Track shipment
                            <svg class="h-4 w-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-6-6 6 6-6 6"/></svg>
                        </button>
                    </div>
                </form>

                <div class="mt-5 flex flex-wrap items-center gap-x-6 gap-y-2 text-xs font-medium text-slate-400">
                    <span class="flex items-center gap-2"><svg class="h-3.5 w-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Real-time events</span>
                    <span class="flex items-center gap-2"><svg class="h-3.5 w-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Secure lookup</span>
                    <span>No account required</span>
                </div>
            </div>
        </div>
    </section>

    @if($error)
        <section class="mx-auto max-w-4xl px-6 py-12" aria-live="polite">
            <div class="rounded-3xl border {{ $lookupUnavailable ? 'border-amber-100' : 'border-red-100' }} bg-white p-7 shadow-sm sm:flex sm:items-center sm:gap-5">
                <div class="mb-4 flex h-12 w-12 flex-none items-center justify-center rounded-2xl {{ $lookupUnavailable ? 'bg-amber-50 text-amber-600' : 'bg-red-50 text-red-600' }} sm:mb-0">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.73-3L13.73 4a2 2 0 00-3.46 0L3.34 16a2 2 0 001.73 3z"/></svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-lg font-bold text-slate-900">{{ $lookupUnavailable ? 'Tracking service unavailable' : 'Shipment not found' }}</h2>
                    <p class="mt-1 text-sm leading-6 text-slate-600">{{ $error }}</p>
                </div>
                <a href="{{ route('track') }}" class="mt-5 inline-flex text-sm font-bold text-violet-700 hover:text-violet-900 sm:mt-0">Try another number</a>
            </div>
        </section>
    @elseif($shipment)
        @php
            $events = $shipment->trackingEvents;
            $statusSteps = ['pending', 'picked_up', 'in_transit', 'out_for_delivery', 'delivered'];
            $statusIndex = array_search($shipment->status, $statusSteps, true);
            $statusIndex = $statusIndex === false ? 0 : $statusIndex;
            $statusTheme = match($shipment->status) {
                'delivered' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500'],
                'failed', 'returned' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'dot' => 'bg-red-500'],
                default => ['bg' => 'bg-violet-50', 'text' => 'text-violet-700', 'dot' => 'bg-violet-500'],
            };
        @endphp
        <section class="mx-auto max-w-7xl px-6 py-12 lg:py-16" aria-live="polite">
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
                            <p class="mt-2 text-sm text-slate-500">{{ $shipment->carrier_name ?: 'ExpressPeek network' }} · {{ $shipment->shipment_type ? ucfirst($shipment->shipment_type) : 'International shipment' }}</p>
                        </div>
                        @if($shipment->carrier_tracking_number)
                            <a href="{{ $shipment->carrier_tracking_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex h-12 items-center justify-center gap-2 rounded-xl border border-violet-200 bg-violet-50 px-5 text-sm font-bold text-violet-700 transition hover:bg-violet-100">
                                View live carrier tracking
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7v7m0-7L10 14M5 7v12h12v-5"/></svg>
                            </a>
                        @endif
                    </div>

                    <div class="mt-9 grid grid-cols-5 gap-1" aria-label="Shipment progress">
                        @foreach(['Booked', 'Picked up', 'In transit', 'Out for delivery', 'Delivered'] as $index => $label)
                            <div class="relative text-center">
                                @if($index < 4)
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

                <div class="track-results-layout">
                    <div class="p-6 sm:p-8 lg:border-r lg:border-slate-100">
                        <h3 class="text-lg font-extrabold text-slate-900">Tracking history</h3>
                        @forelse($events as $event)
                            <div class="relative mt-7 flex gap-4 {{ !$loop->last ? 'pb-7' : '' }}">
                                @if(!$loop->last)<span class="absolute left-[9px] top-6 h-[calc(100%-8px)] w-px bg-slate-200"></span>@endif
                                <span class="relative mt-1.5 h-[19px] w-[19px] flex-none rounded-full border-4 {{ $loop->first ? 'border-violet-100 bg-violet-600' : 'border-slate-100 bg-slate-400' }}"></span>
                                <div class="min-w-0 flex-1 sm:flex sm:justify-between sm:gap-6">
                                    <div>
                                        <p class="font-bold text-slate-900">{{ \App\Models\Shipment::statuses()[$event->status] ?? ucwords(str_replace('_', ' ', $event->status)) }}</p>
                                        <p class="mt-1 text-sm leading-6 text-slate-500">{{ $event->notes ?: 'Shipment status updated.' }}</p>
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
                            <div><dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Package</dt><dd class="mt-1.5 text-sm font-semibold text-slate-900">{{ $shipment->total_packages ?: 1 }} {{ ($shipment->total_packages ?: 1) == 1 ? 'piece' : 'pieces' }}{{ $shipment->total_weight ? ' · '.rtrim(rtrim(number_format($shipment->total_weight, 2), '0'), '.').' kg' : '' }}</dd></div>
                        </dl>
                    </aside>
                </div>
            </div>
        </section>
    @else
        <section class="mx-auto max-w-7xl px-6 py-16 lg:py-24">
            <div class="grid gap-10 pb-4 lg:grid-cols-[.8fr_1.2fr] lg:items-end">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-violet-600">How it works</p>
                    <h2 class="mt-3 text-3xl font-semibold tracking-[-0.035em] text-slate-950 sm:text-4xl">Clarity at every handoff.</h2>
                </div>
                <p class="max-w-2xl text-base leading-7 text-slate-500 lg:justify-self-end">No account, no complicated dashboard. Enter the number you already have and see the journey in seconds.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-6 pt-12">
                @foreach([
                    ['01', 'Search', 'Enter your ExpressPeek or carrier reference in the secure tracker.', 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'],
                    ['02', 'Follow', 'See every scan, route change, and location update in one timeline.', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                    ['03', 'Receive', 'Know when your shipment is out for delivery and ready to arrive.', 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                ] as $item)
                    <article class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm hover:shadow-xl hover:border-violet-100 hover:-translate-y-1 transition-all duration-300 group">
                        <div class="w-14 h-14 rounded-2xl bg-violet-50 text-violet-600 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item[3] }}"/>
                            </svg>
                        </div>
                        <span class="font-mono text-xs font-bold text-violet-600 mb-2 block">{{ $item[0] }}</span>
                        <h3 class="text-xl font-bold tracking-tight text-slate-900 mb-3">{{ $item[1] }}</h3>
                        <p class="text-sm leading-relaxed text-slate-500">{{ $item[2] }}</p>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
</main>
@endsection
