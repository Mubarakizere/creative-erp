<div class="bg-white rounded-2xl border border-gray-200/70 shadow-xs overflow-hidden">
    <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
        <h3 class="text-base font-bold text-gray-900 tracking-tight">Project Details</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6"
             x-data="{
                 companyId: '{{ old('company_id', $project->company_id ?? '') }}',
                 branchId: '{{ old('branch_id', $project->branch_id ?? '') }}',
                 clientId: '{{ old('client_id', $project->client_id ?? '') }}',
                 branches: {{ Js::from($branches) }},
                 clients: {{ Js::from($clients) }},
                 availableBranches: [],
                 availableClients: [],
                 updateDropdowns() {
                     this.availableBranches = this.branches.filter(b => b.company_id == this.companyId);
                     this.availableClients = this.clients.filter(c => c.company_id == this.companyId && (this.branchId === '' || c.branch_id == this.branchId));
                     if(!this.availableBranches.some(b => b.id == this.branchId)) this.branchId = '';
                     if(!this.availableClients.some(c => c.id == this.clientId)) this.clientId = '';
                 }
             }"
             x-init="updateDropdowns()">
         
            {{-- Company --}}
            <x-select
                label="Company"
                name="company_id"
                placeholder="Select Company"
                x-model="companyId"
                @change="updateDropdowns()"
                required
                class="col-span-1"
            >
                @foreach($companies as $company)
                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                @endforeach
            </x-select>

            {{-- Branch --}}
            <div class="col-span-1">
                <x-select
                    label="Branch"
                    name="branch_id"
                    placeholder="Select Branch"
                    x-model="branchId"
                    @change="updateDropdowns()"
                    required
                    ::disabled="availableBranches.length === 0"
                >
                    <template x-for="branch in availableBranches" :key="branch.id">
                        <option :value="branch.id" x-text="branch.name" :selected="branch.id == branchId"></option>
                    </template>
                </x-select>
            </div>

            {{-- Client --}}
            <div class="col-span-1">
                <x-select
                    label="Client"
                    name="client_id"
                    placeholder="Select Client"
                    x-model="clientId"
                    required
                    ::disabled="availableClients.length === 0"
                >
                    <template x-for="client in availableClients" :key="client.id">
                        <option :value="client.id" x-text="client.display_name" :selected="client.id == clientId"></option>
                    </template>
                </x-select>
            </div>

            {{-- Project Manager --}}
            <x-select
                label="Project Manager"
                name="project_manager_id"
                placeholder="Select Manager"
                required
                class="col-span-1"
            >
                @foreach($managers as $manager)
                    <option value="{{ $manager->id }}" @selected(old('project_manager_id', $project->project_manager_id ?? '') == $manager->id)>
                        {{ $manager->full_name ?? $manager->name ?? 'Unknown' }}
                    </option>
                @endforeach
            </x-select>

            {{-- Project Code --}}
            <x-input
                name="project_code"
                label="Project Code"
                :value="old('project_code', $project->project_code ?? '')"
                required
                placeholder="e.g. PRJ-2026-001"
                class="col-span-1"
            />

            {{-- Project Name --}}
            <x-input
                name="name"
                label="Project Name"
                :value="old('name', $project->name ?? '')"
                required
                placeholder="e.g. Headquarters Construction"
                class="col-span-1"
            />

            {{-- Description --}}
            <x-textarea
                name="description"
                label="Description"
                :value="old('description', $project->description ?? '')"
                placeholder="Provide project scope and objectives..."
                rows="3"
                class="col-span-1 md:col-span-2"
            />

            {{-- Category --}}
            <x-input
                name="category"
                label="Category"
                :value="old('category', $project->category ?? '')"
                placeholder="e.g. Construction"
                class="col-span-1"
            />

            {{-- Priority --}}
            <x-select
                label="Priority"
                name="priority"
                required
                class="col-span-1"
            >
                @foreach(['Low', 'Medium', 'High', 'Critical'] as $priority)
                    <option value="{{ $priority }}" @selected(old('priority', $project->priority ?? 'Medium') == $priority)>{{ $priority }}</option>
                @endforeach
            </x-select>

            {{-- Status --}}
            <x-select
                label="Status"
                name="status"
                required
                class="col-span-1"
            >
                @foreach(['Planning', 'Pending', 'In Progress', 'On Hold', 'Completed', 'Cancelled', 'Closed'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $project->status ?? 'Planning') == $status)>{{ $status }}</option>
                @endforeach
            </x-select>

            {{-- Progress --}}
            <x-input
                type="number"
                name="progress"
                label="Progress (%)"
                :value="old('progress', $project->progress ?? 0)"
                min="0" max="100"
                class="col-span-1"
            />
            
            {{-- Divider --}}
            <div class="col-span-1 md:col-span-2 pt-4">
                <h3 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-2 tracking-tight">Financials & Dates</h3>
            </div>

            {{-- Currency --}}
            <div class="col-span-1 md:col-span-2">
                <div class="md:w-1/2 md:pr-3">
                    <x-select
                        label="Currency"
                        name="currency"
                        required
                    >
                        @foreach(['RWF', 'USD', 'EUR', 'GBP', 'AED', 'SAR'] as $currency)
                            <option value="{{ $currency }}" @selected(old('currency', $project->currency ?? 'RWF') == $currency)>{{ $currency }}</option>
                        @endforeach
                    </x-select>
                </div>
            </div>

            {{-- Dates --}}
            <x-input
                type="date"
                name="start_date"
                label="Start Date"
                :value="old('start_date', optional($project ?? null)->start_date?->format('Y-m-d') ?? '')"
                required
                class="col-span-1"
            />
            
            <x-input
                type="date"
                name="planned_end_date"
                label="Planned End Date"
                :value="old('planned_end_date', optional($project ?? null)->planned_end_date?->format('Y-m-d') ?? '')"
                class="col-span-1"
            />

            {{-- Budgets --}}
            <x-input
                type="number"
                step="0.01"
                name="estimated_budget"
                label="Estimated Budget"
                :value="old('estimated_budget', $project->estimated_budget ?? '')"
                class="col-span-1"
            />
            
            <x-input
                type="number"
                step="0.01"
                name="estimated_cost"
                label="Estimated Cost"
                :value="old('estimated_cost', $project->estimated_cost ?? '')"
                class="col-span-1"
            />
            
            @if($project)
                <x-input
                    type="date"
                    name="actual_end_date"
                    label="Actual End Date"
                    :value="old('actual_end_date', optional($project)->actual_end_date?->format('Y-m-d') ?? '')"
                    class="col-span-1"
                />
                
                <div class="col-span-1 hidden md:block"></div>

                <x-input
                    type="number"
                    step="0.01"
                    name="actual_budget"
                    label="Actual Budget"
                    :value="old('actual_budget', $project->actual_budget ?? '')"
                    class="col-span-1"
                />

                <x-input
                    type="number"
                    step="0.01"
                    name="actual_cost"
                    label="Actual Cost"
                    :value="old('actual_cost', $project->actual_cost ?? '')"
                    class="col-span-1"
                />
            @endif
            
            {{-- Divider --}}
            <div class="col-span-1 md:col-span-2 pt-4">
                <h3 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-2 tracking-tight">Additional Information</h3>
            </div>
            
            {{-- References --}}
            <x-input
                name="contract_number"
                label="Contract Number"
                :value="old('contract_number', $project->contract_number ?? '')"
                class="col-span-1"
            />
            
            <x-input
                name="reference_number"
                label="Reference Number"
                :value="old('reference_number', $project->reference_number ?? '')"
                class="col-span-1"
            />
            
            <x-input
                name="location"
                label="Location"
                :value="old('location', $project->location ?? '')"
                class="col-span-1"
            />

            {{-- Notes --}}
            <x-textarea
                name="notes"
                label="Notes"
                :value="old('notes', $project->notes ?? '')"
                placeholder="Additional notes or special requirements..."
                rows="3"
                class="col-span-1 md:col-span-2"
            />
        </div>
    </div>

    <div class="bg-gray-50/60 border-t border-gray-100 px-6 py-4 flex items-center justify-end gap-3">
        <a href="{{ route('admin.projects.index') }}" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-xs">
            Cancel
        </a>
        <button type="submit" form="project-form" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-xs transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none hover:shadow-md">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ $project ? 'Update Project' : 'Create Project' }}
        </button>
    </div>
</div>
