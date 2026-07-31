<x-layouts.admin title="Create RFQ">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Procurement', 'url' => route('admin.procurement.requisitions.index')],
                ['label' => 'Request for Quotations', 'url' => route('admin.procurement.rfqs.index')],
                ['label' => 'Record Quotation'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    @can('create', App\Models\SupplierQuotation::class)
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.procurement.rfqs.index') }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 mb-2 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to RFQs
            </a>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Record Quotation</h1>
            <p class="mt-1 text-sm text-gray-500 font-medium">Create a new Request for Quotation.</p>
        </div>
    </div>

    <div class="mt-6" x-data="{ items: [{ product_id: '', quantity: 1, unit_price: 0, discount: 0, tax: 0 }] }">
        <form action="{{ route('admin.procurement.rfqs.store') }}" method="POST" id="rfq-form">
            @csrf
            
            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden mb-6">
                <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
                    <h3 class="text-lg font-bold text-gray-900 tracking-tight">Quotation Details</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="col-span-1 lg:col-span-2">
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Quotation Number <span class="text-red-500">*</span></label>
                            <input type="text" name="code" id="code" value="{{ old('code') }}" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">
                            @error('code') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="col-span-1 lg:col-span-2">
                            <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-1">Supplier <span class="text-red-500">*</span></label>
                            <select id="supplier_id" name="supplier_id" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors bg-white">
                                <option value="">Select a Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            @error('supplier_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        
                        <div class="col-span-1 sm:col-span-2 lg:col-span-4">
                            <label for="purchase_requisition_id" class="block text-sm font-medium text-gray-700 mb-1">From Purchase Requisition (Approved)</label>
                            <select id="purchase_requisition_id" name="purchase_requisition_id" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors bg-white">
                                <option value="">-- None --</option>
                                @foreach($requisitions as $pr)
                                    <option value="{{ $pr->id }}" {{ old('purchase_requisition_id') == $pr->id ? 'selected' : '' }}>{{ $pr->code }}</option>
                                @endforeach
                            </select>
                            @error('purchase_requisition_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="col-span-1 sm:col-span-1 lg:col-span-2">
                            <label for="issue_date" class="block text-sm font-medium text-gray-700 mb-1">Issue Date <span class="text-red-500">*</span></label>
                            <input type="date" name="issue_date" id="issue_date" value="{{ old('issue_date', date('Y-m-d')) }}" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">
                            @error('issue_date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        
                        <div class="col-span-1 sm:col-span-1 lg:col-span-2">
                            <label for="valid_until" class="block text-sm font-medium text-gray-700 mb-1">Valid Until <span class="text-red-500">*</span></label>
                            <input type="date" name="valid_until" id="valid_until" value="{{ old('valid_until') }}" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">
                            @error('valid_until') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden mb-6">
                <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 tracking-tight">Items (Prices, Discounts, Taxes)</h3>
                        <p class="mt-1 text-sm text-gray-500 font-medium">Add products and pricing details.</p>
                    </div>
                    <button type="button" @click="items.push({ product_id: '', quantity: 1, unit_price: 0, discount: 0, tax: 0 })" class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-700 bg-blue-100 rounded-xl hover:bg-blue-200 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Add Item
                    </button>
                </div>
                
                @error('items') <p class="mt-2 text-sm text-red-600 mb-4 px-6">{{ $message }}</p> @enderror
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200/60">
                        <thead class="bg-gray-50/30">
                            <tr>
                                <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Product <span class="text-red-500">*</span></th>
                                <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-32">Qty <span class="text-red-500">*</span></th>
                                <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-32">Price <span class="text-red-500">*</span></th>
                                <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-32">Discount</th>
                                <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-32">Tax</th>
                                <th class="px-6 py-3 text-right text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-16"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <template x-for="(item, index) in items" :key="index">
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <select x-model="item.product_id" :name="'items[' + index + '][product_id]'" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm bg-white">
                                            <option value="">Select Product</option>
                                            @foreach(\App\Models\Product::all() as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="number" step="0.01" min="0.01" x-model="item.quantity" :name="'items[' + index + '][quantity]'" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="number" step="0.01" min="0" x-model="item.unit_price" :name="'items[' + index + '][unit_price]'" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="number" step="0.01" min="0" x-model="item.discount" :name="'items[' + index + '][discount]'" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="number" step="0.01" min="0" x-model="item.tax" :name="'items[' + index + '][tax]'" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <button type="button" @click="items.splice(index, 1)" class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" x-show="items.length > 1">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden px-6 py-4 flex items-center justify-end gap-3 mb-8">
                <a href="{{ route('admin.procurement.rfqs.index') }}" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">Cancel</a>
                <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none hover:shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Record Quotation
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
        <p class="text-sm text-gray-500 font-medium">You do not have permission to create RFQs.</p>
        <div class="mt-6">
            <a href="{{ route('admin.procurement.rfqs.index') }}" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all">Return to RFQs</a>
        </div>
    </div>
    @endcan
</x-layouts.admin>