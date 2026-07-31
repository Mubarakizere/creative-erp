<div x-data="{ type: '{{ old('client_type', $client->client_type ?? 'Company') }}' }" class="space-y-6">
    
    {{-- Client Classification --}}
    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
        <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
            <h3 class="text-lg font-bold text-gray-900 tracking-tight">Client Classification</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <x-select name="client_type" label="Client Type" :options="['Company' => 'Company', 'Individual' => 'Individual']" :selected="old('client_type', $client->client_type ?? 'Company')" x-model="type" required />
                <x-select name="company_id" label="Assigned Company" :options="$companies->pluck('name', 'id')->toArray()" :selected="old('company_id', $client->company_id ?? '')" required />
                <x-select name="branch_id" label="Assigned Branch" :options="$branches->pluck('name', 'id')->toArray()" :selected="old('branch_id', $client->branch_id ?? '')" required />
            </div>
        </div>
    </div>

    {{-- Basic Information --}}
    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
        <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
            <h3 class="text-lg font-bold text-gray-900 tracking-tight">Basic Information</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Company Fields --}}
                <div x-show="type === 'Company'" class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input name="company_name" label="Company Name" placeholder="Enter company name" :value="old('company_name', $client->company_name ?? '')" x-bind:required="type === 'Company'" />
                    <x-input name="website" label="Website" type="url" placeholder="https://example.com" :value="old('website', $client->website ?? '')" />
                </div>

                {{-- Individual Fields --}}
                <div x-show="type === 'Individual'" class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6" style="display: none;">
                    <x-input name="first_name" label="First Name" placeholder="Enter first name" :value="old('first_name', $client->first_name ?? '')" x-bind:required="type === 'Individual'" />
                    <x-input name="last_name" label="Last Name" placeholder="Enter last name" :value="old('last_name', $client->last_name ?? '')" x-bind:required="type === 'Individual'" />
                </div>

                <x-input name="email" label="Email Address" type="email" placeholder="client@example.com" :value="old('email', $client->email ?? '')" />
                <x-select name="status" label="Status" :options="['active' => 'Active', 'inactive' => 'Inactive']" :selected="old('status', $client->status ?? 'active')" />
            </div>
        </div>
    </div>

    {{-- Contact Information --}}
    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
        <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
            <h3 class="text-lg font-bold text-gray-900 tracking-tight">Contact Information</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-input name="phone" label="Phone" placeholder="+1 234 567 890" :value="old('phone', $client->phone ?? '')" required />
                <x-input name="alternate_phone" label="Alternate Phone" placeholder="+1 234 567 891" :value="old('alternate_phone', $client->alternate_phone ?? '')" />
            </div>
        </div>
    </div>

    {{-- Business Details (Company Only) --}}
    <div x-show="type === 'Company'">
        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
            <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
                <h3 class="text-lg font-bold text-gray-900 tracking-tight">Business Details</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input name="registration_number" label="Registration Number" placeholder="CR-001234" :value="old('registration_number', $client->registration_number ?? '')" />
                    <x-input name="tax_number" label="Tax Number" placeholder="TAX-00123456" :value="old('tax_number', $client->tax_number ?? '')" />
                </div>
            </div>
        </div>
    </div>

    {{-- Address --}}
    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
        <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
            <h3 class="text-lg font-bold text-gray-900 tracking-tight">Address</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <x-input name="country" label="Country" placeholder="United Arab Emirates" :value="old('country', $client->country ?? '')" />
                <x-input name="state" label="State / Province" placeholder="Dubai" :value="old('state', $client->state ?? '')" />
                <x-input name="city" label="City" placeholder="Dubai" :value="old('city', $client->city ?? '')" />
                <div class="md:col-span-2">
                    <x-input name="address" label="Street Address" placeholder="Business Bay, Tower A, Floor 15" :value="old('address', $client->address ?? '')" />
                </div>
                <x-input name="postal_code" label="Postal Code" placeholder="00000" :value="old('postal_code', $client->postal_code ?? '')" />
            </div>
        </div>
    </div>

    {{-- Logo --}}
    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
        <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
            <h3 class="text-lg font-bold text-gray-900 tracking-tight">Client Logo</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 gap-6">
                <div x-data="{ preview: '{{ $client && $client->logo_url ? $client->logo_url : '' }}' }" class="space-y-1.5">
                    <label class="block text-sm font-medium text-gray-700">Logo</label>
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0">
                            <div x-show="!preview" class="w-20 h-20 rounded-xl bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <img x-show="preview" :src="preview" class="w-20 h-20 rounded-xl object-cover border border-gray-200" style="display: none;" />
                        </div>
                        <div class="flex-1">
                            <input type="file" name="logo_file" id="logo_file" accept="image/*" class="hidden"
                                   @change="const file = $event.target.files[0]; if(file) { const reader = new FileReader(); reader.onload = (e) => preview = e.target.result; reader.readAsDataURL(file); }">
                            <label for="logo_file" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                Upload Logo
                            </label>
                            <p class="mt-1 text-xs text-gray-500">JPG, PNG, SVG or WebP. Max 2MB.</p>
                        </div>
                    </div>
                    @error('logo_file')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Notes --}}
    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
        <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
            <h3 class="text-lg font-bold text-gray-900 tracking-tight">Additional Notes</h3>
        </div>
        <div class="p-6">
            <div>
                <textarea
                    name="notes"
                    id="notes"
                    rows="4"
                    placeholder="Add any additional notes about this client..."
                    class="block w-full rounded-xl border border-gray-300 shadow-sm text-sm py-2.5 px-3 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                >{{ old('notes', $client->notes ?? '') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-end gap-3 pb-6">
        <a href="{{ $client ? route('admin.clients.show', $client) : route('admin.clients.index') }}" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">
            Cancel
        </a>
        <button type="submit" form="client-form" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none hover:shadow-md">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ $client ? 'Update Client' : 'Create Client' }}
        </button>
    </div>
</div>
