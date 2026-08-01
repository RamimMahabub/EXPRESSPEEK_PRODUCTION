@extends('layouts.operations-dashboard')

@section('title', 'System Update')
@section('page-title', 'System Update')
@section('page-subtitle', 'Safely apply database updates without Terminal access')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="neon-card rounded-2xl overflow-hidden">
        <div class="p-6 sm:p-8 border-b border-slate-200 bg-gradient-to-br from-violet-50 to-blue-50">
            <div class="w-12 h-12 mb-5 rounded-2xl bg-gradient-to-br from-violet-600 to-blue-600 text-white grid place-items-center shadow-lg shadow-violet-200">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v12m0 0 4-4m-4 4-4-4"/><path d="M5 20h14"/></svg>
            </div>
            <span class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-violet-700">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>Administrator only
            </span>
            <h2 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900">Apply the latest Express Peek update</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">This installs pending database changes, including shipment invoice, user phone-number, and profile-photo support, and then clears outdated application caches.</p>
        </div>

        <div class="p-6 sm:p-8">
            @if(session('success'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800" role="status">
                    <strong class="block mb-1">Update completed successfully</strong>
                    <pre class="whitespace-pre-wrap font-sans text-xs leading-5">{{ session('success') }}</pre>
                </div>
            @endif

            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <strong class="text-sm text-slate-800">Database migration status</strong>
                    <span class="px-2.5 py-1 rounded-full bg-white border border-slate-200 text-[11px] font-bold text-slate-500">Read only</span>
                </div>
                <pre class="max-h-64 overflow-auto whitespace-pre-wrap text-xs leading-5 text-slate-600">{{ $migrationStatus }}</pre>
            </div>

            <div class="mt-4 flex items-start gap-3 rounded-xl border p-4 {{ $avatarOptimizerReady ? 'border-emerald-200 bg-emerald-50' : 'border-amber-200 bg-amber-50' }}">
                <span class="mt-0.5 grid h-7 w-7 shrink-0 place-items-center rounded-full {{ $avatarOptimizerReady ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                    @if($avatarOptimizerReady)
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m5 12 4 4L19 6"/></svg>
                    @else
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4m0 4h.01M10.3 3.8 2.5 18a2 2 0 0 0 1.8 3h15.4a2 2 0 0 0 1.8-3L13.7 3.8a2 2 0 0 0-3.4 0Z"/></svg>
                    @endif
                </span>
                <div>
                    <strong class="block text-sm {{ $avatarOptimizerReady ? 'text-emerald-900' : 'text-amber-900' }}">
                        Profile photo optimizer: {{ $avatarOptimizerReady ? 'Ready' : 'Action required' }}
                    </strong>
                    <p class="mt-1 text-xs leading-5 {{ $avatarOptimizerReady ? 'text-emerald-700' : 'text-amber-800' }}">
                        @if($avatarOptimizerReady)
                            {{ $avatarOptimizerDriver }} is active. Uploaded photos will be cropped to 512×512 and stored as optimized WebP files.
                        @else
                            Enable the PHP <strong>gd</strong> extension in cPanel’s Select PHP Version page, then reload this page.
                        @endif
                    </p>
                </div>
            </div>

            <div class="mt-3 grid gap-3 sm:grid-cols-5">
                <div class="rounded-xl border p-3 {{ $shipmentInvoiceDatabaseReady ? 'border-emerald-200 bg-emerald-50' : 'border-red-200 bg-red-50' }}">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Shipment invoices</span>
                    <strong class="mt-1 block text-sm {{ $shipmentInvoiceDatabaseReady ? 'text-emerald-800' : 'text-red-700' }}">{{ $shipmentInvoiceDatabaseReady ? 'Installed' : 'Missing' }}</strong>
                </div>
                <div class="rounded-xl border p-3 {{ $avatarDatabaseReady ? 'border-emerald-200 bg-emerald-50' : 'border-red-200 bg-red-50' }}">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Database field</span>
                    <strong class="mt-1 block text-sm {{ $avatarDatabaseReady ? 'text-emerald-800' : 'text-red-700' }}">{{ $avatarDatabaseReady ? 'Installed' : 'Missing' }}</strong>
                </div>
                <div class="rounded-xl border p-3 {{ $avatarStorageReady ? 'border-emerald-200 bg-emerald-50' : 'border-red-200 bg-red-50' }}">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Avatar storage</span>
                    <strong class="mt-1 block text-sm {{ $avatarStorageReady ? 'text-emerald-800' : 'text-red-700' }}">{{ $avatarStorageReady ? 'Writable' : 'Not writable' }}</strong>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">PHP upload limit</span>
                    <strong class="mt-1 block text-sm text-slate-800">{{ $avatarUploadLimit ?: 'Unknown' }}</strong>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">PHP memory</span>
                    <strong class="mt-1 block text-sm text-slate-800">{{ $avatarMemoryLimit ?: 'Unknown' }}</strong>
                </div>
            </div>

            @unless($avatarStorageReady)
                <p class="mt-3 rounded-xl border border-red-200 bg-red-50 p-4 text-xs leading-5 text-red-800">In cPanel File Manager, set <strong>public/uploads</strong> and <strong>public/uploads/avatars</strong> permissions to <strong>755</strong>. If it still reports Not writable, use <strong>775</strong>.</p>
            @endunless

            @unless($avatarDatabaseReady)
                <p class="mt-3 rounded-xl border border-red-200 bg-red-50 p-4 text-xs leading-5 text-red-800">The profile-photo database migration is missing. Upload the latest fix package, extract it, and press <strong>Run secure update</strong>.</p>
            @endunless

            @unless($shipmentInvoiceDatabaseReady)
                <p class="mt-3 rounded-xl border border-red-200 bg-red-50 p-4 text-xs leading-5 text-red-800">The shipment invoice table is missing. Press <strong>Run secure update</strong> below to install it and enable invoice generation.</p>
            @endunless

            <form method="POST" action="{{ route('admin.system-update.run') }}" class="mt-6">
                @csrf
                <button type="submit" class="neon-button inline-flex items-center justify-center gap-2 rounded-xl px-6 py-3 text-sm font-bold">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v12m0 0 4-4m-4 4-4-4"/><path d="M5 20h14"/></svg>
                    Run secure update
                </button>
            </form>

            <p class="mt-4 text-xs leading-5 text-slate-500">Only run this after uploading the newest application files. Running it again is safe; completed migrations are not repeated.</p>
        </div>
    </div>
</div>
@endsection
