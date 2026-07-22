<x-layouts.admin title="Create Journal Entry">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Accounting', 'url' => '#'],
                ['label' => 'Journal Entries', 'url' => route('admin.finance.accounting.journals.index')],
                ['label' => 'New Entry']
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">New Journal Entry</h1>
        <p class="mt-1 text-sm text-gray-500">Create a manual double-entry journal.</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div x-data="journalForm()">
        <form method="POST" action="{{ route('admin.finance.accounting.journals.store') }}" class="space-y-6">
            @csrf
            
            <x-card>
                <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-100 pb-2">Journal Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700">Date <span class="text-red-500">*</span></label>
                        <input type="date" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    
                    <div>
                        <label for="fiscal_year_id" class="block text-sm font-medium text-gray-700">Fiscal Year</label>
                        <select name="fiscal_year_id" id="fiscal_year_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">Auto-assign based on date</option>
                            @foreach($fiscalYears as $year)
                                <option value="{{ $year->id }}" {{ old('fiscal_year_id') == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="accounting_period_id" class="block text-sm font-medium text-gray-700">Accounting Period</label>
                        <select name="accounting_period_id" id="accounting_period_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">Auto-assign based on date</option>
                            @foreach($periods as $period)
                                <option value="{{ $period->id }}" {{ old('accounting_period_id') == $period->id ? 'selected' : '' }}>
                                    {{ $period->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label for="reference_number" class="block text-sm font-medium text-gray-700">Reference Number</label>
                        <input type="text" name="reference_number" id="reference_number" value="{{ old('reference_number') }}"
                               placeholder="e.g. INV-2023-001"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    
                    <div>
                        <label for="memo" class="block text-sm font-medium text-gray-700">Memo / Description <span class="text-red-500">*</span></label>
                        <input type="text" name="memo" id="memo" value="{{ old('memo') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                </div>
            </x-card>

            <x-card class="p-0 overflow-hidden">
                <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Journal Lines</h3>
                    <button type="button" @click="addLine" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Line
                    </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider w-1/3">Account</th>
                                <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider w-1/3">Description</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">Debit</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">Credit</th>
                                <th scope="col" class="px-4 py-3 w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="(line, index) in lines" :key="line.id">
                                <tr class="hover:bg-gray-50 transition-colors group">
                                    <td class="px-4 py-3">
                                        <select x-model="line.chart_of_account_id" :name="`entries[${index}][chart_of_account_id]`" required
                                                class="block w-full rounded-md border-gray-300 py-1.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Select Account</option>
                                            @foreach($accounts as $account)
                                                <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" x-model="line.description" :name="`entries[${index}][description]`" required
                                               class="block w-full rounded-md border-gray-300 py-1.5 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Line description">
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="relative rounded-md shadow-sm">
                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <span class="text-gray-500 sm:text-sm">$</span>
                                            </div>
                                            <input type="number" step="0.01" min="0" x-model="line.debit" :name="`entries[${index}][debit]`" required @input="calculateTotals" @change="if(line.debit > 0) line.credit = 0; calculateTotals()"
                                                   class="block w-full rounded-md border-gray-300 pl-7 py-1.5 pr-3 text-right text-sm focus:border-blue-500 focus:ring-blue-500">
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="relative rounded-md shadow-sm">
                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <span class="text-gray-500 sm:text-sm">$</span>
                                            </div>
                                            <input type="number" step="0.01" min="0" x-model="line.credit" :name="`entries[${index}][credit]`" required @input="calculateTotals" @change="if(line.credit > 0) line.debit = 0; calculateTotals()"
                                                   class="block w-full rounded-md border-gray-300 pl-7 py-1.5 pr-3 text-right text-sm focus:border-blue-500 focus:ring-blue-500">
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button type="button" @click="removeLine(index)" x-show="lines.length > 2" class="text-gray-400 hover:text-red-500 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="2" class="px-4 py-3 text-right font-medium text-gray-700">Totals</td>
                                <td class="px-4 py-3 text-right font-bold text-gray-900" x-text="'$' + totalDebit.toFixed(2)"></td>
                                <td class="px-4 py-3 text-right font-bold text-gray-900" x-text="'$' + totalCredit.toFixed(2)"></td>
                                <td></td>
                            </tr>
                            <tr x-show="!isBalanced()">
                                <td colspan="5" class="px-4 py-3 text-center text-sm font-medium text-red-600 bg-red-50 border-t border-red-100">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    Debits and Credits must be equal. Difference: <span x-text="'$' + Math.abs(totalDebit - totalCredit).toFixed(2)"></span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </x-card>

            <div class="flex justify-end gap-3 pt-4">
                <x-button type="default" href="{{ route('admin.finance.accounting.journals.index') }}">Cancel</x-button>
                <button type="submit" :disabled="!isBalanced() || lines.length < 2" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                    Save Draft
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function journalForm() {
            return {
                lines: [
                    { id: 1, chart_of_account_id: '', description: '', debit: 0, credit: 0 },
                    { id: 2, chart_of_account_id: '', description: '', debit: 0, credit: 0 }
                ],
                totalDebit: 0,
                totalCredit: 0,
                
                addLine() {
                    this.lines.push({
                        id: Date.now(),
                        chart_of_account_id: '',
                        description: this.lines.length > 0 ? this.lines[this.lines.length - 1].description : '',
                        debit: 0,
                        credit: 0
                    });
                },
                
                removeLine(index) {
                    if (this.lines.length > 2) {
                        this.lines.splice(index, 1);
                        this.calculateTotals();
                    }
                },
                
                calculateTotals() {
                    this.totalDebit = this.lines.reduce((sum, line) => sum + (parseFloat(line.debit) || 0), 0);
                    this.totalCredit = this.lines.reduce((sum, line) => sum + (parseFloat(line.credit) || 0), 0);
                },
                
                isBalanced() {
                    return Math.abs(this.totalDebit - this.totalCredit) < 0.01 && this.totalDebit > 0;
                }
            }
        }
    </script>
    @endpush
</x-layouts.admin>
