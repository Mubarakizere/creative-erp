<x-layouts.admin title="Add Fixed Asset">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Equipment', 'url' => route('admin.assets.index')],
                ['label' => 'Add Fixed Asset'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Register New Fixed Asset</h1>
        <p class="mt-1 text-sm text-gray-500">Record financial cost, depreciation terms, and initial custody assignment.</p>
    </div>

    <form action="{{ route('admin.assets.store') }}" method="POST">
        @csrf
        <div class="space-y-8">
            <x-form-section 
                title="Asset Identification & Valuation" 
                description="Core asset details including serial numbers, classification category, purchase cost, and residual value.">
                
                <x-card>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        <x-input 
                            label="Asset Number" 
                            name="asset_number" 
                            value="{{ old('asset_number', 'AST-'.str_pad(mt_rand(1,9999), 4, '0', STR_PAD_LEFT)) }}" 
                            required 
                        />

                        <x-input 
                            label="Asset Name" 
                            name="name" 
                            placeholder="e.g. Heavy Duty Generator, MacBook Pro" 
                            required 
                        />

                        <x-select 
                            label="Category" 
                            name="asset_category_id" 
                            required
                            placeholder="Select asset category..."
                        >
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('asset_category_id') == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </x-select>

                        <x-input 
                            label="Serial Number" 
                            name="serial_number" 
                            placeholder="e.g. SN-98234-X" 
                        />

                        <x-input 
                            label="Purchase Cost" 
                            name="purchase_cost" 
                            type="number" 
                            step="0.01" 
                            placeholder="0.00" 
                            required 
                        />

                        <x-input 
                            label="Residual Value" 
                            name="residual_value" 
                            type="number" 
                            step="0.01" 
                            value="0" 
                            required 
                            hint="Scrap value at end of useful life"
                        />

                        <x-input 
                            label="Useful Life (Months)" 
                            name="useful_life" 
                            type="number" 
                            placeholder="36" 
                            required 
                        />

                        <x-input 
                            label="Purchase Date" 
                            name="purchase_date" 
                            type="date" 
                        />

                        <x-input 
                            label="In Service Date" 
                            name="in_service_date" 
                            type="date" 
                        />

                        <div class="md:col-span-2 lg:col-span-3">
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
                    </div>
                </x-card>
            </x-form-section>

            <x-form-section 
                title="Custody & Location Assignment" 
                description="Assign initial branch, department, or individual custodian responsible for the equipment.">
                
                <x-card>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <x-select 
                            label="Branch" 
                            name="branch_id" 
                            placeholder="Select branch..."
                        >
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" @selected(old('branch_id') == $branch->id)>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </x-select>

                        <x-select 
                            label="Department" 
                            name="department_id" 
                            placeholder="Select department..."
                        >
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </x-select>

                        <x-select 
                            label="Assign to User" 
                            name="assigned_user_id" 
                            placeholder="Select custodian..."
                        >
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" @selected(old('assigned_user_id') == $u->id)>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </x-select>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-100">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="auto_capitalize" value="1" class="w-4 h-4 rounded text-blue-600 border-gray-300 focus:ring-blue-500">
                            <div>
                                <span class="text-sm font-semibold text-gray-900">Auto-Capitalize Asset</span>
                                <p class="text-xs text-gray-500">Automatically post journal entry to capitalize asset and set status to Active</p>
                            </div>
                        </label>
                    </div>

                    <x-slot:footer>
                        <a href="{{ route('admin.assets.index') }}">
                            <x-button type="ghost">Cancel</x-button>
                        </a>
                        <x-button type="primary" submit>Register Asset</x-button>
                    </x-slot:footer>
                </x-card>
            </x-form-section>
        </div>
    </form>
</x-layouts.admin>
