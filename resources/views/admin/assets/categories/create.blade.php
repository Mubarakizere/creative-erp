<x-layouts.admin title="Add Asset Category">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Equipment', 'url' => route('admin.assets.index')],
                ['label' => 'Categories', 'url' => route('admin.asset-categories.index')],
                ['label' => 'Add Category'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Create Asset Category</h1>
        <p class="mt-1 text-sm text-gray-500">Configure useful life, depreciation rules, and chart of account integration.</p>
    </div>

    <form action="{{ route('admin.asset-categories.store') }}" method="POST">
        @csrf
        <div class="space-y-8">
            <x-form-section 
                title="Category Info" 
                description="General identification, code, and default depreciation rules for assets belonging to this category.">
                
                <x-card>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <x-input 
                            label="Category Name" 
                            name="name" 
                            placeholder="e.g. IT Equipment, Vehicles" 
                            required 
                        />

                        <x-input 
                            label="Category Code" 
                            name="code" 
                            placeholder="e.g. CAT-IT, VEH" 
                            required 
                        />

                        <div class="md:col-span-2">
                            <x-textarea 
                                label="Description" 
                                name="description" 
                                placeholder="Provide brief summary of equipment classified in this category..." 
                                rows="3" 
                            />
                        </div>

                        <x-input 
                            label="Useful Life (Months)" 
                            name="useful_life" 
                            type="number" 
                            placeholder="36" 
                            required 
                            hint="Expected lifetime in months for depreciation schedule"
                        />

                        <x-select 
                            label="Depreciation Method" 
                            name="depreciation_method" 
                            required
                        >
                            <option value="straight_line" @selected(old('depreciation_method') == 'straight_line')>Straight Line</option>
                            <option value="declining_balance" @selected(old('depreciation_method') == 'declining_balance')>Declining Balance</option>
                            <option value="double_declining_balance" @selected(old('depreciation_method') == 'double_declining_balance')>Double Declining Balance</option>
                        </x-select>
                    </div>
                </x-card>
            </x-form-section>

            <x-form-section 
                title="General Ledger Integration" 
                description="Link asset purchase, accumulated depreciation, and expense accounts to the accounting module.">
                
                <x-card>
                    <div class="space-y-5">
                        <x-select 
                            label="Asset Account (Balance Sheet)" 
                            name="asset_account_id" 
                            required
                            placeholder="Select asset account..."
                        >
                            @foreach($accounts->where('accountType.category', 'Asset') as $account)
                                <option value="{{ $account->id }}" @selected(old('asset_account_id') == $account->id)>
                                    {{ $account->code }} — {{ $account->name }}
                                </option>
                            @endforeach
                        </x-select>

                        <x-select 
                            label="Accumulated Depreciation Account" 
                            name="accumulated_depreciation_account_id" 
                            required
                            placeholder="Select contra-asset account..."
                        >
                            @foreach($accounts->where('accountType.category', 'Asset') as $account)
                                <option value="{{ $account->id }}" @selected(old('accumulated_depreciation_account_id') == $account->id)>
                                    {{ $account->code }} — {{ $account->name }}
                                </option>
                            @endforeach
                        </x-select>

                        <x-select 
                            label="Depreciation Expense Account" 
                            name="depreciation_expense_account_id" 
                            required
                            placeholder="Select expense account..."
                        >
                            @foreach($accounts->where('accountType.category', 'Expense') as $account)
                                <option value="{{ $account->id }}" @selected(old('depreciation_expense_account_id') == $account->id)>
                                    {{ $account->code }} — {{ $account->name }}
                                </option>
                            @endforeach
                        </x-select>

                        <div class="pt-3 border-t border-gray-100">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 rounded text-blue-600 border-gray-300 focus:ring-blue-500">
                                <div>
                                    <span class="text-sm font-semibold text-gray-900">Active Category</span>
                                    <p class="text-xs text-gray-500">Allow new assets to be assigned to this category</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <x-slot:footer>
                        <a href="{{ route('admin.asset-categories.index') }}">
                            <x-button type="ghost">Cancel</x-button>
                        </a>
                        <x-button type="primary" submit>Create Category</x-button>
                    </x-slot:footer>
                </x-card>
            </x-form-section>
        </div>
    </form>
</x-layouts.admin>
