<x-layouts.admin title="Create Invoice">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Finance', 'url' => '#'],
                ['label' => 'Invoices', 'url' => route('admin.finance.invoices.index')],
                ['label' => 'Create Invoice']
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Create New Invoice</h1>
        <p class="mt-1 text-sm text-gray-500">Generate a new invoice for a client.</p>
    </div>

    <form method="POST" action="{{ route('admin.finance.invoices.store') }}" 
          x-data="invoiceForm({{ isset($quotation) ? json_encode($quotation->items->map(function($item) { return ['description' => $item->product_name, 'quantity' => $item->quantity, 'unit_price' => $item->unit_price]; })) : '[]' }})" 
          class="space-y-6">
        @csrf
        
        @if(isset($quotation))
            <input type="hidden" name="quotation_id" value="{{ $quotation->id }}">
            <div class="bg-blue-50 text-blue-700 p-4 rounded-lg flex items-center mb-6">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Creating invoice from Quotation <strong>{{ $quotation->quotation_number }}</strong>.
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-card>
                <x-slot:header>Invoice Details</x-slot:header>
                
                <div class="space-y-4">
                    <div>
                        <label for="client_id" class="block text-sm font-medium text-gray-700">Client <span class="text-red-500">*</span></label>
                        <select name="client_id" id="client_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">Select a Client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ (old('client_id', $quotation->account_id ?? '') == $client->id) ? 'selected' : '' }}>
                                    {{ $client->display_name ?: ($client->company_name ?: trim($client->first_name . ' ' . $client->last_name)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('client_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="project_id" class="block text-sm font-medium text-gray-700">Project (Optional)</label>
                        <select name="project_id" id="project_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">No Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>
            </x-card>

            <x-card>
                <x-slot:header>Dates & Terms</x-slot:header>
                
                <div class="space-y-4">
                    <div>
                        <label for="issue_date" class="block text-sm font-medium text-gray-700">Issue Date <span class="text-red-500">*</span></label>
                        <input type="date" name="issue_date" id="issue_date" required value="{{ old('issue_date', now()->format('Y-m-d')) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @error('issue_date') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date <span class="text-red-500">*</span></label>
                        <input type="date" name="due_date" id="due_date" required value="{{ old('due_date', now()->addDays(30)->format('Y-m-d')) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @error('due_date') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes (Visible to Client)</label>
                        <textarea name="notes" id="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('notes', $quotation->notes ?? '') }}</textarea>
                    </div>
                </div>
            </x-card>
        </div>

        <x-card class="p-0 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Line Items</h3>
                <button type="button" @click="addItem()" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Item
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white border-b border-gray-200">
                            <th class="py-3 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider w-1/2">Description</th>
                            <th class="py-3 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider w-1/6">Qty</th>
                            <th class="py-3 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider w-1/6">Price</th>
                            <th class="py-3 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right w-1/6">Total</th>
                            <th class="py-3 px-4 w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        <template x-for="(item, index) in items" :key="index">
                            <tr>
                                <td class="py-3 px-6">
                                    <input type="text" x-model="item.description" :name="`items[${index}][description]`" required placeholder="Item description"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                </td>
                                <td class="py-3 px-6">
                                    <input type="number" x-model="item.quantity" :name="`items[${index}][quantity]`" required min="1" step="0.01"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                </td>
                                <td class="py-3 px-6">
                                    <input type="number" x-model="item.unit_price" :name="`items[${index}][unit_price]`" required min="0" step="0.01"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                </td>
                                <td class="py-3 px-6 text-right font-medium text-gray-900">
                                    $<span x-text="(item.quantity * item.unit_price).toFixed(2)"></span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700" title="Remove item">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="items.length === 0">
                            <td colspan="5" class="py-6 text-center text-gray-500 text-sm">
                                No items added. Click "Add Item" to begin.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                <div class="w-64 space-y-3">
                    <div class="flex justify-between text-base font-bold text-gray-900 border-t border-gray-300 pt-3">
                        <span>Total</span>
                        <span>$<span x-text="calculateTotal().toFixed(2)"></span></span>
                    </div>
                </div>
            </div>
        </x-card>

        <div class="flex justify-end gap-3">
            <x-button type="default" href="{{ route('admin.finance.invoices.index') }}">Cancel</x-button>
            <x-button type="primary" submit>Save Invoice</x-button>
        </div>
    </form>

    <script>
        function invoiceForm(initialItems) {
            return {
                items: initialItems.length > 0 ? initialItems : [
                    { description: '', quantity: 1, unit_price: 0 }
                ],
                addItem() {
                    this.items.push({ description: '', quantity: 1, unit_price: 0 });
                },
                removeItem(index) {
                    this.items.splice(index, 1);
                },
                calculateTotal() {
                    return this.items.reduce((total, item) => total + (item.quantity * item.unit_price), 0);
                }
            }
        }
    </script>
</x-layouts.admin>
