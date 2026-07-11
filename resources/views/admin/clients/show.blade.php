<x-layouts.admin title="{{ $client->display_name }}">
    {{-- Breadcrumbs --}}
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Clients', 'url' => route('admin.clients.index')],
                ['label' => $client->display_name],
            ];
        @endphp
    </x-slot:breadcrumbs>

    {{-- Page Header --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            @if($client->logo_url)
                <img src="{{ $client->logo_url }}" alt="{{ $client->display_name }}" class="w-16 h-16 rounded-xl object-cover border border-gray-200 shadow-sm">
            @else
                <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white text-xl font-bold shadow-sm">
                    {{ strtoupper(substr($client->display_name, 0, 2)) }}
                </div>
            @endif
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                    {{ $client->display_name }}
                    @php
                        $statusType = match($client->status) {
                            'active' => 'success',
                            'inactive' => 'warning',
                            default => 'default',
                        };
                    @endphp
                    <x-badge :type="$statusType">{{ ucfirst($client->status) }}</x-badge>
                    @if($client->trashed())
                        <x-badge type="danger">Deleted</x-badge>
                    @endif
                </h1>
                <p class="mt-1 text-sm text-gray-500 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    {{ $client->company?->name }} 
                    <span class="text-gray-300">•</span>
                    {{ $client->branch?->name }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if(!$client->trashed())
                @can('update', $client)
                    <x-button type="primary" href="{{ route('admin.clients.edit', $client) }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </x-button>
                @endcan
            @else
                @can('restore', $client)
                    <form method="POST" action="{{ route('admin.clients.restore', $client) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <x-button type="primary" submit>
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Restore Client
                        </x-button>
                    </form>
                @endcan
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column: Details --}}
        <div class="lg:col-span-2 space-y-6">
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Client Information</h3>
                </x-slot:header>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <span class="block text-sm font-medium text-gray-500">Client Type</span>
                        <span class="mt-1 block text-sm text-gray-900">{{ $client->client_type }}</span>
                    </div>
                    
                    @if($client->client_type === 'Company')
                        <div>
                            <span class="block text-sm font-medium text-gray-500">Company Name</span>
                            <span class="mt-1 block text-sm text-gray-900">{{ $client->company_name }}</span>
                        </div>
                    @else
                        <div>
                            <span class="block text-sm font-medium text-gray-500">First Name</span>
                            <span class="mt-1 block text-sm text-gray-900">{{ $client->first_name }}</span>
                        </div>
                        <div>
                            <span class="block text-sm font-medium text-gray-500">Last Name</span>
                            <span class="mt-1 block text-sm text-gray-900">{{ $client->last_name }}</span>
                        </div>
                    @endif

                    <div>
                        <span class="block text-sm font-medium text-gray-500">Email Address</span>
                        <span class="mt-1 block text-sm text-gray-900">
                            @if($client->email)
                                <a href="mailto:{{ $client->email }}" class="text-blue-600 hover:underline">{{ $client->email }}</a>
                            @else
                                <span class="text-gray-400">Not provided</span>
                            @endif
                        </span>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-500">Phone</span>
                        <span class="mt-1 block text-sm text-gray-900">{{ $client->phone ?? 'Not provided' }}</span>
                    </div>
                    @if($client->alternate_phone)
                        <div>
                            <span class="block text-sm font-medium text-gray-500">Alternate Phone</span>
                            <span class="mt-1 block text-sm text-gray-900">{{ $client->alternate_phone }}</span>
                        </div>
                    @endif
                    @if($client->website)
                        <div>
                            <span class="block text-sm font-medium text-gray-500">Website</span>
                            <span class="mt-1 block text-sm text-gray-900">
                                <a href="{{ $client->website }}" target="_blank" class="text-blue-600 hover:underline">{{ $client->website }}</a>
                            </span>
                        </div>
                    @endif
                </div>
            </x-card>

            @if($client->client_type === 'Company')
                <x-card>
                    <x-slot:header>
                        <h3 class="text-lg font-semibold text-gray-900">Business Details</h3>
                    </x-slot:header>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <span class="block text-sm font-medium text-gray-500">Tax Number</span>
                            <span class="mt-1 block text-sm text-gray-900">{{ $client->tax_number ?? 'Not provided' }}</span>
                        </div>
                        <div>
                            <span class="block text-sm font-medium text-gray-500">Registration Number</span>
                            <span class="mt-1 block text-sm text-gray-900">{{ $client->registration_number ?? 'Not provided' }}</span>
                        </div>
                    </div>
                </x-card>
            @endif

            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Address</h3>
                </x-slot:header>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="md:col-span-2 lg:col-span-3">
                        <span class="block text-sm font-medium text-gray-500">Street Address</span>
                        <span class="mt-1 block text-sm text-gray-900">{{ $client->address ?? 'Not provided' }}</span>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-500">City</span>
                        <span class="mt-1 block text-sm text-gray-900">{{ $client->city ?? 'Not provided' }}</span>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-500">State / Province</span>
                        <span class="mt-1 block text-sm text-gray-900">{{ $client->state ?? 'Not provided' }}</span>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-500">Postal Code</span>
                        <span class="mt-1 block text-sm text-gray-900">{{ $client->postal_code ?? 'Not provided' }}</span>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-500">Country</span>
                        <span class="mt-1 block text-sm text-gray-900">{{ $client->country ?? 'Not provided' }}</span>
                    </div>
                </div>
            </x-card>

            @if($client->notes)
                <x-card>
                    <x-slot:header>
                        <h3 class="text-lg font-semibold text-gray-900">Notes</h3>
                    </x-slot:header>
                    <div class="prose prose-sm max-w-none text-gray-600">
                        {{ nl2br(e($client->notes)) }}
                    </div>
                </x-card>
            @endif
            
            {{-- Projects placeholder --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Projects</h3>
                </x-slot:header>
                <div class="text-center py-6 text-gray-500">
                    Projects integration will be available in Sprint 08.
                </div>
            </x-card>
        </div>

        {{-- Right Column: Meta & Actions --}}
        <div class="space-y-6">
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Record Information</h3>
                </x-slot:header>

                <div class="space-y-4">
                    <div>
                        <span class="block text-sm font-medium text-gray-500">Created By</span>
                        <span class="mt-1 block text-sm text-gray-900">
                            {{ $client->creator?->first_name }} {{ $client->creator?->last_name }}
                        </span>
                        <span class="block text-xs text-gray-400 mt-0.5">
                            {{ $client->created_at->format('M d, Y h:i A') }}
                        </span>
                    </div>
                    
                    @if($client->updater)
                        <div class="pt-4 border-t border-gray-100">
                            <span class="block text-sm font-medium text-gray-500">Last Updated By</span>
                            <span class="mt-1 block text-sm text-gray-900">
                                {{ $client->updater?->first_name }} {{ $client->updater?->last_name }}
                            </span>
                            <span class="block text-xs text-gray-400 mt-0.5">
                                {{ $client->updated_at->format('M d, Y h:i A') }}
                            </span>
                        </div>
                    @endif
                </div>
            </x-card>
        </div>
    </div>
</x-layouts.admin>
