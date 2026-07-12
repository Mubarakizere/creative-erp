<x-card>
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
        <div class="col-span-1">
            <label class="block text-sm font-medium text-gray-700 mb-1">Company <span class="text-red-500">*</span></label>
            <select name="company_id" x-model="companyId" @change="updateDropdowns()" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                <option value="">Select Company</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                @endforeach
            </select>
            @error('company_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Branch --}}
        <div class="col-span-1">
            <label class="block text-sm font-medium text-gray-700 mb-1">Branch <span class="text-red-500">*</span></label>
            <select name="branch_id" x-model="branchId" @change="updateDropdowns()" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required :disabled="availableBranches.length === 0">
                <option value="">Select Branch</option>
                <template x-for="branch in availableBranches" :key="branch.id">
                    <option :value="branch.id" x-text="branch.name" :selected="branch.id == branchId"></option>
                </template>
            </select>
            @error('branch_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Client --}}
        <div class="col-span-1">
            <label class="block text-sm font-medium text-gray-700 mb-1">Client <span class="text-red-500">*</span></label>
            <select name="client_id" x-model="clientId" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required :disabled="availableClients.length === 0">
                <option value="">Select Client</option>
                <template x-for="client in availableClients" :key="client.id">
                    <option :value="client.id" x-text="client.display_name" :selected="client.id == clientId"></option>
                </template>
            </select>
            @error('client_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Project Manager --}}
        <div class="col-span-1">
            <label class="block text-sm font-medium text-gray-700 mb-1">Project Manager <span class="text-red-500">*</span></label>
            <select name="project_manager_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                <option value="">Select Manager</option>
                @foreach($managers as $manager)
                    <option value="{{ $manager->id }}" {{ old('project_manager_id', $project->project_manager_id ?? '') == $manager->id ? 'selected' : '' }}>
                        {{ $manager->name }}
                    </option>
                @endforeach
            </select>
            @error('project_manager_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

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
        <div class="col-span-1 md:col-span-2">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea id="description" name="description" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $project->description ?? '') }}</textarea>
            @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Category --}}
        <x-input
            name="category"
            label="Category"
            :value="old('category', $project->category ?? '')"
            placeholder="e.g. Construction"
            class="col-span-1"
        />

        {{-- Priority --}}
        <div class="col-span-1">
            <label class="block text-sm font-medium text-gray-700 mb-1">Priority <span class="text-red-500">*</span></label>
            <select name="priority" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                @foreach(['Low', 'Medium', 'High', 'Critical'] as $priority)
                    <option value="{{ $priority }}" {{ old('priority', $project->priority ?? 'Medium') == $priority ? 'selected' : '' }}>{{ $priority }}</option>
                @endforeach
            </select>
            @error('priority') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Status --}}
        <div class="col-span-1">
            <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
            <select name="status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                @foreach(['Planning', 'Pending', 'In Progress', 'On Hold', 'Completed', 'Cancelled', 'Closed'] as $status)
                    <option value="{{ $status }}" {{ old('status', $project->status ?? 'Planning') == $status ? 'selected' : '' }}>{{ $status }}</option>
                @endforeach
            </select>
            @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

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
        <div class="col-span-1 md:col-span-2 py-2">
            <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Financials & Dates</h3>
        </div>

        {{-- Currency --}}
        <div class="col-span-1">
            <label class="block text-sm font-medium text-gray-700 mb-1">Currency <span class="text-red-500">*</span></label>
            <select name="currency" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                @foreach(['RWF', 'USD', 'EUR', 'GBP', 'AED', 'SAR'] as $currency)
                    <option value="{{ $currency }}" {{ old('currency', $project->currency ?? 'RWF') == $currency ? 'selected' : '' }}>{{ $currency }}</option>
                @endforeach
            </select>
            @error('currency') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
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
        
        @if($project)
            <x-input
                type="date"
                name="actual_end_date"
                label="Actual End Date"
                :value="old('actual_end_date', optional($project)->actual_end_date?->format('Y-m-d') ?? '')"
                class="col-span-1"
            />
        @endif

        {{-- Budgets --}}
        <x-input
            type="number"
            step="0.01"
            name="estimated_budget"
            label="Estimated Budget"
            :value="old('estimated_budget', $project->estimated_budget ?? '')"
            class="col-span-1"
        />
        
        @if($project)
            <x-input
                type="number"
                step="0.01"
                name="actual_budget"
                label="Actual Budget"
                :value="old('actual_budget', $project->actual_budget ?? '')"
                class="col-span-1"
            />
        @endif

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
                type="number"
                step="0.01"
                name="actual_cost"
                label="Actual Cost"
                :value="old('actual_cost', $project->actual_cost ?? '')"
                class="col-span-1"
            />
        @endif
        
        {{-- Divider --}}
        <div class="col-span-1 md:col-span-2 py-2">
            <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Additional Information</h3>
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
        <div class="col-span-1 md:col-span-2">
            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
            <textarea id="notes" name="notes" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes', $project->notes ?? '') }}</textarea>
            @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="mt-6 flex items-center justify-end gap-3">
        <x-button type="ghost" href="{{ route('admin.projects.index') }}">Cancel</x-button>
        <x-button type="primary" submit>{{ $project ? 'Update Project' : 'Create Project' }}</x-button>
    </div>
</x-card>
