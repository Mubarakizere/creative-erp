@extends('layouts.admin')

@section('title', 'Add Fixed Asset')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Asset Information</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Enter the details of the new fixed asset.
                </p>
            </div>
        </div>
        <div class="mt-5 md:mt-0 md:col-span-2">
            <form action="{{ route('admin.assets.store') }}" method="POST">
                @csrf
                <div class="shadow sm:rounded-md sm:overflow-hidden">
                    <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                        
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-3">
                                <label for="asset_number" class="block text-sm font-medium text-gray-700">Asset Number</label>
                                <input type="text" name="asset_number" id="asset_number" value="{{ old('asset_number', 'AST-'.str_pad(mt_rand(1,9999), 4, '0', STR_PAD_LEFT)) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            
                            <div class="col-span-6 sm:col-span-3">
                                <label for="name" class="block text-sm font-medium text-gray-700">Asset Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="asset_category_id" class="block text-sm font-medium text-gray-700">Category</label>
                                <select id="asset_category_id" name="asset_category_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="serial_number" class="block text-sm font-medium text-gray-700">Serial Number</label>
                                <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <div class="col-span-6 sm:col-span-2">
                                <label for="purchase_cost" class="block text-sm font-medium text-gray-700">Purchase Cost</label>
                                <input type="number" step="0.01" name="purchase_cost" id="purchase_cost" value="{{ old('purchase_cost') }}" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <div class="col-span-6 sm:col-span-2">
                                <label for="residual_value" class="block text-sm font-medium text-gray-700">Residual Value</label>
                                <input type="number" step="0.01" name="residual_value" id="residual_value" value="{{ old('residual_value', 0) }}" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <div class="col-span-6 sm:col-span-2">
                                <label for="useful_life" class="block text-sm font-medium text-gray-700">Useful Life (Months)</label>
                                <input type="number" name="useful_life" id="useful_life" value="{{ old('useful_life') }}" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="purchase_date" class="block text-sm font-medium text-gray-700">Purchase Date</label>
                                <input type="date" name="purchase_date" id="purchase_date" value="{{ old('purchase_date') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="in_service_date" class="block text-sm font-medium text-gray-700">In Service Date</label>
                                <input type="date" name="in_service_date" id="in_service_date" value="{{ old('in_service_date') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            
                            <div class="col-span-6 sm:col-span-3">
                                <label for="depreciation_method" class="block text-sm font-medium text-gray-700">Depreciation Method</label>
                                <select id="depreciation_method" name="depreciation_method" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="straight_line">Straight Line</option>
                                    <option value="declining_balance">Declining Balance</option>
                                    <option value="double_declining_balance">Double Declining Balance</option>
                                </select>
                            </div>
                            
                            <div class="col-span-6 mt-4">
                                <h4 class="font-medium text-gray-900 border-b pb-2">Assignments (Optional)</h4>
                            </div>

                            <div class="col-span-6 sm:col-span-2">
                                <label for="branch_id" class="block text-sm font-medium text-gray-700">Branch</label>
                                <select id="branch_id" name="branch_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Select Branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-6 sm:col-span-2">
                                <label for="department_id" class="block text-sm font-medium text-gray-700">Department</label>
                                <select id="department_id" name="department_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-6 sm:col-span-2">
                                <label for="assigned_user_id" class="block text-sm font-medium text-gray-700">Assign to User</label>
                                <select id="assigned_user_id" name="assigned_user_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Select User</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-span-6 mt-4">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="auto_capitalize" name="auto_capitalize" type="checkbox" value="1" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="auto_capitalize" class="font-medium text-gray-700">Auto-Capitalize Asset</label>
                                        <p class="text-gray-500">Automatically create a journal entry to capitalize this asset and set its status to Active.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Save Asset
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
