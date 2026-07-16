<x-layouts.admin title="{{ $company->name }}">
    {{-- Breadcrumbs --}}
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Companies', 'url' => route('admin.companies.index')],
                ['label' => $company->name],
            ];
        @endphp
    </x-slot:breadcrumbs>

    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                @if($company->logo_url)
                    <img src="{{ $company->logo_url }}" alt="{{ $company->name }}" class="w-16 h-16 rounded-xl object-cover border border-gray-200 shadow-sm">
                @else
                    <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-xl font-bold shadow-lg shadow-blue-500/20">
                        {{ strtoupper(substr($company->name, 0, 2)) }}
                    </div>
                @endif
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $company->name }}</h1>
                        @php
                            $statusType = match($company->status) {
                                'active' => 'success',
                                'inactive' => 'warning',
                                'suspended' => 'danger',
                                default => 'default',
                            };
                        @endphp
                        <x-badge :type="$statusType" size="lg">{{ ucfirst($company->status) }}</x-badge>
                    </div>
                    @if($company->legal_name)
                        <p class="mt-1 text-sm text-gray-500">{{ $company->legal_name }}</p>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2 flex-wrap">
                @can('update', $company)
                    <x-button type="primary" href="{{ route('admin.companies.edit', $company) }}" size="sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </x-button>
                    <x-button type="outline" href="{{ route('admin.companies.settings', $company) }}" size="sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Settings
                    </x-button>
                @endcan

                @if($company->status !== 'active')
                    @can('activate', $company)
                        <form method="POST" action="{{ route('admin.companies.activate', $company) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <x-button type="success" size="sm" submit>Activate</x-button>
                        </form>
                    @endcan
                @else
                    @can('deactivate', $company)
                        <form method="POST" action="{{ route('admin.companies.deactivate', $company) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <x-button type="warning" size="sm" submit>Deactivate</x-button>
                        </form>
                    @endcan
                @endif

                <x-button type="ghost" href="{{ route('admin.companies.index') }}" size="sm">
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
                            <a href="mailto:{{ $company->email }}" class="text-blue-600 hover:text-blue-700">{{ $company->email }}</a>
                        </p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $company->phone ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Alternate Phone</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $company->alternate_phone ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Website</label>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($company->website)
                                <a href="{{ $company->website }}" target="_blank" class="text-blue-600 hover:text-blue-700 inline-flex items-center gap-1">
                                    {{ $company->website }}
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                            @else
                                —
                            @endif
                        </p>
                    </div>
                </div>
            </x-card>

            {{-- Address --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Address</h3>
                </x-slot:header>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Country</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $company->country ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">State / Province</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $company->state ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">City</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $company->city ?? '—' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Street Address</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $company->address ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Postal Code</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $company->postal_code ?? '—' }}</p>
                    </div>
                </div>
            </x-card>

            {{-- Business Information --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Business Information</h3>
                </x-slot:header>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Registration Number</label>
                        <p class="mt-1 text-sm text-gray-900 font-mono">{{ $company->registration_number ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Tax Number</label>
                        <p class="mt-1 text-sm text-gray-900 font-mono">{{ $company->tax_number ?? '—' }}</p>
                    </div>
                </div>
            </x-card>

            {{-- Notes --}}
            @if($company->notes)
                <x-card>
                    <x-slot:header>
                        <h3 class="text-lg font-semibold text-gray-900">Notes</h3>
                    </x-slot:header>

                    <p class="text-sm text-gray-600 whitespace-pre-line">{{ $company->notes }}</p>
                </x-card>
            @endif

            {{-- Documents --}}
            <div class="mt-6">
                @include('admin.documents.partials.document_tab', ['documentable' => $company])
            </div>
        </div>

        {{-- Right Column: Settings & Meta --}}
        <div class="space-y-6">
            {{-- Localization --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Localization</h3>
                </x-slot:header>

                <div class="space-y-4">
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">Currency</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $company->currency }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">Timezone</span>
                        <span class="text-sm font-medium text-gray-900">{{ $company->timezone }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-500">Language</span>
                        <span class="text-sm font-medium text-gray-900">{{ strtoupper($company->language) }}</span>
                    </div>
                </div>
            </x-card>

            {{-- Business Hours --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Business Hours</h3>
                </x-slot:header>

                <div class="space-y-4">
                    {{-- Working Days --}}
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Working Days</label>
                        <div class="mt-2 flex flex-wrap gap-1.5">
                            @php
                                $allDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                                $fullDays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                $workingDays = $company->working_days ?? [];
                            @endphp
                            @foreach($fullDays as $index => $day)
                                <span @class([
                                    'inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium',
                                    'bg-blue-100 text-blue-700' => in_array($day, $workingDays),
                                    'bg-gray-100 text-gray-400' => !in_array($day, $workingDays),
                                ])>
                                    {{ $allDays[$index] }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    {{-- Working Hours --}}
                    <div class="flex items-center justify-between py-2 border-t border-gray-100">
                        <span class="text-sm text-gray-500">Hours</span>
                        <span class="text-sm font-medium text-gray-900">
                            @if($company->working_hours_start && $company->working_hours_end)
                                {{ \Carbon\Carbon::parse($company->working_hours_start)->format('h:i A') }} – {{ \Carbon\Carbon::parse($company->working_hours_end)->format('h:i A') }}
                            @else
                                —
                            @endif
                        </span>
                    </div>
                </div>
            </x-card>

            {{-- Branding --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Branding</h3>
                </x-slot:header>

                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Logo</label>
                        <div class="mt-2">
                            @if($company->logo_url)
                                <img src="{{ $company->logo_url }}" alt="{{ $company->name }} Logo" class="w-24 h-24 rounded-xl object-cover border border-gray-200">
                            @else
                                <div class="w-24 h-24 rounded-xl bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center">
                                    <span class="text-xs text-gray-400">No logo</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Favicon</label>
                        <div class="mt-2">
                            @if($company->favicon_url)
                                <img src="{{ $company->favicon_url }}" alt="{{ $company->name }} Favicon" class="w-12 h-12 rounded-lg object-cover border border-gray-200">
                            @else
                                <div class="w-12 h-12 rounded-lg bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center">
                                    <span class="text-[10px] text-gray-400">None</span>
                                </div>
                            @endif
                        </div>
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
                        <span class="text-xs font-mono text-gray-600">{{ Str::limit($company->uuid, 18) }}</span>
                    </div>
                    <div class="flex items-center justify-between py-1.5 border-t border-gray-100">
                        <span class="text-sm text-gray-500">Slug</span>
                        <span class="text-sm font-mono text-gray-600">{{ $company->slug }}</span>
                    </div>
                    <div class="flex items-center justify-between py-1.5 border-t border-gray-100">
                        <span class="text-sm text-gray-500">Created</span>
                        <span class="text-sm text-gray-600">{{ $company->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    <div class="flex items-center justify-between py-1.5 border-t border-gray-100">
                        <span class="text-sm text-gray-500">Updated</span>
                        <span class="text-sm text-gray-600">{{ $company->updated_at->format('M d, Y h:i A') }}</span>
                    </div>
                    @if($company->creator)
                        <div class="flex items-center justify-between py-1.5 border-t border-gray-100">
                            <span class="text-sm text-gray-500">Created By</span>
                            <span class="text-sm text-gray-600">{{ $company->creator->full_name }}</span>
                        </div>
                    @endif
                    @if($company->updater)
                        <div class="flex items-center justify-between py-1.5 border-t border-gray-100">
                            <span class="text-sm text-gray-500">Updated By</span>
                            <span class="text-sm text-gray-600">{{ $company->updater->full_name }}</span>
                        </div>
                    @endif
                </div>
            </x-card>
        </div>
    </div>
</x-layouts.admin>
