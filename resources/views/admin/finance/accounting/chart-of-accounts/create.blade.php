<x-layouts.admin title="Create Account">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Accounting', 'url' => '#'],
                ['label' => 'Chart of Accounts', 'url' => route('admin.finance.accounting.chart-of-accounts.index')],
                ['label' => 'Create']
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Create Account</h1>
        <p class="mt-1 text-sm text-gray-500">Add a new account to the chart of accounts.</p>
    </div>

    <x-card>
        <form method="POST" action="{{ route('admin.finance.accounting.chart-of-accounts.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Account Code --}}
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700">Account Code <span class="text-red-500">*</span></label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Account Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Account Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Account Type --}}
                <div>
                    <label for="account_type_id" class="block text-sm font-medium text-gray-700">Account Type <span class="text-red-500">*</span></label>
                    <select name="account_type_id" id="account_type_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">Select Type</option>
                        @foreach($accountTypes as $type)
                            <option value="{{ $type->id }}" {{ old('account_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }} ({{ $type->category }})
                            </option>
                        @endforeach
                    </select>
                    @error('account_type_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Parent Account --}}
                <div>
                    <label for="parent_id" class="block text-sm font-medium text-gray-700">Parent Account (Optional)</label>
                    <select name="parent_id" id="parent_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">None (Top Level)</option>
                        @foreach($parentAccounts as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                {{ $parent->code }} - {{ $parent->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="3"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('description') }}</textarea>
                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Status --}}
            <div class="flex items-center">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                    Active Account
                </label>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <x-button type="default" href="{{ route('admin.finance.accounting.chart-of-accounts.index') }}">Cancel</x-button>
                <x-button type="primary" submit>Create Account</x-button>
            </div>
        </form>
    </x-card>
</x-layouts.admin>
