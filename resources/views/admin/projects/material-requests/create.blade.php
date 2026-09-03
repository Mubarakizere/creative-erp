<x-layouts.admin title="Create Material Request">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Projects', 'url' => route('admin.projects.index')],
                ['label' => 'Material Requests', 'url' => route('admin.material-requests.index')],
                ['label' => 'Create Request'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    @can('create', App\Models\ProjectMaterialRequest::class)
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.material-requests.index') }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 mb-2 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Material Requests
            </a>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Create Material Request</h1>
            <p class="mt-1 text-sm text-gray-500 font-medium">Add a new material request for a project.</p>
        </div>
    </div>

    <div class="mt-6">
        <form action="{{ route('admin.material-requests.store') }}" method="POST" id="material-request-form">
            @csrf
            
            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden mb-6">
                <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
                    <h3 class="text-lg font-bold text-gray-900 tracking-tight">Request Details</h3>
                    <p class="mt-1 text-sm text-gray-500 font-medium">Basic information about this material request.</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-6">
                        <div class="col-span-1 sm:col-span-2 lg:col-span-2">
                            <label for="request_number" class="block text-sm font-medium text-gray-700 mb-1">Request Number <span class="text-red-500">*</span></label>
                            <input type="text" name="request_number" id="request_number" value="{{ old('request_number', $request_number ?? '') }}" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors bg-gray-50 cursor-not-allowed" readonly>
                            @error('request_number') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="col-span-1 sm:col-span-2 lg:col-span-2">
                            <label for="project_id" class="block text-sm font-medium text-gray-700 mb-1">Project <span class="text-red-500">*</span></label>
                            <select id="project_id" name="project_id" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors bg-white">
                                <option value="">Select a Project</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ (old('project_id') == $project->id || ($selectedProject && $selectedProject->id == $project->id)) ? 'selected' : '' }}>{{ $project->name }} ({{ $project->project_code }})</option>
                                @endforeach
                            </select>
                            @error('project_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="col-span-1 sm:col-span-2 lg:col-span-2">
                            <label for="task_id" class="block text-sm font-medium text-gray-700 mb-1">Task / Activity (Optional)</label>
                            <select id="task_id" name="task_id" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors bg-white">
                                <option value="">Select a Task</option>
                                @foreach($tasks as $task)
                                    <option value="{{ $task->id }}" {{ old('task_id') == $task->id ? 'selected' : '' }}>{{ $task->name }} ({{ $task->task_code }})</option>
                                @endforeach
                            </select>
                            @error('task_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="col-span-1 sm:col-span-2 lg:col-span-2">
                            <label for="request_date" class="block text-sm font-medium text-gray-700 mb-1">Request Date <span class="text-red-500">*</span></label>
                            <input type="date" name="request_date" id="request_date" required value="{{ old('request_date', date('Y-m-d')) }}" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">
                                @error('request_date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                        <div class="col-span-1 sm:col-span-2 lg:col-span-2">
                            <label for="required_date" class="block text-sm font-medium text-gray-700 mb-1">Required Date</label>
                            <input type="date" name="required_date" id="required_date" value="{{ old('required_date') }}" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">
                                @error('required_date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                        <div class="col-span-1 sm:col-span-2 lg:col-span-2">
                            <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority <span class="text-red-500">*</span></label>
                            <select id="priority" name="priority" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors bg-white">
                                <option value="Low" {{ old('priority') == 'Low' ? 'selected' : '' }}>Low</option>
                                <option value="Normal" {{ old('priority', 'Normal') == 'Normal' ? 'selected' : '' }}>Normal</option>
                                <option value="High" {{ old('priority') == 'High' ? 'selected' : '' }}>High</option>
                                <option value="Urgent" {{ old('priority') == 'Urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                                @error('priority') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                        <div class="col-span-1 sm:col-span-2 lg:col-span-6">
                            <label for="purpose" class="block text-sm font-medium text-gray-700 mb-1">Purpose / Justification</label>
                            <textarea id="purpose" name="purpose" rows="3" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">{{ old('purpose') }}</textarea>
                                @error('purpose') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                        <div class="col-span-1 sm:col-span-2 lg:col-span-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Additional Notes</label>
                            <textarea id="notes" name="notes" rows="2" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">{{ old('notes') }}</textarea>
                            @error('notes') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden mb-6" x-data="materialRequestForm()">
                <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 tracking-tight">Material Items</h3>
                        <p class="mt-1 text-sm text-gray-500 font-medium">Add products and quantities needed.</p>
                    </div>
                    <button type="button" @click="addItem" class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-700 bg-blue-100 rounded-xl hover:bg-blue-200 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Add Item
                    </button>
                </div>
                
                @error('items') <p class="mt-2 text-sm text-red-600 mb-4">{{ $message }}</p> @enderror

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200/60">
                        <thead class="bg-gray-50/30">
                            <tr>
                                <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Product <span class="text-red-500">*</span></th>
                                <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-48">Quantity <span class="text-red-500">*</span></th>
                                <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Notes</th>
                                <th class="px-6 py-3 text-right text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-16"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <template x-for="(item, index) in items" :key="index">
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <select x-model="item.product_id" :name="'items[' + index + '][product_id]'" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm bg-white">
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="number" step="0.01" min="0.01" x-model="item.quantity_requested" :name="'items[' + index + '][quantity_requested]'" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="text" x-model="item.notes" :name="'items[' + index + '][notes]'" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <button type="button" @click="removeItem(index)" class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" x-show="items.length > 1">
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

            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden px-6 py-4 flex items-center justify-end gap-3 mb-8">
                <a href="{{ route('admin.material-requests.index') }}" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">Cancel</a>
                <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none hover:shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Create Request
                </button>
            </div>
        </form>
    </div>
    @else
    <div class="text-center py-16 bg-white rounded-2xl border border-gray-200/60 shadow-sm">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4 border border-red-200">
            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Access Denied</h3>
        <p class="text-sm text-gray-500 font-medium">You do not have permission to create material requests.</p>
        <div class="mt-6">
            <a href="{{ route('admin.material-requests.index') }}" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all">Return to Material Requests</a>
        </div>
    </div>
    @endcan

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('materialRequestForm', () => ({
            items: [
                { product_id: '', quantity_requested: '', notes: '' }
            ],
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

    document.getElementById('project_id').addEventListener('change', function() {
        if (this.value) {
            window.location.href = '?project_id=' + this.value;
        } else {
            window.location.href = '?';
        }
    });
</script>
</x-layouts.admin>
