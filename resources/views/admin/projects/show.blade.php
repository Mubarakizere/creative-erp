<x-layouts.admin title="Project Profile">
    {{-- Breadcrumbs --}}
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Projects', 'url' => route('admin.projects.index')],
                ['label' => $project->name],
            ];
        @endphp
    </x-slot:breadcrumbs>

    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $project->name }}</h1>
                <p class="mt-1 text-sm text-gray-500">Code: {{ $project->project_code }}</p>
            </div>
            <div class="flex gap-2">
                @can('view', $project)
                    <x-button type="ghost" href="{{ route('admin.projects.timeline', $project) }}" size="sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Timeline
                    </x-button>
                @endcan
                
                @if(!$project->trashed())
                    @can('update', $project)
                        <x-button type="primary" href="{{ route('admin.projects.edit', $project) }}" size="sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </x-button>
                    @endcan
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Project Information --}}
            <x-card>
                <h3 class="text-lg font-bold text-gray-900 border-b pb-3 mb-4">Project Information</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6">
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase">Category</span>
                        <p class="mt-1 text-sm text-gray-900">{{ $project->category ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase">Priority</span>
                        <p class="mt-1 text-sm text-gray-900">
                            <x-badge :type="match($project->priority) { 'Critical' => 'danger', 'High' => 'warning', 'Low' => 'default', default => 'primary' }">
                                {{ $project->priority }}
                            </x-badge>
                        </p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase">Status</span>
                        <p class="mt-1 text-sm text-gray-900">
                            <x-badge :type="match($project->status) { 'Completed', 'Closed' => 'success', 'Cancelled' => 'danger', 'In Progress' => 'primary', 'Pending' => 'warning', default => 'default' }">
                                {{ $project->status }}
                            </x-badge>
                        </p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase">Progress</span>
                        <div class="mt-1 flex items-center gap-2">
                            <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden max-w-[150px]">
                                <div class="h-full bg-blue-600 rounded-full" style="width: {{ $project->progress }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-900">{{ $project->progress }}%</span>
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="text-xs font-semibold text-gray-500 uppercase">Description</span>
                        <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $project->description ?: 'No description provided.' }}</p>
                    </div>
                </div>
            </x-card>
            
            {{-- Financials --}}
            <x-card>
                <h3 class="text-lg font-bold text-gray-900 border-b pb-3 mb-4">Financials</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6">
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase">Estimated Budget</span>
                        <p class="mt-1 text-lg font-bold text-gray-900">{{ $project->estimated_budget ? format_currency($project->estimated_budget, $project->currency) : 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase">Actual Budget</span>
                        <p class="mt-1 text-lg font-bold text-gray-900">{{ $project->actual_budget ? format_currency($project->actual_budget, $project->currency) : 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase">Estimated Cost</span>
                        <p class="mt-1 text-lg font-bold text-gray-900">{{ $project->estimated_cost ? format_currency($project->estimated_cost, $project->currency) : 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase">Actual Cost</span>
                        <p class="mt-1 text-lg font-bold text-gray-900">{{ $project->actual_cost ? format_currency($project->actual_cost, $project->currency) : 'N/A' }}</p>
                    </div>
                </div>
            </x-card>
            
            {{-- Additional Details --}}
            <x-card>
                <h3 class="text-lg font-bold text-gray-900 border-b pb-3 mb-4">Additional Details</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6">
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase">Contract Number</span>
                        <p class="mt-1 text-sm text-gray-900">{{ $project->contract_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase">Reference Number</span>
                        <p class="mt-1 text-sm text-gray-900">{{ $project->reference_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase">Location</span>
                        <p class="mt-1 text-sm text-gray-900">{{ $project->location ?? 'N/A' }}</p>
                    </div>
                </div>
                
                <div class="mt-4">
                    <span class="text-xs font-semibold text-gray-500 uppercase">Notes</span>
                    <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap bg-gray-50 p-3 rounded-lg">{{ $project->notes ?: 'No notes provided.' }}</p>
                </div>
            </x-card>
        </div>
        
        <div class="space-y-6">
            {{-- Organization & People --}}
            <x-card>
                <h3 class="text-lg font-bold text-gray-900 border-b pb-3 mb-4">Organization & People</h3>
                
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-gray-500 uppercase">Company / Branch</span>
                            <span class="block text-sm font-medium text-gray-900">{{ $project->company?->name }}</span>
                            <span class="block text-xs text-gray-500">{{ $project->branch?->name }}</span>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-gray-500 uppercase">Client</span>
                            <span class="block text-sm font-medium text-gray-900">{{ $project->client?->display_name }}</span>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-gray-500 uppercase">Project Manager</span>
                            <span class="block text-sm font-medium text-gray-900">{{ $project->manager?->name }}</span>
                        </div>
                    </li>
                </ul>
            </x-card>

            {{-- Dates --}}
            <x-card>
                <h3 class="text-lg font-bold text-gray-900 border-b pb-3 mb-4">Dates</h3>
                <ul class="space-y-4">
                    <li>
                        <span class="block text-xs font-semibold text-gray-500 uppercase">Start Date</span>
                        <span class="block text-sm text-gray-900">{{ $project->start_date?->format('F j, Y') ?? 'N/A' }}</span>
                    </li>
                    <li>
                        <span class="block text-xs font-semibold text-gray-500 uppercase">Planned End Date</span>
                        <span class="block text-sm text-gray-900">{{ $project->planned_end_date?->format('F j, Y') ?? 'N/A' }}</span>
                    </li>
                    <li>
                        <span class="block text-xs font-semibold text-gray-500 uppercase">Actual End Date</span>
                        <span class="block text-sm text-gray-900">{{ $project->actual_end_date?->format('F j, Y') ?? 'N/A' }}</span>
                    </li>
                </ul>
            </x-card>
            
            {{-- System Info --}}
            <x-card>
                <h3 class="text-lg font-bold text-gray-900 border-b pb-3 mb-4">System Info</h3>
                <ul class="space-y-3">
                    <li class="text-sm">
                        <span class="font-semibold text-gray-500">Created At:</span>
                        <span class="text-gray-900 ml-1">{{ $project->created_at?->format('Y-m-d H:i') }}</span>
                    </li>
                    <li class="text-sm">
                        <span class="font-semibold text-gray-500">Created By:</span>
                        <span class="text-gray-900 ml-1">{{ $project->creator?->name ?? 'System' }}</span>
                    </li>
                    <li class="text-sm">
                        <span class="font-semibold text-gray-500">Last Updated:</span>
                        <span class="text-gray-900 ml-1">{{ $project->updated_at?->format('Y-m-d H:i') }}</span>
                    </li>
                    <li class="text-sm">
                        <span class="font-semibold text-gray-500">Updated By:</span>
                        <span class="text-gray-900 ml-1">{{ $project->updater?->name ?? 'System' }}</span>
                    </li>
                </ul>
            </x-card>
        </div>
    </div>
</x-layouts.admin>
