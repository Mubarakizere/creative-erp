<x-layouts.admin title="Edit Purchase Invoice">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Procurement', 'url' => route('admin.procurement.pos.index')],
                ['label' => 'Purchase Invoices', 'url' => route('admin.procurement.invoices.index')],
                ['label' => $invoice->invoice_number, 'url' => route('admin.procurement.invoices.show', $invoice->id)],
                ['label' => 'Edit'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    @can('update', $invoice)
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.procurement.invoices.show', $invoice->id) }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 mb-2 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Invoice
            </a>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Invoice: {{ $invoice->invoice_number }}</h1>
            <p class="mt-1 text-sm text-gray-500 font-medium">Update existing invoice details and item amounts.</p>
        </div>
    </div>

    @php
        // Prepare Alpine JS data for existing items
        $mappedItems = $invoice->items->map(function($item) {
            return [
                'product_id' => $item->product_id,
                'purchase_order_item_id' => $item->purchase_order_item_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'tax' => $item->tax ?? 0,
                'discount' => $item->discount ?? 0,
                'product_name' => $item->product?->name ?? 'Product'
            ];
        });
        $alpineData = "{ items: " . Js::from($mappedItems) . " }";
    @endphp

    <div class="mt-6" x-data="{{ $alpineData }}">
        <form action="{{ route('admin.procurement.invoices.update', $invoice->id) }}" method="POST" id="invoice-form">
            @csrf
            @method('PUT')
            
            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden mb-6">
                <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
                    <h3 class="text-lg font-bold text-gray-900 tracking-tight">Invoice Details</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="col-span-1 lg:col-span-2">
                            <label for="invoice_number" class="block text-sm font-medium text-gray-700 mb-1">Invoice Number <span class="text-red-500">*</span></label>
                            <input type="text" name="invoice_number" id="invoice_number" value="{{ old('invoice_number', $invoice->invoice_number) }}" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">
                            @error('invoice_number') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="col-span-1 lg:col-span-2">
                            <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-1">Supplier <span class="text-red-500">*</span></label>
                            @if($invoice->purchase_order_id)
                                <input type="hidden" name="supplier_id" value="{{ $invoice->supplier_id }}">
                                <input type="text" value="{{ $invoice->supplier?->name }}" readonly class="block w-full rounded-xl border-gray-200 bg-gray-50 text-gray-500 sm:text-sm">
                            @else
                                <select id="supplier_id" name="supplier_id" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors bg-white">
                                    <option value="">Select a Supplier</option>
                                    @foreach($suppliers ?? \App\Models\Supplier::all() as $sup)
                                        <option value="{{ $sup->id }}" {{ old('supplier_id', $invoice->supplier_id) == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                                    @endforeach
                                </select>
                            @endif
                            @error('supplier_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="col-span-1 sm:col-span-1 lg:col-span-2">
                            <label for="invoice_date" class="block text-sm font-medium text-gray-700 mb-1">Invoice Date <span class="text-red-500">*</span></label>
                            <input type="date" name="invoice_date" id="invoice_date" value="{{ old('invoice_date', $invoice->invoice_date->format('Y-m-d')) }}" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">
                            @error('invoice_date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        
                        <div class="col-span-1 sm:col-span-1 lg:col-span-2">
                            <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Due Date <span class="text-red-500">*</span></label>
                            <input type="date" name="due_date" id="due_date" value="{{ old('due_date', $invoice->due_date->format('Y-m-d')) }}" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">
                            @error('due_date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden mb-6">
                <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 tracking-tight">Line Items</h3>
                        <p class="mt-1 text-sm text-gray-500 font-medium">Verify or adjust invoice items and pricing.</p>
                    </div>
                    @if(!$invoice->purchase_order_id)
                    <button type="button" @click="items.push({ product_id: '', quantity: 1, unit_price: 0, tax: 0, discount: 0, product_name: '' })" class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-700 bg-blue-100 rounded-xl hover:bg-blue-200 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Add Item
                    </button>
                    @endif
                </div>
                
                @error('items') <p class="mt-2 text-sm text-red-600 mb-4 px-6">{{ $message }}</p> @enderror
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200/60">
                        <thead class="bg-gray-50/30">
                            <tr>
                                <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Product</th>
                                <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-32">Qty <span class="text-red-500">*</span></th>
                                <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-40">Unit Price <span class="text-red-500">*</span></th>
                                <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-32">Tax</th>
                                <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-32">Discount</th>
                                <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-40">Total</th>
                                @if(!$invoice->purchase_order_id)
                                <th class="px-6 py-3 text-right text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-16"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <template x-for="(item, index) in items" :key="index">
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($invoice->purchase_order_id)
                                            <span class="text-sm font-medium text-gray-900" x-text="item.product_name"></span>
                                            <input type="hidden" :name="'items[' + index + '][product_id]'" x-model="item.product_id">
                                            <input type="hidden" :name="'items[' + index + '][purchase_order_item_id]'" x-model="item.purchase_order_item_id">
                                        @else
                                            <select x-model="item.product_id" :name="'items[' + index + '][product_id]'" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm bg-white">
                                                <option value="">Select Product</option>
                                                @foreach(\App\Models\Product::all() as $product)
                                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="number" step="0.01" min="0.01" x-model="item.quantity" :name="'items[' + index + '][quantity]'" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" {{ $invoice->purchase_order_id ? 'readonly' : '' }}>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="number" step="0.01" min="0" x-model="item.unit_price" :name="'items[' + index + '][unit_price]'" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="number" step="0.01" min="0" x-model="item.tax" :name="'items[' + index + '][tax]'" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="number" step="0.01" min="0" x-model="item.discount" :name="'items[' + index + '][discount]'" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium text-gray-900" x-text="(
                                            ((parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0)) 
                                            + (parseFloat(item.tax) || 0) 
                                            - (parseFloat(item.discount) || 0)
                                        ).toFixed(2)"></span>
                                        <input type="hidden" :name="'items[' + index + '][total]'" :value="(
                                            ((parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0)) 
                                            + (parseFloat(item.tax) || 0) 
                                            - (parseFloat(item.discount) || 0)
                                        ).toFixed(2)">
                                    </td>
                                    @if(!$invoice->purchase_order_id)
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <button type="button" @click="items.splice(index, 1)" class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" x-show="items.length > 1">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                    @endif
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Totals Summary Section --}}
                <div class="px-6 py-6 bg-gray-50/50 border-t border-gray-100 flex justify-end">
                    <div class="w-64 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500 font-medium">Subtotal:</span>
                            <span class="text-sm font-semibold text-gray-900" x-text="items.reduce((acc, item) => acc + ((parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0)), 0).toFixed(2)"></span>
                            <input type="hidden" name="subtotal" :value="items.reduce((acc, item) => acc + ((parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0)), 0).toFixed(2)">
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500 font-medium">Total Tax:</span>
                            <span class="text-sm font-semibold text-gray-900" x-text="items.reduce((acc, item) => acc + (parseFloat(item.tax) || 0), 0).toFixed(2)"></span>
                            <input type="hidden" name="tax_amount" :value="items.reduce((acc, item) => acc + (parseFloat(item.tax) || 0), 0).toFixed(2)">
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500 font-medium">Total Discount:</span>
                            <span class="text-sm font-semibold text-gray-900" x-text="items.reduce((acc, item) => acc + (parseFloat(item.discount) || 0), 0).toFixed(2)"></span>
                            <input type="hidden" name="discount_amount" :value="items.reduce((acc, item) => acc + (parseFloat(item.discount) || 0), 0).toFixed(2)">
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                            <span class="text-base font-bold text-gray-900">Grand Total:</span>
                            <span class="text-lg font-bold text-blue-600">RWF <span x-text="items.reduce((acc, item) => acc + (((parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0)) + (parseFloat(item.tax) || 0) - (parseFloat(item.discount) || 0)), 0).toFixed(2)"></span></span>
                            <input type="hidden" name="grand_total" :value="items.reduce((acc, item) => acc + (((parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0)) + (parseFloat(item.tax) || 0) - (parseFloat(item.discount) || 0)), 0).toFixed(2)">
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden px-6 py-4 flex items-center justify-end gap-3 mb-8">
                <a href="{{ route('admin.procurement.invoices.show', $invoice->id) }}" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">Cancel</a>
                <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none hover:shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Update Invoice
                </button>
            </div>
        </form>
    </div>
    @else
    <div class="text-center py-16 bg-white rounded-2xl border border-gray-200/60 shadow-sm">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4 border border-red-200">
            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Access Denied</h3>
        <p class="text-sm text-gray-500 font-medium">You do not have permission to edit invoices.</p>
        <div class="mt-6">
            <a href="{{ route('admin.procurement.invoices.show', $invoice->id) }}" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all">Return to Invoice</a>
        </div>
    </div>
    @endcan
</x-layouts.admin>
