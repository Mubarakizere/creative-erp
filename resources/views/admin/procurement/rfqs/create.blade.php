<x-layouts.admin title="Record Supplier Quotation">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Procurement', 'url' => route('admin.procurement.requisitions.index')],
                ['label' => 'RFQs & Quotations', 'url' => route('admin.procurement.rfqs.index')],
                ['label' => 'Record Quotation'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    @can('create', App\Models\SupplierQuotation::class)
    <div class="space-y-6" x-data="quotationForm({
        projectsData: {{ \Illuminate\Support\Js::from($projectsData) }},
        requisitionsData: {{ \Illuminate\Support\Js::from($requisitionsData) }},
        initialProjectId: '{{ old('project_id', $defaultProjectId ?? '') }}',
        initialCompanyId: '{{ old('company_id', $defaultCompanyId ?? '') }}',
        initialPrId: '{{ old('purchase_requisition_id', $selectedPrId ?? '') }}',
        initialItems: {{ \Illuminate\Support\Js::from(old('items', !empty($preloadedItems) ? $preloadedItems : [['product_id' => '', 'quantity' => 1, 'unit_price' => 0, 'discount' => 0, 'tax' => 0]])) }}
    })">
        {{-- Header & Action Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-sm text-slate-500 mb-1.5">
                    <a href="{{ route('admin.procurement.rfqs.index') }}" class="hover:text-indigo-600 font-semibold transition-colors flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        RFQs & Quotations
                    </a>
                    <span>/</span>
                    <span class="font-semibold text-slate-700">Record Quotation</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Record Supplier Quotation</h1>
                <p class="mt-1 text-sm text-slate-500 font-medium">Capture vendor pricing, discount structure, project scope, and company details.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.procurement.rfqs.index') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition-colors shadow-xs">
                    Cancel & Back
                </a>
            </div>
        </div>

        {{-- Linked Requisition Banner (if preselected from PR) --}}
        @if($selectedPr)
            <div class="bg-indigo-50/80 border border-indigo-200/80 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold shrink-0 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-indigo-700">Linked Purchase Requisition</span>
                            <span class="px-2 py-0.5 rounded-md text-[11px] font-mono font-bold bg-indigo-100 text-indigo-800">{{ $selectedPr->code }}</span>
                        </div>
                        <div class="text-xs text-slate-600 mt-0.5">
                            Project: <strong class="text-slate-800">{{ $selectedPr->project?->name ?? 'General Procurement' }}</strong> • Company: <strong class="text-slate-800">{{ $selectedPr->company?->name ?? 'N/A' }}</strong> • Requested by <span class="font-semibold text-slate-800">{{ $selectedPr->requestedBy?->name ?? 'System' }}</span>
                        </div>
                    </div>
                </div>
                <div class="text-xs font-bold text-indigo-900 bg-white px-3 py-1.5 rounded-xl border border-indigo-100 shrink-0">
                    {{ count($preloadedItems) }} Items Pre-filled
                </div>
            </div>
        @endif

        {{-- Error Summary --}}
        @if ($errors->any())
            <div class="rounded-2xl bg-rose-50 p-4 border border-rose-200 shadow-xs">
                <div class="flex items-start gap-3">
                    <div class="p-1.5 bg-rose-100 rounded-lg text-rose-700 flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-rose-900">There were {{ $errors->count() }} errors with your submission</h3>
                        <ul role="list" class="mt-1 text-xs text-rose-700 list-disc list-inside space-y-0.5 font-medium">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.procurement.rfqs.store') }}" method="POST" id="rfq-form" class="space-y-6">
            @csrf

            {{-- Card 1: Quotation, Project & Vendor Scope --}}
            <x-card>
                <x-slot:header>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 tracking-tight">Quotation & Scope Details</h3>
                        <p class="mt-0.5 text-xs text-slate-500 font-medium">Specify the vendor, destination project, operating company, and quotation validity.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-slate-400">Quotation Code:</span>
                        <span class="font-mono text-xs font-bold text-indigo-700 bg-indigo-50 border border-indigo-100 px-2.5 py-1 rounded-lg">
                            {{ old('code', $code ?? '') }}
                        </span>
                    </div>
                </x-slot:header>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-6">
                    {{-- Quotation Code Input --}}
                    <div class="col-span-1 sm:col-span-2 lg:col-span-2">
                        <label for="code" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Quotation Code <span class="text-rose-500 font-bold ml-0.5">*</span>
                        </label>
                        <input type="text" name="code" id="code" value="{{ old('code', $code ?? '') }}" required
                               class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 text-sm font-mono font-bold focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-xs bg-slate-50/50 hover:bg-white min-h-[42px]">
                        @error('code') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Company Select --}}
                    <div class="col-span-1 sm:col-span-2 lg:col-span-2">
                        <label for="company_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Company <span class="text-rose-500 font-bold ml-0.5">*</span>
                        </label>
                        <div class="relative group">
                            <select name="company_id" id="company_id" required x-model="selectedCompanyId"
                                    class="block w-full px-3.5 pr-10 py-2.5 rounded-xl border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors bg-slate-50/50 hover:bg-white shadow-xs font-medium appearance-none cursor-pointer min-h-[42px]">
                                <option value="">Select Company</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                        @error('company_id') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Project Select --}}
                    <div class="col-span-1 sm:col-span-2 lg:col-span-2">
                        <label for="project_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Project <span class="text-xs text-slate-400 font-normal ml-1">(Optional)</span>
                        </label>
                        <div class="relative group">
                            <select name="project_id" id="project_id" x-model="selectedProjectId" @change="onProjectChange"
                                    class="block w-full px-3.5 pr-10 py-2.5 rounded-xl border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors bg-slate-50/50 hover:bg-white shadow-xs font-medium appearance-none cursor-pointer min-h-[42px]">
                                <option value="">Select Project (General / Non-Project)</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">
                                        {{ $project->name }} ({{ $project->project_code }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                        @error('project_id') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Supplier Select --}}
                    <div class="col-span-1 sm:col-span-2 lg:col-span-2">
                        <label for="supplier_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Supplier / Vendor <span class="text-rose-500 font-bold ml-0.5">*</span>
                        </label>
                        <div class="relative group">
                            <select id="supplier_id" name="supplier_id" required
                                    class="block w-full px-3.5 pr-10 py-2.5 rounded-xl border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors bg-slate-50/50 hover:bg-white shadow-xs font-medium appearance-none cursor-pointer min-h-[42px]">
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }} {{ $supplier->code ? "({$supplier->code})" : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                        @error('supplier_id') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Purchase Requisition Select --}}
                    <div class="col-span-1 sm:col-span-2 lg:col-span-2">
                        <label for="purchase_requisition_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            From Purchase Requisition <span class="text-xs text-slate-400 font-normal ml-1">(Optional)</span>
                        </label>
                        <div class="relative group">
                            <select id="purchase_requisition_id" name="purchase_requisition_id" x-model="selectedPrId" @change="onRequisitionChange"
                                    class="block w-full px-3.5 pr-10 py-2.5 rounded-xl border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors bg-slate-50/50 hover:bg-white shadow-xs font-medium appearance-none cursor-pointer min-h-[42px]">
                                <option value="">-- Direct Quotation (No PR) --</option>
                                @foreach($requisitions as $pr)
                                    <option value="{{ $pr->id }}">
                                        {{ $pr->code }} {{ $pr->project ? "({$pr->project->name})" : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                        @error('purchase_requisition_id') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Issue Date --}}
                    <div class="col-span-1 sm:col-span-1 lg:col-span-1">
                        <label for="issue_date" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Issue Date <span class="text-rose-500 font-bold ml-0.5">*</span>
                        </label>
                        <input type="date" name="issue_date" id="issue_date" value="{{ old('issue_date', date('Y-m-d')) }}" required
                               class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-xs font-medium min-h-[42px]">
                        @error('issue_date') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Valid Until Date --}}
                    <div class="col-span-1 sm:col-span-1 lg:col-span-1">
                        <label for="valid_until" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Valid Until Date <span class="text-rose-500 font-bold ml-0.5">*</span>
                        </label>
                        <input type="date" name="valid_until" id="valid_until" value="{{ old('valid_until', date('Y-m-d', strtotime('+14 days'))) }}" required
                               class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-xs font-medium min-h-[42px]">
                        @error('valid_until') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>
            </x-card>

            {{-- Card 2: Line Items & Live Pricing Calculator Table --}}
            <x-card>
                <x-slot:header>
                    <div class="flex items-center justify-between w-full">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 tracking-tight">Quotation Line Items & Pricing</h3>
                            <p class="mt-0.5 text-xs text-slate-500 font-medium">Enter product prices, quantities, discounts, and taxes for each item.</p>
                        </div>
                        <button type="button" @click="addItem()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition-colors border border-indigo-200 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            + Add Line Item
                        </button>
                    </div>
                </x-slot:header>

                @error('items') <p class="mt-2 text-xs text-rose-600 font-medium px-6 py-2 bg-rose-50 border-b border-rose-100">{{ $message }}</p> @enderror

                <div class="overflow-x-auto border-t border-slate-100">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-100/70 border-b border-slate-200 text-slate-700 font-bold uppercase tracking-wider">
                                <th class="py-3.5 px-4 min-w-[220px]">Product / Item <span class="text-rose-500">*</span></th>
                                <th class="py-3.5 px-4 text-center w-28">Qty <span class="text-rose-500">*</span></th>
                                <th class="py-3.5 px-4 text-right w-36">Unit Price <span class="text-rose-500">*</span></th>
                                <th class="py-3.5 px-4 text-right w-32">Discount</th>
                                <th class="py-3.5 px-4 text-right w-32">Tax</th>
                                <th class="py-3.5 px-4 text-right w-36">Line Total</th>
                                <th class="py-3.5 px-4 text-center w-14"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-800">
                            <template x-for="(item, index) in items" :key="index">
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    {{-- Product Selection --}}
                                    <td class="py-3 px-4">
                                        <select x-model="item.product_id" :name="'items[' + index + '][product_id]'" required class="block w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-900 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white font-medium">
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">
                                                    {{ $product->name }} {{ $product->unit ? "({$product->unit->name})" : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>

                                    {{-- Quantity --}}
                                    <td class="py-3 px-4">
                                        <input type="number" step="0.01" min="0.01" x-model.number="item.quantity" :name="'items[' + index + '][quantity]'" required class="block w-full px-3 py-2 text-center rounded-xl border border-slate-300 text-slate-900 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    </td>

                                    {{-- Unit Price --}}
                                    <td class="py-3 px-4">
                                        <input type="number" step="0.01" min="0" x-model.number="item.unit_price" :name="'items[' + index + '][unit_price]'" required class="block w-full px-3 py-2 text-right rounded-xl border border-slate-300 text-slate-900 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    </td>

                                    {{-- Discount --}}
                                    <td class="py-3 px-4">
                                        <input type="number" step="0.01" min="0" x-model.number="item.discount" :name="'items[' + index + '][discount]'" class="block w-full px-3 py-2 text-right rounded-xl border border-slate-300 text-slate-900 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    </td>

                                    {{-- Tax --}}
                                    <td class="py-3 px-4">
                                        <input type="number" step="0.01" min="0" x-model.number="item.tax" :name="'items[' + index + '][tax]'" class="block w-full px-3 py-2 text-right rounded-xl border border-slate-300 text-slate-900 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    </td>

                                    {{-- Line Total Calculation --}}
                                    <td class="py-3 px-4 text-right font-black text-slate-900 text-sm">
                                        <span x-text="formatMoney(getItemTotal(item))"></span>
                                    </td>

                                    {{-- Remove Row --}}
                                    <td class="py-3 px-4 text-center">
                                        <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Remove Item">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Grand Total Running Summary Footer --}}
                <div class="p-5 bg-slate-50/90 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="text-xs text-slate-500">
                        Total Items: <span class="font-bold text-slate-800" x-text="items.length"></span>
                    </div>

                    <div class="flex items-center gap-6 text-xs">
                        <div>
                            <span class="text-slate-400 font-medium">Subtotal:</span>
                            <span class="font-bold text-slate-800 ml-1" x-text="formatMoney(getSubtotal())"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-medium">Discounts:</span>
                            <span class="font-bold text-emerald-600 ml-1" x-text="'-' + formatMoney(getTotalDiscounts())"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-medium">Taxes:</span>
                            <span class="font-bold text-slate-800 ml-1" x-text="'+' + formatMoney(getTotalTaxes())"></span>
                        </div>
                        <div class="pl-4 border-l border-slate-300">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Quotation Grand Total</span>
                            <span class="text-2xl font-black text-emerald-600" x-text="formatMoney(getGrandTotal())"></span>
                        </div>
                    </div>
                </div>

                <x-slot:footer>
                    <div class="flex items-center justify-end gap-3 w-full">
                        <a href="{{ route('admin.procurement.rfqs.index') }}" class="px-5 py-2.5 text-sm font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 rounded-xl transition-colors shadow-xs">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-6 py-2.5 text-sm font-extrabold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-xs transition-all hover:shadow-md focus:ring-2 focus:ring-indigo-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Record Supplier Quotation
                        </button>
                    </div>
                </x-slot:footer>
            </x-card>
        </form>
    </div>
    @else
    <div class="text-center py-16 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-rose-100 mb-4 border border-rose-200">
            <svg class="h-7 w-7 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-lg font-bold text-slate-900 mb-1">Access Denied</h3>
        <p class="text-xs text-slate-500 font-medium">You do not have permission to record RFQs.</p>
    </div>
    @endcan
</x-layouts.admin>

<script>
    function quotationForm(config = {}) {
        return {
            projectsData: config.projectsData || {},
            requisitionsData: config.requisitionsData || {},
            selectedProjectId: config.initialProjectId || '',
            selectedCompanyId: config.initialCompanyId || '',
            selectedPrId: config.initialPrId || '',
            items: config.initialItems && config.initialItems.length ? config.initialItems : [
                { product_id: '', quantity: 1, unit_price: 0, discount: 0, tax: 0 }
            ],

            onRequisitionChange() {
                if (this.selectedPrId && this.requisitionsData[this.selectedPrId]) {
                    const req = this.requisitionsData[this.selectedPrId];
                    if (req.company_id) {
                        this.selectedCompanyId = String(req.company_id);
                    }
                    if (req.project_id) {
                        this.selectedProjectId = String(req.project_id);
                    }
                    if (req.items && req.items.length) {
                        this.items = JSON.parse(JSON.stringify(req.items));
                    }
                }
            },

            onProjectChange() {
                const proj = this.projectsData[this.selectedProjectId];
                if (proj && proj.company_id) {
                    this.selectedCompanyId = String(proj.company_id);
                }
            },

            addItem() {
                this.items.push({ product_id: '', quantity: 1, unit_price: 0, discount: 0, tax: 0 });
            },

            removeItem(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                }
            },

            getItemTotal(item) {
                const qty = parseFloat(item.quantity) || 0;
                const price = parseFloat(item.unit_price) || 0;
                const discount = parseFloat(item.discount) || 0;
                const tax = parseFloat(item.tax) || 0;
                return (qty * price) - discount + tax;
            },

            getSubtotal() {
                return this.items.reduce((sum, item) => sum + ((parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0)), 0);
            },

            getTotalDiscounts() {
                return this.items.reduce((sum, item) => sum + (parseFloat(item.discount) || 0), 0);
            },

            getTotalTaxes() {
                return this.items.reduce((sum, item) => sum + (parseFloat(item.tax) || 0), 0);
            },

            getGrandTotal() {
                return this.getSubtotal() - this.getTotalDiscounts() + this.getTotalTaxes();
            },

            formatMoney(amount) {
                const currency = '{{ session('currency', 'RWF') }}';
                return currency + ' ' + (parseFloat(amount) || 0).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
            }
        };
    }
</script>