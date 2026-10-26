@extends('layouts.operations-dashboard')

@section('title', 'Invoice ' . $sourcingInvoice->invoice_number)
@section('page-title', 'Invoice Details')
@section('page-subtitle', 'View and pay your sourcing invoice.')

@section('content')
<div class="ap-shell">
    <section class="ap-page-head">
        <div class="ap-head-copy">
            <a href="{{ route('customer.sourcing-requests.show', $sourcingInvoice->sourcingRequest) }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700 mb-2 inline-flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Request
            </a>
            <h1>
                {{ collect($sourcingInvoice->items)->pluck('description')->join(', ') }}
            </h1>
            <p class="text-xs text-slate-500 mt-1">Invoice: {{ $sourcingInvoice->invoice_number }}</p>
            <p>Created on {{ $sourcingInvoice->created_at->format('M d, Y') }}</p>
        </div>
        <div class="ap-head-actions">
            @if($sourcingInvoice->status === 'paid')
                <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold uppercase tracking-wider border bg-emerald-50 text-emerald-600 border-emerald-200">Paid</span>
            @elseif($sourcingInvoice->status === 'cancelled')
                <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold uppercase tracking-wider border bg-red-50 text-red-600 border-red-200">Cancelled</span>
            @elseif($sourcingInvoice->status === 'processing')
                <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold uppercase tracking-wider border bg-blue-50 text-blue-600 border-blue-200">Processing</span>
            @else
                <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold uppercase tracking-wider border bg-amber-50 text-amber-600 border-amber-200">Unpaid</span>
            @endif
        </div>
    </section>

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl">
            {{ session('error') }}
        </div>
    @endif
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Invoice Items --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <h2 class="text-base font-bold text-slate-800">Invoice Items</h2>
                    <p class="text-sm font-medium text-slate-500">Due: {{ $sourcingInvoice->due_date ? $sourcingInvoice->due_date->format('M d, Y') : 'N/A' }}</p>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach($sourcingInvoice->items as $item)
                        <div class="flex justify-between items-center p-6">
                            <p class="text-sm font-medium text-slate-700">{{ $item['description'] }}</p>
                            <p class="text-sm font-mono font-bold text-slate-800">{{ number_format($item['amount'], 2) }} {{ $sourcingInvoice->currency }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="px-6 py-5 bg-slate-50 border-t border-slate-200 flex justify-between items-center">
                    <p class="text-base font-bold text-slate-700">Total Amount</p>
                    <p class="text-lg font-mono font-bold text-violet-700">{{ number_format($sourcingInvoice->total_amount, 2) }} {{ $sourcingInvoice->currency }}</p>
                </div>
            </div>
            
            @if($sourcingInvoice->notes)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <h2 class="text-base font-bold text-slate-800">Notes</h2>
                </div>
                <div class="p-6">
                    <p class="text-sm text-slate-600 whitespace-pre-wrap">{{ $sourcingInvoice->notes }}</p>
                </div>
            </div>
            @endif

            @if($sourcingInvoice->status === 'processing')
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <h2 class="text-base font-bold text-slate-800">Payment Details Submitted</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="font-medium text-slate-500">Method</p>
                            <p class="font-bold text-slate-800 uppercase">{{ $sourcingInvoice->payment_method }}</p>
                        </div>
                        <div>
                            <p class="font-medium text-slate-500">Transaction ID</p>
                            <p class="font-bold font-mono text-slate-800">{{ $sourcingInvoice->transaction_id }}</p>
                        </div>
                    </div>
                    @if($sourcingInvoice->proof_of_payment_path)
                    <div class="mt-4">
                        <p class="font-medium text-slate-500 mb-2 text-sm">Proof of Payment</p>
                        <a href="{{ asset('storage/' . $sourcingInvoice->proof_of_payment_path) }}" target="_blank" class="inline-block border border-slate-200 rounded-lg p-1 hover:border-violet-500 transition-colors">
                            <img src="{{ asset('storage/' . $sourcingInvoice->proof_of_payment_path) }}" alt="Proof" class="h-32 object-cover rounded">
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <div class="space-y-6">
            @if($sourcingInvoice->status === 'unpaid')
            {{-- Payment Form --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden" x-data="{ method: 'bkash' }">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <h2 class="text-base font-bold text-slate-800">Pay Invoice</h2>
                </div>
                <div class="p-6">
                    <form action="{{ route('customer.sourcing-invoices.pay', $sourcingInvoice) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Select Payment Method</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_method" value="bkash" class="peer sr-only" x-model="method">
                                    <div class="rounded-xl border border-slate-200 p-3 text-center peer-checked:border-pink-500 peer-checked:bg-pink-50 peer-checked:text-pink-700 hover:bg-slate-50 transition-colors">
                                        <span class="font-bold text-sm block">bKash</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_method" value="bank" class="peer sr-only" x-model="method">
                                    <div class="rounded-xl border border-slate-200 p-3 text-center peer-checked:border-violet-500 peer-checked:bg-violet-50 peer-checked:text-violet-700 hover:bg-slate-50 transition-colors">
                                        <span class="font-bold text-sm block">Bank Transfer</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Instructions -->
                        <div class="mb-5 p-4 bg-slate-50 rounded-xl border border-slate-100 text-sm" x-show="method === 'bkash'">
                            <p class="font-bold text-pink-600 mb-2">bKash Payment Details</p>
                            <div class="flex items-center gap-3">
                                <p class="text-slate-700">bKash Merchant: <span id="copyBkash" class="font-mono font-bold">{{ \App\Models\Setting::get('payment_bkash_number', '01973-954578') }}</span> (Payment)</p>
                                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('copyBkash').innerText); alert('bKash number copied!')" class="text-xs bg-slate-200 hover:bg-slate-300 text-slate-700 px-2 py-1 rounded font-semibold transition-colors">Copy</button>
                            </div>
                        </div>
                        <div class="mb-5 p-4 bg-slate-50 rounded-xl border border-slate-100 text-sm" x-show="method === 'bank'" style="display: none;">
                            <p class="font-bold text-violet-600 mb-2">Bank Transfer Details</p>
                            <p class="text-slate-700">Name: <span class="font-bold">{{ \App\Models\Setting::get('payment_bank_account_name', 'EXPRESS PEEK') }}</span></p>
                            <div class="flex items-center gap-3 my-1">
                                <p class="text-slate-700">A/C No: <span id="copyBank" class="font-mono font-bold">{{ \App\Models\Setting::get('payment_bank_account_no', '1224 7151 65001') }}</span></p>
                                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('copyBank').innerText); alert('Account number copied!')" class="text-xs bg-slate-200 hover:bg-slate-300 text-slate-700 px-2 py-1 rounded font-semibold transition-colors">Copy</button>
                            </div>
                            <p class="text-slate-700">Bank: <span class="font-bold">{{ \App\Models\Setting::get('payment_bank_name', 'CITY BANK PLC.') }}</span></p>
                            <p class="text-slate-700 text-xs mt-1">{{ \App\Models\Setting::get('payment_bank_branch', 'Foreign Exchange Branch. (Dhaka South)') }}</p>
                        </div>

                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-slate-700 mb-1" for="transaction_id">Transaction ID</label>
                            <input type="text" name="transaction_id" id="transaction_id" required placeholder="e.g. 7N3F5B2K" class="w-full rounded-lg border-slate-300 focus:border-violet-500 focus:ring-violet-500 shadow-sm text-sm" value="{{ old('transaction_id') }}">
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-slate-700 mb-1" for="proof_of_payment">Proof of Payment (Photo)</label>
                            <input type="file" name="proof_of_payment" id="proof_of_payment" required accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                            <div id="proof-preview" class="hidden mt-3 items-center gap-3 rounded-xl border border-violet-100 bg-violet-50/50 p-3">
                                <img id="proof-preview-image" src="" alt="Selected payment proof preview" class="h-20 w-20 flex-shrink-0 rounded-lg border border-white object-cover shadow-sm">
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-violet-600">Preview</p>
                                    <p id="proof-preview-name" class="mt-1 truncate text-sm font-medium text-slate-700"></p>
                                </div>
                                <button type="button" id="proof-preview-clear" title="Remove selected proof" aria-label="Remove selected proof" class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg text-slate-500 hover:bg-white hover:text-red-600 transition-colors">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6L6 18"/></svg>
                                </button>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Upload a clear photo or screenshot of the payment receipt.</p>
                        </div>

                        <button type="submit" class="w-full bg-violet-600 hover:bg-violet-700 text-white font-bold py-3 px-4 rounded-xl transition-colors">
                            Submit Payment Details
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <a href="{{ route('customer.sourcing-invoices.download', $sourcingInvoice) }}" target="_blank" class="flex items-center justify-center w-full py-3 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 shadow-sm transition-colors gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download PDF Invoice
            </a>
        </div>
    </div>
</div>
<script>
    (() => {
        const input = document.getElementById('proof_of_payment');
        const preview = document.getElementById('proof-preview');
        const previewImage = document.getElementById('proof-preview-image');
        const previewName = document.getElementById('proof-preview-name');
        const clearButton = document.getElementById('proof-preview-clear');
        let previewUrl = null;

        if (!input || !preview || !previewImage || !previewName || !clearButton) return;

        const clearPreview = () => {
            if (previewUrl) URL.revokeObjectURL(previewUrl);
            previewUrl = null;
            previewImage.removeAttribute('src');
            previewName.textContent = '';
            preview.classList.add('hidden');
            preview.classList.remove('flex');
        };

        input.addEventListener('change', () => {
            const file = input.files && input.files[0];
            clearPreview();

            if (!file || !file.type.startsWith('image/')) return;

            previewUrl = URL.createObjectURL(file);
            previewImage.src = previewUrl;
            previewName.textContent = file.name;
            preview.classList.remove('hidden');
            preview.classList.add('flex');
        });

        clearButton.addEventListener('click', () => {
            input.value = '';
            clearPreview();
        });
    })();
</script>
@endsection
