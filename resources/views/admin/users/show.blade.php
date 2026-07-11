<x-layouts.admin title="User Details">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Users', 'url' => route('admin.users.index')],
                ['label' => $user->full_name],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="h-16 w-16 rounded-full overflow-hidden border bg-gray-100 flex items-center justify-center">
                @if($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}" class="h-full w-full object-cover" alt="{{ $user->full_name }}">
                @else
                    <span class="text-2xl font-bold text-blue-600">{{ $user->initials }}</span>
                @endif
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    {{ $user->full_name }}
                    @if($user->isActive())
                        <x-badge color="green">Active</x-badge>
                    @elseif($user->isSuspended() || $user->isLocked())
                        <x-badge color="red">{{ ucfirst($user->status) }}</x-badge>
                    @else
                        <x-badge color="gray">{{ ucfirst($user->status) }}</x-badge>
                    @endif
                </h1>
                <p class="mt-1 text-sm text-gray-500">{{ $user->job_title ?? 'User' }} &bull; {{ $user->email }}</p>
            </div>
        </div>
        
        <div class="flex items-center gap-2">
            @can('user.update', $user)
                <x-button type="ghost" href="{{ route('admin.users.edit', $user) }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit User
                </x-button>
            @endcan

            @can('user.activate', $user)
                @if(!$user->isActive())
                    <form method="POST" action="{{ route('admin.users.activate', $user) }}" class="inline">
                        @csrf @method('PATCH')
                        <x-button type="primary" submit>Activate</x-button>
                    </form>
                @endif
            @endcan
            
            @can('user.deactivate', $user)
                @if($user->isActive())
                    <form method="POST" action="{{ route('admin.users.deactivate', $user) }}" class="inline">
                        @csrf @method('PATCH')
                        <x-button type="secondary" submit>Deactivate</x-button>
                    </form>
                @endif
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Organization info --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Organization</h3>
                </x-slot:header>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Company</p>
                        <p class="font-medium text-gray-900">{{ $user->company?->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Branch</p>
                        <p class="font-medium text-gray-900">{{ $user->branch?->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Department</p>
                        <p class="font-medium text-gray-900">{{ $user->department?->name ?? 'N/A' }}</p>
                    </div>
                </div>
            </x-card>

            {{-- Security --}}
            <x-card>
                <x-slot:header>
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Security & Roles</h3>
                        @can('user.resetPassword', $user)
                            <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" onsubmit="return confirm('Are you sure you want to reset this user\'s password? They will receive an email with their new password.');">
                                @csrf
                                <x-button type="ghost" size="sm" submit>Reset Password</x-button>
                            </form>
                        @endcan
                    </div>
                </x-slot:header>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500 mb-2">Assigned Roles</p>
                        <div class="flex flex-wrap gap-2">
                            @forelse($user->roles as $role)
                                <x-badge color="blue">{{ $role->name }}</x-badge>
                            @empty
                                <span class="text-sm text-gray-500">No roles assigned</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        <div class="space-y-6">
            {{-- Contact Info --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Contact</h3>
                </x-slot:header>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-gray-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Email Address</p>
                            <p class="text-sm text-gray-500"><a href="mailto:{{ $user->email }}" class="text-blue-600 hover:underline">{{ $user->email }}</a></p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-gray-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Phone Number</p>
                            <p class="text-sm text-gray-500">{{ $user->phone ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>
            </x-card>
            
            {{-- Meta Data --}}
            <x-card>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Last Login:</span>
                        <span class="font-medium text-gray-900">{{ $user->last_login_at ? $user->last_login_at->format('M d, Y h:i A') : 'Never' }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Created:</span>
                        <span class="font-medium text-gray-900">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </x-card>

            @can('user.delete', $user)
                <x-card class="border-red-200 bg-red-50">
                    <h3 class="text-lg font-semibold text-red-800 mb-2">Danger Zone</h3>
                    <p class="text-sm text-red-600 mb-4">Deleting this user will revoke their access immediately.</p>
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Are you sure you want to delete this user?');">
                        @csrf @method('DELETE')
                        <x-button type="danger" submit class="w-full justify-center">Delete User</x-button>
                    </form>
                </x-card>
            @endcan
        </div>
    </div>
</x-layouts.admin>
