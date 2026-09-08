@php
    $user = auth()->user();
    $canManageExpenses = $user && ($user->hasRole('Super Admin') || $user->hasRole('CEO') || $project->hasPermissionForUser($user, 'project_expense.create') || $project->hasPermissionForUser($user, 'project.view-budget') || $project->hasPermissionForUser($user, 'project.update'));
    $expenses = $project->expenses->sortByDesc('expense_date');
@endphp


<div x-data="{ 
    showModal: false, 
    isEdit: false, 
    editUrl: '', 
    form: { 
        title: '', 
        category: 'Miscellaneous', 
        amount: '', 
        expense_date: '{{ date('Y-m-d') }}', 
        vendor_name: '', 
        user_id: '', 
        payment_status: 'Paid', 
        payment_method: 'Cash', 
        description: '' 
    },
    openCreateModal(defaultCategory = 'Miscellaneous') {
        this.isEdit = false;
        this.editUrl = '';
        this.form = {
            title: defaultCategory === 'Worker Salary' ? 'Worker Salary Payout' : '',
            category: defaultCategory,
            amount: '',
            expense_date: '{{ date('Y-m-d') }}',
            vendor_name: '',
            user_id: '',
            payment_status: 'Paid',
            payment_method: 'Bank Transfer',
            description: ''
        };
        this.showModal = true;
    },
    openEditModal(expense) {
        this.isEdit = true;
        this.editUrl = '{{ url('/admin/projects/expenses') }}/' + expense.id;
        this.form = {
            title: expense.title,
            category: expense.category,
            amount: expense.amount,
            expense_date: expense.expense_date ? expense.expense_date.split('T')[0] : '',
            vendor_name: expense.vendor_name || '',
            user_id: expense.user_id || '',
            payment_status: expense.payment_status || 'Paid',
            payment_method: expense.payment_method || 'Cash',
            description: expense.description || ''
        };
        this.showModal = true;
    }
}">

    {{-- Expense KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Direct Expenses</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ format_currency($financialSummary['direct_expenses'], $project->currency) }}</span>
                <p class="text-xs text-slate-500 mt-1">Operational & Site expenses</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Worker Salary & Labor</span>
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-extrabold text-indigo-700 tracking-tight">{{ format_currency($financialSummary['total_labor_cost'], $project->currency) }}</span>
                <p class="text-xs text-slate-500 mt-1">
                    Direct Salary: <strong class="text-slate-700">{{ format_currency($financialSummary['direct_labor_expenses'], $project->currency) }}</strong>
                </p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Materials Issued</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ format_currency($financialSummary['material_cost'], $project->currency) }}</span>
                <p class="text-xs text-slate-500 mt-1">Inventory items issued</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Combined Costs</span>
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2z"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-extrabold text-rose-700 tracking-tight">{{ format_currency($financialSummary['total_costs'], $project->currency) }}</span>
                <p class="text-xs text-slate-500 mt-1">All expenses & labor combined</p>
            </div>
        </div>
    </div>

    {{-- Expense List Card --}}
    <x-card>
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h3 class="text-base font-bold text-slate-900 tracking-tight">Project Expenses & Worker Salaries</h3>
                <p class="text-xs text-slate-500">Record site costs, equipment hire, supplies, and worker salary payouts.</p>
            </div>
            
            @if($canManageExpenses)
                <div class="flex items-center gap-2">
                    <x-button type="secondary" size="sm" @click="openCreateModal('Worker Salary')">
                        <svg class="w-4 h-4 mr-1 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Record Worker Salary
                    </x-button>
                    <x-button type="primary" size="sm" @click="openCreateModal('Miscellaneous')">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Expense
                    </x-button>
                </div>
            @endif
        </div>

        <x-table>
            <x-slot:head>
                <th class="px-5 py-3.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Expense & Category</th>
                <th class="px-5 py-3.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Payee / Worker</th>
                <th class="px-5 py-3.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Date</th>
                <th class="px-5 py-3.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Payment</th>
                <th class="px-5 py-3.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Amount</th>
                <th class="px-5 py-3.5 text-right text-[11px] font-bold text-slate-400 uppercase tracking-widest">Actions</th>
            </x-slot:head>

            @forelse($expenses as $expense)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold shrink-0 {{ in_array($expense->category, ['Worker Salary', 'Labor', 'Payroll']) ? 'bg-indigo-50 text-indigo-600' : 'bg-slate-100 text-slate-600' }}">
                                @if(in_array($expense->category, ['Worker Salary', 'Labor', 'Payroll']))
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 v14a2 2 0 01-2 2z"/></svg>
                                @endif
                            </div>
                            <div>
                                <span class="block text-sm font-bold text-slate-900">{{ $expense->title }}</span>
                                <span class="inline-block text-[10px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded {{ in_array($expense->category, ['Worker Salary', 'Labor', 'Payroll']) ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $expense->category }}
                                </span>
                            </div>
                        </div>
                    </td>

                    <td class="px-5 py-3.5">
                        @if($expense->user)
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center">
                                    {{ substr($expense->user->full_name ?? $expense->user->first_name ?? 'U', 0, 1) }}
                                </div>
                                <span class="text-xs font-semibold text-slate-900">{{ $expense->user->full_name ?? ($expense->user->first_name . ' ' . $expense->user->last_name) }}</span>
                            </div>
                        @elseif($expense->vendor_name)
                            <span class="text-xs font-medium text-slate-700">{{ $expense->vendor_name }}</span>
                        @else
                            <span class="text-xs text-slate-400">—</span>
                        @endif
                    </td>

                    <td class="px-5 py-3.5 text-xs font-medium text-slate-600">
                        {{ $expense->expense_date?->format('M d, Y') }}
                    </td>

                    <td class="px-5 py-3.5">
                        <div class="flex flex-col gap-0.5">
                            <span class="inline-flex items-center text-[10px] font-bold px-2 py-0.5 rounded-full w-max {{ $expense->payment_status === 'Paid' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($expense->payment_status === 'Pending' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-blue-50 text-blue-700 border border-blue-200') }}">
                                {{ $expense->payment_status }}
                            </span>
                            @if($expense->payment_method)
                                <span class="text-[10px] text-slate-400">{{ $expense->payment_method }}</span>
                            @endif
                        </div>
                    </td>

                    <td class="px-5 py-3.5">
                        <span class="text-sm font-extrabold text-slate-900">{{ format_currency($expense->amount, $project->currency) }}</span>
                    </td>

                    <td class="px-5 py-3.5 text-right space-x-2">
                        @if($expense->receipt_path)
                            <a href="{{ Storage::disk('public')->url($expense->receipt_path) }}" target="_blank" class="inline-flex items-center p-1.5 text-slate-500 hover:text-blue-600 transition-colors" title="View Receipt">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                            </a>
                        @endif

                        @if($canManageExpenses)
                            <button @click="openEditModal({{ json_encode($expense) }})" class="p-1.5 text-slate-400 hover:text-blue-600 transition-colors" title="Edit">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>

                            <form action="{{ route('admin.projects.expenses.destroy', $expense) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this expense entry?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 transition-colors" title="Delete">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center text-slate-500 text-xs">
                        No expenses or worker salary entries recorded for this project yet.
                    </td>
                </tr>
            @endforelse
        </x-table>
    </x-card>

    {{-- Create / Edit Modal Dialog --}}
    <template x-teleport="body">
        <div x-show="showModal" x-cloak class="fixed inset-0 z-[9999] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="showModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200 relative z-[10000]">
                    <form :action="isEdit ? editUrl : '{{ route('admin.projects.expenses.store', $project) }}'" method="POST" enctype="multipart/form-data">
                        @csrf
                        <template x-if="isEdit">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div class="bg-white px-6 pt-6 pb-4">
                            <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-100">
                                <h3 class="text-lg font-bold text-slate-900" x-text="isEdit ? 'Edit Project Expense' : (form.category === 'Worker Salary' ? 'Record Worker Salary' : 'Add Project Expense')"></h3>
                                <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Expense Category *</label>
                                    <select name="category" x-model="form.category" required class="w-full rounded-xl border-slate-200 text-xs font-medium focus:border-blue-500 focus:ring-blue-500">
                                        <option value="Worker Salary">Worker Salary / Wages</option>
                                        <option value="Equipment Hire">Equipment Hire</option>
                                        <option value="Subcontractor">Subcontractor Fees</option>
                                        <option value="Travel & Logistics">Travel & Logistics</option>
                                        <option value="Permits & Legal">Permits & Legal Fees</option>
                                        <option value="Site Supplies">Site Supplies</option>
                                        <option value="Utilities">Utilities & Power</option>
                                        <option value="Miscellaneous">Miscellaneous Expense</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Expense Title / Reason *</label>
                                    <input type="text" name="title" x-model="form.title" required placeholder="e.g. Weekly Wages for John Doe or Generator Fuel" class="w-full rounded-xl border-slate-200 text-xs font-medium focus:border-blue-500 focus:ring-blue-500">
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Amount ({{ $project->currency }}) *</label>
                                        <input type="number" step="0.01" min="0.01" name="amount" x-model="form.amount" required placeholder="0.00" class="w-full rounded-xl border-slate-200 text-xs font-medium focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Date *</label>
                                        <input type="date" name="expense_date" x-model="form.expense_date" required class="w-full rounded-xl border-slate-200 text-xs font-medium focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Assign Worker (Optional)</label>
                                        <select name="user_id" x-model="form.user_id" class="w-full rounded-xl border-slate-200 text-xs font-medium focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">-- Select Team Member --</option>
                                            @foreach($teamUsers as $user)
                                                <option value="{{ $user->id }}">{{ $user->full_name ?? ($user->first_name . ' ' . $user->last_name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Vendor / Payee Name</label>
                                        <input type="text" name="vendor_name" x-model="form.vendor_name" placeholder="Worker or Supplier Name" class="w-full rounded-xl border-slate-200 text-xs font-medium focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Payment Status *</label>
                                        <select name="payment_status" x-model="form.payment_status" required class="w-full rounded-xl border-slate-200 text-xs font-medium focus:border-blue-500 focus:ring-blue-500">
                                            <option value="Paid">Paid</option>
                                            <option value="Pending">Pending Payout</option>
                                            <option value="Reimbursement">Worker Reimbursement</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Payment Method</label>
                                        <select name="payment_method" x-model="form.payment_method" class="w-full rounded-xl border-slate-200 text-xs font-medium focus:border-blue-500 focus:ring-blue-500">
                                            <option value="Bank Transfer">Bank Transfer</option>
                                            <option value="Cash">Cash</option>
                                            <option value="Card">Credit/Debit Card</option>
                                            <option value="Check">Check</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Receipt / Voucher Attachment</label>
                                    <input type="file" name="receipt" accept=".pdf,.png,.jpg,.jpeg,.doc,.docx" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Notes / Description</label>
                                    <textarea name="description" x-model="form.description" rows="2" placeholder="Additional details..." class="w-full rounded-xl border-slate-200 text-xs font-medium focus:border-blue-500 focus:ring-blue-500"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-100">
                            <button type="button" @click="showModal = false" class="px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">Cancel</button>
                            <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-xs transition-colors" x-text="isEdit ? 'Update Entry' : 'Save Entry'"></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>

