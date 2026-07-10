<x-layouts.admin title="{{ $department->name }}">
    {{-- Breadcrumbs --}}
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Departments', 'url' => route('admin.departments.index')],
                ['label' => $department->name],
            ];
        @endphp
    </x-slot:breadcrumbs>

    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white text-xl font-bold shadow-lg shadow-purple-500/20">
                    {{ strtoupper(substr($department->name, 0, 2)) }}
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $department->name }}</h1>
                        @php
                            $statusType = match($department->status) {
                                'active' => 'success',
                                'inactive' => 'warning',
                                default => 'default',
                            };
                        @endphp
                        <x-badge :type="$statusType" size="lg">{{ ucfirst($department->status) }}</x-badge>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">{{ $department->company->name ?? '' }} &middot; {{ $department->branch->name ?? '' }} &middot; {{ $department->code }}</p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2 flex-wrap">
                @can('update', $department)
                    <x-button type="primary" href="{{ route('admin.departments.edit', $department) }}" size="sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </x-button>
                @endcan

                @if($department->status !== 'active')
                    @can('activate', $department)
                        <form method="POST" action="{{ route('admin.departments.activate', $department) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <x-button type="success" size="sm" submit>Activate</x-button>
                        </form>
                    @endcan
                @else
                    @can('deactivate', $department)
                        <form method="POST" action="{{ route('admin.departments.deactivate', $department) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <x-button type="warning" size="sm" submit>Deactivate</x-button>
                        </form>
                    @endcan
                @endif

                <x-button type="ghost" href="{{ route('admin.departments.index') }}" size="sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back
                </x-button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column: Main Info --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Contact Information --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Contact Information</h3>
                </x-slot:header>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Email</label>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($department->email)
                                <a href="mailto:{{ $department->email }}" class="text-blue-600 hover:text-blue-700">{{ $department->email }}</a>
                            @else
                                —
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $department->phone ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Manager</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $department->manager_name ?? '—' }}</p>
                    </div>
                </div>
            </x-card>

            {{-- Description --}}
            @if($department->description)
                <x-card>
                    <x-slot:header>
                        <h3 class="text-lg font-semibold text-gray-900">Description</h3>
                    </x-slot:header>

                    <p class="text-sm text-gray-600 whitespace-pre-line">{{ $department->description }}</p>
                </x-card>
            @endif
        </div>

        {{-- Right Column: Summary & Meta --}}
        <div class="space-y-6">
            {{-- Company --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Company</h3>
                </x-slot:header>

                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-sm font-bold shadow-sm">
                        {{ strtoupper(substr($department->company->name ?? '', 0, 2)) }}
                    </div>
                    <div>
                        <a href="{{ route('admin.companies.show', $department->company) }}" class="text-sm font-semibold text-gray-900 hover:text-blue-600 transition-colors">
                            {{ $department->company->name ?? '—' }}
                        </a>
                        @if($department->company?->email)
                            <p class="text-xs text-gray-500">{{ $department->company->email }}</p>
                        @endif
                    </div>
                </div>
            </x-card>

            {{-- Branch --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Branch</h3>
                </x-slot:header>

                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white text-sm font-bold shadow-sm">
                        {{ strtoupper(substr($department->branch->name ?? '', 0, 2)) }}
                    </div>
                    <div>
                        <a href="{{ route('admin.branches.show', $department->branch) }}" class="text-sm font-semibold text-gray-900 hover:text-blue-600 transition-colors">
                            {{ $department->branch->name ?? '—' }}
                        </a>
                        @if($department->branch?->city)
                            <p class="text-xs text-gray-500">{{ $department->branch->city }}{{ $department->branch->country ? ', ' . $department->branch->country : '' }}</p>
                        @endif
                    </div>
                </div>
            </x-card>

            {{-- Department Details --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Details</h3>
                </x-slot:header>

                <div class="space-y-3">
                    <div class="flex items-center justify-between py-1.5">
                        <span class="text-sm text-gray-500">Code</span>
                        <span class="text-sm font-mono font-semibold text-gray-900">{{ $department->code }}</span>
                    </div>
                    <div class="flex items-center justify-between py-1.5 border-t border-gray-100">
                        <span class="text-sm text-gray-500">Status</span>
                        <x-badge :type="$statusType">{{ ucfirst($department->status) }}</x-badge>
                    </div>
                </div>
            </x-card>

            {{-- Metadata --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Metadata</h3>
                </x-slot:header>

                <div class="space-y-3">
                    <div class="flex items-center justify-between py-1.5">
                        <span class="text-sm text-gray-500">UUID</span>
                        <span class="text-xs font-mono text-gray-600">{{ Str::limit($department->uuid, 18) }}</span>
                    </div>
                    <div class="flex items-center justify-between py-1.5 border-t border-gray-100">
                        <span class="text-sm text-gray-500">Created</span>
                        <span class="text-sm text-gray-600">{{ $department->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    <div class="flex items-center justify-between py-1.5 border-t border-gray-100">
                        <span class="text-sm text-gray-500">Updated</span>
                        <span class="text-sm text-gray-600">{{ $department->updated_at->format('M d, Y h:i A') }}</span>
                    </div>
                    @if($department->creator)
                        <div class="flex items-center justify-between py-1.5 border-t border-gray-100">
                            <span class="text-sm text-gray-500">Created By</span>
                            <span class="text-sm text-gray-600">{{ $department->creator->full_name }}</span>
                        </div>
                    @endif
                    @if($department->updater)
                        <div class="flex items-center justify-between py-1.5 border-t border-gray-100">
                            <span class="text-sm text-gray-500">Updated By</span>
                            <span class="text-sm text-gray-600">{{ $department->updater->full_name }}</span>
                        </div>
                    @endif
                </div>
            </x-card>
        </div>
    </div>
</x-layouts.admin>
