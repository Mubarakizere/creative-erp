<x-layouts.admin title="Edit Budget - {{ $budget->name }}">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Finance', 'url' => '#'],
                ['label' => 'Budgets', 'url' => route('admin.finance.budgets.index')],
                ['label' => $budget->name, 'url' => route('admin.finance.budgets.show', $budget)],
                ['label' => 'Edit'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="space-y-6" x-data="projectBudgetEditForm()">
        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
                    <a href="{{ route('admin.finance.budgets.show', $budget) }}" class="hover:text-indigo-600 font-medium transition-colors flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Quotation / Budget
                    </a>
                    <span>/</span>
                    <span class="font-semibold text-slate-700">Edit Budget</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Edit Project Budget: {{ $budget->name }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Adjust activity financial allocations, status, and notes.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.finance.budgets.show', $budget) }}" class="px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors shadow-xs">
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

        <form action="{{ route('admin.finance.budgets.update', $budget) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Section 1: Project & Scope Context --}}
            <x-card class="border-slate-200/80 shadow-xs">
                <div class="border-b border-slate-100 pb-3 mb-5 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">1. Project & Scope Context</h3>
                        <p class="text-xs text-slate-500">Project details linked to this budget.</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                        {{ $budget->project?->project_code ?? 'Project Budget' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    {{-- Project (Read-only on edit to preserve historical activity integrity) --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Target Project
                        </label>
                        <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-900 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span>{{ $budget->project?->name ?? 'General Project' }}</span>
                                <span class="px-2 py-0.5 rounded text-[11px] font-mono bg-slate-200 text-slate-700 font-semibold">
                                    {{ $budget->project?->project_code ?? 'N/A' }}
                                </span>
                            </div>
                            <span class="text-xs font-medium text-slate-500">
                                {{ $budget->project?->company?->name ?? $budget->company?->name }}
                            </span>
                        </div>
                    </div>

                    {{-- Fiscal Year --}}
                    <div>
                        <label for="fiscal_year_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Fiscal Year
                        </label>
                        <select name="fiscal_year_id" id="fiscal_year_id"
                                class="w-full text-sm rounded-xl border-slate-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                            <option value="">— No Fiscal Year —</option>
                            @foreach($fiscalYears as $fy)
                                <option value="{{ $fy->id }}" {{ old('fiscal_year_id', $budget->fiscal_year_id) == $fy->id ? 'selected' : '' }}>
                                    {{ $fy->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Budget Name --}}
                    <div class="md:col-span-2">
                        <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Budget Title <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $budget->name) }}" required
                               class="w-full text-sm rounded-xl border-slate-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Budget Status
                        </label>
                        <select name="status" id="status"
                                class="w-full text-sm font-semibold rounded-xl border-slate-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                            <option value="draft" {{ old('status', $budget->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="active" {{ old('status', $budget->status) === 'active' ? 'selected' : '' }}>Active (Syncs Project Budget)</option>
                            <option value="approved" {{ old('status', $budget->status) === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="closed" {{ old('status', $budget->status) === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>

                    {{-- Description --}}
                    <div class="md:col-span-3">
                        <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Budget Notes & Justification
                        </label>
                        <textarea name="description" id="description" rows="2"
                                  class="w-full text-sm rounded-xl border-slate-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $budget->description) }}</textarea>
                    </div>
                </div>
            </x-card>

            {{-- Section 2: Activity Breakdown Lines Table --}}
            <x-card class="border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-4 border-b border-slate-100 bg-slate-50/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">2. Activity Budget Allocations</h3>
                        <p class="text-xs text-slate-500">Edit allocations per task or activity.</p>
                    </div>

                    <div class="text-right">
                        <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Allocated Budget</span>
                        <span class="text-xl font-black text-emerald-600" x-text="formatCurrency(totalAmount)"></span>
                    </div>
                </div>

                <div class="p-4 overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-100/70 border-b border-slate-200 text-slate-700 font-bold uppercase tracking-wider">
                                <th class="py-3 px-4 w-72">Project Activity / Task</th>
                                <th class="py-3 px-4 w-48">Cost Category</th>
                                <th class="py-3 px-4 w-44 text-right">Allocated Amount (RWF)</th>
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

                                            {{-- Custom Activity Name --}}
                                            <div>
                                                <input type="text" :name="`lines[${index}][activity_name]`" x-model="line.activity_name"
                                                       class="w-full text-xs rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-1.5"
                                                       placeholder="Activity name...">
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Cost Category --}}
                                    <td class="py-3 px-4 align-top">
                                        <select :name="`lines[${index}][budget_category_id]`" x-model="line.budget_category_id"
                                                class="w-full text-xs rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-1.5">
                                            <option value="">— Category —</option>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>

                                    {{-- Amount --}}
                                    <td class="py-3 px-4 align-top">
                                        <input type="number" step="0.01" min="0" required
                                               :name="`lines[${index}][amount]`" x-model.number="line.amount"
                                               class="w-full text-xs text-right font-bold text-slate-900 rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-1.5 pr-3"
                                               placeholder="0.00">
                                    </td>

                                    {{-- Notes --}}
                                    <td class="py-3 px-4 align-top">
                                        <input type="text" :name="`lines[${index}][notes]`" x-model="line.notes"
                                               class="w-full text-xs rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-1.5"
                                               placeholder="Notes...">
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
                                <td class="py-3.5 px-4 text-xs font-bold" colspan="2">
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
                <a href="{{ route('admin.finance.budgets.show', $budget) }}" class="px-5 py-2.5 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors shadow-xs">
                    Cancel & Discard
                </a>

                <button type="submit" class="px-6 py-2.5 text-xs font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition-colors shadow-xs flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Update Project Budget
                </button>
            </div>
        </form>
    </div>

    <script>
        function projectBudgetEditForm() {
            const initialLines = @json($initialLines ?? []);
            const projectTasks = @json($projectTasks ?? []);

            return {
                projectTasks: projectTasks,
                lines: initialLines.length > 0 ? initialLines : [
                    { task_id: '', activity_name: '', budget_category_id: '', amount: 0, notes: '' }
                ],

                addLine() {
                    this.lines.push({
                        task_id: '',
                        activity_name: '',
                        budget_category_id: '',
                        amount: 0,
                        notes: ''
                    });
                },

                removeLine(index) {
                    if (this.lines.length > 1) {
                        this.lines.splice(index, 1);
                    } else {
                        this.lines[0] = { task_id: '', activity_name: '', budget_category_id: '', amount: 0, notes: '' };
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
