<x-layouts.admin title="Issue Project Material">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Project Material Issues', 'url' => route('admin.project-material-issues.index')],
                ['label' => 'Issue Material'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.project-material-issues.index') }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 mb-2 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to List
            </a>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Issue Material</h1>
            <p class="mt-1 text-sm text-gray-500 font-medium">Record materials being issued from a warehouse to a specific project.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="rounded-xl bg-red-50 p-4 mb-6 border border-red-100">
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

    <form action="{{ route('admin.project-material-issues.store') }}" method="POST" x-data="materialIssueForm()">
        @csrf
        <x-card class="mb-6">
            <x-slot:header>
                <h3 class="text-lg font-medium text-gray-900">Issue Details</h3>
                <p class="mt-1 text-sm text-gray-500">Select the project, warehouse, and date for this issue.</p>
            </x-slot:header>
            
            <div class="grid grid-cols-1 gap-y-6 gap-x-6 sm:grid-cols-2 lg:grid-cols-3">
                <div class="sm:col-span-1 lg:col-span-2">
                    <x-select name="project_id" label="Project" required x-on:change="window.location.search = '?project_id=' + $event.target.value + '&warehouse_id=' + (new URLSearchParams(window.location.search).get('warehouse_id') || '')">
                        <option value="">Select Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project_id', old('project_id')) == $project->id ? 'selected' : '' }}>{{ $project->name }} ({{ $project->project_code }})</option>
                        @endforeach
                    </x-select>
                </div>

                <div class="sm:col-span-1">
                    <x-select name="task_id" label="Task / Activity (Optional)">
                        <option value="">Select Task</option>
                        @foreach($tasks as $task)
                            <option value="{{ $task->id }}" {{ old('task_id') == $task->id ? 'selected' : '' }}>{{ $task->name }} ({{ $task->task_code }})</option>
                        @endforeach
                    </x-select>
                </div>

                <div class="sm:col-span-1 lg:col-span-2">
                    <x-select name="warehouse_id" label="Warehouse" required x-on:change="window.location.search = '?warehouse_id=' + $event.target.value + '&project_id=' + (new URLSearchParams(window.location.search).get('project_id') || '')">
                        <option value="">Select Warehouse (Reloads items)</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                        @endforeach
                    </x-select>
                </div>

                <div class="sm:col-span-1 border border-gray-100 bg-gray-50/50 rounded-xl p-4 flex flex-col justify-center">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Issue Number</label>
                    <div class="text-lg font-mono font-semibold text-gray-900">{{ old('issue_number', $issueNumber) }}</div>
                    <input type="hidden" name="issue_number" value="{{ old('issue_number', $issueNumber) }}">
                </div>

                <div class="sm:col-span-1 lg:col-span-1">
                    <x-input type="date" name="issue_date" label="Issue Date" value="{{ old('issue_date', $today) }}" required />
                </div>
                
                <div class="sm:col-span-2 lg:col-span-2">
                    <x-textarea name="notes" label="Notes (Optional)" rows="3" placeholder="Add any relevant notes about this issue...">{{ old('notes') }}</x-textarea>
                </div>
            </div>
        </x-card>

        <x-card>
            <x-slot:header>
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Items to Issue</h3>
                        <p class="mt-1 text-sm text-gray-500">Add the materials and quantities you are issuing.</p>
                    </div>
                    <x-button type="button" variant="secondary" @click="addItem()">
                        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Add Item
                    </x-button>
                </div>
            </x-slot:header>

            <div class="overflow-x-auto border-t border-gray-100">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Product</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 w-48">Quantity</th>
                            <th scope="col" class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900 w-32">Available</th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 w-16">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white" id="items-container">
                        <template x-for="(item, index) in items" :key="index">
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-3 pl-4 pr-3 text-sm align-top">
                                    <select :name="`items[${index}][product_id]`" x-model="item.product_id" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 transition-colors" required>
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-3 py-3 text-sm align-top">
                                    <input type="number" :name="`items[${index}][quantity]`" x-model="item.quantity" step="0.01" min="0.01" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 transition-colors" required>
                                </td>
                                <td class="px-3 py-3 text-sm text-center align-middle">
                                    <span class="inline-flex items-center justify-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800" x-text="getAvailableQty(item.product_id)"></span>
                                </td>
                                <td class="relative py-3 pl-3 pr-4 text-center text-sm font-medium sm:pr-6 align-middle">
                                    <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg transition-colors" title="Remove Item">
                                        <svg class="h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="items.length === 0">
                            <td colspan="4" class="py-8 text-center text-sm text-gray-500 bg-gray-50/50">
                                No items added yet. Click "Add Item" to begin.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <x-slot:footer>
                <div class="flex justify-end gap-3 w-full">
                    <a href="{{ route('admin.project-material-issues.index') }}" class="inline-flex justify-center items-center rounded-xl border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all">Cancel</a>
                    <x-button type="button" variant="primary" @click="$dispatch('open-issue-confirm')">
                        Issue Material
                    </x-button>
                </div>
            </x-slot:footer>
        </x-card>
    </form>

    {{-- Issue Confirmation Modal --}}
    <div x-data="{ open: false }"
         x-on:open-issue-confirm.window="open = true"
         x-on:keydown.escape.window="open = false">

        <template x-teleport="body">
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 overflow-y-auto"
                 style="display: none;">

                {{-- Backdrop --}}
                <div class="fixed inset-0 bg-black/50 backdrop-blur-xs" @click="open = false"></div>

                {{-- Modal --}}
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden" @click.stop>
                        {{-- Header --}}
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                            <div class="text-lg font-bold text-gray-900">Confirm Material Issue</div>
                            <button @click="open = false" type="button" class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Content --}}
                        <div class="px-6 py-5">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900 mb-1">Are you sure you want to issue this material?</h3>
                                    <p class="text-sm text-gray-500">This action will deduct stock from the selected warehouse and add the material cost to the project. This cannot be undone.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="flex items-center gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50/50 justify-end">
                            <button type="button" @click="open = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-xs">
                                Cancel
                            </button>
                            <button type="button"
                                    @click="open = false; document.querySelector('form[action*=project-material-issues]').submit();"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-colors shadow-xs cursor-pointer">
                                Confirm Issue
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
    
    <script>
        function materialIssueForm() {
            return {
                items: [{ product_id: '{{ $initialProductId }}', quantity: 1 }],
                productStock: @json($productStockMap),
                addItem() {
                    this.items.push({ product_id: '', quantity: 1 });
                },
                removeItem(index) {
                    this.items.splice(index, 1);
                },
                getAvailableQty(productId) {
                    if (!productId) return '-';
                    return this.productStock[productId] !== undefined ? this.productStock[productId] : 0;
                }
            }
        }
    </script>
</x-layouts.admin>
