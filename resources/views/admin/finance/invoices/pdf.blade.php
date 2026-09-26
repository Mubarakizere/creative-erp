<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        @page { size: A4 portrait; margin: 34px 42px 38px; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #202a35; font: 11px/1.5 "DejaVu Sans", Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        .topline { height: 4px; background: #263e50; margin-bottom: 27px; }
        .masthead { margin-bottom: 25px; }
        .masthead td { vertical-align: top; }
        .company { font-size: 17px; line-height: 1.25; font-weight: bold; color: #263e50; }
        .company-meta { margin-top: 7px; color: #65717c; font-size: 9.5px; line-height: 1.55; }
        .invoice-heading { text-align: right; }
        .invoice-heading h1 { margin: 0 0 4px; color: #263e50; font-size: 25px; line-height: 1.15; font-weight: normal; letter-spacing: 1.2px; }
        .number { color: #536574; font-size: 11px; }
        .rule { border-bottom: 1px solid #cbd2d8; margin-bottom: 19px; }
        .section-label { margin: 0 0 7px; color: #78838c; font-size: 8.5px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; }
        .parties { margin-bottom: 24px; }
        .parties td { width: 50%; vertical-align: top; }
        .parties td + td { padding-left: 28px; }
        .party-name { font-size: 12px; font-weight: bold; color: #263e50; margin-bottom: 3px; }
        .muted { color: #687580; }
        .meta td { padding: 2px 0; }
        .meta td:first-child { color: #78838c; width: 43%; }
        .meta td:last-child { color: #303b45; text-align: right; }
        .items { margin: 0 0 20px; }
        .items thead th { padding: 8px 7px; border-top: 1px solid #263e50; border-bottom: 1px solid #263e50; color: #263e50; font-size: 8px; letter-spacing: .6px; text-align: left; text-transform: uppercase; }
        .items th.num, .items td.num { text-align: right; }
        .items th.qty, .items td.qty { text-align: center; }
        .items tbody td { padding: 9px 7px; border-bottom: 1px solid #e4e8eb; vertical-align: top; }
        .items tbody tr { page-break-inside: avoid; }
        .summary { page-break-inside: avoid; }
        .summary td { vertical-align: top; }
        .payment { width: 55%; padding-right: 28px; }
        .totals { width: 45%; }
        .payment-copy { color: #596671; font-size: 9.5px; line-height: 1.65; }
        .totals td { padding: 4px 0; }
        .totals .label { color: #64717b; }
        .totals .value { text-align: right; white-space: nowrap; }
        .totals .grand td { padding-top: 8px; border-top: 1px solid #9da8b0; color: #263e50; font-size: 12px; font-weight: bold; }
        .totals .paid td { color: #536d5a; }
        .totals .due td { padding: 9px 10px; background: #f0f3f5; border-top: 1px solid #d9dfe3; color: #263e50; font-weight: bold; }
        .totals .due .value { font-size: 13px; }
        .payments { margin-top: 17px; }
        .payments th { padding: 5px 6px; border-bottom: 1px solid #cbd2d8; color: #78838c; font-size: 8px; text-align: left; text-transform: uppercase; }
        .payments td { padding: 5px 6px; border-bottom: 1px solid #e8ebed; color: #56626c; font-size: 9px; }
        .payments th:last-child, .payments td:last-child { text-align: right; }
        .notes { margin-top: 20px; padding-top: 12px; border-top: 1px solid #d9dfe3; page-break-inside: avoid; }
        .note-block { margin-bottom: 9px; }
        .note-title { margin-bottom: 3px; color: #78838c; font-size: 8px; font-weight: bold; letter-spacing: .7px; text-transform: uppercase; }
        .note-body { color: #596671; white-space: pre-line; }
        .thanks { margin-top: 22px; color: #596671; font-size: 9px; page-break-inside: avoid; }
        .footer { position: fixed; bottom: -20px; left: 0; right: 0; padding-top: 7px; border-top: 1px solid #d9dfe3; color: #8a949c; font-size: 8px; text-align: center; }
    </style>
</head>
<body>
    @php
        $company = $invoice->company;
        $client = $invoice->client;
        $clientName = $client ? ($client->company_name ?: ($client->display_name ?: trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? '')))) : 'Valued Client';
        $bank = $bankAccount ?? ($company ? \App\Models\BankAccount::where('company_id', $invoice->company_id)->first() : null);
    @endphp
    <div class="topline"></div>
    <table class="masthead"><tr>
        <td style="width:60%">
            <div class="company">{{ $company->name ?? 'Creative Century Engineering Ltd' }}</div>
            @if($company && $company->legal_name && $company->legal_name !== $company->name)
                <div class="muted">{{ $company->legal_name }}</div>
            @endif
            <div class="company-meta">
                @if($company && $company->address)
                    {{ $company->address }}
                    @if($company->city), {{ $company->city }}@endif
                    @if($company->country), {{ $company->country }}@endif<br>
                @endif
                @if($company && $company->phone){{ $company->phone }}@endif
                @if($company && $company->phone) &nbsp;·&nbsp; @endif
                info@creativecenturyengineering.com
                @if($company && $company->tax_number)<br>TIN {{ $company->tax_number }}@endif
                @if($company && $company->registration_number) &nbsp;·&nbsp; Reg. {{ $company->registration_number }}@endif
            </div>
        </td>
        <td class="invoice-heading"><h1>INVOICE</h1><div class="number">{{ $invoice->invoice_number }}</div></td>
    </tr></table>
    <div class="rule"></div>

    <table class="parties"><tr>
        <td>
            <div class="section-label">Bill to</div>
            <div class="party-name">{{ $clientName }}</div>
            <div class="muted">
                @if($client && $client->company_name && $client->display_name)
                    Attn: {{ $client->display_name }}<br>
                @endif
                @if($client && $client->address)
                    {{ $client->address }}@if($client->city), {{ $client->city }}@endif<br>
                @endif
                @if($client && $client->tax_number)TIN / VAT {{ $client->tax_number }}<br>@endif
                @if($client && $client->email){{ $client->email }}<br>@endif
                @if($client && $client->phone){{ $client->phone }}@endif
            </div>
        </td>
        <td>
            <div class="section-label">Invoice details</div>
            <table class="meta">
                <tr><td>Issue date</td><td>{{ $invoice->issue_date ? $invoice->issue_date->format('d M Y') : now()->format('d M Y') }}</td></tr>
                <tr><td>Due date</td><td>{{ $invoice->due_date ? $invoice->due_date->format('d M Y') : 'Due on receipt' }}</td></tr>
                @if($invoice->project)<tr><td>Project</td><td>{{ $invoice->project->name }}</td></tr>@endif
                @if($invoice->quotation_id)<tr><td>Quotation ref.</td><td>#{{ $invoice->quotation_id }}</td></tr>@endif
                <tr><td>Status</td><td>{{ $invoice->status }}</td></tr>
            </table>
        </td>
    </tr></table>

    <table class="items">
        <thead><tr><th style="width:54%">Description</th><th class="qty" style="width:10%">Qty</th><th class="num" style="width:18%">Unit price (RWF)</th><th class="num" style="width:18%">Amount (RWF)</th></tr></thead>
        <tbody>
            @forelse($invoice->items as $item)
                @php $qty = $item->quantity == intval($item->quantity) ? intval($item->quantity) : number_format($item->quantity, 2); $lineTotal = $item->total_amount ?? ($item->quantity * $item->unit_price); @endphp
                <tr><td>{{ $item->description }}</td><td class="qty">{{ $qty }}</td><td class="num">{{ number_format($item->unit_price) }}</td><td class="num">{{ number_format($lineTotal) }}</td></tr>
            @empty
                <tr><td colspan="4" class="muted">No line items attached to this invoice.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="summary"><tr>
        <td class="payment">
            <div class="section-label">Payment details</div>
            <div class="payment-copy">
                @if($bank)
                    {{ $bank->bank_name }}<br>Account name: {{ $bank->account_name }}<br>Account number: {{ $bank->account_number }}<br>
                    @if($bank->swift_code)SWIFT / BIC: {{ $bank->swift_code }}<br>@endif
                    @if($bank->currency)Currency: {{ $bank->currency }}<br>@endif
                @else
                    Please transfer to {{ $company->name ?? 'Creative Century Engineering' }}.<br>
                @endif
                Payment reference: <strong>{{ $invoice->invoice_number }}</strong>
            </div>
            @if($invoice->allocations && $invoice->allocations->count() > 0)
                <div class="section-label" style="margin-top:16px">Payments received</div>
                <table class="payments"><thead><tr><th>Date</th><th>Reference</th><th>Method</th><th>Amount (RWF)</th></tr></thead><tbody>
                    @foreach($invoice->allocations as $alloc)
                        <tr><td>{{ $alloc->payment && $alloc->payment->payment_date ? $alloc->payment->payment_date->format('d M Y') : '—' }}</td><td>{{ $alloc->payment ? $alloc->payment->reference_number : 'Payment' }}</td><td>{{ $alloc->payment && $alloc->payment->paymentMethod ? $alloc->payment->paymentMethod->name : 'Transfer' }}</td><td>{{ number_format($alloc->amount) }}</td></tr>
                    @endforeach
                </tbody></table>
            @endif
        </td>
        <td class="totals">
            <table>
                <tr><td class="label">Subtotal</td><td class="value">RWF {{ number_format($invoice->subtotal) }}</td></tr>
                @if($invoice->tax_total > 0)<tr><td class="label">Tax / VAT</td><td class="value">RWF {{ number_format($invoice->tax_total) }}</td></tr>@endif
                @if($invoice->discount_total > 0)<tr><td class="label">Discount</td><td class="value">− RWF {{ number_format($invoice->discount_total) }}</td></tr>@endif
                <tr class="grand"><td>Total</td><td class="value">RWF {{ number_format($invoice->total_amount) }}</td></tr>
                @if($invoice->paid_amount > 0)<tr class="paid"><td class="label">Paid</td><td class="value">− RWF {{ number_format($invoice->paid_amount) }}</td></tr>@endif
                <tr class="due"><td>Balance due</td><td class="value">RWF {{ number_format($invoice->balance_due) }}</td></tr>
            </table>
        </td>
    </tr></table>

    @if($invoice->notes || $invoice->terms)
        <div class="notes">
            @if($invoice->notes)<div class="note-block"><div class="note-title">Notes</div><div class="note-body">{{ $invoice->notes }}</div></div>@endif
            @if($invoice->terms)<div class="note-block"><div class="note-title">Terms and conditions</div><div class="note-body">{{ $invoice->terms }}</div></div>@endif
        </div>
    @endif
    <div class="thanks">Thank you for your business. Please include the invoice number with your payment.</div>
    <div class="footer">{{ $company->name ?? 'Creative Century Engineering' }} &nbsp;·&nbsp; {{ $invoice->invoice_number }}</div>
</body>
</html>
