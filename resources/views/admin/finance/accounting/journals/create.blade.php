<x-layouts.admin title="Create Journal Entry">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Accounting', 'url' => route('admin.finance.accounting.ledger.index')],
                ['label' => 'Journal Entries', 'url' => route('admin.finance.accounting.journals.index')],
                ['label' => 'New Journal Entry']
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="space-y-6" x-data="journalForm({{ json_encode($projects) }}, '{{ old('company_id', $selectedCompanyId) }}', '{{ old('project_id', $selectedProjectId ?? '') }}')">
        {{-- Header Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-blue-600/10 rounded-xl text-blue-600 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">New Journal Entry</h1>
                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Record double-entry manual journal transactions linked to projects and companies in RWF.</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.finance.accounting.journals.index') }}" 
                   class="inline-flex items-center px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition-colors shadow-xs">
                    <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Journals
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-50/70 border border-red-200 text-red-700 px-5 py-4 rounded-2xl shadow-xs">
                <div class="flex items-center gap-2 mb-2 font-bold text-sm">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Please correct the following errors:
                </div>
                <ul class="list-disc list-inside text-xs space-y-1 pl-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.finance.accounting.journals.store') }}" class="space-y-6" id="journal-form">
            @csrf

            {{-- Journal Header Details --}}
            <div class="bg-white rounded-2xl border border-gray-200/70 shadow-sm p-6 space-y-6">
                <div class="border-b border-gray-100 pb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-gray-900 tracking-tight">Journal Header</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Specify transaction date, company entity, associated project, and reference.</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                        Manual Entry
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    {{-- Company Field --}}
                    <div>
                        <label for="company_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Company <span class="text-red-500">*</span>
                        </label>
                        <select name="company_id" id="company_id" x-model="selectedCompany" @change="onCompanyChange" required
                                class="block w-full rounded-xl border-gray-300 shadow-xs focus:border-blue-500 focus:ring-blue-500 text-xs py-2.5 transition-colors bg-white">
                            <option value="">Select Company</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('company_id') <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Project Field --}}
                    <div>
                        <label for="project_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Project <span class="text-[11px] text-gray-400 font-normal lowercase">(optional)</span>
                        </label>
                        <select name="project_id" id="project_id" x-model="selectedProject" @change="onProjectChange"
                                class="block w-full rounded-xl border-gray-300 shadow-xs focus:border-blue-500 focus:ring-blue-500 text-xs py-2.5 transition-colors bg-white">
                            <option value="">No Project (General Entry)</option>
                            <template x-for="proj in filteredProjects" :key="proj.id">
                                <option :value="proj.id" x-text="`${proj.name} (${proj.project_code})`" :selected="proj.id == selectedProject"></option>
                            </template>
                        </select>
                        @error('project_id') <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Journal Number --}}
                    <div>
                        <label for="journal_number" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Journal Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="journal_number" id="journal_number" 
                               value="{{ old('journal_number', $journal_number ?? '') }}" required
                               class="block w-full rounded-xl border-gray-300 shadow-xs focus:border-blue-500 focus:ring-blue-500 text-xs py-2.5 transition-colors font-mono">
                        @error('journal_number') <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Date --}}
                    <div>
                        <label for="date" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Entry Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date" id="date" 
                               value="{{ old('date', date('Y-m-d')) }}" required
                               class="block w-full rounded-xl border-gray-300 shadow-xs focus:border-blue-500 focus:ring-blue-500 text-xs py-2.5 transition-colors">
                        @error('date') <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 pt-2 border-t border-gray-100">
                    {{-- Reference Number --}}
                    <div>
                        <label for="reference_number" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Reference Number <span class="text-[11px] text-gray-400 font-normal lowercase">(optional)</span>
                        </label>
                        <input type="text" name="reference_number" id="reference_number" 
                               value="{{ old('reference_number') }}" placeholder="e.g. VOUCH-001, Check #, Receipt #"
                               class="block w-full rounded-xl border-gray-300 shadow-xs focus:border-blue-500 focus:ring-blue-500 text-xs py-2.5 transition-colors">
                        @error('reference_number') <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Memo / Description --}}
                    <div class="md:col-span-2">
                        <label for="memo" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Memo / Description <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="memo" id="memo" 
                               value="{{ old('memo') }}" placeholder="e.g. Project materials reclassification or equipment depreciation" required
                               class="block w-full rounded-xl border-gray-300 shadow-xs focus:border-blue-500 focus:ring-blue-500 text-xs py-2.5 transition-colors">
                        @error('memo') <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Journal Lines Card --}}
            <div class="bg-white rounded-2xl border border-gray-200/70 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h2 class="text-base font-bold text-gray-900 tracking-tight">Journal Lines</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Debits must equal credits in Rwandan Francs (RWF).</p>
                    </div>

                    {{-- Live Balance Pill & Line Action Buttons --}}
                    <div class="flex items-center gap-3 flex-wrap">
                        {{-- Live Balance Indicator --}}
                        <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border text-xs font-bold"
                             :class="{
                                 'bg-emerald-50 text-emerald-700 border-emerald-200': isBalanced(),
                                 'bg-rose-50 text-rose-700 border-rose-200': !isBalanced() && (totalDebit > 0 || totalCredit > 0),
                                 'bg-gray-100 text-gray-600 border-gray-200': totalDebit === 0 && totalCredit === 0
                             }">
                            <template x-if="isBalanced()">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Balanced (RWF <span x-text="totalDebit.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>)</span>
                                </div>
                            </template>
                            <template x-if="!isBalanced() && (totalDebit > 0 || totalCredit > 0)">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    <span>Diff: RWF <span x-text="Math.abs(totalDebit - totalCredit).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span></span>
                                </div>
                            </template>
                            <template x-if="totalDebit === 0 && totalCredit === 0">
                                <span>Awaiting amounts</span>
                            </template>
                        </div>

                        {{-- Auto-Balance Button --}}
                        <button type="button" @click="autoBalanceLastLine" 
                                x-show="!isBalanced() && (totalDebit > 0 || totalCredit > 0)"
                                class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200 hover:bg-indigo-100 transition-colors">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Auto-Balance
                        </button>

                        {{-- Add Line Button --}}
                        <button type="button" @click="addLine" 
                                class="inline-flex items-center px-3.5 py-1.5 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 transition-colors shadow-xs">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Line
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-full divide-y divide-gray-200/60">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th scope="col" class="px-5 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-widest w-12 text-center">#</th>
                                <th scope="col" class="px-5 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-widest w-2/5">Chart of Account <span class="text-red-500">*</span></th>
                                <th scope="col" class="px-5 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-widest w-1/4">Line Description <span class="text-red-500">*</span></th>
                                <th scope="col" class="px-5 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-widest text-right w-44">Debit (RWF)</th>
                                <th scope="col" class="px-5 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-widest text-right w-44">Credit (RWF)</th>
                                <th scope="col" class="px-4 py-3 w-12"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <template x-for="(line, index) in lines" :key="line.id">
                                <tr class="hover:bg-blue-50/20 transition-colors group">
                                    {{-- Row Number --}}
                                    <td class="px-5 py-3.5 text-center text-xs font-bold text-gray-400" x-text="index + 1"></td>

                                    {{-- Account Selection --}}
                                    <td class="px-5 py-3.5">
                                        <select x-model="line.chart_of_account_id" :name="`entries[${index}][chart_of_account_id]`" required
                                                class="block w-full rounded-xl border-gray-300 py-2 text-xs focus:border-blue-500 focus:ring-blue-500 transition-colors bg-white">
                                            <option value="">Select Account</option>
                                            @foreach($accounts as $account)
                                                <option value="{{ $account->id }}">
                                                    {{ $account->code }} — {{ $account->name }} ({{ $account->accountType->name ?? 'Account' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>

                                    {{-- Description --}}
                                    <td class="px-5 py-3.5">
                                        <input type="text" x-model="line.description" :name="`entries[${index}][description]`" required
                                               class="block w-full rounded-xl border-gray-300 py-2 text-xs focus:border-blue-500 focus:ring-blue-500 transition-colors"
                                               placeholder="Line memo / explanation">
                                    </td>

                                    {{-- Debit Amount --}}
                                    <td class="px-5 py-3.5">
                                        <div class="relative rounded-xl shadow-xs">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-400 text-xs font-semibold">RWF</span>
                                            </div>
                                            <input type="number" step="0.01" min="0" x-model="line.debit" :name="`entries[${index}][debit]`" required
                                                   @input="onDebitInput(index)"
                                                   class="pl-12 block w-full rounded-xl border-gray-300 py-2 pr-3 text-right text-xs font-bold text-gray-900 focus:border-blue-500 focus:ring-blue-500 transition-colors"
                                                   placeholder="0.00">
                                        </div>
                                    </td>

                                    {{-- Credit Amount --}}
                                    <td class="px-5 py-3.5">
                                        <div class="relative rounded-xl shadow-xs">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-400 text-xs font-semibold">RWF</span>
                                            </div>
                                            <input type="number" step="0.01" min="0" x-model="line.credit" :name="`entries[${index}][credit]`" required
                                                   @input="onCreditInput(index)"
                                                   class="pl-12 block w-full rounded-xl border-gray-300 py-2 pr-3 text-right text-xs font-bold text-gray-900 focus:border-blue-500 focus:ring-blue-500 transition-colors"
                                                   placeholder="0.00">
                                        </div>
                                    </td>

                                    {{-- Row Actions --}}
                                    <td class="px-4 py-3.5 text-center">
                                        <button type="button" @click="removeLine(index)" x-show="lines.length > 2"
                                                class="text-gray-300 hover:text-red-600 transition-colors p-1 rounded-lg hover:bg-red-50"
                                                title="Delete Line">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot class="bg-gray-50/80 border-t border-gray-200">
                            <tr>
                                <td colspan="3" class="px-5 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Total Transaction Volume
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Debit</div>
                                    <div class="text-sm font-black text-slate-900 font-mono mt-0.5" 
                                         x-text="'RWF ' + totalDebit.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></div>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Credit</div>
                                    <div class="text-sm font-black text-slate-900 font-mono mt-0.5" 
                                         x-text="'RWF ' + totalCredit.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></div>
                                </td>
                                <td></td>
                            </tr>
                            <tr x-show="!isBalanced() && (totalDebit > 0 || totalCredit > 0)">
                                <td colspan="6" class="px-6 py-3.5 bg-rose-50/80 border-t border-rose-200 text-center">
                                    <div class="flex items-center justify-center gap-2 text-xs font-bold text-rose-700">
                                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        <span>Journal is out of balance by: <span class="font-mono underline" x-text="'RWF ' + Math.abs(totalDebit - totalCredit).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>. Debits must equal Credits before saving.</span>
                                        <button type="button" @click="autoBalanceLastLine" class="ml-2 px-2.5 py-0.5 bg-rose-100 hover:bg-rose-200 text-rose-800 rounded-lg text-xs font-extrabold transition-colors">
                                            Fix Balance
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Form Submit & Action Bar --}}
            <div class="bg-white rounded-2xl border border-gray-200/70 shadow-sm p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-gray-500 font-medium flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full" :class="isBalanced() ? 'bg-emerald-500' : 'bg-amber-400'"></span>
                    <span x-text="isBalanced() ? 'Ready to record journal entry' : 'Requires at least 2 balanced debit/credit lines'"></span>
                </div>

                <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                    <a href="{{ route('admin.finance.accounting.journals.index') }}" 
                       class="inline-flex justify-center px-5 py-2.5 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors shadow-xs">
                        Cancel
                    </a>
                    <button type="submit" 
                            :disabled="!isBalanced() || lines.length < 2"
                            class="inline-flex items-center justify-center px-6 py-2.5 text-xs font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-all shadow-xs hover:shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Save Journal Entry
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function journalForm(projects, initialCompanyId, initialProjectId) {
            return {
                allProjects: projects || [],
                filteredProjects: [],
                selectedCompany: initialCompanyId ? String(initialCompanyId) : '',
                selectedProject: initialProjectId ? String(initialProjectId) : '',
                lines: [
                    { id: 1, chart_of_account_id: '', description: '', debit: '', credit: '' },
                    { id: 2, chart_of_account_id: '', description: '', debit: '', credit: '' }
                ],
                totalDebit: 0,
                totalCredit: 0,

                init() {
                    this.updateFilteredProjects();

                    // If preselected project exists, auto-sync its company
                    if (this.selectedProject) {
                        this.onProjectChange();
                    }

                    this.calculateTotals();
                },

                updateFilteredProjects() {
                    if (this.selectedCompany) {
                        this.filteredProjects = this.allProjects.filter(p => p.company_id == this.selectedCompany);
                    } else {
                        this.filteredProjects = this.allProjects;
                    }
                },

                onCompanyChange() {
                    this.updateFilteredProjects();
                    // If selected project does not belong to new company, reset project
                    if (this.selectedProject) {
                        let proj = this.allProjects.find(p => p.id == this.selectedProject);
                        if (proj && proj.company_id && proj.company_id != this.selectedCompany) {
                            this.selectedProject = '';
                        }
                    }
                },

                onProjectChange() {
                    if (this.selectedProject) {
                        let project = this.allProjects.find(p => p.id == this.selectedProject);
                        if (project && project.company_id) {
                            this.selectedCompany = String(project.company_id);
                            this.updateFilteredProjects();
                        }
                    }
                },

                addLine() {
                    let defaultDesc = '';
                    let memoInput = document.getElementById('memo');
                    if (memoInput && memoInput.value) {
                        defaultDesc = memoInput.value;
                    } else if (this.lines.length > 0 && this.lines[this.lines.length - 1].description) {
                        defaultDesc = this.lines[this.lines.length - 1].description;
                    }

                    this.lines.push({
                        id: Date.now() + Math.random(),
                        chart_of_account_id: '',
                        description: defaultDesc,
                        debit: '',
                        credit: ''
                    });
                },

                removeLine(index) {
                    if (this.lines.length > 2) {
                        this.lines.splice(index, 1);
                        this.calculateTotals();
                    }
                },

                onDebitInput(index) {
                    let val = parseFloat(this.lines[index].debit) || 0;
                    if (val > 0) {
                        this.lines[index].credit = '';
                    }
                    this.calculateTotals();
                },

                onCreditInput(index) {
                    let val = parseFloat(this.lines[index].credit) || 0;
                    if (val > 0) {
                        this.lines[index].debit = '';
                    }
                    this.calculateTotals();
                },

                calculateTotals() {
                    let debitSum = 0;
                    let creditSum = 0;
                    for (let line of this.lines) {
                        debitSum += parseFloat(line.debit) || 0;
                        creditSum += parseFloat(line.credit) || 0;
                    }
                    this.totalDebit = Math.round(debitSum * 100) / 100;
                    this.totalCredit = Math.round(creditSum * 100) / 100;
                },

                isBalanced() {
                    return Math.abs(this.totalDebit - this.totalCredit) < 0.005 && this.totalDebit > 0;
                },

                autoBalanceLastLine() {
                    if (this.lines.length < 2) return;
                    let diff = Math.round((this.totalDebit - this.totalCredit) * 100) / 100;
                    let lastLine = this.lines[this.lines.length - 1];

                    if (diff > 0) {
                        // More debits than credits, add to credit
                        let existingCredit = parseFloat(lastLine.credit) || 0;
                        lastLine.debit = '';
                        lastLine.credit = (existingCredit + diff).toFixed(2);
                    } else if (diff < 0) {
                        // More credits than debits, add to debit
                        let existingDebit = parseFloat(lastLine.debit) || 0;
                        lastLine.credit = '';
                        lastLine.debit = (existingDebit + Math.abs(diff)).toFixed(2);
                    }
                    this.calculateTotals();
                }
            }
        }
    </script>
    @endpush
</x-layouts.admin>
