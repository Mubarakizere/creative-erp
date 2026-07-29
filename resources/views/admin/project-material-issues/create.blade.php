@extends('layouts.admin')

@section('title', 'Issue Project Material')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="materialIssueForm()">
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                Issue Material
            </h2>
        </div>
    </div>

    @if ($errors->any())
        <div class="rounded-md bg-red-50 p-4 mb-6">
            <div class="flex">
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">There were {{ $errors->count() }} errors with your submission</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul role="list" class="list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.project-material-issues.store') }}" method="POST" class="space-y-8 divide-y divide-gray-200">
        @csrf
        <div class="space-y-8 divide-y divide-gray-200">
            <div>
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label for="project_id" class="block text-sm font-medium text-gray-700">Project</label>
                        <div class="mt-1">
                            <select id="project_id" name="project_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required x-on:change="window.location.search = '?project_id=' + $event.target.value + '&warehouse_id=' + (new URLSearchParams(window.location.search).get('warehouse_id') || '')">
                                <option value="">Select Project</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ request('project_id', old('project_id')) == $project->id ? 'selected' : '' }}>{{ $project->name }} ({{ $project->project_code }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="task_id" class="block text-sm font-medium text-gray-700">Task / Activity (Optional)</label>
                        <div class="mt-1">
                            <select id="task_id" name="task_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">Select Task</option>
                                @foreach($tasks as $task)
                                    <option value="{{ $task->id }}" {{ old('task_id') == $task->id ? 'selected' : '' }}>{{ $task->name }} ({{ $task->task_code }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="warehouse_id" class="block text-sm font-medium text-gray-700">Warehouse</label>
                        <div class="mt-1">
                            <select id="warehouse_id" name="warehouse_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required x-on:change="window.location.search = '?warehouse_id=' + $event.target.value + '&project_id=' + (new URLSearchParams(window.location.search).get('project_id') || '')">
                                <option value="">Select Warehouse (Reloads items)</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="issue_number" class="block text-sm font-medium text-gray-700">Issue Number</label>
                        <div class="mt-1">
                            <input type="text" name="issue_number" id="issue_number" value="{{ old('issue_number', $issueNumber) }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" readonly>
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="issue_date" class="block text-sm font-medium text-gray-700">Issue Date</label>
                        <div class="mt-1">
                            <input type="date" name="issue_date" id="issue_date" value="{{ old('issue_date', $today) }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>
                    </div>
                    
                    <div class="sm:col-span-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <div class="mt-1">
                            <textarea id="notes" name="notes" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-8">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Items to Issue</h3>
                    <button type="button" @click="addItem()" class="inline-flex items-center rounded-md border border-transparent bg-indigo-100 px-3 py-2 text-sm font-medium leading-4 text-indigo-700 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Add Item
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead>
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Product</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Quantity</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Available</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200" id="items-container">
                            <template x-for="(item, index) in items" :key="index">
                                <tr>
                                    <td class="py-4 pl-4 pr-3 text-sm">
                                        <select :name="`items[${index}][product_id]`" x-model="item.product_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-3 py-4 text-sm">
                                        <input type="number" :name="`items[${index}][quantity]`" x-model="item.quantity" step="0.01" min="0.01" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    </td>
                                    <td class="px-3 py-4 text-sm text-gray-500">
                                        <span x-text="getAvailableQty(item.product_id)"></span>
                                    </td>
                                    <td class="relative py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                        <button type="button" @click="removeItem(index)" class="text-red-600 hover:text-red-900">Remove</button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="pt-5">
            <div class="flex justify-end">
                <a href="{{ route('admin.project-material-issues.index') }}" class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Cancel</a>
                <button type="submit" onclick="return confirm('Are you sure you want to issue this material? This will deduct inventory and add cost to the project.')" class="ml-3 inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Issue Material</button>
            </div>
        </div>
    </form>
</div>

<script>
    function materialIssueForm() {
        return {
            items: [{ product_id: '', quantity: 1 }],
            productStock: {
                @foreach($products as $product)
                    '{{ $product->id }}': 'Check Server',
                @endforeach
            },
            addItem() {
                this.items.push({ product_id: '', quantity: 1 });
            },
            removeItem(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                }
            },
            getAvailableQty(productId) {
                return productId ? (this.productStock[productId] || 'N/A') : '';
            }
        }
    }
</script>
@endsection
