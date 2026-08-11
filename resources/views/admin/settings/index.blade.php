<x-layouts.admin title="System Settings">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'System Settings']
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div x-data="settingsPage()" class="max-w-6xl mx-auto">
        {{-- Page Header --}}
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">System Settings</h1>
                    <p class="mt-1 text-sm text-gray-500">Configure your {{ system_name() }} system preferences and behavior.</p>
                </div>
                @unless($canManage)
                    <div class="flex items-center gap-2 px-4 py-2.5 bg-amber-50 border border-amber-200 rounded-xl text-amber-700 text-sm">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span class="font-medium">View Only</span>
                        <span class="hidden sm:inline">&mdash; You don't have permission to modify settings.</span>
                    </div>
                @endunless
            </div>
        </div>

        {{-- Settings Layout: Sidebar + Content --}}
        <div class="flex flex-col lg:flex-row gap-6">

            {{-- Settings Navigation Sidebar --}}
            <div class="lg:w-64 flex-shrink-0">
                {{-- Mobile: Dropdown --}}
                <div class="lg:hidden mb-4">
                    <select
                        x-model="activeTab"
                        class="block w-full rounded-xl border border-gray-300 shadow-sm text-sm py-2.5 pl-3 pr-10 focus:ring-blue-500 focus:border-blue-500"
                    >
                        <template x-for="tab in tabs" :key="tab.id">
                            <option :value="tab.id" x-text="tab.label"></option>
                        </template>
                    </select>
                </div>

                {{-- Desktop: Vertical Nav --}}
                <nav class="hidden lg:block bg-white rounded-2xl border border-gray-200/60 shadow-sm p-2 sticky top-24">
                    <template x-for="tab in tabs" :key="tab.id">
                        <button
                            @click="activeTab = tab.id"
                            :class="activeTab === tab.id
                                ? 'bg-blue-50 text-blue-700 border-blue-200'
                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-transparent'"
                            class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 border mb-0.5"
                        >
                            <span x-html="tab.icon" class="w-5 h-5 flex-shrink-0"></span>
                            <span x-text="tab.label"></span>
                            <template x-if="tab.badge">
                                <span class="ml-auto text-[10px] uppercase font-bold bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded" x-text="tab.badge"></span>
                            </template>
                        </button>
                    </template>
                </nav>
            </div>

            {{-- Settings Content Area --}}
            <div class="flex-1 min-w-0">

                {{-- =============== GENERAL =============== --}}
                <div x-show="activeTab === 'general'" x-cloak>
                    <x-card>
                        <div class="border-b border-gray-200 pb-4 mb-6">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-blue-50 text-blue-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">General Configuration</h3>
                                    <p class="text-sm text-gray-500">Core system identity and behavior settings.</p>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('admin.settings.update') }}" method="POST" x-ref="generalForm" @submit="handleSubmit($event)">
                            @csrf

                            <div class="space-y-6">
                                {{-- System Name --}}
                                <div class="space-y-1.5">
                                    <label for="settings_system_name" class="block text-sm font-medium text-gray-700">
                                        System Name
                                    </label>
                                    <p class="text-xs text-gray-400">The display name for your ERP system shown in headers and emails.</p>
                                    @if($canManage)
                                        <input
                                            type="text"
                                            name="settings[system_name]"
                                            id="settings_system_name"
                                            value="{{ old('settings.system_name', $settings['system_name'] ?? 'Creative Century Engineering') }}"
                                            placeholder="e.g. Creative Century Engineering"
                                            maxlength="255"
                                            x-on:input="markDirty()"
                                            class="block w-full rounded-xl border {{ $errors->has('settings.system_name') ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500' }} shadow-sm text-sm py-2.5 pl-3 pr-3 transition-colors duration-200 min-h-[42px]"
                                        >
                                    @else
                                        <div class="block w-full rounded-xl border border-gray-200 bg-gray-50 text-gray-600 shadow-sm text-sm py-2.5 px-3 min-h-[42px]">
                                            {{ $settings['system_name'] ?? 'Creative Century Engineering' }}
                                        </div>
                                    @endif
                                    @error('settings.system_name')
                                        <p class="text-sm text-red-600 flex items-center gap-1">
                                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- Date Format --}}
                                <div class="space-y-1.5">
                                    <label for="settings_date_format" class="block text-sm font-medium text-gray-700">
                                        Date Format
                                    </label>
                                    <p class="text-xs text-gray-400">How dates are displayed throughout the system.</p>
                                    @if($canManage)
                                        <select
                                            name="settings[date_format]"
                                            id="settings_date_format"
                                            x-on:change="markDirty()"
                                            class="block w-full rounded-xl border {{ $errors->has('settings.date_format') ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500' }} shadow-sm text-sm py-2.5 pl-3 pr-10 transition-colors duration-200"
                                        >
                                            @php
                                                $dateFormats = [
                                                    'Y-m-d' => 'YYYY-MM-DD (2026-08-09)',
                                                    'd/m/Y' => 'DD/MM/YYYY (09/08/2026)',
                                                    'm/d/Y' => 'MM/DD/YYYY (08/09/2026)',
                                                    'd-m-Y' => 'DD-MM-YYYY (09-08-2026)',
                                                    'd.m.Y' => 'DD.MM.YYYY (09.08.2026)',
                                                    'M d, Y' => 'Mon DD, YYYY (Aug 09, 2026)',
                                                    'F d, Y' => 'Month DD, YYYY (August 09, 2026)',
                                                ];
                                                $currentDateFormat = old('settings.date_format', $settings['date_format'] ?? 'Y-m-d');
                                            @endphp
                                            @foreach($dateFormats as $value => $label)
                                                <option value="{{ $value }}" @selected($currentDateFormat === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <div class="block w-full rounded-xl border border-gray-200 bg-gray-50 text-gray-600 shadow-sm text-sm py-2.5 px-3 min-h-[42px]">
                                            {{ $settings['date_format'] ?? 'Y-m-d' }}
                                        </div>
                                    @endif
                                    @error('settings.date_format')
                                        <p class="text-sm text-red-600 flex items-center gap-1">
                                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- Time Format --}}
                                <div class="space-y-1.5">
                                    <label for="settings_time_format" class="block text-sm font-medium text-gray-700">
                                        Time Format
                                    </label>
                                    <p class="text-xs text-gray-400">How times are displayed throughout the system.</p>
                                    @if($canManage)
                                        <select
                                            name="settings[time_format]"
                                            id="settings_time_format"
                                            x-on:change="markDirty()"
                                            class="block w-full rounded-xl border {{ $errors->has('settings.time_format') ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500' }} shadow-sm text-sm py-2.5 pl-3 pr-10 transition-colors duration-200"
                                        >
                                            @php
                                                $timeFormats = [
                                                    'H:i' => '24 Hour (14:30)',
                                                    'h:i A' => '12 Hour (02:30 PM)',
                                                    'H:i:s' => '24 Hour with seconds (14:30:00)',
                                                    'h:i:s A' => '12 Hour with seconds (02:30:00 PM)',
                                                ];
                                                $currentTimeFormat = old('settings.time_format', $settings['time_format'] ?? 'H:i');
                                            @endphp
                                            @foreach($timeFormats as $value => $label)
                                                <option value="{{ $value }}" @selected($currentTimeFormat === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <div class="block w-full rounded-xl border border-gray-200 bg-gray-50 text-gray-600 shadow-sm text-sm py-2.5 px-3 min-h-[42px]">
                                            {{ $settings['time_format'] ?? 'H:i' }}
                                        </div>
                                    @endif
                                    @error('settings.time_format')
                                        <p class="text-sm text-red-600 flex items-center gap-1">
                                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- Maintenance Mode --}}
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label for="settings_maintenance_mode" class="block text-sm font-medium text-gray-700">
                                                Maintenance Mode
                                            </label>
                                            <p class="text-xs text-gray-400 mt-0.5">Controls the configured maintenance state. When enabled, a maintenance flag is stored.</p>
                                        </div>
                                        @if($canManage)
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input
                                                    type="hidden"
                                                    name="settings[maintenance_mode]"
                                                    value="0"
                                                >
                                                <input
                                                    type="checkbox"
                                                    name="settings[maintenance_mode]"
                                                    id="settings_maintenance_mode"
                                                    value="1"
                                                    x-on:change="markDirty()"
                                                    @checked(old('settings.maintenance_mode', $settings['maintenance_mode'] ?? false))
                                                    class="sr-only peer"
                                                >
                                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-100 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                            </label>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ ($settings['maintenance_mode'] ?? false) ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ ($settings['maintenance_mode'] ?? false) ? 'bg-amber-500' : 'bg-green-500' }}"></span>
                                                {{ ($settings['maintenance_mode'] ?? false) ? 'Enabled' : 'Disabled' }}
                                            </span>
                                        @endif
                                    </div>
                                    @error('settings.maintenance_mode')
                                        <p class="text-sm text-red-600 flex items-center gap-1">
                                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Save Button --}}
                            @if($canManage)
                                <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-100">
                                    <p class="text-sm text-gray-400" x-show="isDirty" x-cloak>
                                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-400 mr-1"></span>
                                        You have unsaved changes
                                    </p>
                                    <div class="flex-shrink-0 ml-auto">
                                        <x-button type="primary" submit>
                                            Save Settings
                                        </x-button>
                                    </div>
                                </div>
                            @endif
                        </form>
                    </x-card>
                </div>

                {{-- =============== LOCALIZATION =============== --}}
                <div x-show="activeTab === 'localization'" x-cloak>
                    <x-card>
                        <div class="border-b border-gray-200 pb-4 mb-6">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Localization</h3>
                                    <p class="text-sm text-gray-500">Regional and language preferences.</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col items-center justify-center py-8 text-center">
                            <div class="inline-flex items-center justify-center w-14 h-14 mb-4 rounded-full bg-emerald-50 text-emerald-400">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                </svg>
                            </div>
                            <h4 class="text-base font-semibold text-gray-900 mb-1">Managed by Company Settings</h4>
                            <p class="text-sm text-gray-500 max-w-sm mb-4">Currency, timezone, and language settings are configured per company to support multi-company operations.</p>
                            @if(Route::has('admin.companies.index'))
                                <x-button type="outline" size="sm" :href="route('admin.companies.index')">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    View Company Settings
                                </x-button>
                            @endif
                        </div>
                    </x-card>
                </div>



                {{-- =============== DOCUMENT NUMBERING =============== --}}
                <div x-show="activeTab === 'numbering'" x-cloak>
                    <x-card>
                        <div class="border-b border-gray-200 pb-4 mb-6">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-blue-50 text-blue-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Document Numbering</h3>
                                    <p class="text-sm text-gray-500">Configure sequence generation for business documents.</p>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('admin.settings.sequences.update') }}" method="POST">
                            @csrf
                            
                            <div class="divide-y divide-gray-100 border border-gray-200/60 rounded-xl overflow-hidden">
                                @foreach($sequences ?? [] as $i => $sequence)
                                    <div class="p-4 sm:p-6 transition hover:bg-gray-50/30" x-data="{ prefix: '{{ $sequence->prefix }}', nextNumber: {{ $sequence->next_number }}, padding: {{ $sequence->padding }} }">
                                        <input type="hidden" name="sequences[{{ $i }}][id]" value="{{ $sequence->id }}">
                                        
                                        <div class="flex flex-col lg:flex-row lg:items-start gap-4 sm:gap-6">
                                            <div class="lg:w-1/3">
                                                <h3 class="text-sm font-medium text-gray-900 capitalize">{{ str_replace('_', ' ', $sequence->document_type) }}</h3>
                                                <div class="mt-2 text-xs font-mono bg-gray-100 text-gray-600 px-2 py-1 rounded inline-block border border-gray-200">
                                                    Preview: <span x-text="prefix + String(nextNumber).padStart(padding, '0')"></span>
                                                </div>
                                            </div>
                                            
                                            <div class="lg:w-2/3 grid grid-cols-1 sm:grid-cols-3 gap-4">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Prefix</label>
                                                    <input type="text" name="sequences[{{ $i }}][prefix]" x-model="prefix" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm {{ !$canManage ? 'bg-gray-50 text-gray-500' : '' }}" {{ !$canManage ? 'readonly' : '' }} required>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Next Number</label>
                                                    <input type="number" name="sequences[{{ $i }}][next_number]" x-model="nextNumber" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm {{ !$canManage ? 'bg-gray-50 text-gray-500' : '' }}" {{ !$canManage ? 'readonly' : '' }} min="1" required>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Padding</label>
                                                    <input type="number" name="sequences[{{ $i }}][padding]" x-model="padding" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm {{ !$canManage ? 'bg-gray-50 text-gray-500' : '' }}" {{ !$canManage ? 'readonly' : '' }} min="3" max="10" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if($canManage)
                                <div class="mt-6 flex justify-end">
                                    <x-button type="submit" variant="primary">
                                        Save Numbering Settings
                                    </x-button>
                                </div>
                            @endif
                        </form>
                    </x-card>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function settingsPage() {
            return {
                activeTab: 'general',
                isDirty: false,
                tabs: [
                    {
                        id: 'general',
                        label: 'General',
                        badge: null,
                        icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>'
                    },
                    {
                        id: 'localization',
                        label: 'Localization',
                        badge: null,
                        icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                    },
                    {
                        id: 'numbering',
                        label: 'Numbering',
                        badge: null,
                        icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>'
                    }
                ],
                markDirty() {
                    this.isDirty = true;
                },
                handleSubmit(event) {
                    // The x-button component handles the loading state
                },
                init() {
                    // Unsaved changes detection
                    window.addEventListener('beforeunload', (e) => {
                        if (this.isDirty) {
                            e.preventDefault();
                            e.returnValue = '';
                        }
                    });
                }
            };
        }
    </script>
    @endpush
</x-layouts.admin>
