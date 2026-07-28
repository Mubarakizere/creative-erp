<x-layouts.admin title="Edit Material Request">
<div class="px-4 sm:px-6 lg:px-8" x-data="materialRequestForm()">
    <div class="sm:flex sm:items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Edit Material Request: {{ $materialRequest->request_number }}</h1>
    </div>

    <div class="mt-6">
        <form action="{{ route('admin.material-requests.update', $materialRequest) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="bg-white dark:bg-gray-800 shadow px-4 py-5 sm:rounded-lg sm:p-6">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Request Details</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update information about this material request.</p>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2 space-y-6">
                        
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-4">
                                <label for="project_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Project <span class="text-red-500">*</span></label>
                                <select id="project_id" name="project_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select a Project</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" {{ (old('project_id', $materialRequest->project_id) == $project->id) ? 'selected' : '' }}>{{ $project->name }} ({{ $project->project_code }})</option>
                                    @endforeach
                                </select>
                                @error('project_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="request_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Request Date <span class="text-red-500">*</span></label>
                                <input type="date" name="request_date" id="request_date" required value="{{ old('request_date', $materialRequest->request_date->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('request_date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="required_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Required Date</label>
                                <input type="date" name="required_date" id="required_date" value="{{ old('required_date', $materialRequest->required_date ? $materialRequest->required_date->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('required_date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority <span class="text-red-500">*</span></label>
                                <select id="priority" name="priority" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="Low" {{ old('priority', $materialRequest->priority) == 'Low' ? 'selected' : '' }}>Low</option>
                                    <option value="Normal" {{ old('priority', $materialRequest->priority) == 'Normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="High" {{ old('priority', $materialRequest->priority) == 'High' ? 'selected' : '' }}>High</option>
                                    <option value="Urgent" {{ old('priority', $materialRequest->priority) == 'Urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                                @error('priority') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="col-span-6">
                                <label for="purpose" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Purpose / Justification</label>
                                <textarea id="purpose" name="purpose" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('purpose', $materialRequest->purpose) }}</textarea>
                                @error('purpose') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="col-span-6">
                                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Additional Notes</label>
                                <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('notes', $materialRequest->notes) }}</textarea>
                                @error('notes') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow px-4 py-5 sm:rounded-lg sm:p-6 mt-6">
                <div class="mb-4 flex justify-between items-center">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Material Items</h3>
                    <button type="button" @click="addItem" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">Add Item</button>
                </div>
                
                @error('items') <p class="mt-2 text-sm text-red-600 mb-4">{{ $message }}</p> @enderror

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                        <thead>
                            <tr>
                                <th class="px-3 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product <span class="text-red-500">*</span></th>
                                <th class="px-3 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Quantity <span class="text-red-500">*</span></th>
                                <th class="px-3 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                                <th class="px-3 py-3 bg-gray-50 dark:bg-gray-700 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-16"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                            <template x-for="(item, index) in items" :key="index">
                                <tr>
                                    <td class="px-3 py-4 whitespace-nowrap">
                                        <select x-model="item.product_id" :name="'items[' + index + '][product_id]'" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap">
                                        <input type="number" step="0.01" min="0.01" x-model="item.quantity_requested" :name="'items[' + index + '][quantity_requested]'" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap">
                                        <input type="text" x-model="item.notes" :name="'items[' + index + '][notes]'" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap text-right">
                                        <button type="button" @click="removeItem(index)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" x-show="items.length > 1">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <a href="{{ route('admin.material-requests.show', $materialRequest) }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">Update Request</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('materialRequestForm', () => ({
            items: @json($materialRequest->items->map(function($item) {
                return [
                    'product_id' => $item->product_id,
                    'quantity_requested' => $item->quantity_requested,
                    'notes' => $item->notes ?? ''
                ];
            })->toArray() ?: [['product_id' => '', 'quantity_requested' => '', 'notes' => '']]),
            addItem() {
                this.items.push({ product_id: '', quantity_requested: '', notes: '' });
            },
            removeItem(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                }
            }
        }));
    });
</script>
</x-layouts.admin>
