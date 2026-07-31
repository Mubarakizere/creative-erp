<x-layouts.admin title="Edit Supplier Payment">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Procurement', 'url' => route('admin.procurement.pos.index')],
                ['label' => 'Supplier Payments', 'url' => route('admin.procurement.payments.index')],
                ['label' => $payment->payment_number, 'url' => route('admin.procurement.payments.show', $payment->id)],
                ['label' => 'Edit'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    @can('update', $payment)
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.procurement.payments.show', $payment->id) }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 mb-2 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Payment
            </a>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Payment: {{ $payment->payment_number }}</h1>
            <p class="mt-1 text-sm text-gray-500 font-medium">Update logged payment details.</p>
        </div>
    </div>

    <div class="mt-6">
        <form action="{{ route('admin.procurement.payments.update', $payment->id) }}" method="POST" id="payment-form">
            @csrf
            @method('PUT')
            
            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden mb-6">
                <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
                    <h3 class="text-lg font-bold text-gray-900 tracking-tight">Payment Details</h3>
                </div>
                
                @if($payment->invoice)
                <div class="px-6 py-4 bg-blue-50/50 border-b border-blue-100 flex items-start gap-3">
                    <div class="bg-blue-100 text-blue-600 p-2 rounded-lg shrink-0 mt-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-blue-900">Applied to Invoice: {{ $payment->invoice->invoice_number }}</p>
                        <input type="hidden" name="purchase_invoice_id" value="{{ $payment->purchase_invoice_id }}">
                    </div>
                </div>
                @endif
                
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="col-span-1 lg:col-span-2">
                            <label for="payment_number" class="block text-sm font-medium text-gray-700 mb-1">Payment Number <span class="text-red-500">*</span></label>
                            <input type="text" name="payment_number" id="payment_number" value="{{ old('payment_number', $payment->payment_number) }}" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">
                            @error('payment_number') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="col-span-1 lg:col-span-2">
                            <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-1">Supplier <span class="text-red-500">*</span></label>
                            @if($payment->invoice)
                                <input type="hidden" name="supplier_id" value="{{ $payment->supplier_id }}">
                                <input type="text" value="{{ $payment->supplier?->name }}" readonly class="block w-full rounded-xl border-gray-200 bg-gray-50 text-gray-500 sm:text-sm">
                            @else
                                <select id="supplier_id" name="supplier_id" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors bg-white">
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers ?? \App\Models\Supplier::all() as $sup)
                                        <option value="{{ $sup->id }}" {{ old('supplier_id', $payment->supplier_id) == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                                    @endforeach
                                </select>
                            @endif
                            @error('supplier_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        
                        <div class="col-span-1 sm:col-span-1 lg:col-span-2">
                            <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-1">Payment Date <span class="text-red-500">*</span></label>
                            <input type="date" name="payment_date" id="payment_date" value="{{ old('payment_date', $payment->payment_date->format('Y-m-d')) }}" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">
                            @error('payment_date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="col-span-1 sm:col-span-1 lg:col-span-2">
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount Paid <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-gray-500 sm:text-sm">RWF</span>
                                </div>
                                <input type="number" step="0.01" name="amount" id="amount" value="{{ old('amount', $payment->amount) }}" required class="block w-full rounded-xl border-gray-300 pl-12 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">
                            </div>
                            @error('amount') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="col-span-1 lg:col-span-2">
                            <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Payment Method <span class="text-red-500">*</span></label>
                            <select id="payment_method" name="payment_method" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors bg-white">
                                <option value="bank_transfer" {{ old('payment_method', $payment->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="cash" {{ old('payment_method', $payment->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="cheque" {{ old('payment_method', $payment->payment_method) == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="credit_card" {{ old('payment_method', $payment->payment_method) == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                            </select>
                            @error('payment_method') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="col-span-1 lg:col-span-2">
                            <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-1">Reference Number</label>
                            <input type="text" name="reference_number" id="reference_number" value="{{ old('reference_number', $payment->reference_number) }}" placeholder="e.g. Wire Transfer ID" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">
                            @error('reference_number') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden px-6 py-4 flex items-center justify-end gap-3 mb-8">
                <a href="{{ route('admin.procurement.payments.show', $payment->id) }}" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">Cancel</a>
                <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none hover:shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Update Payment
                </button>
            </div>
        </form>
    </div>
    @else
    <div class="text-center py-16 bg-white rounded-2xl border border-gray-200/60 shadow-sm">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4 border border-red-200">
            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Access Denied</h3>
        <p class="text-sm text-gray-500 font-medium">You do not have permission to edit payments.</p>
        <div class="mt-6">
            <a href="{{ route('admin.procurement.payments.show', $payment->id) }}" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all">Return to Payment</a>
        </div>
    </div>
    @endcan
</x-layouts.admin>
