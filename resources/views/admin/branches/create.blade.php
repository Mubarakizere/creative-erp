<x-layouts.admin title="Create Branch">
    {{-- Breadcrumbs --}}
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Branches', 'url' => route('admin.branches.index')],
                ['label' => 'Create'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Create Branch</h1>
                <p class="mt-1 text-sm text-gray-500">Register a new branch for a company.</p>
            </div>
            <x-button type="ghost" href="{{ route('admin.branches.index') }}" size="sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to List
            </x-button>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.branches.store') }}">
        @csrf

        <div class="space-y-6">
            {{-- Basic Information --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Basic Information</h3>
                </x-slot:header>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-select name="company_id" label="Company" :options="$companies" :selected="old('company_id')" placeholder="Select a company" required />
                    <x-input name="name" label="Branch Name" placeholder="Enter branch name" required />
                    <x-input name="code" label="Branch Code" placeholder="BR-001" required />
                    <x-select name="status" label="Status" :options="['active' => 'Active', 'inactive' => 'Inactive']" selected="active" />
                </div>
            </x-card>

            {{-- Contact Information --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Contact Information</h3>
                </x-slot:header>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <x-input name="email" label="Email" type="email" placeholder="branch@company.com" />
                    <x-input name="phone" label="Phone" placeholder="+971 4 123 4567" />
                    <x-input name="manager_name" label="Manager Name" placeholder="Full name of the branch manager" />
                </div>
            </x-card>

            {{-- Address --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Address</h3>
                </x-slot:header>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <x-input name="country" label="Country" placeholder="United Arab Emirates" />
                    <x-input name="state" label="State / Province" placeholder="Dubai" />
                    <x-input name="city" label="City" placeholder="Dubai" />
                    <div class="md:col-span-2">
                        <x-input name="address" label="Street Address" placeholder="Business Bay, Tower A, Floor 15" />
                    </div>
                    <x-input name="postal_code" label="Postal Code" placeholder="00000" />
                </div>
            </x-card>

            {{-- Geolocation --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Geolocation</h3>
                </x-slot:header>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input name="latitude" label="Latitude" type="number" step="0.0000001" placeholder="25.1860667" />
                    <x-input name="longitude" label="Longitude" type="number" step="0.0000001" placeholder="55.2628581" />
                </div>
                <p class="mt-2 text-xs text-gray-500">Optional. Coordinates can be used for map integration in the future.</p>
            </x-card>

            {{-- Notes --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Additional Notes</h3>
                </x-slot:header>

                <div>
                    <textarea
                        name="notes"
                        id="notes"
                        rows="4"
                        placeholder="Add any additional notes about this branch..."
                        class="block w-full rounded-lg border border-gray-300 shadow-sm text-sm py-2.5 px-3 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                    >{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </x-card>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pb-6">
                <x-button type="ghost" href="{{ route('admin.branches.index') }}">Cancel</x-button>
                <x-button type="primary" submit>
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Create Branch
                </x-button>
            </div>
        </div>
    </form>
</x-layouts.admin>
