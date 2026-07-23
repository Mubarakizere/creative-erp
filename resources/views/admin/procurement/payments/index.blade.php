<x-layouts.admin title="Supplier Payments">
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Supplier Payments</h1>
        <a href="{{ route('admin.procurement.payments.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
            + Record Payment
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Payment #</th>
                        <th class="px-6 py-4">Supplier</th>
                        <th class="px-6 py-4">Invoice Ref</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $payment->payment_number }}</td>
                            <td class="px-6 py-4">{{ $payment->supplier->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                @if($payment->invoice)
                                    <a href="{{ route('admin.procurement.invoices.show', $payment->invoice->id) }}" class="text-blue-600 hover:underline">
                                        {{ $payment->invoice->invoice_number }}
                                    </a>
                                @else
                                    <span class="text-gray-400">Direct</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">{{ $payment->payment_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 font-medium text-green-600">${{ number_format($payment->amount, 2) }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.procurement.payments.show', $payment->id) }}" class="text-blue-600 hover:text-blue-900">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">No payments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $payments->links() }}
        </div>
    </div>
</div>
</x-layouts.admin>