<x-layouts.admin title="Create Project Budget">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Finance', 'url' => '#'],
                ['label' => 'Budgets', 'url' => route('admin.finance.budgets.index')],
                ['label' => 'New Project Budget'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="space-y-6" x-data="projectBudgetForm()">
        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
                    <a href="{{ route('admin.finance.budgets.index') }}" class="hover:text-indigo-600 font-medium transition-colors flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Project Budgets
                    </a>
                    <span>/</span>
                    <span class="font-semibold text-slate-700">New Budget</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Create Project Activity Budget
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Assign budgetary funds directly to specific project activities and work breakdown tasks.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.finance.budgets.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors shadow-xs">
                    Cancel
                </a>
            </div>
        </div>

        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm">
                <div class="font-bold mb-1 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Please fix the following validation errors:
                </div>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.finance.budgets.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Section 1: Project & Scope Selection --}}
            <x-card class="border-slate-200/80 shadow-xs">
                <div class="border-b border-slate-100 pb-3 mb-5 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">1. Project & Scope Context</h3>
                        <p class="text-xs text-slate-500">Select the target project. The budget will directly align with this project's activities.</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                        Project-Centric Budget
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    {{-- Project Select --}}
                    <div class="md:col-span-2">
                        <label for="project_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Target Project <span class="text-rose-500">*</span>
                        </label>
                        <select name="project_id" id="project_id" x-model="selectedProjectId" @change="onProjectChange()" required
                                class="w-full text-sm font-semibold rounded-xl border-slate-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                            <option value="">— Select Project —</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}" {{ (old('project_id', $selectedProjectId) == $p->id) ? 'selected' : '' }}>
                                    {{ $p->name }} ({{ $p->project_code ?? $p->code }}) &bull; {{ $p->company?->name ?? 'General' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-400 mt-1">Selecting a project unlocks its activities below. This is a project cost budget; the project’s sales estimate stays separate.</p>
                    </div>

                    {{-- Fiscal Year (Optional) --}}
                    <div>
                        <label for="fiscal_year_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Fiscal Year <span class="text-slate-400 font-normal">(Optional)</span>
                        </label>
                        <select name="fiscal_year_id" id="fiscal_year_id"
                                class="w-full text-sm rounded-xl border-slate-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                            <option value="">— No Fiscal Year Restriction —</option>
                            @foreach($fiscalYears as $fy)
                                <option value="{{ $fy->id }}" {{ old('fiscal_year_id', $currentFiscalYear?->id) == $fy->id ? 'selected' : '' }}>
                                    {{ $fy->name }} {{ ($currentFiscalYear?->id == $fy->id) ? '(Current)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Budget Name --}}
                    <div class="md:col-span-2">
                        <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Budget Title <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" x-model="budgetName" required
                               class="w-full text-sm rounded-xl border-slate-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500 py-2.5"
                               placeholder="e.g. Alpha Tower - Execution Budget">
                    </div>

                    {{-- Initial Status --}}
                    <div>
                        <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Initial Status <span class="text-rose-500">*</span>
                        </label>
                        <select name="status" id="status" required
                                class="w-full text-sm rounded-xl border-slate-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                            <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active project cost budget</option>
                            <option value="approved" {{ old('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        </select>
                    </div>

                    {{-- Description --}}
                    <div class="md:col-span-3">
                        <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Budget Description & Justification <span class="text-slate-400 font-normal">(Optional)</span>
                        </label>
                        <textarea name="description" id="description" rows="2"
                                  class="w-full text-sm rounded-xl border-slate-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500"
                                  placeholder="Provide context, constraints, or milestone phases for this project budget..."></textarea>
                    </div>
                </div>
            </x-card>

            {{-- Section 2: Activity Breakdown Lines Table --}}
            <x-card class="border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-4 border-b border-slate-100 bg-slate-50/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">2. Activity Budget Allocations</h3>
                        <p class="text-xs text-slate-500">Allocate money to each project task/activity. Actual expenses will be tracked against these lines.</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Allocated Budget</span>
                            <span class="text-xl font-black text-emerald-600" x-text="formatCurrency(totalAmount)"></span>
                        </div>
                    </div>
                </div>

                <div class="p-4 overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-100/70 border-b border-slate-200 text-slate-700 font-bold uppercase tracking-wider">
                                <th class="py-3 px-4 w-72">Project Activity / Task</th>
                                <th class="py-3 px-4 w-40">
                                    <div class="flex items-center justify-between">
                                        <span>Cost Type</span>
                                        <div class="flex items-center gap-1.5">
                                            <button type="button" @click="$dispatch('open-modal', 'quick-add-cost-type')" class="text-[10px] font-bold text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 px-1 py-0.5 rounded transition-colors inline-flex items-center gap-0.5" title="Quick Add New Cost Type">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                                <span>New</span>
                                            </button>
                                            <span class="text-slate-300">|</span>
                                            <a href="{{ route('admin.finance.settings') }}#cost-types" target="_blank" class="text-[10px] normal-case font-semibold text-indigo-600 hover:text-indigo-800 hover:underline inline-flex items-center gap-0.5" title="Manage cost types in Finance Settings">
                                                <span>Manage</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                        </div>
                                    </div>
                                </th>
                                <th class="py-3 px-4 w-56">Resource / Material</th>
                                <th class="py-3 px-4 w-52">
                                    <div class="flex items-center justify-between">
                                        <span>Cost Category</span>
                                        <div class="flex items-center gap-1.5">
                                            <button type="button" @click="$dispatch('open-modal', 'quick-add-budget-category')" class="text-[10px] font-bold text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 px-1 py-0.5 rounded transition-colors inline-flex items-center gap-0.5" title="Quick Add Cost Category">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                                <span>New</span>
                                            </button>
                                            <span class="text-slate-300">|</span>
                                            <a href="{{ route('admin.finance.settings') }}" target="_blank" class="text-[10px] normal-case font-semibold text-indigo-600 hover:text-indigo-800 hover:underline inline-flex items-center gap-0.5" title="Manage categories in Finance Settings">
                                                <span>Manage</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                        </div>
                                    </div>
                                </th>
                                <th class="py-3 px-4 w-44 text-right">Allocated Amount (RWF) <span class="text-rose-500">*</span></th>
                                <th class="py-3 px-4">Notes / Scope</th>
                                <th class="py-3 px-2 w-12 text-center"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="(line, index) in lines" :key="index">
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    {{-- Task / Activity --}}
                                    <td class="py-3 px-4 align-top">
                                        <div class="space-y-1">
                                            <template x-if="projectTasks.length > 0">
                                                <select :name="`lines[${index}][task_id]`" x-model="line.task_id"
                                                        class="w-full text-xs font-semibold rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-1.5">
                                                    <option value="">— Select Project Activity —</option>
                                                    <template x-for="task in projectTasks" :key="task.id">
                                                        <option :value="task.id" x-text="task.display_label" :selected="line.task_id == task.id"></option>
                                                    </template>
                                                </select>
                                            </template>

                                            {{-- Fallback / Custom Activity Name --}}
                                            <div>
                                                <input type="text" :name="`lines[${index}][activity_name]`" x-model="line.activity_name"
                                                       class="w-full text-xs rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-1.5"
                                                       :placeholder="projectTasks.length > 0 ? 'Or custom activity name...' : 'Enter activity / task name...'">
                                            </div>
                                        </div>
                                    </td>

                                    <td class="py-3 px-4 align-top space-y-2">
                                        <select :name="`lines[${index}][cost_type]`" x-model="line.cost_type" @change="if (line.cost_type !== 'materials') line.product_id = ''" class="w-full text-xs rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-1.5">
                                            <template x-for="ct in costTypesList" :key="ct.slug">
                                                <option :value="ct.slug" x-text="ct.name" :selected="line.cost_type === ct.slug"></option>
                                            </template>
                                            @foreach($costTypes as $ct)
                                                <option value="{{ $ct->slug }}" x-show="false">{{ $ct->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="py-3 px-4 align-top space-y-2">
                                        <input type="text" :name="`lines[${index}][resource_name]`" x-model="line.resource_name" class="w-full text-xs rounded-lg border-slate-300 py-1.5" placeholder="e.g. Mason / cement">
                                        <select x-show="line.cost_type === 'materials'" :name="`lines[${index}][product_id]`" x-model="line.product_id" class="w-full text-xs rounded-lg border-slate-300 py-1.5">
                                            <option value="">Optional product match</option>
                                            @foreach($products as $product)<option value="{{ $product->id }}">{{ $product->name }}</option>@endforeach
                                        </select>
                                    </td>

                                    {{-- Cost Category --}}
                                    <td class="py-3 px-4 align-top">
                                        <select :name="`lines[${index}][budget_category_id]`" x-model="line.budget_category_id"
                                                class="w-full text-xs rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-1.5">
                                            <option value="">— Category —</option>
                                            <template x-for="cat in categoriesList" :key="cat.id">
                                                <option :value="cat.id" x-text="cat.name" :selected="line.budget_category_id == cat.id"></option>
                                            </template>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->id }}" x-show="false">{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>

                                    {{-- Amount --}}
                                    <td class="py-3 px-4 align-top">
                                        <div class="relative rounded-lg shadow-2xs">
                                            <input type="number" step="0.01" min="0" required
                                                   :name="`lines[${index}][amount]`" x-model.number="line.amount"
                                                   class="w-full text-xs text-right font-bold text-slate-900 rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-1.5 pr-3"
                                                   placeholder="0.00">
                                        </div>
                                    </td>

                                    {{-- Notes --}}
                                    <td class="py-3 px-4 align-top">
                                        <input type="text" :name="`lines[${index}][notes]`" x-model="line.notes"
                                               class="w-full text-xs rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-1.5"
                                               placeholder="Specifications, limits, or team notes...">
                                    </td>

                                    {{-- Remove Button --}}
                                    <td class="py-3 px-2 align-top text-center">
                                        <button type="button" @click="removeLine(index)"
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors"
                                                title="Remove this line">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-100/90 border-t-2 border-slate-300 font-extrabold text-slate-900">
                                <td class="py-3.5 px-4 text-xs font-bold" colspan="4">
                                    <button type="button" @click="addLine()"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition-colors cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Add Activity Allocation
                                    </button>
                                </td>
                                <td class="py-3.5 px-4 text-right text-sm font-black text-emerald-600" x-text="formatCurrency(totalAmount)"></td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </x-card>

            {{-- Footer Action Bar --}}
            <div class="flex items-center justify-between pt-4 border-t border-slate-200">
                <a href="{{ route('admin.finance.budgets.index') }}" class="px-5 py-2.5 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors shadow-xs">
                    Cancel & Return
                </a>

                <button type="submit" class="px-6 py-2.5 text-xs font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition-colors shadow-xs flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Save Project Budget
                </button>
            </div>
        </form>
        {{-- Quick Add Cost Type Modal --}}
        <x-modal id="quick-add-cost-type" maxWidth="md">
            <x-slot:header>Quick Add Cost Type</x-slot:header>
            <form @submit.prevent="submitQuickCostType()" class="p-6 space-y-4 text-left">
                <div x-show="quickCostTypeError" class="p-3 bg-red-50 border border-red-200 rounded-lg text-xs text-red-600 font-medium" x-text="quickCostTypeError" style="display: none;"></div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cost Type Name <span class="text-red-500">*</span></label>
                    <input type="text" x-model="newCostType.name" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm min-h-[42px]" placeholder="e.g. Subcontractor, Machinery, Permits">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-gray-400 font-normal">(Optional)</span></label>
                    <textarea x-model="newCostType.description" rows="2" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Define expenditure classification..."></textarea>
                </div>
                <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="$dispatch('close-modal', 'quick-add-cost-type')" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">Cancel</button>
                    <button type="submit" :disabled="isSavingCostType" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-colors shadow-sm disabled:opacity-50">
                        <span x-show="!isSavingCostType">Save Cost Type</span>
                        <span x-show="isSavingCostType" style="display: none;">Saving...</span>
                    </button>
                </div>
            </form>
        </x-modal>

        {{-- Quick Add Cost Category Modal --}}
        <x-modal id="quick-add-budget-category" maxWidth="md">
            <x-slot:header>Quick Add Cost Category</x-slot:header>
            <form @submit.prevent="submitQuickCategory()" class="p-6 space-y-4 text-left">
                <div x-show="quickCategoryError" class="p-3 bg-red-50 border border-red-200 rounded-lg text-xs text-red-600 font-medium" x-text="quickCategoryError" style="display: none;"></div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category Name <span class="text-red-500">*</span></label>
                    <input type="text" x-model="newCategory.name" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm min-h-[42px]" placeholder="e.g. Earthworks & Retaining">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Classification Type <span class="text-red-500">*</span></label>
                    <select x-model="newCategory.type" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm bg-white min-h-[42px]">
                        <option value="expense">Expense</option>
                        <option value="revenue">Revenue</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-gray-400 font-normal">(Optional)</span></label>
                    <textarea x-model="newCategory.description" rows="2" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Scope or expenditure rules..."></textarea>
                </div>
                <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="$dispatch('close-modal', 'quick-add-budget-category')" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">Cancel</button>
                    <button type="submit" :disabled="isSavingCategory" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-colors shadow-sm disabled:opacity-50">
                        <span x-show="!isSavingCategory">Save Category</span>
                        <span x-show="isSavingCategory" style="display: none;">Saving...</span>
                    </button>
                </div>
            </form>
        </x-modal>
    </div>

    <script>
        function projectBudgetForm() {
            const projectsData = @json($projectsData);
            const oldLines = @json(old('lines', []));
            const initialProjectId = '{{ old('project_id', $selectedProjectId ?? '') }}';

            return {
                selectedProjectId: initialProjectId,
                budgetName: '{{ old('name', '') }}',
                projectTasks: [],
                costTypesList: @json($costTypes->map(fn($ct) => ['slug' => $ct->slug, 'name' => $ct->name])),
                categoriesList: @json($categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])),
                newCostType: { name: '', description: '' },
                isSavingCostType: false,
                quickCostTypeError: '',
                newCategory: { name: '', type: 'expense', description: '' },
                isSavingCategory: false,
                quickCategoryError: '',
                lines: oldLines.length ? oldLines.map(line => ({...line, cost_type: line.cost_type || 'other', resource_name: line.resource_name || '', product_id: line.product_id || ''})) : [
                    { task_id: '', activity_name: '', cost_type: 'other', resource_name: '', product_id: '', budget_category_id: '', amount: 0, notes: '' }
                ],

                init() {
                    if (this.selectedProjectId && projectsData[this.selectedProjectId]) {
                        this.projectTasks = projectsData[this.selectedProjectId].tasks || [];
                        if (!this.budgetName) {
                            const p = projectsData[this.selectedProjectId];
                            this.budgetName = `${p.name} - Project Budget`;
                        }
                    }
                },

                async submitQuickCostType() {
                    this.quickCostTypeError = '';
                    if (!this.newCostType.name || !this.newCostType.name.trim()) {
                        this.quickCostTypeError = 'Cost Type Name is required.';
                        return;
                    }
                    this.isSavingCostType = true;
                    try {
                        const response = await fetch('{{ route('admin.finance.settings.cost-types.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(this.newCostType)
                        });
                        const data = await response.json();
                        if (response.ok && data.success) {
                            if (!this.costTypesList.some(ct => ct.slug === data.cost_type.slug)) {
                                this.costTypesList.push({ slug: data.cost_type.slug, name: data.cost_type.name });
                            }
                            this.newCostType = { name: '', description: '' };
                            this.$dispatch('close-modal', 'quick-add-cost-type');
                        } else {
                            this.quickCostTypeError = data.message || 'Failed to create cost type.';
                        }
                    } catch (err) {
                        this.quickCostTypeError = 'Network error occurred while saving cost type.';
                    } finally {
                        this.isSavingCostType = false;
                    }
                },

                async submitQuickCategory() {
                    this.quickCategoryError = '';
                    if (!this.newCategory.name || !this.newCategory.name.trim()) {
                        this.quickCategoryError = 'Category Name is required.';
                        return;
                    }
                    this.isSavingCategory = true;
                    try {
                        const response = await fetch('{{ route('admin.finance.settings.budget-categories.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(this.newCategory)
                        });
                        const data = await response.json();
                        if (response.ok && data.success) {
                            if (!this.categoriesList.some(c => c.id === data.category.id)) {
                                this.categoriesList.push({ id: data.category.id, name: data.category.name });
                            }
                            this.newCategory = { name: '', type: 'expense', description: '' };
                            this.$dispatch('close-modal', 'quick-add-budget-category');
                        } else {
                            this.quickCategoryError = data.message || 'Failed to create category.';
                        }
                    } catch (err) {
                        this.quickCategoryError = 'Network error occurred while saving category.';
                    } finally {
                        this.isSavingCategory = false;
                    }
                },

                onProjectChange() {
                    const project = projectsData[this.selectedProjectId];
                    if (project) {
                        this.projectTasks = project.tasks || [];
                        if (!this.budgetName || this.budgetName.includes('Project Budget')) {
                            this.budgetName = `${project.name} - Project Budget`;
                        }

                        // Auto-populate lines with project tasks if lines are empty
                        if (this.lines.length === 1 && !this.lines[0].task_id && !this.lines[0].activity_name && this.projectTasks.length > 0) {
                            this.lines = this.projectTasks.map(t => ({
                                task_id: t.id,
                                activity_name: t.name,
                                cost_type: 'other',
                                resource_name: '',
                                product_id: '',
                                budget_category_id: '',
                                amount: 0,
                                notes: ''
                            }));
                        }
                    } else {
                        this.projectTasks = [];
                    }
                },

                addLine() {
                    this.lines.push({
                        task_id: '',
                        activity_name: '',
                        cost_type: 'other',
                        resource_name: '',
                        product_id: '',
                        budget_category_id: '',
                        amount: 0,
                        notes: ''
                    });
                },

                removeLine(index) {
                    if (this.lines.length > 1) {
                        this.lines.splice(index, 1);
                    } else {
                        this.lines[0] = { task_id: '', activity_name: '', cost_type: 'other', resource_name: '', product_id: '', budget_category_id: '', amount: 0, notes: '' };
                    }
                },

                get totalAmount() {
                    return this.lines.reduce((sum, line) => {
                        const val = parseFloat(line.amount) || 0;
                        return sum + val;
                    }, 0);
                },

                formatCurrency(val) {
                    return 'RWF ' + new Intl.NumberFormat('en-US', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 2
                    }).format(val || 0);
                }
            };
        }
    </script>
</x-layouts.admin>
