<x-layouts.admin title="Expenses">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Finance'],
                ['label' => 'Expenses'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div
        x-data="{
            projects: {{ Illuminate\Support\Js::from($projectOptions) }},
            accounts: {{ Illuminate\Support\Js::from($accountOptions) }},
            resources: {{ Illuminate\Support\Js::from($resourceTypes) }},
            requestedProjectId: {{ Illuminate\Support\Js::from((string) request('project_id', '')) }},
            showForm: {{ Illuminate\Support\Js::from((bool) (old('kind') && $errors->any())) }},
            isEdit: {{ Illuminate\Support\Js::from((bool) old('expense_id')) }},
            expenseId: {{ Illuminate\Support\Js::from(old('expense_id')) }},
            form: {
                kind: {{ Illuminate\Support\Js::from(old('kind', 'labor')) }},
                project_id: {{ Illuminate\Support\Js::from((string) old('project_id', request('project_id', ''))) }},
                task_id: {{ Illuminate\Support\Js::from((string) old('task_id', '')) }},
                type_choice: {{ Illuminate\Support\Js::from(old('resource_type', old('account_type', ''))) }},
                custom_type: {{ Illuminate\Support\Js::from(old('custom_type', '')) }},
                amount: {{ Illuminate\Support\Js::from(old('amount', '')) }},
                expense_date: {{ Illuminate\Support\Js::from(old('expense_date', now()->format('Y-m-d'))) }},
                vendor_name: {{ Illuminate\Support\Js::from(old('vendor_name', '')) }},
                payment_status: {{ Illuminate\Support\Js::from(old('payment_status', 'Pending')) }},
                payment_method: {{ Illuminate\Support\Js::from(old('payment_method', 'Bank Transfer')) }},
                description: {{ Illuminate\Support\Js::from(old('description', '')) }}
            },
            get selectedProject() {
                return this.projects.find(project => String(project.id) === String(this.form.project_id)) || null;
            },
            openCreate(kind) {
                this.isEdit = false;
                this.expenseId = null;
                const requestedProject = this.projects.find(project => String(project.id) === String(this.requestedProjectId) && project.canCreate);
                this.form = {
                    kind,
                    project_id: requestedProject ? String(requestedProject.id) : '',
                    task_id: '',
                    type_choice: '',
                    custom_type: '',
                    amount: '',
                    expense_date: '{{ now()->format('Y-m-d') }}',
                    vendor_name: '',
                    payment_status: 'Pending',
                    payment_method: 'Bank Transfer',
                    description: ''
                };
                this.showForm = true;
            },
            openEdit(expense) {
                this.isEdit = true;
                this.expenseId = expense.id;
                const choices = expense.kind === 'labor' ? this.resources : this.accounts.map(account => account.value);
                const hasChoice = choices.includes(expense.title);
                this.form = {
                    ...expense,
                    type_choice: hasChoice ? expense.title : 'Other',
                    custom_type: hasChoice ? '' : expense.title
                };
                this.showForm = true;
            },
            updateTypeChoice() {
                this.form.custom_type = '';
            }
        }"
        @keydown.escape.window="showForm = false"
    >
        <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Expenses</h1>
                <p class="mt-1 text-sm text-slate-500">Record labor and materials payable by project task.</p>
                <div class="mt-3 flex flex-wrap gap-2 text-xs font-semibold">
                    <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-indigo-700">Labor lines: {{ number_format($stats['labor']) }}</span>
                    <span class="rounded-full bg-blue-50 px-2.5 py-1 text-blue-700">Materials lines: {{ number_format($stats['materials']) }}</span>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-slate-600">Other project expenses: {{ number_format($stats['other']) }}</span>
                </div>
            </div>

            @if($formProjects->isNotEmpty())
                <div class="flex flex-wrap gap-2">
                    <button type="button" @click="openCreate('labor')" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                        <span class="text-lg leading-none">+</span> Add Labor Expense
                    </button>
                    <button type="button" @click="openCreate('materials')" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                        <span class="text-lg leading-none">+</span> Add Materials Expense
                    </button>
                </div>
            @endif
        </div>

        @if($errors->any())
            <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                <p class="font-semibold">Please correct the following:</p>
                <ul class="mt-1 list-inside list-disc">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <x-card class="mb-5">
            <form method="GET" action="{{ route('admin.finance.expenses.index') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-7">
                <div class="xl:col-span-2">
                    <label for="expense-search" class="mb-1 block text-xs font-semibold text-slate-600">Search</label>
                    <input id="expense-search" type="search" name="search" value="{{ request('search') }}" placeholder="Project, task, resource, or account" class="w-full rounded-lg border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="expense-project" class="mb-1 block text-xs font-semibold text-slate-600">Project</label>
                    <select id="expense-project" name="project_id" class="w-full rounded-lg border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All projects</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" @selected((string) request('project_id') === (string) $project->id)>
                                {{ $project->project_code ? $project->project_code.' · ' : '' }}{{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="expense-type" class="mb-1 block text-xs font-semibold text-slate-600">Expense section</label>
                    <select id="expense-type" name="expense_type" class="w-full rounded-lg border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All expenses</option>
                        <option value="labor" @selected(request('expense_type') === 'labor')>Labor</option>
                        <option value="materials" @selected(request('expense_type') === 'materials')>Materials</option>
                        <option value="other" @selected(request('expense_type') === 'other')>Other</option>
                    </select>
                </div>
                <div>
                    <label for="expense-payment-status" class="mb-1 block text-xs font-semibold text-slate-600">Payment status</label>
                    <select id="expense-payment-status" name="payment_status" class="w-full rounded-lg border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All statuses</option>
                        @foreach(['Paid', 'Pending', 'Reimbursement'] as $status)
                            <option value="{{ $status }}" @selected(request('payment_status') === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="expense-date-from" class="mb-1 block text-xs font-semibold text-slate-600">From</label>
                    <input id="expense-date-from" type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="flex items-end gap-2">
                    <div class="min-w-0 flex-1">
                        <label for="expense-date-to" class="mb-1 block text-xs font-semibold text-slate-600">To</label>
                        <input id="expense-date-to" type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <button type="submit" class="rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-700">Filter</button>
                    @if(request()->anyFilled(['search', 'project_id', 'expense_type', 'payment_status', 'date_from', 'date_to']))
                        <a href="{{ route('admin.finance.expenses.index') }}" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Clear</a>
                    @endif
                </div>
            </form>
        </x-card>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-4 py-4 sm:px-5">
                <h2 class="font-bold text-slate-900">Project expense summaries</h2>
                <p class="mt-1 text-xs text-slate-500">Open a project to see its full expense breakdown by task.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-4 py-3 sm:px-5">Project</th>
                            <th class="px-4 py-3">Task</th>
                            <th class="px-4 py-3 text-right">Labor</th>
                            <th class="px-4 py-3 text-right">Materials</th>
                            <th class="px-4 py-3 text-right">Task Total</th>
                            <th class="px-4 py-3 text-right sm:px-5">Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($taskGroups as $group)
                            @php
                                $project = $group['project'];
                                $task = $group['task'];
                                $currency = $project?->currency ?? 'RWF';
                                $taskTotal = $group['labor_total'] + $group['materials_total'];
                            @endphp
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-3 sm:px-5">
                                    <span class="block font-semibold text-slate-800">{{ $project?->name ?? 'Project unavailable' }}</span>
                                    @if($project?->project_code)<span class="mt-0.5 block text-xs text-slate-400">{{ $project->project_code }}</span>@endif
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $task?->name ?? 'Unassigned task' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <span class="block font-semibold text-indigo-700">{{ format_currency($group['labor_total'], $currency) }}</span>
                                    <span class="text-[11px] text-slate-400">{{ $group['labor']->count() }} line(s)</span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <span class="block font-semibold text-blue-700">{{ format_currency($group['materials_total'], $currency) }}</span>
                                    <span class="text-[11px] text-slate-400">{{ $group['materials']->count() }} line(s)</span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right font-extrabold text-slate-900">{{ format_currency($taskTotal, $currency) }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right sm:px-5">
                                    @if($project)
                                        <a href="{{ route('admin.finance.expenses.projects.show', $project) }}" class="inline-flex items-center rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700">View project expenses</a>
                                    @else
                                        <span class="text-xs text-slate-400">Unavailable</span>
                                    @endif
                                    @if($group['other']->isNotEmpty())
                                        <span class="mt-1 block text-[10px] text-slate-400">{{ $group['other']->count() }} other line(s) on project page</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-slate-500">No expense records found. Add a labor or materials line, or adjust the filters.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($taskGroups->hasPages())
                <div class="border-t border-slate-200 px-4 py-3">{{ $taskGroups->links() }}</div>
            @endif
        </div>
        <template x-teleport="body">
            <div x-show="showForm" x-cloak class="fixed inset-0 z-[100] overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="expense-form-title" style="display:none">
                <div class="fixed inset-0 bg-slate-950/50 backdrop-blur-sm" @click="showForm = false"></div>
                <div class="flex min-h-full items-center justify-center p-4">
                    <div x-show="showForm" x-transition class="relative w-full max-w-2xl overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl">
                        <form method="POST" enctype="multipart/form-data" :action="isEdit ? ('{{ url('/admin/finance/expenses') }}/' + expenseId) : '{{ route('admin.finance.expenses.store') }}'">
                            @csrf
                            <template x-if="isEdit">
                                <div>
                                    <input type="hidden" name="_method" value="PUT">
                                    <input type="hidden" name="expense_id" x-model="expenseId">
                                    <input type="hidden" name="project_id" x-model="form.project_id">
                                </div>
                            </template>
                            <input type="hidden" name="kind" x-model="form.kind">

                            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Project Expense</p>
                                        <h2 id="expense-form-title" class="mt-1 text-lg font-bold text-slate-900" x-text="(isEdit ? 'Edit ' : 'Add ') + (form.kind === 'labor' ? 'Labor Expense' : 'Materials Expense')"></h2>
                                    </div>
                                    <button type="button" @click="showForm = false" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700" aria-label="Close">✕</button>
                                </div>
                            </div>

                            <div class="grid max-h-[70vh] grid-cols-1 gap-4 overflow-y-auto px-5 py-5 sm:grid-cols-2 sm:px-6">
                                <div class="sm:col-span-2">
                                    <label for="finance-expense-project" class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-600">Project Name *</label>
                                    <select id="finance-expense-project" name="project_id" x-model="form.project_id" :disabled="isEdit" required class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Select a project</option>
                                        <template x-for="project in projects" :key="project.id">
                                            <option :value="project.id" :disabled="!isEdit && !project.canCreate" x-text="project.label"></option>
                                        </template>
                                    </select>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="finance-expense-task" class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-600">Task Name</label>
                                    <select id="finance-expense-task" name="task_id" x-model="form.task_id" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">No task assigned</option>
                                        <template x-for="task in (selectedProject?.tasks || [])" :key="task.id">
                                            <option :value="task.id" x-text="task.name"></option>
                                        </template>
                                    </select>
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-600" x-text="form.kind === 'labor' ? 'Resource Type *' : 'Account Type *'"></label>
                                    <template x-if="form.kind === 'labor'">
                                        <div>
                                            <select name="resource_type" x-model="form.type_choice" @change="updateTypeChoice()" required class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="">Select a resource type</option>
                                                <template x-for="resource in resources" :key="resource">
                                                    <option :value="resource" x-text="resource"></option>
                                                </template>
                                            </select>
                                        </div>
                                    </template>
                                    <template x-if="form.kind === 'materials'">
                                        <div>
                                            <select name="account_type" x-model="form.type_choice" @change="updateTypeChoice()" required class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                                                <option value="">Select an expense account</option>
                                                <template x-for="account in accounts" :key="account.value">
                                                    <option :value="account.value" x-text="account.label"></option>
                                                </template>
                                                <option value="Other">Other</option>
                                            </select>
                                            <p class="mt-1 text-[11px] text-slate-500">Options come from active Expense accounts in Finance &gt; Chart of Accounts.</p>
                                        </div>
                                    </template>
                                    <template x-if="form.type_choice === 'Other'">
                                        <input type="text" name="custom_type" x-model="form.custom_type" required maxlength="255" placeholder="Enter resource or account type" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                                    </template>
                                </div>

                                <div>
                                    <label for="finance-expense-amount" class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-600">Amount Payable (<span x-text="selectedProject?.currency || 'RWF'"></span>) *</label>
                                    <input id="finance-expense-amount" type="number" name="amount" x-model="form.amount" min="0.01" step="0.01" required class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label for="finance-expense-date" class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-600">Expense Date *</label>
                                    <input id="finance-expense-date" type="date" name="expense_date" x-model="form.expense_date" required class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label for="finance-expense-vendor" class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-600">Vendor / Payee</label>
                                    <input id="finance-expense-vendor" type="text" name="vendor_name" x-model="form.vendor_name" maxlength="255" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label for="finance-expense-status" class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-600">Payment Status *</label>
                                    <select id="finance-expense-status" name="payment_status" x-model="form.payment_status" required class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="Pending">Pending</option>
                                        <option value="Paid">Paid</option>
                                        <option value="Reimbursement">Reimbursement</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="finance-expense-method" class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-600">Payment Method</label>
                                    <select id="finance-expense-method" name="payment_method" x-model="form.payment_method" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="Bank Transfer">Bank Transfer</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Card">Credit / Debit Card</option>
                                        <option value="Check">Check</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="finance-expense-receipt" class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-600">Receipt / Voucher</label>
                                    <input id="finance-expense-receipt" type="file" name="receipt" accept=".pdf,.png,.jpg,.jpeg,.webp,.doc,.docx" class="w-full text-xs text-slate-500">
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="finance-expense-description" class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-600">Notes</label>
                                    <textarea id="finance-expense-description" name="description" x-model="form.description" rows="3" maxlength="1000" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-2 border-t border-slate-100 bg-slate-50 px-5 py-4 sm:px-6">
                                <button type="button" @click="showForm = false" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100">Cancel</button>
                                <button type="submit" class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-700" x-text="isEdit ? 'Save Changes' : 'Add Expense Line'"></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>
</x-layouts.admin>
