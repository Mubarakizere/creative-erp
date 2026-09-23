<x-layouts.admin title="Create Purchase Requisition">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Procurement', 'url' => route('admin.procurement.requisitions.index')],
                ['label' => 'Purchase Requisitions', 'url' => route('admin.procurement.requisitions.index')],
                ['label' => 'Create Requisition'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    @can('create', App\Models\PurchaseRequisition::class)
        <div class="space-y-6">
            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <a href="{{ route('admin.procurement.requisitions.index') }}" class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-800 mb-2 transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Back to Purchase Requisitions
                    </a>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Create Purchase Requisition</h1>
                    <p class="mt-1 text-sm text-slate-500 font-medium">Create a new purchase requisition, link to project & company, and specify required products.</p>
                </div>
            </div>

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

            <form action="{{ route('admin.procurement.requisitions.store') }}" method="POST" id="requisition-form"
                  x-data="purchaseRequisitionForm({
                      projectsData: {{ \Illuminate\Support\Js::from($projectsData) }},
                      initialProjectId: '{{ old('project_id', $selectedProject->id ?? '') }}',
                      initialCompanyId: '{{ old('company_id', $defaultCompanyId ?? '') }}',
                      initialItems: {{ \Illuminate\Support\Js::from(old('items', [['product_id' => '', 'quantity' => 1, 'description' => '']])) }},
                      products: {{ \Illuminate\Support\Js::from($products->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'sku' => $p->sku, 'unit' => $p->unit?->name ?? ($p->unit?->code ?? '')])) }}
                  })">
                @csrf

                {{-- Requisition Details Card --}}
                <x-card class="mb-6">
                    <x-slot:header>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 tracking-tight">Requisition Scope & Details</h3>
                            <p class="mt-0.5 text-xs text-slate-500 font-medium">Specify the destination project, company, urgency, and schedule for this procurement.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold text-slate-400">Requisition Code:</span>
                            <span class="font-mono text-xs font-bold text-indigo-700 bg-indigo-50 border border-indigo-100 px-2.5 py-1 rounded-lg">
                                {{ old('code', $code ?? '') }}
                            </span>
                        </div>
                    </x-slot:header>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-6">
                        {{-- Requisition Code Input --}}
                        <div class="col-span-1 sm:col-span-2 lg:col-span-2">
                            <label for="code" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                                Requisition Code <span class="text-rose-500 font-bold ml-0.5">*</span>
                            </label>
                            <input type="text" name="code" id="code" value="{{ old('code', $code ?? '') }}" required
                                   class="block w-full rounded-xl text-sm font-mono font-bold transition-all duration-200 border border-slate-300 bg-slate-50/50 hover:bg-white text-slate-900 focus:bg-white focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 px-3.5 py-2.5 min-h-[42px] shadow-xs">
                            @error('code') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        {{-- Company Select --}}
                        <div class="col-span-1 sm:col-span-2 lg:col-span-2">
                            <label for="company_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                                Company <span class="text-rose-500 font-bold ml-0.5">*</span>
                            </label>
                            <div class="relative group">
                                <select name="company_id" id="company_id" required x-model="selectedCompanyId"
                                        class="block w-full rounded-xl text-sm transition-all duration-200 border appearance-none border-slate-300 bg-slate-50/50 hover:bg-white hover:border-slate-400 text-slate-900 focus:bg-white focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 pl-3.5 pr-10 py-2.5 min-h-[42px] shadow-xs cursor-pointer font-medium">
                                    <option value="">Select Company</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}">
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
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
                                        class="block w-full rounded-xl text-sm transition-all duration-200 border appearance-none border-slate-300 bg-slate-50/50 hover:bg-white hover:border-slate-400 text-slate-900 focus:bg-white focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 pl-3.5 pr-10 py-2.5 min-h-[42px] shadow-xs cursor-pointer font-medium">
                                    <option value="">Select Project (General / Non-Project)</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}">
                                            {{ $project->name }} ({{ $project->project_code }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                            @error('project_id') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        {{-- Status Select --}}
                        <div class="col-span-1 sm:col-span-2 lg:col-span-2">
                            <label for="status" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                                Initial Status <span class="text-rose-500 font-bold ml-0.5">*</span>
                            </label>
                            <div class="relative group">
                                <select id="status" name="status" required
                                        class="block w-full rounded-xl text-sm transition-all duration-200 border appearance-none border-slate-300 bg-slate-50/50 hover:bg-white hover:border-slate-400 text-slate-900 focus:bg-white focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 pl-3.5 pr-10 py-2.5 min-h-[42px] shadow-xs cursor-pointer font-semibold">
                                    <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Save as Draft</option>
                                    <option value="submitted" {{ old('status') == 'submitted' ? 'selected' : '' }}>Submit for Approval</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                            @error('status') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        {{-- Priority Select --}}
                        <div class="col-span-1 sm:col-span-2 lg:col-span-2">
                            <label for="priority" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                                Priority Level <span class="text-rose-500 font-bold ml-0.5">*</span>
                            </label>
                            <div class="relative group">
                                <select id="priority" name="priority" required
                                        class="block w-full rounded-xl text-sm transition-all duration-200 border appearance-none border-slate-300 bg-slate-50/50 hover:bg-white hover:border-slate-400 text-slate-900 focus:bg-white focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 pl-3.5 pr-10 py-2.5 min-h-[42px] shadow-xs cursor-pointer font-semibold">
                                    <option value="normal" {{ old('priority', 'normal') == 'normal' ? 'selected' : '' }}>Normal Priority</option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High Priority</option>
                                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent Priority</option>
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low Priority</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                            @error('priority') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        {{-- Required Date Input --}}
                        <div class="col-span-1 sm:col-span-2 lg:col-span-2">
                            <label for="required_date" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                                Required By Date <span class="text-xs text-slate-400 font-normal ml-1">(Optional)</span>
                            </label>
                            <input type="date" name="required_date" id="required_date" value="{{ old('required_date') }}"
                                   class="block w-full rounded-xl text-sm transition-all duration-200 border border-slate-300 bg-slate-50/50 hover:bg-white text-slate-900 focus:bg-white focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 px-3.5 py-2.5 min-h-[42px] shadow-xs font-medium">
                            @error('required_date') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        {{-- Requisition Notes / Justification --}}
                        <div class="col-span-1 sm:col-span-2 lg:col-span-6">
                            <label for="notes" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                                Requisition Notes & Justification <span class="text-xs text-slate-400 font-normal ml-1">(Optional)</span>
                            </label>
                            <textarea name="notes" id="notes" rows="3" placeholder="Provide background, justification, or special purchasing constraints..."
                                      class="block w-full rounded-xl text-sm transition-all duration-200 border border-slate-300 bg-slate-50/50 hover:bg-white text-slate-900 focus:bg-white focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 p-3.5 shadow-xs font-medium">{{ old('notes') }}</textarea>
                            @error('notes') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </x-card>

                {{-- Requisition Items Card --}}
                <div class="mb-6">
                    <x-card>
                        <x-slot:header>
                            <div class="flex items-center justify-between w-full">
                                <div>
                                    <h3 class="text-lg font-bold text-slate-900 tracking-tight">Requisition Items</h3>
                                    <p class="mt-0.5 text-xs text-slate-500 font-medium">Specify the catalog products, quantities, and specifications needed.</p>
                                </div>
                                <button type="button" @click="addItem"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-xl hover:bg-indigo-100 transition-colors shadow-xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    Add Item
                                </button>
                            </div>
                        </x-slot:header>

                        @error('items') <p class="mt-2 text-xs text-rose-600 font-bold mb-4">{{ $message }}</p> @enderror

                        <div class="overflow-x-auto border-t border-slate-100">
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead class="bg-slate-50/80">
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-bold uppercase tracking-wider text-slate-600">Product / Item <span class="text-rose-500">*</span></th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-600 w-48">Quantity <span class="text-rose-500">*</span></th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-600">Description / Specifications</th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 w-16">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    <template x-for="(item, index) in items" :key="index">
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            {{-- Product Select --}}
                                            <td class="py-3 pl-4 pr-3 text-sm align-top">
                                                <div class="relative group">
                                                    <select x-model="item.product_id" :name="'items[' + index + '][product_id]'" required
                                                            class="block w-full rounded-xl border border-slate-300 shadow-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm py-2.5 pl-3.5 pr-10 transition-colors bg-white font-medium appearance-none cursor-pointer">
                                                        <option value="">Select Product</option>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}">
                                                                {{ $product->name }} ({{ $product->sku ?? 'No SKU' }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                    </div>
                                                </div>
                                                <div class="mt-1 flex items-center gap-2" x-show="getProductUnit(item.product_id)">
                                                    <span class="text-[11px] font-semibold text-slate-500">Unit:</span>
                                                    <span class="text-[11px] font-mono font-bold text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded" x-text="getProductUnit(item.product_id)"></span>
                                                </div>
                                            </td>

                                            {{-- Quantity Input --}}
                                            <td class="px-3 py-3 text-sm align-top">
                                                <div class="relative">
                                                    <input type="number" step="0.01" min="0.01" x-model="item.quantity" :name="'items[' + index + '][quantity]'" required
                                                           class="block w-full rounded-xl border border-slate-300 shadow-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm py-2.5 px-3.5 transition-colors font-bold text-slate-900">
                                                </div>
                                            </td>

                                            {{-- Item Description / Notes --}}
                                            <td class="px-3 py-3 text-sm align-top">
                                                <input type="text" x-model="item.description" :name="'items[' + index + '][description]'"
                                                       placeholder="Optional brand, model, or delivery notes..."
                                                       class="block w-full rounded-xl border border-slate-300 shadow-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:text-sm py-2.5 px-3.5 transition-colors text-slate-700">
                                            </td>

                                            {{-- Remove Row Button --}}
                                            <td class="relative py-3 pl-3 pr-4 text-center text-sm font-medium sm:pr-6 align-top pt-3.5">
                                                <button type="button" @click="removeItem(index)" class="text-rose-500 hover:text-rose-700 hover:bg-rose-50 p-2 rounded-xl transition-colors" x-show="items.length > 1" title="Remove Item">
                                                    <svg class="h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <x-slot:footer>
                            <div class="flex items-center justify-end gap-3 w-full">
                                <a href="{{ route('admin.procurement.requisitions.index') }}"
                                   class="inline-flex justify-center items-center rounded-xl border border-slate-300 bg-white py-2.5 px-5 text-sm font-semibold text-slate-700 shadow-xs hover:bg-slate-50 focus:outline-none transition-all">
                                    Cancel
                                </a>
                                <button type="submit"
                                        class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-xs transition-all focus:ring-2 focus:ring-indigo-500 focus:outline-none hover:shadow-md">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Save Requisition
                                </button>
                            </div>
                        </x-slot:footer>
                    </x-card>
                </div>
            </form>
        </div>
    @else
        <div class="text-center py-16 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-rose-50 mb-4 border border-rose-200">
                <svg class="h-8 w-8 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-2">Access Denied</h3>
            <p class="text-sm text-slate-500 font-medium">You do not have permission to create purchase requisitions.</p>
            <div class="mt-6">
                <a href="{{ route('admin.procurement.requisitions.index') }}" class="px-5 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-xs transition-all">Return to Requisitions</a>
            </div>
        </div>
    @endcan

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('purchaseRequisitionForm', (config = {}) => ({
                projectsData: config.projectsData || {},
                selectedProjectId: config.initialProjectId || '',
                selectedCompanyId: config.initialCompanyId || '',
                productsList: config.products || [],
                items: config.initialItems && config.initialItems.length ? config.initialItems : [
                    { product_id: '', quantity: 1, description: '' }
                ],

                onProjectChange() {
                    const proj = this.projectsData[this.selectedProjectId];
                    if (proj && proj.company_id) {
                        this.selectedCompanyId = String(proj.company_id);
                    }
                },

                getProductUnit(productId) {
                    if (!productId) return '';
                    const p = this.productsList.find(prod => String(prod.id) === String(productId));
                    return p && p.unit ? p.unit : '';
                },

                addItem() {
                    this.items.push({ product_id: '', quantity: 1, description: '' });
                },

                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                }
            }));
        });
    </script>
</x-layouts.admin>