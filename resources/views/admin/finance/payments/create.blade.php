<x-layouts.admin title="Record Payment">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Finance', 'url' => '#'],
                ['label' => 'Payments', 'url' => route('admin.finance.payments.index')],
                ['label' => 'Record Payment']
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Record Payment</h1>
        <p class="mt-1 text-sm text-gray-500 font-medium">Record a new payment and allocate it to open invoices.</p>
    </div>

    @if($errors->any())
        <div class="mb-6 bg-red-50/50 p-4 rounded-xl border border-red-100">
            <ul class="list-disc pl-5 text-sm text-red-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.finance.payments.store') }}" 
          x-data="paymentForm({{ json_encode($openInvoices) }}, {{ $preselectedInvoice ? $preselectedInvoice->client_id : 'null' }}, {{ $preselectedInvoice ? $preselectedInvoice->id : 'null' }})" 
          class="space-y-6" id="payment-form">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
                    <h3 class="text-lg font-bold text-gray-900 tracking-tight">Payment Details</h3>
                </div>
                
                <div class="p-6 space-y-4">
                    <div>
                        <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1">Client <span class="text-red-500">*</span></label>
                        <select name="client_id" id="client_id" x-model="selectedClient" @change="updateAvailableInvoices" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors bg-white min-h-[42px]">
                            <option value="">Select a Client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount Received <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm font-medium">RWF</span>
                            </div>
                            <input type="number" name="amount" id="amount" x-model="totalAmount" @input="autoAllocate" required min="0.01" step="0.01"
                                   class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-12 sm:text-sm border-gray-300 rounded-xl transition-colors min-h-[42px]" placeholder="0.00">
                        </div>
                    </div>

                    <div>
                        <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-1">Payment Date <span class="text-red-500">*</span></label>
                        <input type="date" name="payment_date" id="payment_date" required value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                               class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors min-h-[42px]">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
                    <h3 class="text-lg font-bold text-gray-900 tracking-tight">Method & Reference</h3>
                </div>
                
                <div class="p-6 space-y-4">
                    <div>
                        <label for="payment_method_id" class="block text-sm font-medium text-gray-700 mb-1">Payment Method <span class="text-red-500">*</span></label>
                        <select name="payment_method_id" id="payment_method_id" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors bg-white min-h-[42px]">
                            <option value="">Select Method</option>
                            @foreach($paymentMethods as $method)
                                <option value="{{ $method->id }}" {{ old('payment_method_id') == $method->id ? 'selected' : '' }}>
                                    {{ $method->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="bank_account_id" class="block text-sm font-medium text-gray-700 mb-1">Deposit To (Optional)</label>
                        <select name="bank_account_id" id="bank_account_id" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors bg-white min-h-[42px]">
                            <option value="">Select Account</option>
                            @foreach($bankAccounts as $account)
                                <option value="{{ $account->id }}" {{ old('bank_account_id') == $account->id ? 'selected' : '' }}>
                                    {{ $account->bank_name }} - {{ $account->account_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="reference" class="block text-sm font-medium text-gray-700 mb-1">Reference / Check No.</label>
                        <input type="text" name="reference" id="reference" value="{{ old('reference') }}" placeholder="Optional reference"
                               class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors min-h-[42px]">
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (Internal)</label>
                        <textarea name="notes" id="notes" rows="1" placeholder="Optional notes" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden" x-show="selectedClient" style="display: none;">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row justify-between items-center gap-3">
                <h3 class="text-lg font-bold text-gray-900 tracking-tight">Allocate Payment</h3>
                <div class="bg-white px-3 py-1.5 rounded-lg border border-gray-200 shadow-sm text-sm font-semibold text-gray-700">
                    Remaining to Allocate: <span x-text="'RWF ' + remainingAmount().toFixed(2)" :class="{'text-red-500': remainingAmount() < 0, 'text-green-600': remainingAmount() === 0}"></span>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200/60">
                    <thead class="bg-gray-50/30">
                        <tr>
                            <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Invoice #</th>
                            <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Issue Date</th>
                            <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Original Amount</th>
                            <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Balance Due</th>
                            <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Payment Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        <template x-for="(invoice, index) in availableInvoices" :key="invoice.id">
                            <tr :class="{'bg-blue-50/40': allocations[invoice.id] > 0}" class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <input type="hidden" :name="`allocations[${index}][invoice_id]`" :value="invoice.id" :disabled="!allocations[invoice.id] || allocations[invoice.id] <= 0">
                                    <span class="text-sm font-bold text-blue-600" x-text="invoice.invoice_number"></span>
                                    <span x-show="invoice.status === 'Overdue'" class="ml-2 px-2.5 py-0.5 inline-flex text-[11px] font-bold rounded-full bg-red-100 text-red-800 uppercase tracking-wide">Overdue</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 font-medium" x-text="new Date(invoice.issue_date).toLocaleDateString()"></td>
                                <td class="px-6 py-4 text-sm text-gray-500 text-right">RWF <span x-text="parseFloat(invoice.total_amount).toFixed(2)"></span></td>
                                <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">RWF <span x-text="parseFloat(invoice.balance_due).toFixed(2)"></span></td>
                                <td class="px-6 py-4 text-right">
                                    <div class="relative rounded-xl shadow-sm w-40 ml-auto">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm font-medium">RWF</span>
                                        </div>
                                        <input type="number" :name="`allocations[${index}][amount]`" x-model.number="allocations[invoice.id]" :max="invoice.balance_due" min="0" step="0.01" :disabled="!allocations[invoice.id] && remainingAmount() <= 0"
                                               class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-12 sm:text-sm border-gray-300 rounded-xl transition-colors min-h-[42px]">
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="availableInvoices.length === 0">
                            <td colspan="5" class="py-12 text-center">
                                <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 border border-gray-100">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <p class="text-sm text-gray-500 font-medium">No open invoices found for this client.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm px-6 py-4 flex items-center justify-end gap-3 mb-8">
            <a href="{{ route('admin.finance.payments.index') }}" class="inline-flex justify-center px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">Cancel</a>
            <button type="submit" x-bind:disabled="remainingAmount() !== 0 || availableInvoices.length === 0 || totalAmount <= 0" class="inline-flex justify-center px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Save Payment
            </button>
        </div>
    </form>

    <script>
        function paymentForm(allOpenInvoices, initialClientId, initialInvoiceId) {
            return {
                allInvoices: allOpenInvoices,
                selectedClient: initialClientId ? String(initialClientId) : '',
                availableInvoices: [],
                allocations: {},
                totalAmount: 0,
                
                init() {
                    if (this.selectedClient) {
                        this.updateAvailableInvoices();
                        
                        if (initialInvoiceId) {
                            let invoice = this.availableInvoices.find(i => i.id === initialInvoiceId);
                            if (invoice) {
                                this.totalAmount = parseFloat(invoice.balance_due);
                                this.allocations[invoice.id] = this.totalAmount;
                            }
                        }
                    }
                },
                
                updateAvailableInvoices() {
                    this.availableInvoices = this.allInvoices.filter(i => i.client_id == this.selectedClient);
                    this.allocations = {};
                    this.autoAllocate();
                },
                
                autoAllocate() {
                    // Reset allocations
                    this.allocations = {};
                    let amountToAllocate = parseFloat(this.totalAmount) || 0;
                    
                    if (amountToAllocate <= 0) return;

                    // Sort oldest first
                    let sortedInvoices = [...this.availableInvoices].sort((a, b) => new Date(a.issue_date) - new Date(b.issue_date));
                    
                    for (let invoice of sortedInvoices) {
                        let balance = parseFloat(invoice.balance_due);
                        if (amountToAllocate >= balance) {
                            this.allocations[invoice.id] = balance;
                            amountToAllocate -= balance;
                        } else if (amountToAllocate > 0) {
                            this.allocations[invoice.id] = parseFloat(amountToAllocate.toFixed(2));
                            amountToAllocate = 0;
                        } else {
                            this.allocations[invoice.id] = 0;
                        }
                    }
                },
                
                remainingAmount() {
                    let totalAllocated = 0;
                    for (let id in this.allocations) {
                        totalAllocated += parseFloat(this.allocations[id]) || 0;
                    }
                    let remaining = (parseFloat(this.totalAmount) || 0) - totalAllocated;
                    return parseFloat(remaining.toFixed(2));
                }
            }
        }
    </script>
</x-layouts.admin>
