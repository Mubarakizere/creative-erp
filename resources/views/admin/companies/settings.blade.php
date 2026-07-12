<x-layouts.admin title="Company Settings">
    {{-- Breadcrumbs --}}
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Companies', 'url' => route('admin.companies.index')],
                ['label' => $company->name, 'url' => route('admin.companies.show', $company)],
                ['label' => 'Settings'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                @if($company->logo_url)
                    <img src="{{ $company->logo_url }}" alt="{{ $company->name }}" class="w-12 h-12 rounded-xl object-cover border border-gray-200">
                @else
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-lg font-bold shadow-sm">
                        {{ strtoupper(substr($company->name, 0, 2)) }}
                    </div>
                @endif
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $company->name }} — Settings</h1>
                    <p class="mt-1 text-sm text-gray-500">Manage company settings and preferences.</p>
                </div>
            </div>
            <x-button type="ghost" href="{{ route('admin.companies.show', $company) }}" size="sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Details
            </x-button>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.companies.update', $company) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Hidden fields to preserve required fields --}}
        <input type="hidden" name="name" value="{{ $company->name }}">
        <input type="hidden" name="email" value="{{ $company->email }}">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Localization Settings --}}
            <x-card>
                <x-slot:header>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Localization</h3>
                            <p class="text-xs text-gray-500">Currency, timezone and language preferences</p>
                        </div>
                    </div>
                </x-slot:header>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-select name="currency" label="Currency" :options="['RWF' => 'RWF - Rwandan Franc', 'USD' => 'USD - US Dollar', 'EUR' => 'EUR - Euro', 'GBP' => 'GBP - British Pound', 'KES' => 'KES - Kenyan Shilling', 'UGX' => 'UGX - Ugandan Shilling', 'TZS' => 'TZS - Tanzanian Shilling', 'BIF' => 'BIF - Burundian Franc', 'AED' => 'AED - UAE Dirham', 'SAR' => 'SAR - Saudi Riyal', 'QAR' => 'QAR - Qatari Riyal', 'KWD' => 'KWD - Kuwaiti Dinar', 'BHD' => 'BHD - Bahraini Dinar', 'OMR' => 'OMR - Omani Rial', 'EGP' => 'EGP - Egyptian Pound', 'JOD' => 'JOD - Jordanian Dinar', 'INR' => 'INR - Indian Rupee']" :selected="$company->currency" />
                    <x-select name="timezone" label="Timezone" :options="[
                        'UTC' => 'UTC',
                        'Africa/Kigali' => 'Kigali (Rwanda)',
                        'Africa/Nairobi' => 'Nairobi (Kenya)',
                        'Africa/Kampala' => 'Kampala (Uganda)',
                        'Africa/Dar_es_Salaam' => 'Dar es Salaam (Tanzania)',
                        'Africa/Bujumbura' => 'Bujumbura (Burundi)',
                        'America/New_York' => 'Eastern Time (US)',
                        'America/Chicago' => 'Central Time (US)',
                        'America/Denver' => 'Mountain Time (US)',
                        'America/Los_Angeles' => 'Pacific Time (US)',
                        'Europe/London' => 'London',
                        'Europe/Paris' => 'Paris',
                        'Europe/Berlin' => 'Berlin',
                        'Asia/Dubai' => 'Dubai',
                        'Asia/Riyadh' => 'Riyadh',
                        'Asia/Kolkata' => 'Kolkata',
                        'Asia/Shanghai' => 'Shanghai',
                        'Asia/Tokyo' => 'Tokyo',
                        'Australia/Sydney' => 'Sydney',
                    ]" :selected="$company->timezone" />
                    <x-select name="language" label="Language" :options="['en' => 'English', 'rw' => 'Kinyarwanda', 'sw' => 'Swahili', 'ar' => 'Arabic', 'fr' => 'French', 'es' => 'Spanish']" :selected="$company->language" />
                </div>
            </x-card>

            {{-- Business Hours --}}
            <x-card>
                <x-slot:header>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Business Hours</h3>
                            <p class="text-xs text-gray-500">Working days and operating hours</p>
                        </div>
                    </div>
                </x-slot:header>

                <div class="space-y-5">
                    {{-- Working Days --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Working Days</label>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                                <label class="relative flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 hover:border-blue-300 cursor-pointer transition-colors has-[:checked]:bg-blue-50 has-[:checked]:border-blue-500 has-[:checked]:text-blue-700">
                                    <input type="checkbox" name="working_days[]" value="{{ $day }}"
                                           @checked(in_array($day, old('working_days', $company->working_days ?? [])))
                                           class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm font-medium">{{ $day }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Working Hours --}}
                    <div class="grid grid-cols-2 gap-4">
                        <x-input name="working_hours_start" label="Start Time" type="time" :value="$company->working_hours_start ? \Carbon\Carbon::parse($company->working_hours_start)->format('H:i') : '08:00'" />
                        <x-input name="working_hours_end" label="End Time" type="time" :value="$company->working_hours_end ? \Carbon\Carbon::parse($company->working_hours_end)->format('H:i') : '17:00'" />
                    </div>
                </div>
            </x-card>

            {{-- Branding --}}
            <x-card class="lg:col-span-2">
                <x-slot:header>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Branding</h3>
                            <p class="text-xs text-gray-500">Company logo and favicon</p>
                        </div>
                    </div>
                </x-slot:header>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Logo --}}
                    <div x-data="{ preview: '{{ $company->logo_url }}' }" class="space-y-1.5">
                        <label class="block text-sm font-medium text-gray-700">Company Logo</label>
                        <div class="flex items-center gap-4">
                            <div class="flex-shrink-0">
                                <div x-show="!preview" class="w-20 h-20 rounded-xl bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <img x-show="preview" :src="preview" class="w-20 h-20 rounded-xl object-cover border border-gray-200" style="{{ $company->logo_url ? '' : 'display: none;' }}" />
                            </div>
                            <div class="flex-1">
                                <input type="file" name="logo" id="settings_logo" accept="image/*" class="hidden"
                                       @change="const file = $event.target.files[0]; if(file) { const reader = new FileReader(); reader.onload = (e) => preview = e.target.result; reader.readAsDataURL(file); }">
                                <label for="settings_logo" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                    Change Logo
                                </label>
                                <p class="mt-1 text-xs text-gray-500">JPG, PNG, SVG or WebP. Max 2MB.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Favicon --}}
                    <div x-data="{ preview: '{{ $company->favicon_url }}' }" class="space-y-1.5">
                        <label class="block text-sm font-medium text-gray-700">Favicon</label>
                        <div class="flex items-center gap-4">
                            <div class="flex-shrink-0">
                                <div x-show="!preview" class="w-16 h-16 rounded-lg bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                    </svg>
                                </div>
                                <img x-show="preview" :src="preview" class="w-16 h-16 rounded-lg object-cover border border-gray-200" style="{{ $company->favicon_url ? '' : 'display: none;' }}" />
                            </div>
                            <div class="flex-1">
                                <input type="file" name="favicon" id="settings_favicon" accept=".png,.ico" class="hidden"
                                       @change="const file = $event.target.files[0]; if(file) { const reader = new FileReader(); reader.onload = (e) => preview = e.target.result; reader.readAsDataURL(file); }">
                                <label for="settings_favicon" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                    Change Favicon
                                </label>
                                <p class="mt-1 text-xs text-gray-500">PNG or ICO. Max 512KB.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3 mt-6 pb-6">
            <x-button type="ghost" href="{{ route('admin.companies.show', $company) }}">Cancel</x-button>
            <x-button type="primary" submit>
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Save Settings
            </x-button>
        </div>
    </form>
</x-layouts.admin>
