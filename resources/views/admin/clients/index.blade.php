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
    @can('viewAny', App\Models\Client::class)
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Clients</h1>
            <p class="mt-1 text-sm text-gray-500 font-medium">Manage all clients in the system.</p>
        </div>
        <div class="flex items-center gap-3">
            @can('create', App\Models\Client::class)
                <a href="{{ route('admin.clients.create') }}" class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all hover:shadow-md focus:ring-2 focus:ring-blue-500 focus:outline-none shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Create Client
                </a>
            @endcan
        </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-gray-200/60 shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.clients.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="search" placeholder="Search clients..." value="{{ request('search') }}" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors shadow-sm">
            </div>

            <x-select name="client_type" placeholder="All Types" :options="['Company' => 'Company', 'Individual' => 'Individual']" :selected="request('client_type')" />
            <x-select name="company_id" placeholder="All Companies" :options="$companies->pluck('name', 'id')->toArray()" :selected="request('company_id')" />
            <x-select name="status" placeholder="All Statuses" :options="['active' => 'Active', 'inactive' => 'Inactive']" :selected="request('status')" />

            <div class="flex items-end gap-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors shadow-sm border border-gray-200 w-full justify-center sm:w-auto">
                    <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'client_type', 'company_id']))
                    <a href="{{ route('admin.clients.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-900 bg-white border border-transparent hover:border-gray-300 rounded-xl transition-colors shrink-0">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200/60">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-16">Logo</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Client Name</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 hidden sm:table-cell">Type</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 hidden md:table-cell">Company / Branch</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 hidden lg:table-cell">Contact</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Status</th>
                        <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-24">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">

        @forelse($clients as $client)
                        <tr class="hover:bg-blue-50/30 transition-colors group {{ $client->trashed() ? 'bg-red-50/30' : '' }}">
                            <td class="px-6 py-4">
                                @if($client->logo_url)
                                    <img src="{{ $client->logo_url }}" alt="{{ $client->display_name }}" class="w-10 h-10 rounded-xl object-cover border border-gray-200 shadow-sm">
                                @else
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white text-sm font-bold shadow-sm">
                                        {{ strtoupper(substr($client->display_name, 0, 2)) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <a href="{{ route('admin.clients.show', $client) }}" class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                                        {{ $client->display_name }}
                                    </a>
                                    <p class="text-xs text-gray-500 mt-0.5 font-medium">{{ $client->email ?? 'No email' }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 hidden sm:table-cell">
                                <span class="text-sm text-gray-600 font-medium">{{ $client->client_type }}</span>
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell">
                                <div class="text-sm font-medium text-gray-900">{{ $client->company?->name }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $client->branch?->name }}</div>
                            </td>
                            <td class="px-6 py-4 hidden lg:table-cell">
                                <div class="text-sm text-gray-600 font-medium">{{ $client->phone }}</div>
                            </td>
                            <td class="px-6 py-4">
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
                            <td class="px-6 py-4 text-right">
                                <x-action-dropdown>
                                    @can('view', $client)
                                        <x-action-dropdown-item href="{{ route('admin.clients.show', $client) }}" icon="view">
                                            View Details
                                        </x-action-dropdown-item>
                                    @endcan

                                    @if(!$client->trashed())
                                        @can('update', $client)
                                            <x-action-dropdown-item href="{{ route('admin.clients.edit', $client) }}" icon="edit">
                                                Edit Client
                                            </x-action-dropdown-item>
                                        @endcan

                                        @if($client->status !== 'active')
                                            @can('activate', $client)
                                                <form method="POST" action="{{ route('admin.clients.activate', $client) }}" id="activate-client-form-{{ $client->id }}">
                                                    @csrf
                                                    @method('PATCH')
                                                </form>
                                                <x-action-dropdown-item onclick="document.getElementById('activate-client-form-{{ $client->id }}').submit()">
                                                    Activate
                                                </x-action-dropdown-item>
                                            @endcan
                                        @endif

                                        @if($client->status === 'active')
                                            @can('deactivate', $client)
                                                <form method="POST" action="{{ route('admin.clients.deactivate', $client) }}" id="deactivate-client-form-{{ $client->id }}">
                                                    @csrf
                                                    @method('PATCH')
                                                </form>
                                                <x-action-dropdown-item onclick="document.getElementById('deactivate-client-form-{{ $client->id }}').submit()">
                                                    Deactivate
                                                </x-action-dropdown-item>
                                            @endcan
                                        @endif

                                        @can('delete', $client)
                                            <x-action-dropdown-item @click="$dispatch('open-modal', 'delete-client-{{ $client->id }}')" icon="delete" variant="danger">
                                                Delete Client
                                            </x-action-dropdown-item>
                                        @endcan
                                    @else
                                        @can('restore', $client)
                                            <form method="POST" action="{{ route('admin.clients.restore', $client) }}" id="restore-client-form-{{ $client->id }}">
                                                @csrf
                                                @method('PATCH')
                                            </form>
                                            <x-action-dropdown-item onclick="document.getElementById('restore-client-form-{{ $client->id }}').submit()">
                                                Restore
                                            </x-action-dropdown-item>
                                        @endcan
                                    @endif
                                </x-action-dropdown>
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
                    <td colspan="7" class="px-6 py-16 text-center">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">No clients found</h3>
                        <p class="text-sm text-gray-500 font-medium">Create your first client to get started.</p>
                        @can('create', App\Models\Client::class)
                            <a href="{{ route('admin.clients.create') }}" class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                Create Client
                            </a>
                        @endcan
                    </td>
                </tr>
            @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($clients, 'hasPages') && $clients->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
            {{ $clients->links('components.pagination') }}
        </div>
        @endif
    </div>
    @else
    <div class="text-center py-16 bg-white rounded-2xl border border-gray-200/60 shadow-sm">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4 border border-red-200">
            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Access Denied</h3>
        <p class="text-sm text-gray-500 font-medium">You do not have permission to view clients.</p>
    </div>
    @endcan
</x-layouts.admin>
