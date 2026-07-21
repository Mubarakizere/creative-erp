<x-layouts.admin title="Finance Settings">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Finance', 'url' => '#'],
                ['label' => 'Settings']
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Finance Settings</h1>
        <p class="mt-1 text-sm text-gray-500">Manage payment methods and bank accounts.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 p-4 rounded-md">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-50 p-4 rounded-md">
            <ul class="list-disc pl-5 text-sm text-red-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Payment Methods --}}
        <div>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Payment Methods</h2>
                <x-button type="primary" size="sm" @click="$dispatch('open-modal', 'add-payment-method')">
                    Add Method
                </x-button>
            </div>
            
            <x-card class="p-0 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Name</th>
                            <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($paymentMethods as $method)
                            <tr>
                                <td class="py-3 px-4 text-sm font-medium text-gray-900">{{ $method->name }}</td>
                                <td class="py-3 px-4 text-sm text-right">
                                    <form method="POST" action="{{ route('admin.finance.payment-methods.destroy', $method->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this payment method?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="py-4 px-4 text-center text-sm text-gray-500">No payment methods configured.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </x-card>
        </div>

        {{-- Bank Accounts --}}
        <div>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Bank Accounts</h2>
                <x-button type="primary" size="sm" @click="$dispatch('open-modal', 'add-bank-account')">
                    Add Account
                </x-button>
            </div>
            
            <x-card class="p-0 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Account Name</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Bank</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($bankAccounts as $account)
                                <tr>
                                    <td class="py-3 px-4 text-sm font-medium text-gray-900">
                                        {{ $account->account_name }}
                                        <span class="block text-xs text-gray-500 font-normal">No: {{ $account->account_number }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-700">
                                        {{ $account->bank_name }}
                                        @if($account->swift_code)
                                            <span class="block text-xs text-gray-500 font-normal">SWIFT: {{ $account->swift_code }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-sm text-right">
                                        <form method="POST" action="{{ route('admin.finance.bank-accounts.destroy', $account->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this bank account?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-4 px-4 text-center text-sm text-gray-500">No bank accounts configured.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>

    {{-- Add Payment Method Modal --}}
    <x-modal id="add-payment-method" maxWidth="md">
        <x-slot:header>Add Payment Method</x-slot:header>
        <form method="POST" action="{{ route('admin.finance.payment-methods.store') }}" class="p-4 space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Method Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="e.g. Credit Card, Wire Transfer">
            </div>
            
            <div class="mt-6 flex justify-end gap-3">
                <x-button type="default" @click="open = false">Cancel</x-button>
                <x-button type="primary" submit>Save</x-button>
            </div>
        </form>
    </x-modal>

    {{-- Add Bank Account Modal --}}
    <x-modal id="add-bank-account" maxWidth="md">
        <x-slot:header>Add Bank Account</x-slot:header>
        <form method="POST" action="{{ route('admin.finance.bank-accounts.store') }}" class="p-4 space-y-4">
            @csrf
            <div>
                <label for="account_name" class="block text-sm font-medium text-gray-700">Account Name <span class="text-red-500">*</span></label>
                <input type="text" name="account_name" id="account_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="e.g. Primary Checking">
            </div>
            
            <div>
                <label for="bank_name" class="block text-sm font-medium text-gray-700">Bank Name <span class="text-red-500">*</span></label>
                <input type="text" name="bank_name" id="bank_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="e.g. Chase Bank">
            </div>
            
            <div>
                <label for="account_number" class="block text-sm font-medium text-gray-700">Account Number <span class="text-red-500">*</span></label>
                <input type="text" name="account_number" id="account_number" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="swift_code" class="block text-sm font-medium text-gray-700">SWIFT/BIC Code</label>
                    <input type="text" name="swift_code" id="swift_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
                <div>
                    <label for="currency" class="block text-sm font-medium text-gray-700">Currency</label>
                    <input type="text" name="currency" id="currency" value="USD" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
            </div>
            
            <div class="mt-6 flex justify-end gap-3">
                <x-button type="default" @click="open = false">Cancel</x-button>
                <x-button type="primary" submit>Save</x-button>
            </div>
        </form>
    </x-modal>
</x-layouts.admin>
