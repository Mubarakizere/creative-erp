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
        <h1 class="text-2xl font-bold text-gray-900">Record Payment</h1>
        <p class="mt-1 text-sm text-gray-500">Record a new payment and allocate it to open invoices.</p>
    </div>

    @if($errors->any())
        <div class="mb-6 bg-red-50 p-4 rounded-md">
            <ul class="list-disc pl-5 text-sm text-red-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.finance.payments.store') }}" 
          x-data="paymentForm({{ json_encode($openInvoices) }}, {{ $preselectedInvoice ? $preselectedInvoice->client_id : 'null' }}, {{ $preselectedInvoice ? $preselectedInvoice->id : 'null' }})" 
          class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-card>
                <x-slot:header>Payment Details</x-slot:header>
                
                <div class="space-y-4">
                    <div>
                        <label for="client_id" class="block text-sm font-medium text-gray-700">Client <span class="text-red-500">*</span></label>
                        <select name="client_id" id="client_id" x-model="selectedClient" @change="updateAvailableInvoices" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">Select a Client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700">Amount Received <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" name="amount" id="amount" x-model="totalAmount" @input="autoAllocate" required min="0.01" step="0.01"
                                   class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-7 sm:text-sm border-gray-300 rounded-md" placeholder="0.00">
                        </div>
                    </div>

                    <div>
                        <label for="payment_date" class="block text-sm font-medium text-gray-700">Payment Date <span class="text-red-500">*</span></label>
                        <input type="date" name="payment_date" id="payment_date" required value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                </div>
            </x-card>

            <x-card>
                <x-slot:header>Method & Reference</x-slot:header>
                
                <div class="space-y-4">
                    <div>
                        <label for="payment_method_id" class="block text-sm font-medium text-gray-700">Payment Method <span class="text-red-500">*</span></label>
                        <select name="payment_method_id" id="payment_method_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">Select Method</option>
                            @foreach($paymentMethods as $method)
                                <option value="{{ $method->id }}" {{ old('payment_method_id') == $method->id ? 'selected' : '' }}>
                                    {{ $method->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="bank_account_id" class="block text-sm font-medium text-gray-700">Deposit To (Optional)</label>
                        <select name="bank_account_id" id="bank_account_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">Select Account</option>
                            @foreach($bankAccounts as $account)
                                <option value="{{ $account->id }}" {{ old('bank_account_id') == $account->id ? 'selected' : '' }}>
                                    {{ $account->bank_name }} - {{ $account->account_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="reference" class="block text-sm font-medium text-gray-700">Reference / Check No.</label>
                        <input type="text" name="reference" id="reference" value="{{ old('reference') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes (Internal)</label>
                        <textarea name="notes" id="notes" rows="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </x-card>
        </div>

        <x-card class="p-0 overflow-hidden" x-show="selectedClient">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Allocate Payment</h3>
                <span class="text-sm font-semibold text-gray-700">
                    Remaining to Allocate: $<span x-text="remainingAmount().toFixed(2)" :class="{'text-red-500': remainingAmount() < 0, 'text-green-500': remainingAmount() === 0}"></span>
                </span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white border-b border-gray-200">
                            <th class="py-3 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Invoice #</th>
                            <th class="py-3 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Issue Date</th>
                            <th class="py-3 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Original Amount</th>
                            <th class="py-3 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Balance Due</th>
                            <th class="py-3 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Payment Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        <template x-for="(invoice, index) in availableInvoices" :key="invoice.id">
                            <tr :class="{'bg-blue-50': allocations[invoice.id] > 0}">
                                <td class="py-3 px-6">
                                    <input type="hidden" :name="`allocations[${index}][invoice_id]`" :value="invoice.id" :disabled="!allocations[invoice.id] || allocations[invoice.id] <= 0">
                                    <span class="text-sm font-medium text-blue-600" x-text="invoice.invoice_number"></span>
                                    <span x-show="invoice.status === 'Overdue'" class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Overdue</span>
                                </td>
                                <td class="py-3 px-6 text-sm text-gray-500" x-text="new Date(invoice.issue_date).toLocaleDateString()"></td>
                                <td class="py-3 px-6 text-sm text-gray-500 text-right">$<span x-text="parseFloat(invoice.total_amount).toFixed(2)"></span></td>
                                <td class="py-3 px-6 text-sm font-medium text-gray-900 text-right">$<span x-text="parseFloat(invoice.balance_due).toFixed(2)"></span></td>
                                <td class="py-3 px-6 text-right">
                                    <div class="relative rounded-md shadow-sm w-32 ml-auto">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">$</span>
                                        </div>
                                        <input type="number" :name="`allocations[${index}][amount]`" x-model.number="allocations[invoice.id]" :max="invoice.balance_due" min="0" step="0.01" :disabled="!allocations[invoice.id] && remainingAmount() <= 0"
                                               class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-7 sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="availableInvoices.length === 0">
                            <td colspan="5" class="py-6 text-center text-gray-500 text-sm">
                                No open invoices found for this client.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-card>

        <div class="flex justify-end gap-3">
            <x-button type="default" href="{{ route('admin.finance.payments.index') }}">Cancel</x-button>
            <x-button type="primary" submit x-bind:disabled="remainingAmount() !== 0 || availableInvoices.length === 0 || totalAmount <= 0">Save Payment</x-button>
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
