@extends('layouts.admin')

@section('title', 'Invoice ' . $invoice->invoice_number)

@section('content')
<div class="p-6 max-w-5xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Invoice: {{ $invoice->invoice_number }}</h1>
            <p class="text-gray-500">Supplier: {{ $invoice->supplier->name }} | Date: {{ $invoice->invoice_date->format('M d, Y') }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.procurement.payments.create', ['purchase_invoice_id' => $invoice->id]) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium">
                Record Payment
            </a>
            <a href="{{ route('admin.procurement.invoices.index') }}" class="bg-white border hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg font-medium">
                Back
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <h3 class="text-lg font-medium border-b pb-2 mb-4">Invoice Items</h3>
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500">
                <tr>
                    <th class="px-4 py-2">Product</th>
                    <th class="px-4 py-2">Qty</th>
                    <th class="px-4 py-2">Unit Price</th>
                    <th class="px-4 py-2">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr class="border-b">
                    <td class="px-4 py-3 font-medium">{{ $item->product->name ?? 'Unknown' }}</td>
                    <td class="px-4 py-3">{{ $item->quantity }}</td>
                    <td class="px-4 py-3">${{ number_format($item->unit_price, 2) }}</td>
                    <td class="px-4 py-3">${{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="mt-4 flex justify-end">
            <div class="w-64">
                <div class="flex justify-between py-1"><span class="text-gray-500">Subtotal:</span><span class="font-medium">${{ number_format($invoice->subtotal, 2) }}</span></div>
                <div class="flex justify-between py-1 text-lg font-bold border-t mt-2 pt-2"><span>Grand Total:</span><span>${{ number_format($invoice->grand_total, 2) }}</span></div>
                <div class="flex justify-between py-1 text-green-600 font-medium"><span>Paid:</span><span>${{ number_format($invoice->paid_amount, 2) }}</span></div>
                <div class="flex justify-between py-1 text-red-600 font-bold border-t mt-2 pt-2"><span>Balance Due:</span><span>${{ number_format($invoice->grand_total - $invoice->paid_amount, 2) }}</span></div>
            </div>
        </div>
    </div>

    @if($invoice->payments->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-medium border-b pb-2 mb-4">Payment History</h3>
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500">
                <tr>
                    <th class="px-4 py-2">Date</th>
                    <th class="px-4 py-2">Reference</th>
                    <th class="px-4 py-2">Method</th>
                    <th class="px-4 py-2">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->payments as $payment)
                <tr class="border-b">
                    <td class="px-4 py-3">{{ $payment->payment_date->format('M d, Y') }}</td>
                    <td class="px-4 py-3">{{ $payment->reference_number ?? $payment->payment_number }}</td>
                    <td class="px-4 py-3">{{ ucfirst($payment->payment_method) }}</td>
                    <td class="px-4 py-3 font-medium text-green-600">${{ number_format($payment->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection