@extends('layouts.dashboard')

@section('title', 'Create Invoice - ' . $sourcingRequest->reference_number)
@section('page-title', 'Create Invoice')
@section('page-subtitle', 'Generate an itemized invoice for ' . $sourcingRequest->reference_number)

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.sourcing-requests.show', $sourcingRequest) }}" class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-white transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to Request
    </a>
</div>

<div class="max-w-4xl">
    <form action="{{ route('admin.sourcing-requests.invoice.store', $sourcingRequest) }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="neon-card rounded-2xl p-6">
            <h3 class="text-sm font-bold text-white mb-5 flex items-center gap-2">
                <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Invoice Details
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Currency</label>
                    <select name="currency" class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-violet-500 transition-colors">
                        @foreach(['BDT','USD','GBP','EUR','AUD','AED','SGD','CAD'] as $cur)
                            <option value="{{ $cur }}" {{ 'BDT' === $cur ? 'selected' : '' }}>{{ $cur }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Due Date (Optional)</label>
                    <input type="date" name="due_date" class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-violet-500 transition-colors">
                </div>
            </div>

            <hr class="border-gray-800 mb-6">

            <div x-data="invoiceItems()">
                <div class="flex items-center justify-between mb-4">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide">Line Items</label>
                    <button type="button" @click="addItem()" class="text-xs font-bold text-violet-400 hover:text-violet-300 bg-violet-400/10 hover:bg-violet-400/20 px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Item
                    </button>
                </div>

                <div class="space-y-3 mb-6">
                    <template x-for="(item, index) in items" :key="item.id">
                        <div class="flex gap-3 items-start">
                            <div class="flex-1">
                                <input type="text" :name="`items[${index}][description]`" x-model="item.description" placeholder="Item description (e.g. Product Cost, Shipping)" required
                                    class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-violet-500 transition-colors placeholder-gray-600">
                            </div>
                            <div class="w-32">
                                <input type="number" :name="`items[${index}][amount]`" x-model.number="item.amount" step="0.01" min="0" placeholder="0.00" required
                                    class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-violet-500 transition-colors placeholder-gray-600">
                            </div>
                            <button type="button" @click="removeItem(item.id)" x-show="items.length > 1" class="p-3 text-red-400 hover:text-red-300 hover:bg-red-400/10 rounded-xl transition-colors mt-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </template>
                </div>

                <div class="flex justify-end pt-4 border-t border-gray-800">
                    <div class="text-right">
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-wide mb-1">Total Amount</p>
                        <p class="text-3xl font-black text-white" x-text="totalFormatted()"></p>
                    </div>
                </div>
            </div>

            <hr class="border-gray-800 my-6">

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Invoice Notes (Visible to Customer)</label>
                <textarea name="notes" rows="3" placeholder="Thank you for your business! Payment instructions..." class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-violet-500 transition-colors placeholder-gray-600 resize-none">Please deposit the total amount to our bank account or via bKash. Contact us on WhatsApp if you have any questions.</textarea>
            </div>
            
            <div class="mt-8 flex gap-3">
                <button type="submit" class="flex-1 py-3 rounded-xl bg-violet-600 hover:bg-violet-700 text-white font-bold transition-colors">
                    Generate Invoice
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('invoiceItems', () => ({
        items: [
            { id: Date.now().toString(), description: 'Product Cost', amount: 0 },
            { id: (Date.now() + 1).toString(), description: 'Base Shipping Cost', amount: 0 },
            { id: (Date.now() + 2).toString(), description: 'Handling Charge', amount: 0 }
        ],
        addItem() {
            this.items.push({ id: Date.now().toString(), description: '', amount: 0 });
        },
        removeItem(id) {
            if (this.items.length > 1) {
                this.items = this.items.filter(i => i.id !== id);
            }
        },
        total() {
            return this.items.reduce((sum, item) => sum + (parseFloat(item.amount) || 0), 0);
        },
        totalFormatted() {
            return new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(this.total());
        }
    }));
});
</script>
@endpush

@endsection
