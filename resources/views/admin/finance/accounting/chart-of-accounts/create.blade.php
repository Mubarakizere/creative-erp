<x-layouts.admin title="Create Account - Chart of Accounts">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Accounting', 'url' => route('admin.finance.accounting.ledger.index')],
                ['label' => 'Chart of Accounts', 'url' => route('admin.finance.accounting.chart-of-accounts.index')],
                ['label' => 'New Account']
            ];
        @endphp
    </x-slot:breadcrumbs>

    {{-- Header Section --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.finance.accounting.chart-of-accounts.index') }}" 
               class="p-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl transition-colors shrink-0"
               title="Back to Chart of Accounts">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Create Ledger Account</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Add a new account to your general ledger chart of accounts.</p>
            </div>
        </div>
    </div>

    {{-- Error Banner --}}
    @if ($errors->any())
        <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl shadow-sm">
            <div class="flex items-center gap-2 font-bold text-xs mb-1">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Please fix the following validation errors:
            </div>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div x-data="{
        showTypeModal: false,
        newTypeName: '',
        newTypeCategory: 'Asset',
        newTypeCode: '',
        typeSaving: false,
        typeError: '',
        async submitNewType() {
            if (!this.newTypeName.trim()) {
                this.typeError = 'Please enter an account type name.';
                return;
            }
            this.typeSaving = true;
            this.typeError = '';
            try {
                const res = await fetch('{{ route('admin.account-types.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        name: this.newTypeName,
                        category: this.newTypeCategory,
                        code: this.newTypeCode,
                        is_active: 1
                    })
                });
                const data = await res.json();
                if (res.ok && data.account_type) {
                    const sel = document.getElementById('account_type_id');
                    const opt = new Option(data.account_type.name + ' (' + data.account_type.category + ')', data.account_type.id, true, true);
                    sel.add(opt);
                    sel.value = data.account_type.id;
                    this.newTypeName = '';
                    this.newTypeCode = '';
                    this.showTypeModal = false;
                } else {
                    this.typeError = data.message || 'Failed to save account type.';
                }
            } catch (e) {
                this.typeError = 'An error occurred while creating the account type.';
            } finally {
                this.typeSaving = false;
            }
        }
    }">

    <form method="POST" action="{{ route('admin.finance.accounting.chart-of-accounts.store') }}" class="space-y-6 max-w-4xl">
        @csrf

        {{-- Main Account Details Card --}}
        <x-card class="p-6 bg-white border border-gray-200/80 shadow-sm rounded-xl">
            <h3 class="text-sm font-bold text-gray-900 border-b border-gray-100 pb-3 mb-6 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h10M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Account Definition & Classification
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Account Code --}}
                <div>
                    <label for="code" class="block text-xs font-semibold text-gray-700 mb-1">
                        Account Code <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="code" 
                           id="code" 
                           value="{{ old('code') }}" 
                           placeholder="e.g. 1010, 4010, 5020" 
                           required
                           class="w-full px-3.5 py-2 text-xs rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors font-mono">
                    <p class="mt-1 text-[11px] text-gray-400">Standard convention: 1000s (Assets), 2000s (Liabilities), 3000s (Equity), 4000s (Revenue), 5000s (Expenses).</p>
                    @error('code') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                {{-- Account Name --}}
                <div>
                    <label for="name" class="block text-xs font-semibold text-gray-700 mb-1">
                        Account Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name') }}" 
                           placeholder="e.g. Bank Account - Equity, Rent Expense" 
                           required
                           class="w-full px-3.5 py-2 text-xs rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                {{-- Account Type --}}
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="account_type_id" class="block text-xs font-semibold text-gray-700">
                            Account Type <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="showTypeModal = true" class="text-[11px] font-semibold text-blue-600 hover:text-blue-800 flex items-center gap-1 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span>+ Quick Add</span>
                            </button>
                            <span class="text-gray-300">|</span>
                            <a href="{{ route('admin.account-types.index') }}" target="_blank" class="text-[11px] font-semibold text-gray-500 hover:text-gray-700 flex items-center gap-1 transition-colors" title="Manage all account types in Administration">
                                <span>Manage Types</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    <select name="account_type_id" id="account_type_id" required class="w-full text-xs">
                        <option value="">Select Account Type</option>
                        @foreach($accountTypes as $type)
                            <option value="{{ $type->id }}" {{ old('account_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }} ({{ $type->category }})
                            </option>
                        @endforeach
                    </select>
                    @error('account_type_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                {{-- Parent Account --}}
                <div>
                    <label for="parent_id" class="block text-xs font-semibold text-gray-700 mb-1">
                        Parent Account (Sub-account of)
                    </label>
                    <select name="parent_id" id="parent_id" class="w-full text-xs">
                        <option value="">None (Top Level Account)</option>
                        @foreach($parentAccounts as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                {{ $parent->code }} - {{ $parent->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-[11px] text-gray-400">Select a parent account if this is a sub-account for hierarchical rollups.</p>
                    @error('parent_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Description --}}
            <div class="mt-6 pt-4 border-t border-gray-100">
                <label for="description" class="block text-xs font-semibold text-gray-700 mb-1">
                    Description & Operating Purpose
                </label>
                <textarea name="description" 
                          id="description" 
                          rows="3" 
                          placeholder="Briefly describe what transactions should be posted to this account..." 
                          class="w-full px-3.5 py-2 text-xs rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">{{ old('description') }}</textarea>
                @error('description') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            {{-- Status Toggle --}}
            <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-900 block">Account Status</span>
                    <span class="text-[11px] text-gray-500">Active accounts appear in journal entry selectors across the ERP.</span>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', true) ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>
        </x-card>

        {{-- Action Buttons --}}
        <div class="flex items-center justify-end gap-3 pt-2">
            <x-button type="default" href="{{ route('admin.finance.accounting.chart-of-accounts.index') }}" class="shadow-sm">
                Cancel
            </x-button>
            <x-button type="primary" submit class="shadow-sm hover:shadow-md transition-shadow">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Create Account
            </x-button>
        </div>
    </form>

    {{-- Quick Add Account Type Modal --}}
    <div x-show="showTypeModal" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title" role="dialog" aria-modal="true"
         style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showTypeModal" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                 @click="showTypeModal = false"
                 aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showTypeModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-100">
                
                <div class="bg-white px-5 pt-5 pb-4 sm:p-6">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <div class="flex items-center gap-2.5">
                            <div class="p-2 bg-blue-50 text-blue-600 rounded-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900" id="modal-title">New Account Type</h3>
                                <p class="text-[11px] text-gray-500">Create an account classification on the fly.</p>
                            </div>
                        </div>
                        <button type="button" @click="showTypeModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <template x-if="typeError">
                        <div class="mt-3 p-2.5 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-lg" x-text="typeError"></div>
                    </template>

                    <div class="mt-4 space-y-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">
                                Type Name <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   x-model="newTypeName" 
                                   placeholder="e.g. Other Asset, Marketing Expense" 
                                   class="w-full px-3 py-2 text-xs rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">
                                Category <span class="text-rose-500">*</span>
                            </label>
                            <select x-model="newTypeCategory" class="w-full text-xs rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="Asset">Asset</option>
                                <option value="Liability">Liability</option>
                                <option value="Equity">Equity</option>
                                <option value="Revenue">Revenue</option>
                                <option value="Expense">Expense</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">
                                Short Code (Optional)
                            </label>
                            <input type="text" 
                                   x-model="newTypeCode" 
                                   placeholder="e.g. OA, MKT" 
                                   class="w-full px-3 py-2 text-xs rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono">
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-5 py-3 sm:px-6 flex items-center justify-end gap-2.5 border-t border-gray-100">
                    <button type="button" 
                            @click="showTypeModal = false" 
                            class="px-3.5 py-1.5 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="button" 
                            @click="submitNewType" 
                            :disabled="typeSaving"
                            class="px-4 py-1.5 text-xs font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50 flex items-center gap-1.5 shadow-sm">
                        <span x-show="typeSaving" class="inline-block animate-spin">&#9696;</span>
                        <span x-text="typeSaving ? 'Saving...' : 'Save Type'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    </div>
</x-layouts.admin>
