@extends('layouts.admin')

@section('title', 'Record Supplier Payment')

@section('content')
<div class="p-6 max-w-3xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900">Record Supplier Payment</h1>
        <a href="{{ route('admin.procurement.payments.index') }}" class="text-gray-500 hover:text-gray-700">Back</a>
    </div>

    <form action="{{ route('admin.procurement.payments.store') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        @csrf
        
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Number</label>
                <input type="text" name="payment_number" value="PAY-{{ time() }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Supplier</label>
                <select name="supplier_id" class="w-full rounded-lg border-gray-300 shadow-sm" required>
                    <option value="">Select Supplier</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}" {{ (isset($invoice) && $invoice->supplier_id == $sup->id) ? 'selected' : '' }}>{{ $sup->name }}</option>
                    @endforeach
                </select>
            </div>
            
            @if(isset($invoice))
            <div class="col-span-2 bg-blue-50 p-4 rounded-lg border border-blue-100">
                <p class="text-sm text-blue-800 font-medium mb-1">Applying to Invoice: {{ $invoice->invoice_number }}</p>
                <div class="flex gap-6 text-sm mt-2">
                    <div><span class="text-blue-600">Grand Total:</span> <span class="font-bold">${{ number_format($invoice->grand_total, 2) }}</span></div>
                    <div><span class="text-blue-600">Paid Amount:</span> <span class="font-bold">${{ number_format($invoice->paid_amount, 2) }}</span></div>
                    <div><span class="text-red-600">Balance Due:</span> <span class="font-bold">${{ number_format($invoice->grand_total - $invoice->paid_amount, 2) }}</span></div>
                </div>
                <input type="hidden" name="purchase_invoice_id" value="{{ $invoice->id }}">
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Date</label>
                <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" class="w-full rounded-lg border-gray-300 shadow-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Amount to Pay</label>
                <input type="number" step="0.01" name="amount" value="{{ isset($invoice) ? ($invoice->grand_total - $invoice->paid_amount) : '' }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                <select name="payment_method" class="w-full rounded-lg border-gray-300 shadow-sm" required>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="cash">Cash</option>
                    <option value="cheque">Cheque</option>
                    <option value="credit_card">Credit Card</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Reference Number</label>
                <input type="text" name="reference_number" class="w-full rounded-lg border-gray-300 shadow-sm placeholder-gray-400" placeholder="e.g. Wire Transfer ID">
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-gray-100">
            <a href="{{ route('admin.procurement.payments.index') }}" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Record Payment</button>
        </div>
    </form>
</div>
@endsection