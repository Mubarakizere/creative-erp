<x-layouts.admin title="Users">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Users']
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">System Users & Accounts</h1>
            <p class="mt-1 text-sm text-slate-500 font-medium">Manage user identity, department roles, and security access.</p>
        </div>
        @can('user.create')
            <x-button type="primary" href="{{ route('admin.users.create') }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create User
            </x-button>
        @endcan
    </div>

    {{-- Filter Bar --}}
    <x-card class="mb-6">
        <form action="{{ route('admin.users.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <x-input name="search" placeholder="Search user name or email..." :value="request('search')" />
            
            <x-select name="company_id" :options="$companies" :selected="request('company_id')" placeholder="All Companies" />
            
            <x-select name="role" :options="$roles" :selected="request('role')" placeholder="All Roles" />

            <x-select name="status" :options="['active' => 'Active', 'inactive' => 'Inactive', 'suspended' => 'Suspended', 'locked' => 'Locked', 'pending' => 'Pending']" :selected="request('status')" placeholder="All Statuses" />

            <div class="flex items-center gap-2">
                <x-button type="primary" submit class="w-full justify-center">Filter</x-button>
                <x-button type="ghost" href="{{ route('admin.users.index') }}" class="px-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </x-button>
            </div>
        </form>
    </x-card>

    <x-card :padding="false">
        <x-table>
            <x-slot:head>
                <th class="px-5 py-3.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">User Profile</th>
                <th class="px-5 py-3.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Assigned Roles</th>
                <th class="px-5 py-3.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Organization</th>
                <th class="px-5 py-3.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Status</th>
                <th class="px-5 py-3.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Last Login</th>
                <th class="px-5 py-3.5 text-right text-[11px] font-bold text-slate-400 uppercase tracking-widest">Actions</th>
            </x-slot:head>

            @forelse($users as $user)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="shrink-0 h-9 w-9">
                                @if($user->avatar)
                                    <img class="h-9 w-9 rounded-full object-cover shadow-xs border border-slate-200" src="{{ Storage::url($user->avatar) }}" alt="">
                                @else
                                    <div class="h-9 w-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-xs">
                                        {{ $user->initials }}
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <div class="text-xs font-bold text-slate-900 truncate">{{ $user->full_name }}</div>
                                <div class="text-[11px] text-slate-500 truncate">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex flex-wrap gap-1">
                            @foreach($user->roles as $role)
                                <x-badge type="primary">{{ $role->name }}</x-badge>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="text-xs font-bold text-slate-900">{{ $user->company?->name ?? '—' }}</div>
                        <div class="text-[11px] text-slate-500 font-medium">{{ $user->department?->name }} ({{ $user->branch?->name }})</div>
                    </td>
                    <td class="px-5 py-3.5">
                        @if($user->isActive())
                            <x-badge type="success">Active</x-badge>
                        @elseif($user->isSuspended() || $user->isLocked())
                            <x-badge type="danger">{{ ucfirst($user->status) }}</x-badge>
                        @else
                            <x-badge type="default">{{ ucfirst($user->status) }}</x-badge>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-xs text-slate-500 font-medium">
                        {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <x-action-dropdown>
                            @can('view', $user)
                                <x-action-dropdown-item href="{{ route('admin.users.show', $user) }}" icon="view">
                                    View Profile
                                </x-action-dropdown-item>
                            @endcan
                            @can('update', $user)
                                <x-action-dropdown-item href="{{ route('admin.users.edit', $user) }}" icon="edit">
                                    Edit Account
                                </x-action-dropdown-item>
                            @endcan
                            @can('delete', $user)
                                <x-action-dropdown-item @click="$dispatch('open-modal', 'delete-user-{{ $user->id }}')" icon="delete" variant="danger">
                                    Delete User
                                </x-action-dropdown-item>
                            @endcan
                        </x-action-dropdown>

                        <x-modal id="delete-user-{{ $user->id }}" maxWidth="md">
                            <x-slot:header>Delete User Account</x-slot:header>
                            <div class="text-center py-4 whitespace-normal">
                                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-rose-100 mb-4 border border-rose-200">
                                    <svg class="h-6 w-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </div>
                                <h3 class="text-base font-bold text-slate-900 mb-2">Delete {{ $user->full_name }}?</h3>
                                <p class="text-xs text-slate-500">Are you sure you want to delete this user account? This action cannot be undone.</p>
                            </div>
                            <x-slot:footer>
                                <div class="flex items-center gap-3 w-full justify-end">
                                    <x-button type="ghost" @click="open = false">Cancel</x-button>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <x-button type="danger" submit>Delete User</x-button>
                                    </form>
                                </div>
                            </x-slot:footer>
                        </x-modal>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center text-slate-500">
                        <p class="text-sm font-bold text-slate-900">No users found</p>
                        <p class="text-xs text-slate-500 mt-1">Adjust your filters or create a new user account.</p>
                    </td>
                </tr>
            @endforelse

            <x-slot:pagination>
                @if($users->hasPages())
                    <div class="px-5 py-3">
                        {{ $users->links() }}
                    </div>
                @endif
            </x-slot:pagination>
        </x-table>
    </x-card>
</x-layouts.admin>
