@extends('layouts.operations-dashboard')

@section('title', 'Notifications')
@section('page-title', 'Notifications')
@section('page-subtitle', 'All account, shipment, sourcing, and payment updates.')

@section('content')
<div class="max-w-4xl mx-auto space-y-3">
    @forelse($notifications as $notification)
        @php($data = $notification->data)
        <a href="{{ $data['action_url'] ?? '#' }}" class="block rounded-2xl border {{ $notification->read_at ? 'border-slate-200 bg-white' : 'border-violet-200 bg-violet-50/40' }} p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start gap-4">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ ($data['color'] ?? 'blue') === 'red' ? 'bg-red-100 text-red-600' : (($data['color'] ?? 'blue') === 'green' ? 'bg-emerald-100 text-emerald-600' : (($data['color'] ?? 'blue') === 'orange' ? 'bg-orange-100 text-orange-600' : 'bg-blue-100 text-blue-600')) }}">
                    <svg class="ops-icon" viewBox="0 0 24 24"><path d="M12 2v20M2 12h20"/></svg>
                </span>
                <span class="min-w-0 flex-1">
                    <span class="flex flex-wrap items-center justify-between gap-2">
                        <strong class="text-sm text-slate-900">{{ $data['title'] ?? 'Notification' }}</strong>
                        <time class="text-xs text-slate-400">{{ $notification->created_at->format('d/m/Y H:i') }}</time>
                    </span>
                    <span class="mt-1 block text-sm text-slate-600">{{ $data['message'] ?? '' }}</span>
                </span>
            </div>
        </a>
    @empty
        <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center text-sm text-slate-500">No notifications yet.</div>
    @endforelse

    @if($notifications->hasPages())
        <div class="pt-4">{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
