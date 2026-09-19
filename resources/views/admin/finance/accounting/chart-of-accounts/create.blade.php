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
                    <label for="account_type_id" class="block text-xs font-semibold text-gray-700 mb-1">
                        Account Type <span class="text-rose-500">*</span>
                    </label>
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
</x-layouts.admin>
