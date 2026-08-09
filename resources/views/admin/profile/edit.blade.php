<x-layouts.admin title="Profile Settings">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Profile Settings']
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Profile Settings</h1>
        <p class="mt-1 text-sm text-gray-500">Manage your account details and password.</p>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Avatar and Quick Info -->
        <div class="lg:col-span-1">
            <x-card>
                <div class="flex flex-col items-center">
                    <div class="relative group">
                        @if($user->avatar)
                            <img src="{{ Storage::url($user->avatar) }}" alt="Avatar" class="h-32 w-32 rounded-full object-cover border-4 border-white shadow-lg">
                        @else
                            <div class="h-32 w-32 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-4xl shadow-lg border-4 border-white">
                                {{ $user->initials }}
                            </div>
                        @endif
                        
                        <form action="{{ route('admin.profile.avatar.destroy') }}" method="POST" class="absolute bottom-0 right-0 bg-white rounded-full p-1 shadow-sm border border-gray-100">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 p-1 bg-white rounded-full transition-colors hover:bg-gray-50" title="Remove Avatar">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>

                    <h2 class="mt-4 text-xl font-bold text-gray-900">{{ $user->full_name }}</h2>
                    <p class="text-sm text-gray-500">{{ $user->job_title ?? 'No Job Title' }}</p>
                    <div class="mt-2 flex flex-wrap gap-1 justify-center">
                        @foreach($user->roles as $role)
                            <x-badge color="blue">{{ $role->name }}</x-badge>
                        @endforeach
                    </div>

                    <div class="mt-6 w-full pt-6 border-t border-gray-100">
                        <form action="{{ route('admin.profile.avatar.update') }}" method="POST" enctype="multipart/form-data" class="flex flex-col items-center">
                            @csrf
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload New Avatar</label>
                            <input type="file" name="avatar" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 mb-3" required>
                            @error('avatar', 'updateAvatar')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <x-button type="primary" submit class="w-full justify-center">Upload Avatar</x-button>
                        </form>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Forms -->
        <div class="lg:col-span-2 space-y-6">
            <!-- General Info -->
            <x-card>
                <div class="border-b border-gray-200 pb-4 mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Personal Information</h3>
                    <p class="mt-1 text-sm text-gray-500">Update your basic profile details.</p>
                </div>

                <form action="{{ route('admin.profile.update') }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <x-input name="first_name" label="First Name" :value="old('first_name', $user->first_name)" required />
                        </div>
                        <div>
                            <x-input name="last_name" label="Last Name" :value="old('last_name', $user->last_name)" required />
                        </div>
                        <div class="md:col-span-2">
                            <x-input name="email" type="email" label="Email Address" :value="old('email', $user->email)" required />
                        </div>
                        <div>
                            <x-input name="phone" label="Phone Number" :value="old('phone', $user->phone)" />
                        </div>
                        <div>
                            <x-input name="job_title" label="Job Title" :value="old('job_title', $user->job_title)" />
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <x-button type="primary" submit>Save Changes</x-button>
                    </div>
                </form>
            </x-card>

            <!-- Password -->
            <x-card>
                <div class="border-b border-gray-200 pb-4 mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Update Password</h3>
                    <p class="mt-1 text-sm text-gray-500">Ensure your account is using a long, random password to stay secure.</p>
                </div>

                <form action="{{ route('admin.profile.password.update') }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-6 mb-6">
                        <div>
                            <x-input type="password" name="current_password" label="Current Password" required />
                            @error('current_password', 'updatePassword')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <x-input type="password" name="password" label="New Password" required />
                            @error('password', 'updatePassword')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <x-input type="password" name="password_confirmation" label="Confirm Password" required />
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <x-button type="primary" submit>Update Password</x-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-layouts.admin>
