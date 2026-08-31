<x-layouts.admin title="Finance Settings">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Finance', 'url' => '#'],
                ['label' => 'Settings']
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Finance Settings</h1>
            <p class="mt-1 text-sm text-gray-500 font-medium">Manage payment methods, bank accounts, and tax rates.</p>
        </div>
    </div>


    @if($errors->any())
        <div class="mb-6 bg-red-50/50 border border-red-100 p-4 rounded-xl flex items-start">
            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center mr-3 shrink-0">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <ul class="list-disc pl-2 mt-1.5 text-sm font-medium text-red-800">
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
                <h2 class="text-lg font-bold text-gray-900 tracking-tight">Payment Methods</h2>
                <button type="button" @click="$dispatch('open-modal', 'add-payment-method')" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-100 rounded-lg hover:bg-blue-100 transition-colors focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Add Method
                </button>
            </div>
            
            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                <table class="min-w-full text-left divide-y divide-gray-200/60">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="py-3 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Name</th>
                            <th class="py-3 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 text-right w-24">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($paymentMethods as $method)
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="py-4 px-6 text-sm font-bold text-gray-900">{{ $method->name }}</td>
                                <td class="py-4 px-6 text-right">
                                    <div x-data>
                                        <button @click="$dispatch('open-modal', 'delete-method-{{ $method->id }}')" class="p-1.5 rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 transition-colors inline-flex" title="Delete">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                        
                                        <x-modal id="delete-method-{{ $method->id }}" maxWidth="md">
                                            <x-slot:header>Delete Payment Method</x-slot:header>
                                            <div class="text-center py-4 whitespace-normal">
                                                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4 border border-red-200">
                                                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </div>
                                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete {{ $method->name }}?</h3>
                                                <p class="text-sm text-gray-500">This action will permanently delete this payment method. This cannot be undone.</p>
                                            </div>
                                            <x-slot:footer>
                                                <div class="flex items-center gap-3 w-full justify-end">
                                                    <button type="button" @click="open = false" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">Cancel</button>
                                                    <form method="POST" action="{{ route('admin.finance.payment-methods.destroy', $method->id) }}" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-xl hover:bg-red-700 transition-colors shadow-sm">Delete Method</button>
                                                    </form>
                                                </div>
                                            </x-slot:footer>
                                        </x-modal>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="py-12 px-6 text-center">
                                    <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 border border-gray-100">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                    </div>
                                    <p class="text-sm text-gray-500 font-medium">No payment methods configured.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Bank Accounts --}}
        <div>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-900 tracking-tight">Bank Accounts</h2>
                <button type="button" @click="$dispatch('open-modal', 'add-bank-account')" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-100 rounded-lg hover:bg-blue-100 transition-colors focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Add Account
                </button>
            </div>
            
            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left divide-y divide-gray-200/60">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="py-3 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Account Details</th>
                                <th class="py-3 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Bank Info</th>
                                <th class="py-3 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 text-right w-24">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($bankAccounts as $account)
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="py-4 px-6">
                                        <div class="text-sm font-bold text-gray-900">{{ $account->account_name }}</div>
                                        <div class="text-xs text-gray-500 font-mono mt-0.5">No: {{ $account->account_number }}</div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="text-sm font-medium text-gray-700">{{ $account->bank_name }}</div>
                                        @if($account->swift_code)
                                            <div class="text-xs text-gray-500 font-mono mt-0.5">SWIFT: {{ $account->swift_code }}</div>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <div x-data>
                                            <button @click="$dispatch('open-modal', 'delete-bank-{{ $account->id }}')" class="p-1.5 rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 transition-colors inline-flex" title="Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                            
                                            <x-modal id="delete-bank-{{ $account->id }}" maxWidth="md">
                                                <x-slot:header>Delete Bank Account</x-slot:header>
                                                <div class="text-center py-4 whitespace-normal">
                                                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4 border border-red-200">
                                                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </div>
                                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete {{ $account->account_name }}?</h3>
                                                    <p class="text-sm text-gray-500">This action will permanently delete this bank account. This cannot be undone.</p>
                                                </div>
                                                <x-slot:footer>
                                                    <div class="flex items-center gap-3 w-full justify-end">
                                                        <button type="button" @click="open = false" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">Cancel</button>
                                                        <form method="POST" action="{{ route('admin.finance.settings.bank-accounts.destroy', $account->id) }}" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-xl hover:bg-red-700 transition-colors shadow-sm">Delete Account</button>
                                                        </form>
                                                    </div>
                                                </x-slot:footer>
                                            </x-modal>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-12 px-6 text-center">
                                        <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 border border-gray-100">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                                        </div>
                                        <p class="text-sm text-gray-500 font-medium">No bank accounts configured.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Taxes --}}
        <div class="lg:col-span-2">
            <div class="flex justify-between items-center mb-4 mt-8 lg:mt-4">
                <h2 class="text-lg font-bold text-gray-900 tracking-tight">Taxes</h2>
                <button type="button" @click="$dispatch('open-modal', 'add-tax')" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-100 rounded-lg hover:bg-blue-100 transition-colors focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Add Tax
                </button>
            </div>
            
            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left divide-y divide-gray-200/60">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="py-3 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Tax Name</th>
                                <th class="py-3 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Rate</th>
                                <th class="py-3 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Type</th>
                                <th class="py-3 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 text-right w-24">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($taxes ?? [] as $tax)
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="py-4 px-6 text-sm font-bold text-gray-900">{{ $tax->name }}</td>
                                    <td class="py-4 px-6 text-sm font-medium text-gray-700">{{ $tax->rate }}{{ $tax->type === 'percentage' ? '%' : '' }}</td>
                                    <td class="py-4 px-6 text-sm font-medium text-gray-700 capitalize">
                                        <span class="inline-flex rounded-md px-2 py-0.5 text-xs font-semibold leading-5 bg-gray-100 text-gray-700 border border-gray-200">
                                            {{ $tax->type }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <div x-data>
                                            <button @click="$dispatch('open-modal', 'delete-tax-{{ $tax->id }}')" class="p-1.5 rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 transition-colors inline-flex" title="Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                            
                                            <x-modal id="delete-tax-{{ $tax->id }}" maxWidth="md">
                                                <x-slot:header>Delete Tax Rate</x-slot:header>
                                                <div class="text-center py-4 whitespace-normal">
                                                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4 border border-red-200">
                                                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </div>
                                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete {{ $tax->name }}?</h3>
                                                    <p class="text-sm text-gray-500">This action will permanently delete this tax rate. This cannot be undone.</p>
                                                </div>
                                                <x-slot:footer>
                                                    <div class="flex items-center gap-3 w-full justify-end">
                                                        <button type="button" @click="open = false" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">Cancel</button>
                                                        <form method="POST" action="{{ route('admin.finance.taxes.destroy', $tax->id) }}" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-xl hover:bg-red-700 transition-colors shadow-sm">Delete Tax</button>
                                                        </form>
                                                    </div>
                                                </x-slot:footer>
                                            </x-modal>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-12 px-6 text-center">
                                        <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 border border-gray-100">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                                        </div>
                                        <p class="text-sm text-gray-500 font-medium">No tax rates configured.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Payment Method Modal --}}
    <x-modal id="add-payment-method" maxWidth="md">
        <x-slot:header>Add Payment Method</x-slot:header>
        <form method="POST" action="{{ route('admin.finance.payment-methods.store') }}" class="p-6 space-y-5">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Method Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors min-h-[42px]" placeholder="e.g. Credit Card, Wire Transfer">
            </div>
            
            <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" @click="open = false" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">Cancel</button>
                <button type="submit" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-colors shadow-sm">Save Method</button>
            </div>
        </form>
    </x-modal>

    {{-- Add Bank Account Modal --}}
    <x-modal id="add-bank-account" maxWidth="md">
        <x-slot:header>Add Bank Account</x-slot:header>
        <form method="POST" action="{{ route('admin.finance.settings.bank-accounts.store') }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label for="account_name" class="block text-sm font-medium text-gray-700 mb-1">Account Name <span class="text-red-500">*</span></label>
                <input type="text" name="account_name" id="account_name" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors min-h-[42px]" placeholder="e.g. Primary Checking">
            </div>
            
            <div>
                <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-1">Bank Name <span class="text-red-500">*</span></label>
                <input type="text" name="bank_name" id="bank_name" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors min-h-[42px]" placeholder="e.g. Chase Bank">
            </div>
            
            <div>
                <label for="account_number" class="block text-sm font-medium text-gray-700 mb-1">Account Number <span class="text-red-500">*</span></label>
                <input type="text" name="account_number" id="account_number" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors min-h-[42px]">
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="swift_code" class="block text-sm font-medium text-gray-700 mb-1">SWIFT/BIC Code</label>
                    <input type="text" name="swift_code" id="swift_code" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors min-h-[42px]">
                </div>
                <div>
                    <label for="currency" class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                    <input type="text" name="currency" id="currency" value="RWF" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors min-h-[42px]">
                </div>
            </div>
            
            <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" @click="open = false" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">Cancel</button>
                <button type="submit" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-colors shadow-sm">Save Account</button>
            </div>
        </form>
    </x-modal>

    {{-- Add Tax Modal --}}
    <x-modal id="add-tax" maxWidth="md">
        <x-slot:header>Add Tax Rate</x-slot:header>
        <form method="POST" action="{{ route('admin.finance.taxes.store') }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tax Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors min-h-[42px]" placeholder="e.g. VAT, Sales Tax">
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="rate" class="block text-sm font-medium text-gray-700 mb-1">Rate <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="rate" id="rate" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors min-h-[42px]" placeholder="e.g. 18">
                </div>
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
                    <select name="type" id="type" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors bg-white min-h-[42px]">
                        <option value="percentage">Percentage (%)</option>
                        <option value="fixed">Fixed Amount</option>
                    </select>
                </div>
            </div>
            
            <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" @click="open = false" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">Cancel</button>
                <button type="submit" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-colors shadow-sm">Save Tax</button>
            </div>
        </form>
    </x-modal>
</x-layouts.admin>
