<x-layouts.admin title="Create Account Type - Administration">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Administration', 'url' => route('admin.dashboard')],
                ['label' => 'Account Types', 'url' => route('admin.account-types.index')],
                ['label' => 'New Account Type']
            ];
        @endphp
    </x-slot:breadcrumbs>

    {{-- Header Section --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.account-types.index') }}" 
               class="p-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl transition-colors shrink-0"
               title="Back to Account Types">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Create Account Type</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Add a new financial account classification category for your general ledger.</p>
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

    <form method="POST" action="{{ route('admin.account-types.store') }}" class="space-y-6 max-w-2xl">
        @csrf

        {{-- Main Details Card --}}
        <x-card class="p-6 bg-white border border-gray-200/80 shadow-sm rounded-xl">
            <h3 class="text-sm font-bold text-gray-900 border-b border-gray-100 pb-3 mb-6 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                Account Type Details
            </h3>

            <div class="space-y-5">
                {{-- Name --}}
                <div>
                    <label for="name" class="block text-xs font-semibold text-gray-700 mb-1">
                        Type Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name') }}" 
                           placeholder="e.g. Current Asset, Operating Expense, Cost of Sales" 
                           required
                           class="w-full px-3.5 py-2 text-xs rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                {{-- Category --}}
                <div>
                    <label for="category" class="block text-xs font-semibold text-gray-700 mb-1">
                        Category <span class="text-rose-500">*</span>
                    </label>
                    <select name="category" id="category" required class="w-full text-xs">
                        <option value="">Select Financial Category</option>
                        @foreach($categories as $value => $label)
                            <option value="{{ $value }}" {{ old('category') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-[11px] text-gray-400">Controls balance sheet vs income statement classification and standard debit/credit behavior.</p>
                    @error('category') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                {{-- Code --}}
                <div>
                    <label for="code" class="block text-xs font-semibold text-gray-700 mb-1">
                        Short Code (Optional)
                    </label>
                    <input type="text" 
                           name="code" 
                           id="code" 
                           value="{{ old('code') }}" 
                           placeholder="e.g. CA, LTL, EXP" 
                           class="w-full px-3.5 py-2 text-xs rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors font-mono">
                    <p class="mt-1 text-[11px] text-gray-400">Short abbreviation used in financial reporting filters.</p>
                    @error('code') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                {{-- Status Toggle --}}
                <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-gray-900 block">Active Status</span>
                        <span class="text-[11px] text-gray-500">Active account types appear in the Chart of Accounts type selector.</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', true) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
            </div>
        </x-card>

        {{-- Action Buttons --}}
        <div class="flex items-center justify-end gap-3 pt-2">
            <x-button type="default" href="{{ route('admin.account-types.index') }}" class="shadow-sm">
                Cancel
            </x-button>
            <x-button type="primary" submit class="shadow-sm hover:shadow-md transition-shadow">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Create Account Type
            </x-button>
        </div>
    </form>
</x-layouts.admin>
