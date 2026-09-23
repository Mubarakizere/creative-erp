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
        <div class="space-y-6">
            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <a href="{{ route('admin.material-requests.index') }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 mb-2 transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Back to Material Requests
                    </a>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Create Material Request</h1>
                    <p class="mt-1 text-sm text-slate-500 font-medium">Submit a new material request for project site delivery.</p>
                </div>
            </div>

            @if ($errors->any())
                <div class="rounded-2xl bg-rose-50 p-4 border border-rose-200 shadow-xs">
                    <div class="flex items-start gap-3">
                        <div class="p-1.5 bg-rose-100 rounded-lg text-rose-700 flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-rose-900">There were {{ $errors->count() }} errors with your submission</h3>
                            <ul role="list" class="mt-1 text-xs text-rose-700 list-disc list-inside space-y-0.5 font-medium">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('admin.material-requests.store') }}" method="POST" id="material-request-form"
                  x-data="materialRequestForm({
                      projectsData: {{ \Illuminate\Support\Js::from($projectsData) }},
                      initialProjectId: '{{ old('project_id', $selectedProject->id ?? '') }}',
                      initialCompanyId: '{{ old('company_id', $defaultCompanyId ?? '') }}',
                      initialTaskId: '{{ old('task_id', '') }}',
                      initialItems: {{ \Illuminate\Support\Js::from(old('items', [['product_id' => '', 'quantity_requested' => '', 'notes' => '']])) }}
                  })">
                @csrf
                
                {{-- Request Details Card --}}
                <x-card class="mb-6">
                    <x-slot:header>
                        <h3 class="text-lg font-bold text-slate-900 tracking-tight">Request Details</h3>
                        <p class="mt-0.5 text-sm text-slate-500">General information about this material request.</p>
                    </x-slot:header>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-6">
                        {{-- Request Number --}}
                        <div class="col-span-1 sm:col-span-2 lg:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Request Number</label>
                            <div class="px-3.5 py-2.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-900 font-mono font-bold text-sm">
                                {{ old('request_number', $request_number ?? '') }}
                            </div>
                            <input type="hidden" name="request_number" value="{{ old('request_number', $request_number ?? '') }}">
                        </div>

                        {{-- Company --}}
                        <div class="col-span-1 sm:col-span-2 lg:col-span-2">
                            <label for="company_id" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                                Company <span class="text-rose-500 font-bold ml-0.5">*</span>
                            </label>
                            <div class="relative group">
                                <select name="company_id" id="company_id" required x-model="selectedCompanyId"
                                        class="block w-full rounded-xl text-sm transition-all duration-200 border appearance-none border-gray-200 bg-gray-50/50 hover:bg-white hover:border-gray-300 text-gray-900 focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-500/10 pl-3.5 pr-10 py-2.5 min-h-[42px] shadow-xs cursor-pointer">
                                    <option value="">Select a Company</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}">
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-blue-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Project --}}
                        <div class="col-span-1 sm:col-span-2 lg:col-span-2">
                            <label for="project_id" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                                Project <span class="text-rose-500 font-bold ml-0.5">*</span>
                            </label>
                            <div class="relative group">
                                <select name="project_id" id="project_id" required x-model="selectedProjectId" @change="onProjectChange"
                                        class="block w-full rounded-xl text-sm transition-all duration-200 border appearance-none border-gray-200 bg-gray-50/50 hover:bg-white hover:border-gray-300 text-gray-900 focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-500/10 pl-3.5 pr-10 py-2.5 min-h-[42px] shadow-xs cursor-pointer">
                                    <option value="">Select a Project</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}">
                                            {{ $project->name }} ({{ $project->project_code }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-blue-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Task --}}
                        <div class="col-span-1 sm:col-span-2 lg:col-span-2">
                            <label for="task_id" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                                Task / Activity (Optional)
                            </label>
                            <div class="relative group">
                                <select name="task_id" id="task_id" x-model="selectedTaskId"
                                        class="block w-full rounded-xl text-sm transition-all duration-200 border appearance-none border-gray-200 bg-gray-50/50 hover:bg-white hover:border-gray-300 text-gray-900 focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-500/10 pl-3.5 pr-10 py-2.5 min-h-[42px] shadow-xs cursor-pointer">
                                    <option value="">Select a Task</option>
                                    <template x-for="task in availableTasks" :key="task.id">
                                        <option :value="task.id" x-text="task.name + (task.task_code ? ' (' + task.task_code + ')' : '')"></option>
                                    </template>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-blue-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Request Date --}}
                        <div class="col-span-1 sm:col-span-2 lg:col-span-2">
                            <x-input type="date" name="request_date" label="Request Date" value="{{ old('request_date', date('Y-m-d')) }}" required />
                        </div>

                        {{-- Required Date --}}
                        <div class="col-span-1 sm:col-span-2 lg:col-span-2">
                            <x-input type="date" name="required_date" label="Required Date" value="{{ old('required_date') }}" />
                        </div>

                        {{-- Priority --}}
                        <div class="col-span-1 sm:col-span-2 lg:col-span-2">
                            <x-select name="priority" label="Priority Level" required>
                                <option value="Low" {{ old('priority') == 'Low' ? 'selected' : '' }}>Low</option>
                                <option value="Normal" {{ old('priority', 'Normal') == 'Normal' ? 'selected' : '' }}>Normal</option>
                                <option value="High" {{ old('priority') == 'High' ? 'selected' : '' }}>High</option>
                                <option value="Urgent" {{ old('priority') == 'Urgent' ? 'selected' : '' }}>Urgent</option>
                            </x-select>
                        </div>

                        {{-- Purpose / Justification --}}
                        <div class="col-span-1 sm:col-span-2 lg:col-span-6">
                            <x-textarea name="purpose" label="Purpose / Justification (Optional)" rows="3" placeholder="Explain why these materials are needed for the site work...">{{ old('purpose') }}</x-textarea>
                        </div>

                        {{-- Additional Notes --}}
                        <div class="col-span-1 sm:col-span-2 lg:col-span-6">
                            <x-textarea name="notes" label="Additional Notes (Optional)" rows="2" placeholder="Any special delivery instructions or specifications...">{{ old('notes') }}</x-textarea>
                        </div>
                    </div>
                </x-card>

                {{-- Material Items Card --}}
                <div>
                    <x-card class="mb-6">
                        <x-slot:header>
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-bold text-slate-900 tracking-tight">Material Items</h3>
                                    <p class="mt-0.5 text-sm text-slate-500 font-medium">Add products and quantities needed for site delivery.</p>
                                </div>
                                <button type="button" @click="addItem" class="inline-flex items-center px-4 py-2 text-sm font-bold text-blue-700 bg-blue-50 border border-blue-200 rounded-xl hover:bg-blue-100 transition-colors shadow-xs">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    Add Item
                                </button>
                            </div>
                        </x-slot:header>

                        <div class="overflow-x-auto border-t border-slate-100">
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead class="bg-slate-50/80">
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-bold uppercase tracking-wider text-slate-600">Product / Material <span class="text-rose-500">*</span></th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-600 w-48">Quantity <span class="text-rose-500">*</span></th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-600">Notes / Remarks</th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 w-16">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    <template x-for="(item, index) in items" :key="index">
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="py-3 pl-4 pr-3 text-sm align-top">
                                                <select x-model="item.product_id" :name="'items[' + index + '][product_id]'" required class="block w-full rounded-xl border-slate-300 shadow-xs focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 transition-colors bg-white font-medium">
                                                    <option value="">Select Product</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="px-3 py-3 text-sm align-top">
                                                <input type="number" step="0.01" min="0.01" x-model="item.quantity_requested" :name="'items[' + index + '][quantity_requested]'" required class="block w-full rounded-xl border-slate-300 shadow-xs focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 transition-colors font-bold text-slate-900">
                                            </td>
                                            <td class="px-3 py-3 text-sm align-top">
                                                <input type="text" x-model="item.notes" :name="'items[' + index + '][notes]'" placeholder="Optional notes..." class="block w-full rounded-xl border-slate-300 shadow-xs focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 transition-colors">
                                            </td>
                                            <td class="relative py-3 pl-3 pr-4 text-center text-sm font-medium sm:pr-6 align-middle">
                                                <button type="button" @click="removeItem(index)" class="text-rose-500 hover:text-rose-700 hover:bg-rose-50 p-2 rounded-lg transition-colors" x-show="items.length > 1" title="Remove Item">
                                                    <svg class="h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <x-slot:footer>
                            <div class="flex items-center justify-end gap-3 w-full">
                                <a href="{{ route('admin.material-requests.index') }}" class="inline-flex justify-center items-center rounded-xl border border-slate-300 bg-white py-2.5 px-5 text-sm font-semibold text-slate-700 shadow-xs hover:bg-slate-50 focus:outline-none transition-all">Cancel</a>
                                <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-xs transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Create Request
                                </button>
                            </div>
                        </x-slot:footer>
                    </x-card>
                </div>
            </form>
        </div>
    @else
        <div class="text-center py-16 bg-white rounded-2xl border border-slate-200 shadow-xs">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-rose-50 mb-4 border border-rose-200">
                <svg class="h-8 w-8 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-2">Access Denied</h3>
            <p class="text-sm text-slate-500 font-medium">You do not have permission to create material requests.</p>
            <div class="mt-6">
                <a href="{{ route('admin.material-requests.index') }}" class="px-5 py-2.5 text-sm font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-xs transition-all">Return to Material Requests</a>
            </div>
        </div>
    @endcan

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('materialRequestForm', (config = {}) => ({
                projectsData: config.projectsData || {},
                selectedProjectId: config.initialProjectId || '',
                selectedCompanyId: config.initialCompanyId || '',
                selectedTaskId: config.initialTaskId || '',
                items: config.initialItems && config.initialItems.length ? config.initialItems : [
                    { product_id: '', quantity_requested: '', notes: '' }
                ],

                get availableTasks() {
                    if (!this.selectedProjectId || !this.projectsData[this.selectedProjectId]) {
                        return [];
                    }
                    return this.projectsData[this.selectedProjectId].tasks || [];
                },

                onProjectChange() {
                    const proj = this.projectsData[this.selectedProjectId];
                    if (proj && proj.company_id) {
                        this.selectedCompanyId = String(proj.company_id);
                    }
                    const taskExists = this.availableTasks.some(t => String(t.id) === String(this.selectedTaskId));
                    if (!taskExists) {
                        this.selectedTaskId = '';
                    }
                },

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
