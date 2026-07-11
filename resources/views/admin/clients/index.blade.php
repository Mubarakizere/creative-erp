<x-layouts.admin title="Clients">
    {{-- Breadcrumbs --}}
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Clients'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Clients</h1>
                <p class="mt-1 text-sm text-gray-500">Manage all clients in the system.</p>
            </div>
            @can('create', App\Models\Client::class)
                <x-button type="primary" href="{{ route('admin.clients.create') }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Create Client
                </x-button>
            @endcan
        </div>
    </div>

    {{-- Filters --}}
    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.clients.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            {{-- Search --}}
            <x-input
                name="search"
                placeholder="Search clients..."
                :value="request('search')"
                :icon="'<svg class=&quot;w-4 h-4&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; viewBox=&quot;0 0 24 24&quot;><path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z&quot;/></svg>'"
            />

            {{-- Type Filter --}}
            <x-select
                name="client_type"
                placeholder="All Types"
                :options="['Company' => 'Company', 'Individual' => 'Individual']"
                :selected="request('client_type')"
            />
            
            {{-- Company Filter --}}
            <x-select
                name="company_id"
                placeholder="All Companies"
                :options="$companies->pluck('name', 'id')->toArray()"
                :selected="request('company_id')"
            />
            
            {{-- Status Filter --}}
            <x-select
                name="status"
                placeholder="All Statuses"
                :options="['active' => 'Active', 'inactive' => 'Inactive']"
                :selected="request('status')"
            />

            {{-- Actions --}}
            <div class="flex items-end gap-2">
                <x-button type="primary" size="md">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filter
                </x-button>
                @if(request()->hasAny(['search', 'status', 'client_type', 'company_id']))
                    <x-button type="ghost" href="{{ route('admin.clients.index') }}" size="md">
                        Clear
                    </x-button>
                @endif
            </div>
        </form>
    </x-card>

    {{-- Clients Table --}}
    <x-table>
        <x-slot:head>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-12">Logo</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Client Name</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden sm:table-cell">Type</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden md:table-cell">Company / Branch</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden lg:table-cell">Contact</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider w-20">Actions</th>
        </x-slot:head>

        @forelse($clients as $client)
            <tr @class(['bg-red-50/30' => $client->trashed()])>
                {{-- Logo --}}
                <td class="px-4 py-3">
                    @if($client->logo_url)
                        <img src="{{ $client->logo_url }}" alt="{{ $client->display_name }}" class="w-10 h-10 rounded-lg object-cover border border-gray-200">
                    @else
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white text-sm font-bold shadow-sm">
                            {{ strtoupper(substr($client->display_name, 0, 2)) }}
                        </div>
                    @endif
                </td>

                {{-- Name --}}
                <td class="px-4 py-3">
                    <div>
                        <a href="{{ route('admin.clients.show', $client) }}" class="text-sm font-semibold text-gray-900 hover:text-blue-600 transition-colors">
                            {{ $client->display_name }}
                        </a>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $client->email ?? 'No email' }}</p>
                    </div>
                </td>

                {{-- Type --}}
                <td class="px-4 py-3 hidden sm:table-cell">
                    <span class="text-sm text-gray-600">{{ $client->client_type }}</span>
                </td>

                {{-- Company / Branch --}}
                <td class="px-4 py-3 hidden md:table-cell">
                    <div class="text-sm font-medium text-gray-900">{{ $client->company?->name }}</div>
                    <div class="text-xs text-gray-500">{{ $client->branch?->name }}</div>
                </td>

                {{-- Contact --}}
                <td class="px-4 py-3 hidden lg:table-cell">
                    <div class="text-sm text-gray-600">{{ $client->phone }}</div>
                </td>

                {{-- Status --}}
                <td class="px-4 py-3">
                    @php
                        $statusType = match($client->status) {
                            'active' => 'success',
                            'inactive' => 'warning',
                            default => 'default',
                        };
                    @endphp
                    <x-badge :type="$statusType">{{ ucfirst($client->status) }}</x-badge>
                    @if($client->trashed())
                        <x-badge type="danger" class="ml-1">Deleted</x-badge>
                    @endif
                </td>

                {{-- Actions --}}
                <td class="px-4 py-3 text-right">
                    <div x-data="{ open: false }" class="relative inline-block text-left">
                        <button @click="open = !open" @click.outside="open = false" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                            </svg>
                        </button>

                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 z-10 mt-2 w-48 rounded-xl bg-white shadow-lg ring-1 ring-black/5 py-1"
                             style="display: none;">

                            @can('view', $client)
                                <a href="{{ route('admin.clients.show', $client) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    View
                                </a>
                            @endcan

                            @if(!$client->trashed())
                                @can('update', $client)
                                    <a href="{{ route('admin.clients.edit', $client) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </a>
                                @endcan

                                @if($client->status !== 'active')
                                    @can('activate', $client)
                                        <form method="POST" action="{{ route('admin.clients.activate', $client) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-emerald-700 hover:bg-emerald-50 transition-colors">
                                                <svg class="w-4 h-4 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Activate
                                            </button>
                                        </form>
                                    @endcan
                                @endif

                                @if($client->status === 'active')
                                    @can('deactivate', $client)
                                        <form method="POST" action="{{ route('admin.clients.deactivate', $client) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-amber-700 hover:bg-amber-50 transition-colors">
                                                <svg class="w-4 h-4 mr-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                </svg>
                                                Deactivate
                                            </button>
                                        </form>
                                    @endcan
                                @endif

                                <div class="border-t border-gray-100 my-1"></div>

                                @can('delete', $client)
                                    <button @click="open = false; $dispatch('open-modal', 'delete-client-{{ $client->id }}')"
                                            class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4 mr-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Delete
                                    </button>
                                @endcan
                            @else
                                @can('restore', $client)
                                    <form method="POST" action="{{ route('admin.clients.restore', $client) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-blue-700 hover:bg-blue-50 transition-colors">
                                            <svg class="w-4 h-4 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                            Restore
                                        </button>
                                    </form>
                                @endcan
                            @endif
                        </div>
                    </div>
                </td>
            </tr>

            {{-- Delete Modal --}}
            @if(!$client->trashed())
                <x-modal id="delete-client-{{ $client->id }}" maxWidth="md">
                    <x-slot:header>Delete Client</x-slot:header>

                    <div class="text-center py-4">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete "{{ $client->display_name }}"?</h3>
                        <p class="text-sm text-gray-500">This action will soft-delete the client. You can restore it later from the deleted items.</p>
                    </div>

                    <x-slot:footer>
                        <x-button type="ghost" @click="open = false">Cancel</x-button>
                        <form method="POST" action="{{ route('admin.clients.destroy', $client) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <x-button type="danger" submit>Delete Client</x-button>
                        </form>
                    </x-slot:footer>
                </x-modal>
            @endif
        @empty
            <tr>
                <td colspan="7" class="px-4 py-12 text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <p class="text-gray-500 text-sm font-medium">No clients found</p>
                        <p class="text-gray-400 text-xs mt-1">Create your first client to get started.</p>
                        @can('create', App\Models\Client::class)
                            <x-button type="primary" href="{{ route('admin.clients.create') }}" class="mt-4" size="sm">
                                Create Client
                            </x-button>
                        @endcan
                    </div>
                </td>
            </tr>
        @endforelse

        <x-slot:pagination>
            {{ $clients->links('components.pagination') }}
        </x-slot:pagination>
    </x-table>
</x-layouts.admin>
