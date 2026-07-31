<x-layouts.admin title="Edit Invoice {{ $invoice->invoice_number }}">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Finance', 'url' => '#'],
                ['label' => 'Invoices', 'url' => route('admin.finance.invoices.index')],
                ['label' => $invoice->invoice_number, 'url' => route('admin.finance.invoices.show', $invoice)],
                ['label' => 'Edit']
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.finance.invoices.show', $invoice) }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 mb-2 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Invoice
            </a>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Invoice: {{ $invoice->invoice_number }}</h1>
            <p class="mt-1 text-sm text-gray-500 font-medium">Modify the invoice details before issuing.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.finance.invoices.update', $invoice) }}" 
          x-data="invoiceForm({{ json_encode($invoice->items->map(function($item) { return ['description' => $item->description, 'quantity' => $item->quantity, 'unit_price' => $item->unit_price]; })) }})" 
          class="space-y-6" id="invoice-form">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
                    <h3 class="text-lg font-bold text-gray-900 tracking-tight">Invoice Details</h3>
                </div>
                
                <div class="p-6 space-y-4">
                    <div>
                        <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1">Client <span class="text-red-500">*</span></label>
                        <select name="client_id" id="client_id" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors bg-white min-h-[42px]">
                            <option value="">Select a Client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ (old('client_id', $invoice->client_id) == $client->id) ? 'selected' : '' }}>
                                    {{ $client->display_name ?: ($client->company_name ?: trim($client->first_name . ' ' . $client->last_name)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('client_id') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="project_id" class="block text-sm font-medium text-gray-700 mb-1">Project (Optional)</label>
                        <select name="project_id" id="project_id" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors bg-white min-h-[42px]">
                            <option value="">No Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id', $invoice->project_id) == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
                    <h3 class="text-lg font-bold text-gray-900 tracking-tight">Dates & Terms</h3>
                </div>
                
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="issue_date" class="block text-sm font-medium text-gray-700 mb-1">Issue Date <span class="text-red-500">*</span></label>
                            <input type="date" name="issue_date" id="issue_date" required value="{{ old('issue_date', $invoice->issue_date->format('Y-m-d')) }}"
                                   class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors min-h-[42px]">
                            @error('issue_date') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Due Date <span class="text-red-500">*</span></label>
                            <input type="date" name="due_date" id="due_date" required value="{{ old('due_date', $invoice->due_date->format('Y-m-d')) }}"
                                   class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors min-h-[42px]">
                            @error('due_date') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (Visible to Client)</label>
                        <textarea name="notes" id="notes" rows="2" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">{{ old('notes', $invoice->notes) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900 tracking-tight">Line Items</h3>
                <button type="button" @click="addItem()" class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-700 bg-blue-100 rounded-xl hover:bg-blue-200 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 border border-blue-200">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Add Item
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200/60">
                    <thead class="bg-gray-50/30">
                        <tr>
                            <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-1/2">Description</th>
                            <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-1/6">Qty</th>
                            <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-1/6">Price</th>
                            <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-1/6">Total</th>
                            <th class="px-6 py-4 w-12 border-b border-gray-100"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        <template x-for="(item, index) in items" :key="index">
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <input type="text" x-model="item.description" :name="`items[${index}][description]`" required placeholder="Item description"
                                           class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors min-h-[42px]">
                                </td>
                                <td class="px-6 py-4">
                                    <input type="number" x-model="item.quantity" :name="`items[${index}][quantity]`" required min="1" step="0.01"
                                           class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors min-h-[42px]">
                                </td>
                                <td class="px-6 py-4">
                                    <input type="number" x-model="item.unit_price" :name="`items[${index}][unit_price]`" required min="0" step="0.01"
                                           class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors min-h-[42px]">
                                </td>
                                <td class="px-6 py-4 text-right font-medium text-gray-900 whitespace-nowrap">
                                    RWF <span x-text="(item.quantity * item.unit_price).toFixed(2)"></span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button type="button" @click="removeItem(index)" class="p-1.5 rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 transition-colors inline-flex" title="Remove item">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="items.length === 0">
                            <td colspan="5" class="py-12 text-center">
                                <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 border border-gray-100">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                </div>
                                <p class="text-sm text-gray-500 font-medium">No items added. Click "Add Item" to begin.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-5 bg-gray-50/50 border-t border-gray-100 flex justify-end">
                <div class="w-72 space-y-3">
                    <div class="flex justify-between items-center text-lg font-bold text-gray-900 border-t border-gray-200 pt-4">
                        <span>Total</span>
                        <span class="text-blue-600">RWF <span x-text="calculateTotal().toFixed(2)"></span></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm px-6 py-4 flex items-center justify-end gap-3 mb-8">
            <a href="{{ route('admin.finance.invoices.show', $invoice) }}" class="inline-flex justify-center px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">Cancel</a>
            <button type="submit" class="inline-flex justify-center px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-sm hover:shadow-md">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Update Invoice
            </button>
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
