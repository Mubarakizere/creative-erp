<x-layouts.admin title="Edit Quotation">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'CRM', 'url' => '#'],
                ['label' => 'Quotations', 'url' => route('admin.crm.quotations.index')],
                ['label' => $quotation->quotation_number, 'url' => route('admin.crm.quotations.show', $quotation)],
                ['label' => 'Edit'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    @can('update', $quotation)
    <div class="mb-8">
        <a href="{{ route('admin.crm.quotations.show', $quotation) }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 mb-2 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Quotation
        </a>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Quotation</h1>
        <p class="mt-1 text-sm text-gray-500 font-medium">Update quotation {{ $quotation->quotation_number }}.</p>
    </div>

    <form method="POST" action="{{ route('admin.crm.quotations.update', $quotation) }}" 
          x-data="quotationForm({{ json_encode($taxes) }}, {{ json_encode($quotation->items) }})" 
          class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column: Main Details --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Quotation Details --}}
                <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                    <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
                        <h3 class="text-lg font-bold text-gray-900 tracking-tight">Quotation Details</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <x-input name="reference" label="Reference Number" placeholder="e.g. PO-12345" :value="$quotation->reference" />
                            <x-input name="valid_until" type="date" label="Valid Until" required :value="\Carbon\Carbon::parse($quotation->valid_until)->format('Y-m-d')" />

                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Customer (Select One)</label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <x-select name="account_id" label="Account" :selected="$quotation->account_id">
                                        <option value="">Select Account</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}" @selected(old('account_id', $quotation->account_id) == $account->id)>{{ $account->name }}</option>
                                        @endforeach
                                    </x-select>
                                    <x-select name="opportunity_id" label="Opportunity" :selected="$quotation->opportunity_id">
                                        <option value="">Select Opportunity</option>
                                        @foreach($opportunities as $opportunity)
                                            <option value="{{ $opportunity->id }}" @selected(old('opportunity_id', $quotation->opportunity_id) == $opportunity->id)>{{ $opportunity->name }}</option>
                                        @endforeach
                                    </x-select>
                                    <x-select name="lead_id" label="Lead" :selected="$quotation->lead_id">
                                        <option value="">Select Lead</option>
                                        @foreach($leads as $lead)
                                            <option value="{{ $lead->id }}" @selected(old('lead_id', $quotation->lead_id) == $lead->id)>{{ $lead->first_name }} {{ $lead->last_name }}</option>
                                        @endforeach
                                    </x-select>
                                    <x-select name="contact_id" label="Contact" :selected="$quotation->contact_id">
                                        <option value="">Select Contact</option>
                                        @foreach($contacts as $contact)
                                            <option value="{{ $contact->id }}" @selected(old('contact_id', $quotation->contact_id) == $contact->id)>{{ $contact->first_name }} {{ $contact->last_name }}</option>
                                        @endforeach
                                    </x-select>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Select the primary entity this quotation is for.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Line Items --}}
                <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                    <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900 tracking-tight">Line Items</h3>
                        <button type="button" @click.prevent="addItem()" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Item
                        </button>
                    </div>
                    
                    <div class="p-6 overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="py-2 text-xs font-semibold text-gray-600 w-1/3">Product / Service</th>
                                    <th class="py-2 text-xs font-semibold text-gray-600 w-24">Qty</th>
                                    <th class="py-2 text-xs font-semibold text-gray-600 w-32">Price</th>
                                    <th class="py-2 text-xs font-semibold text-gray-600 w-32">Discount</th>
                                    <th class="py-2 text-xs font-semibold text-gray-600 w-32">Tax</th>
                                    <th class="py-2 text-right text-xs font-semibold text-gray-600 w-32">Total</th>
                                    <th class="py-2 w-10"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, index) in items" :key="index">
                                    <tr class="border-b border-gray-100 last:border-0 group">
                                        <td class="py-3 pr-2">
                                            <input type="hidden" x-model="item.id" :name="'items['+index+'][id]'">
                                            <input type="text" x-model="item.product_name" :name="'items['+index+'][product_name]'" required
                                                   class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors" placeholder="Item Name">
                                        </td>
                                        <td class="py-3 pr-2">
                                            <input type="number" step="0.01" min="0.01" x-model.number="item.quantity" :name="'items['+index+'][quantity]'" required
                                                   class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">
                                        </td>
                                        <td class="py-3 pr-2">
                                            <input type="number" step="0.01" min="0" x-model.number="item.unit_price" :name="'items['+index+'][unit_price]'" required
                                                   class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">
                                        </td>
                                        <td class="py-3 pr-2">
                                            <div class="flex rounded-xl shadow-sm">
                                                <input type="number" step="0.01" min="0" x-model.number="item.discount" :name="'items['+index+'][discount]'"
                                                       class="block w-full rounded-none rounded-l-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">
                                                <select x-model="item.discount_type" :name="'items['+index+'][discount_type]'"
                                                        class="block w-16 rounded-none rounded-r-xl border-gray-300 bg-gray-50 py-2 pl-3 pr-8 text-gray-500 focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">
                                                    <option value="fixed">RWF</option>
                                                    <option value="percentage">%</option>
                                                </select>
                                            </div>
                                        </td>
                                        <td class="py-3 pr-2">
                                            <select x-model="item.tax_id" :name="'items['+index+'][tax_id]'"
                                                    class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">
                                                <option value="">No Tax</option>
                                                @foreach($taxes as $tax)
                                                    <option value="{{ $tax->id }}">{{ $tax->name }} ({{ (float)$tax->rate }}% {{ ucfirst($tax->type) }})</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="py-3 pr-2 text-right font-medium text-gray-900" x-text="'RWF ' + calculateItemTotal(item).toFixed(2)">
                                        </td>
                                        <td class="py-3 text-right">
                                            <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors focus:outline-none">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Totals Summary --}}
                    <div class="mt-6 border-t border-gray-200 pt-6 flex justify-end">
                        <div class="w-full sm:w-1/2 md:w-1/3 space-y-3">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Subtotal</span>
                                <span class="font-medium" x-text="'RWF ' + summary.subtotal.toFixed(2)"></span>
                            </div>
                            <div class="flex justify-between text-sm text-green-600" x-show="summary.discount > 0">
                                <span>Total Savings</span>
                                <span class="font-medium" x-text="'RWF ' + summary.discount.toFixed(2)"></span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600" x-show="summary.tax > 0">
                                <span>Tax</span>
                                <span class="font-medium" x-text="'RWF ' + summary.tax.toFixed(2)"></span>
                            </div>
                            <div class="pt-3 border-t border-gray-200 flex justify-between text-base font-bold text-gray-900">
                                <span>Grand Total</span>
                                <span x-text="'RWF ' + summary.grandTotal.toFixed(2)"></span>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>

                {{-- Terms & Notes --}}
                <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                    <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
                        <h3 class="text-lg font-bold text-gray-900 tracking-tight">Notes & Terms</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <x-textarea name="notes" label="Customer Notes" placeholder="Notes visible to the customer..." rows="3" :value="$quotation->notes" />
                        <x-textarea name="terms" label="Terms & Conditions" placeholder="Standard terms and conditions..." rows="3" :value="$quotation->terms" />
                    </div>
                </div>
            </div>

            {{-- Right Column: Settings --}}
            <div class="space-y-6">
                <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                    <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
                        <h3 class="text-lg font-bold text-gray-900 tracking-tight">Settings</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <x-select name="template_id" label="Quotation Template" :selected="$quotation->template_id">
                            <option value="">Default Template</option>
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}" @selected(old('template_id', $quotation->template_id) == $template->id)>{{ $template->name }}</option>
                            @endforeach
                        </x-select>

                        <x-select name="payment_term_id" label="Payment Terms" :selected="$quotation->payment_term_id">
                            <option value="">Custom Terms</option>
                            @foreach($paymentTerms as $term)
                                <option value="{{ $term->id }}" @selected(old('payment_term_id', $quotation->payment_term_id) == $term->id)>{{ $term->name }}</option>
                            @endforeach
                        </x-select>

                        <x-input name="currency" label="Currency" :value="$quotation->currency ?? 'RWF'" />
                    </div>
                </div>

                <div class="flex flex-col gap-3">
                    <button type="submit" class="w-full inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none hover:shadow-md">
                        Update Quotation
                    </button>
                    <a href="{{ route('admin.crm.quotations.show', $quotation) }}" class="w-full inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
    @else
    <div class="text-center py-16 bg-white rounded-2xl border border-gray-200/60 shadow-sm">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4 border border-red-200">
            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Access Denied</h3>
        <p class="text-sm text-gray-500 font-medium">You do not have permission to edit this quotation.</p>
        <div class="mt-6">
            <a href="{{ route('admin.crm.quotations.show', $quotation) }}" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all">Return to Quotation</a>
        </div>
    </div>
    @endcan

    {{-- Alpine Component Logic --}}
    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('quotationForm', (taxesData, existingItems) => ({
                taxes: taxesData,
                items: existingItems.length > 0 ? existingItems : [
                    { id: null, product_name: '', quantity: 1, unit_price: 0, discount: 0, discount_type: 'fixed', tax_id: '' }
                ],
                
                addItem() {
                    this.items.push({ id: null, product_name: '', quantity: 1, unit_price: 0, discount: 0, discount_type: 'fixed', tax_id: '' });
                },
                
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },
                
                calculateItemTotal(item) {
                    let qty = parseFloat(item.quantity) || 0;
                    let price = parseFloat(item.unit_price) || 0;
                    let lineGross = qty * price;
                    
                    let discountAmt = 0;
                    let discountVal = parseFloat(item.discount) || 0;
                    if (item.discount_type === 'percentage') {
                        discountAmt = lineGross * (discountVal / 100);
                    } else {
                        discountAmt = discountVal;
                    }
                    
                    let lineNet = lineGross - discountAmt;
                    
                    let taxAmt = 0;
                    if (item.tax_id) {
                        let tax = this.taxes.find(t => t.id == item.tax_id);
                        if (tax) {
                            let rate = parseFloat(tax.rate) / 100;
                            if (tax.type === 'inclusive') {
                                // Tax is already in the price, do nothing to total
                            } else {
                                taxAmt = lineNet * rate;
                            }
                        }
                    }
                    
                    return lineNet + taxAmt;
                },

                get summary() {
                    let subtotal = 0;
                    let totalDiscount = 0;
                    let totalTax = 0;
                    let grandTotal = 0;

                    this.items.forEach(item => {
                        let qty = parseFloat(item.quantity) || 0;
                        let price = parseFloat(item.unit_price) || 0;
                        let lineGross = qty * price;
                        
                        let discountAmt = 0;
                        let discountVal = parseFloat(item.discount) || 0;
                        if (item.discount_type === 'percentage') {
                            discountAmt = lineGross * (discountVal / 100);
                        } else {
                            discountAmt = discountVal;
                        }
                        
                        totalDiscount += discountAmt;
                        let lineNet = lineGross - discountAmt;
                        
                        let taxAmt = 0;
                        let itemSub = lineNet;
                        let itemTot = lineNet;

                        if (item.tax_id) {
                            let tax = this.taxes.find(t => t.id == item.tax_id);
                            if (tax) {
                                let rate = parseFloat(tax.rate) / 100;
                                if (tax.type === 'inclusive') {
                                    itemTot = lineNet;
                                    taxAmt = itemTot - (itemTot / (1 + rate));
                                    itemSub = itemTot - taxAmt;
                                } else {
                                    itemSub = lineNet;
                                    taxAmt = itemSub * rate;
                                    itemTot = itemSub + taxAmt;
                                }
                            }
                        }

                        subtotal += itemSub;
                        totalTax += taxAmt;
                        grandTotal += itemTot;
                    });

                    return {
                        subtotal: subtotal,
                        discount: totalDiscount,
                        tax: totalTax,
                        grandTotal: grandTotal
                    };
                }
            }));
        });
    </script>
    @endpush
</x-layouts.admin>
