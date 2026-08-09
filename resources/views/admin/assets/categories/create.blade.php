<x-layouts.admin title="Add Asset Category">
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Category Details</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Define default settings like useful life, depreciation method, and linked chart of accounts for assets in this category.
                </p>
            </div>
        </div>
        <div class="mt-5 md:mt-0 md:col-span-2">
            <form action="{{ route('admin.asset-categories.store') }}" method="POST">
                @csrf
                <div class="shadow sm:rounded-md sm:overflow-hidden">
                    <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                        
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-3">
                                <label for="name" class="block text-sm font-medium text-gray-700">Category Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            
                            <div class="col-span-6 sm:col-span-3">
                                <label for="code" class="block text-sm font-medium text-gray-700">Code</label>
                                <input type="text" name="code" id="code" value="{{ old('code') }}" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <div class="col-span-6 sm:col-span-6">
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea id="description" name="description" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md">{{ old('description') }}</textarea>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="useful_life" class="block text-sm font-medium text-gray-700">Useful Life (Months)</label>
                                <input type="number" name="useful_life" id="useful_life" value="{{ old('useful_life') }}" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            
                            <div class="col-span-6 sm:col-span-3">
                                <label for="depreciation_method" class="block text-sm font-medium text-gray-700">Depreciation Method</label>
                                <select id="depreciation_method" name="depreciation_method" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="straight_line" {{ old('depreciation_method') == 'straight_line' ? 'selected' : '' }}>Straight Line</option>
                                    <option value="declining_balance" {{ old('depreciation_method') == 'declining_balance' ? 'selected' : '' }}>Declining Balance</option>
                                    <option value="double_declining_balance" {{ old('depreciation_method') == 'double_declining_balance' ? 'selected' : '' }}>Double Declining Balance</option>
                                </select>
                            </div>

                            <!-- Ledger Accounts -->
                            <div class="col-span-6 mt-4">
                                <h4 class="font-medium text-gray-900 border-b pb-2">Ledger Integration Accounts</h4>
                            </div>

                            <div class="col-span-6 sm:col-span-4">
                                <label for="asset_account_id" class="block text-sm font-medium text-gray-700">Asset Account</label>
                                <select id="asset_account_id" name="asset_account_id" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Select Account</option>
                                    @foreach($accounts->where('accountType.category', 'Asset') as $account)
                                        <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-6 sm:col-span-4">
                                <label for="accumulated_depreciation_account_id" class="block text-sm font-medium text-gray-700">Accumulated Depreciation Account</label>
                                <select id="accumulated_depreciation_account_id" name="accumulated_depreciation_account_id" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Select Account</option>
                                    @foreach($accounts->where('accountType.category', 'Asset') as $account)
                                        <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-6 sm:col-span-4">
                                <label for="depreciation_expense_account_id" class="block text-sm font-medium text-gray-700">Depreciation Expense Account</label>
                                <select id="depreciation_expense_account_id" name="depreciation_expense_account_id" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Select Account</option>
                                    @foreach($accounts->where('accountType.category', 'Expense') as $account)
                                        <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-6">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="is_active" name="is_active" type="checkbox" value="1" checked class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="is_active" class="font-medium text-gray-700">Active</label>
                                        <p class="text-gray-500">Allow new assets to be assigned to this category.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Create Category
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</x-layouts.admin>
