@extends('layouts.operations-dashboard')

@section('title', 'Sourcing Payments Tracking')

@section('content')
<div class="ap-shell" x-data="{ showSettingsModal: false }">
    <section class="ap-page-head">
        <div class="ap-head-copy">
            <span class="ap-eyebrow">Finance</span>
            <h1>Sourcing Payments</h1>
            <p>Track, verify, and manage all sourcing invoice payments.</p>
        </div>
        <div class="ap-head-actions">
            <button type="button" @click="showSettingsModal = true" class="ap-button light">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Payment Settings
            </button>
        </div>
    </section>

    @if(session('success'))
        <div class="ops-flash success">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="ops-flash error">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="ops-flash error">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- KPI Widgets -->
    <div class="ap-kpi-grid">
        <div class="ap-kpi green">
            <div class="ap-kpi-top">
                <div class="ap-kpi-icon"><svg viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
                <div class="ap-kpi-label">Total Received (Paid)</div>
            </div>
            <div class="ap-kpi-value">{{ number_format($totalReceived, 2) }} <span class="text-sm text-slate-500 font-normal">BDT</span></div>
        </div>
        <div class="ap-kpi blue">
            <div class="ap-kpi-top">
                <div class="ap-kpi-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></div>
                <div class="ap-kpi-label">Pending Verification (Processing)</div>
            </div>
            <div class="ap-kpi-value">{{ number_format($totalProcessing, 2) }} <span class="text-sm text-slate-500 font-normal">BDT</span></div>
        </div>
    </div>

    <!-- Filters & Table -->
    <div class="ap-card ap-table-card">
        <div class="ap-card-head" style="flex-wrap: wrap;">
            <div>
                <h3 class="ap-card-title">All Invoices</h3>
                <p class="ap-card-copy">Showing {{ $invoices->firstItem() ?? 0 }}-{{ $invoices->lastItem() ?? 0 }} of {{ $invoices->total() }} results.</p>
            </div>
            
            <form method="GET" action="{{ route('admin.sourcing-invoices.index') }}" class="flex items-center gap-3">
                <div class="ap-field">
                    <svg viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Invoice #, Txn ID, Name..." class="ap-input" style="width: 220px;">
                </div>
                <select name="status" class="ap-select" style="width: 150px;" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.sourcing-invoices.index') }}" class="ap-button ghost text-xs px-3">Clear</a>
                @endif
                <button type="submit" class="hidden">Search</button>
            </form>
        </div>
        
        <div class="ap-table-scroll">
            <table class="ap-table">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Transaction Info</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                    <tr>
                        <td>
                            <strong>{{ $invoice->invoice_number }}</strong>
                            <small>{{ $invoice->created_at->format('M d, Y') }}</small>
                        </td>
                        <td>
                            <div class="ap-person">
                                <div>
                                    <strong>{{ $invoice->sourcingRequest->user->name ?? 'N/A' }}</strong>
                                    <small>{{ $invoice->sourcingRequest->user->email ?? '' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <strong>{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}</strong>
                        </td>
                        <td>
                            @if($invoice->status === 'paid')
                                <span class="ap-badge customer">Paid</span>
                            @elseif($invoice->status === 'cancelled')
                                <span class="ap-badge" style="color:#d8344e; background:#fff4f5;">Cancelled</span>
                            @elseif($invoice->status === 'processing')
                                <span class="ap-badge agent">Processing</span>
                            @else
                                <span class="ap-badge admin" style="color:#e47b0a; background:#fff2df;">Unpaid</span>
                            @endif
                        </td>
                        <td>
                            @if($invoice->payment_method)
                                <strong>{{ strtoupper($invoice->payment_method) }}</strong>
                                <small class="font-mono">{{ $invoice->transaction_id }}</small>
                            @else
                                <span class="text-slate-400 text-xs">--</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <a href="{{ route('admin.sourcing-requests.show', $invoice->sourcingRequest) }}#invoices" class="ap-button ghost" style="padding: 0 10px; min-height: 32px; font-size: 10px;">
                                View Request
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="ap-empty">
                                <div class="ap-empty-icon"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                                <strong>No invoices found</strong>
                                <p>There are no sourcing invoices matching your criteria.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($invoices->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>

    <!-- Settings Modal -->
    <div x-show="showSettingsModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showSettingsModal = false" x-transition.opacity></div>
        
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh]" x-transition.scale.origin.bottom>
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <h3 class="text-lg font-bold text-slate-800">Company Payment Settings</h3>
                <button type="button" @click="showSettingsModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <div class="p-6 overflow-y-auto">
                <form id="paymentSettingsForm" action="{{ route('admin.settings.payment.update') }}" method="POST">
                    @csrf
                    
                    <div class="mb-6">
                        <h4 class="font-bold text-slate-700 mb-3 text-sm uppercase tracking-wider text-pink-600">bKash Details</h4>
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Merchant Number</label>
                            <input type="text" name="payment_bkash_number" class="ap-input" value="{{ \App\Models\Setting::get('payment_bkash_number', '01973-954578') }}">
                        </div>
                    </div>

                    <div>
                        <h4 class="font-bold text-slate-700 mb-3 text-sm uppercase tracking-wider text-violet-600">Bank Account Details</h4>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Account Name</label>
                                <input type="text" name="payment_bank_account_name" class="ap-input" value="{{ \App\Models\Setting::get('payment_bank_account_name', 'EXPRESS PEEK') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Account Number</label>
                                <input type="text" name="payment_bank_account_no" class="ap-input" value="{{ \App\Models\Setting::get('payment_bank_account_no', '1224 7151 65001') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Bank Name</label>
                                <input type="text" name="payment_bank_name" class="ap-input" value="{{ \App\Models\Setting::get('payment_bank_name', 'CITY BANK PLC.') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Branch Name</label>
                                <input type="text" name="payment_bank_branch" class="ap-input" value="{{ \App\Models\Setting::get('payment_bank_branch', 'Foreign Exchange Branch. (Dhaka South)') }}">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" @click="showSettingsModal = false" class="ap-button ghost">Cancel</button>
                <button type="submit" form="paymentSettingsForm" class="ap-button primary">Save Settings</button>
            </div>
        </div>
    </div>
</div>
@endsection
