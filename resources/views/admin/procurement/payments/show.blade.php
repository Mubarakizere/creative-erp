<x-layouts.admin :title="'Payment ' . $payment->payment_number">
<div class="p-6 max-w-4xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Payment: {{ $payment->payment_number }}</h1>
            <p class="text-gray-500">Date: {{ $payment->payment_date->format('M d, Y') }}</p>
        </div>
        <a href="{{ route('admin.procurement.payments.index') }}" class="bg-white border hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg font-medium">
            Back
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="grid grid-cols-2 gap-8">
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Supplier</h3>
                <p class="text-lg font-medium text-gray-900">{{ $payment->supplier->name ?? 'N/A' }}</p>
                
                <h3 class="text-sm font-medium text-gray-500 mt-6 mb-1">Payment Method</h3>
                <p class="text-md text-gray-900">{{ ucfirst($payment->payment_method) }}</p>
                
                <h3 class="text-sm font-medium text-gray-500 mt-6 mb-1">Reference Number</h3>
                <p class="text-md text-gray-900">{{ $payment->reference_number ?? 'N/A' }}</p>
            </div>
            <div>
                <div class="bg-gray-50 p-6 rounded-xl border border-gray-100 text-center">
                    <p class="text-sm font-medium text-gray-500 mb-2">Amount Paid</p>
                    <p class="text-4xl font-bold text-green-600">${{ number_format($payment->amount, 2) }}</p>
                </div>
                
                @if($payment->invoice)
                <div class="mt-6 border-t border-gray-100 pt-4">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Applied To Invoice</h3>
                    <a href="{{ route('admin.procurement.invoices.show', $payment->invoice->id) }}" class="flex items-center p-3 bg-blue-50 border border-blue-100 rounded-lg hover:bg-blue-100 transition-colors">
                        <i data-lucide="file-text" class="w-5 h-5 text-blue-600 mr-3"></i>
                        <div>
                            <p class="font-medium text-blue-900">{{ $payment->invoice->invoice_number }}</p>
                            <p class="text-xs text-blue-700">Total: ${{ number_format($payment->invoice->grand_total, 2) }}</p>
                        </div>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
</x-layouts.admin>