<x-layouts.admin title="Edit Company">
    {{-- Breadcrumbs --}}
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Companies', 'url' => route('admin.companies.index')],
                ['label' => $company->name, 'url' => route('admin.companies.show', $company)],
                ['label' => 'Edit'],
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
                    <h1 class="text-2xl font-bold text-gray-900">Edit {{ $company->name }}</h1>
                    <p class="mt-1 text-sm text-gray-500">Update company information and settings.</p>
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

        <div class="space-y-6">
            {{-- Basic Information --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Basic Information</h3>
                </x-slot:header>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input name="name" label="Company Name" placeholder="Enter company name" required :value="$company->name" />
                    <x-input name="legal_name" label="Legal Name" placeholder="Enter legal name (optional)" :value="$company->legal_name" />
                    <x-input name="email" label="Email Address" type="email" placeholder="company@example.com" required :value="$company->email" />
                    <x-select name="status" label="Status" :options="['active' => 'Active', 'inactive' => 'Inactive', 'suspended' => 'Suspended']" :selected="$company->status" />
                </div>
            </x-card>

            {{-- Contact Information --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Contact Information</h3>
                </x-slot:header>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <x-input name="phone" label="Phone" placeholder="+1 234 567 890" :value="$company->phone" />
                    <x-input name="alternate_phone" label="Alternate Phone" placeholder="+1 234 567 891" :value="$company->alternate_phone" />
                    <x-input name="website" label="Website" type="url" placeholder="https://example.com" :value="$company->website" />
                </div>
            </x-card>

            {{-- Branding --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Branding</h3>
                </x-slot:header>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Logo Upload --}}
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
                                <input type="file" name="logo" id="logo" accept="image/*" class="hidden"
                                       @change="const file = $event.target.files[0]; if(file) { const reader = new FileReader(); reader.onload = (e) => preview = e.target.result; reader.readAsDataURL(file); }">
                                <label for="logo" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    Change Logo
                                </label>
                                <p class="mt-1 text-xs text-gray-500">JPG, PNG, SVG or WebP. Max 2MB.</p>
                            </div>
                        </div>
                        @error('logo')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Favicon Upload --}}
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
                                <input type="file" name="favicon" id="favicon" accept=".png,.ico" class="hidden"
                                       @change="const file = $event.target.files[0]; if(file) { const reader = new FileReader(); reader.onload = (e) => preview = e.target.result; reader.readAsDataURL(file); }">
                                <label for="favicon" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    Change Favicon
                                </label>
                                <p class="mt-1 text-xs text-gray-500">PNG or ICO. Max 512KB.</p>
                            </div>
                        </div>
                        @error('favicon')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </x-card>

            {{-- Business Information --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Business Information</h3>
                </x-slot:header>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input name="registration_number" label="Registration Number" placeholder="CR-001234" :value="$company->registration_number" />
                    <x-input name="tax_number" label="Tax Number" placeholder="TAX-00123456" :value="$company->tax_number" />
                </div>
            </x-card>

            {{-- Address --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Address</h3>
                </x-slot:header>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <x-input name="country" label="Country" placeholder="United Arab Emirates" :value="$company->country" />
                    <x-input name="state" label="State / Province" placeholder="Dubai" :value="$company->state" />
                    <x-input name="city" label="City" placeholder="Dubai" :value="$company->city" />
                    <div class="md:col-span-2">
                        <x-input name="address" label="Street Address" placeholder="Business Bay, Tower A, Floor 15" :value="$company->address" />
                    </div>
                    <x-input name="postal_code" label="Postal Code" placeholder="00000" :value="$company->postal_code" />
                </div>
            </x-card>

            {{-- Localization --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Localization</h3>
                </x-slot:header>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-select name="currency" label="Currency" :options="['USD' => 'USD - US Dollar', 'EUR' => 'EUR - Euro', 'GBP' => 'GBP - British Pound', 'RWF' => 'RWF - Rwandan Franc', 'KES' => 'KES - Kenyan Shilling', 'UGX' => 'UGX - Ugandan Shilling', 'TZS' => 'TZS - Tanzanian Shilling', 'BIF' => 'BIF - Burundian Franc', 'AED' => 'AED - UAE Dirham', 'SAR' => 'SAR - Saudi Riyal', 'QAR' => 'QAR - Qatari Riyal', 'KWD' => 'KWD - Kuwaiti Dinar', 'BHD' => 'BHD - Bahraini Dinar', 'OMR' => 'OMR - Omani Rial', 'EGP' => 'EGP - Egyptian Pound', 'JOD' => 'JOD - Jordanian Dinar', 'INR' => 'INR - Indian Rupee']" :selected="$company->currency" />
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
                    <h3 class="text-lg font-semibold text-gray-900">Business Hours</h3>
                </x-slot:header>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Working Days</label>
                        <div class="flex flex-wrap gap-3">
                            @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                                <label class="relative flex items-center gap-2 px-4 py-2.5 rounded-lg border border-gray-200 hover:border-blue-300 cursor-pointer transition-colors has-[:checked]:bg-blue-50 has-[:checked]:border-blue-500 has-[:checked]:text-blue-700">
                                    <input type="checkbox" name="working_days[]" value="{{ $day }}"
                                           @checked(in_array($day, old('working_days', $company->working_days ?? [])))
                                           class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm font-medium">{{ $day }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('working_days')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-input name="working_hours_start" label="Start Time" type="time" :value="$company->working_hours_start ? \Carbon\Carbon::parse($company->working_hours_start)->format('H:i') : '08:00'" />
                        <x-input name="working_hours_end" label="End Time" type="time" :value="$company->working_hours_end ? \Carbon\Carbon::parse($company->working_hours_end)->format('H:i') : '17:00'" />
                    </div>
                </div>
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
                        placeholder="Add any additional notes about this company..."
                        class="block w-full rounded-lg border border-gray-300 shadow-sm text-sm py-2.5 px-3 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                    >{{ old('notes', $company->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </x-card>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pb-6">
                <x-button type="ghost" href="{{ route('admin.companies.show', $company) }}">Cancel</x-button>
                <x-button type="primary" submit>
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update Company
                </x-button>
            </div>
        </div>
    </form>
</x-layouts.admin>
