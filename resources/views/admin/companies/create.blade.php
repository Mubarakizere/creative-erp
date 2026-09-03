<x-layouts.admin title="Create Company">
    {{-- Breadcrumbs --}}
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Companies', 'url' => route('admin.companies.index')],
                ['label' => 'Create'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Create Company Profile</h1>
                <p class="mt-1 text-sm text-slate-500 font-medium">Register a new operating corporate entity or group subsidiary.</p>
            </div>
            <x-button type="ghost" href="{{ route('admin.companies.index') }}" size="sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to List
            </x-button>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.companies.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="space-y-8">
            {{-- Basic Information --}}
            <x-form-section title="Corporate Identification" description="Legal company name, system registration status, and official email.">
                <x-card>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <x-input name="name" label="Company Name" placeholder="e.g. Creative Engineering Ltd" required />
                        <x-input name="legal_name" label="Legal Name" placeholder="e.g. Creative Engineering Group Ltd" />
                        <x-input name="email" label="Email Address" type="email" placeholder="info@company.com" required />
                        <x-select name="status" label="Status" :options="['active' => 'Active', 'inactive' => 'Inactive', 'suspended' => 'Suspended']" selected="active" />
                    </div>
                </x-card>
            </x-form-section>

            {{-- Contact Information --}}
            <x-form-section title="Contact Channels" description="Official phone numbers and primary web address.">
                <x-card>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <x-input name="phone" label="Phone Number" placeholder="+250 788 000 000" />
                        <x-input name="alternate_phone" label="Alternate Phone" placeholder="+250 788 000 001" />
                        <x-input name="website" label="Website URL" type="url" placeholder="https://creative.rw" />
                    </div>
                </x-card>
            </x-form-section>

            {{-- Branding --}}
            <x-form-section title="Brand Assets" description="Upload official company logo and favicon graphics for branded documents.">
                <x-card>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Logo Upload --}}
                        <div x-data="{ preview: null }" class="space-y-1.5">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Company Logo</label>
                            <div class="flex items-center gap-4 mt-2">
                                <div class="shrink-0">
                                    <div x-show="!preview" class="w-16 h-16 rounded-xl bg-slate-100 border border-dashed border-slate-300 flex items-center justify-center">
                                        <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <img x-show="preview" :src="preview" class="w-16 h-16 rounded-xl object-cover border border-slate-200" style="display: none;" />
                                </div>
                                <div class="flex-1">
                                    <input type="file" name="logo" id="logo" accept="image/*" class="hidden"
                                           @change="const file = $event.target.files[0]; if(file) { const reader = new FileReader(); reader.onload = (e) => preview = e.target.result; reader.readAsDataURL(file); }">
                                    <label for="logo" class="inline-flex items-center px-3.5 py-2 text-xs font-bold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 cursor-pointer transition-colors shadow-xs">
                                        Upload Logo
                                    </label>
                                    <p class="mt-1 text-[11px] text-slate-400">JPG, PNG, SVG or WebP. Max 2MB.</p>
                                </div>
                            </div>
                            @error('logo')
                                <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Favicon Upload --}}
                        <div x-data="{ preview: null }" class="space-y-1.5">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Favicon Icon</label>
                            <div class="flex items-center gap-4 mt-2">
                                <div class="shrink-0">
                                    <div x-show="!preview" class="w-14 h-14 rounded-xl bg-slate-100 border border-dashed border-slate-300 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                        </svg>
                                    </div>
                                    <img x-show="preview" :src="preview" class="w-14 h-14 rounded-xl object-cover border border-slate-200" style="display: none;" />
                                </div>
                                <div class="flex-1">
                                    <input type="file" name="favicon" id="favicon" accept=".png,.ico" class="hidden"
                                           @change="const file = $event.target.files[0]; if(file) { const reader = new FileReader(); reader.onload = (e) => preview = e.target.result; reader.readAsDataURL(file); }">
                                    <label for="favicon" class="inline-flex items-center px-3.5 py-2 text-xs font-bold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 cursor-pointer transition-colors shadow-xs">
                                        Upload Favicon
                                    </label>
                                    <p class="mt-1 text-[11px] text-slate-400">PNG or ICO. Max 512KB.</p>
                                </div>
                            </div>
                            @error('favicon')
                                <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-card>
            </x-form-section>

            {{-- Localization & Business Info --}}
            <x-form-section title="Localization & Registration" description="Tax identification numbers, base currency, and default timezone.">
                <x-card>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                        <x-input name="registration_number" label="Registration #" placeholder="RDB-001234" />
                        <x-input name="tax_number" label="TIN / Tax Number" placeholder="100200300" />
                        <x-select name="currency" label="Currency" :options="['RWF' => 'RWF - Rwandan Franc', 'USD' => 'USD - US Dollar', 'EUR' => 'EUR - Euro', 'KES' => 'KES - Kenyan Shilling', 'UGX' => 'UGX - Ugandan Shilling']" selected="RWF" />
                        <x-select name="timezone" label="Timezone" :options="[
                            'Africa/Kigali' => 'Kigali (Rwanda)',
                            'Africa/Nairobi' => 'Nairobi (Kenya)',
                            'Africa/Kampala' => 'Kampala (Uganda)',
                            'UTC' => 'UTC',
                        ]" selected="Africa/Kigali" />
                    </div>

                    <x-slot:footer>
                        <a href="{{ route('admin.companies.index') }}">
                            <x-button type="ghost">Cancel</x-button>
                        </a>
                        <x-button type="primary" submit>
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Create Company Profile
                        </x-button>
                    </x-slot:footer>
                </x-card>
            </x-form-section>
        </div>
    </form>
</x-layouts.admin>
