<x-layouts.admin title="Website CMS">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Website CMS'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Website CMS</h1>
            <p class="mt-1 text-sm text-gray-500">Manage the public website content and imagery directly from the dashboard.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-4 rounded-xl flex items-center shadow-sm border border-green-100 mb-6">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.website-settings.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="space-y-6">
            {{-- General Settings Section --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">General Information</h3>
                </x-slot:header>

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-input name="company_email" type="email" label="Public Contact Email" value="{{ $settings['company_email'] ?? '' }}" />
                        <x-input name="company_phone" label="Public Contact Phone" value="{{ $settings['company_phone'] ?? '' }}" />
                    </div>
                </div>
            </x-card>

            <div class="flex items-center justify-end pb-6">
                <x-button type="primary" submit>
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Save Settings
                </x-button>
            </div>
        </div>
    </form>
</x-layouts.admin>
