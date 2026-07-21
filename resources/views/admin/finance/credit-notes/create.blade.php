<x-layouts.admin title="Issue Credit Note">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Finance', 'url' => '#'],
                ['label' => 'Credit Notes', 'url' => route('admin.finance.credit-notes.index')],
                ['label' => 'Issue']
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Issue Credit Note</h1>
        <p class="mt-1 text-sm text-gray-500">Create a new credit note for a client.</p>
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

    <form method="POST" action="{{ route('admin.finance.credit-notes.store') }}" class="space-y-6 max-w-3xl">
        @csrf

        <x-card>
            <div class="space-y-6">
                <div>
                    <label for="client_id" class="block text-sm font-medium text-gray-700">Client <span class="text-red-500">*</span></label>
                    <select name="client_id" id="client_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">Select a Client</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id', $preselectedInvoice ? $preselectedInvoice->client_id : '') == $client->id ? 'selected' : '' }}>
                                {{ $client->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if($preselectedInvoice)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Related Invoice</label>
                        <input type="hidden" name="invoice_id" value="{{ $preselectedInvoice->id }}">
                        <p class="mt-2 text-sm text-gray-900">{{ $preselectedInvoice->invoice_number }} (Balance: ${{ number_format($preselectedInvoice->balance_due, 2) }})</p>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700">Credit Amount <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" name="amount" id="amount" required min="0.01" step="0.01" value="{{ old('amount', $preselectedInvoice ? $preselectedInvoice->balance_due : '') }}"
                                   class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-7 sm:text-sm border-gray-300 rounded-md" placeholder="0.00">
                        </div>
                    </div>

                    <div>
                        <label for="issue_date" class="block text-sm font-medium text-gray-700">Issue Date <span class="text-red-500">*</span></label>
                        <input type="date" name="issue_date" id="issue_date" required value="{{ old('issue_date', now()->format('Y-m-d')) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                </div>

                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700">Reason for Credit <span class="text-red-500">*</span></label>
                    <textarea name="reason" id="reason" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('reason') }}</textarea>
                </div>
            </div>
            
            <x-slot:footer>
                <div class="flex justify-end gap-3">
                    <x-button type="default" href="{{ route('admin.finance.credit-notes.index') }}">Cancel</x-button>
                    <x-button type="primary" submit>Issue Credit Note</x-button>
                </div>
            </x-slot:footer>
        </x-card>
    </form>
</x-layouts.admin>
