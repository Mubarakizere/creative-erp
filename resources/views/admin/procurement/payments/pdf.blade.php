<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Supplier Payment {{ $payment->payment_number }}</title>
    <style>
        @page { size: A4 portrait; margin: 38px 44px 44px; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #25313a; font: 10px/1.55 Helvetica, Arial, sans-serif; }
        .topline { height: 4px; background: #263e50; margin-bottom: 24px; }
        .head { width: 100%; border-collapse: collapse; }
        .head td { vertical-align: top; }
        .company { color: #536574; font-size: 10px; font-weight: bold; }
        .muted { color: #6d7982; font-size: 9px; }
        h1 { margin: 0; color: #263e50; font-size: 21px; font-weight: normal; }
        .reference { margin-top: 3px; color: #536574; font-size: 11px; }
        .divider { border-top: 1px solid #cbd2d8; margin: 20px 0 21px; }
        .amount-panel { margin-bottom: 24px; padding: 16px 18px; background: #f1f4f6; border-left: 3px solid #536c5b; }
        .label { color: #7b8790; font-size: 8px; font-weight: bold; letter-spacing: .7px; text-transform: uppercase; }
        .amount { margin-top: 4px; color: #263e50; font-size: 23px; font-weight: bold; }
        .section { margin: 0 0 9px; color: #64727c; font-size: 8px; font-weight: bold; letter-spacing: .8px; text-transform: uppercase; }
        .details { width: 100%; border-collapse: collapse; margin-bottom: 22px; }
        .details td { width: 50%; padding: 0 14px 13px 0; vertical-align: top; }
        .value { margin-top: 3px; color: #26323b; font-size: 10px; }
        .value.strong { font-size: 11px; font-weight: bold; }
        .invoice { margin: 4px 0 20px; border: 1px solid #d9dfe3; border-collapse: collapse; width: 100%; }
        .invoice td { padding: 9px 10px; }
        .invoice td:last-child { text-align: right; white-space: nowrap; }
        .notes { margin-top: 18px; padding: 11px 13px; background: #f6f7f8; border-left: 2px solid #9ba8b0; white-space: pre-line; }
        .footer { position: fixed; bottom: -24px; left: 0; right: 0; padding-top: 7px; border-top: 1px solid #d9dfe3; color: #89939a; font-size: 8px; text-align: center; }
    </style>
</head>
<body>
    <div class="topline"></div>
    <table class="head"><tr>
        <td style="width:62%">
            <div class="company">{{ $payment->company?->name ?? 'Creative Century Engineering' }}</div>
            @if($payment->company?->address)
                <div class="muted">{{ $payment->company->address }}</div>
            @endif
            <div class="muted">info@creativecenturyengineering.com</div>
        </td>
        <td style="text-align:right"><h1>Supplier Payment</h1><div class="reference">{{ $payment->payment_number }}</div></td>
    </tr></table>
    <div class="divider"></div>

    <div class="amount-panel"><div class="label">Amount paid</div><div class="amount">RWF {{ number_format($payment->amount, 2) }}</div></div>
    <div class="section">Payment details</div>
    <table class="details"><tr>
        <td><div class="label">Payment date</div><div class="value strong">{{ $payment->payment_date?->format('d M Y') ?? '—' }}</div></td>
        <td><div class="label">Payment method</div><div class="value strong">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</div></td>
    </tr><tr>
        <td><div class="label">Reference</div><div class="value">{{ $payment->reference ?? $payment->reference_number ?? '—' }}</div></td>
        <td><div class="label">Paid from</div><div class="value">{{ $payment->bankAccount?->account_name ?? '—' }}@if($payment->bankAccount?->bank_name)<br>{{ $payment->bankAccount->bank_name }}@endif</div></td>
    </tr></table>

    <div class="section">Supplier</div>
    <table class="details"><tr>
        <td><div class="value strong">{{ $payment->supplier?->name ?? 'Supplier not available' }}</div>
            @if($payment->supplier?->code)<div class="value">Supplier code: {{ $payment->supplier->code }}</div>@endif
        </td>
        <td><div class="value">{{ $payment->supplier?->email ?? '' }}</div><div class="value">{{ $payment->supplier?->phone ?? '' }}</div></td>
    </tr></table>

    @if($payment->invoice)
        <div class="section">Applied to supplier invoice</div>
        <table class="invoice"><tr><td>{{ $payment->invoice->invoice_number }}</td><td>Invoice total: <strong>RWF {{ number_format($payment->invoice->grand_total, 2) }}</strong></td></tr></table>
    @endif
    @if($payment->notes)
        <div class="section">Notes</div>
        <div class="notes">{{ $payment->notes }}</div>
    @endif

    <div class="footer">{{ $payment->company?->name ?? 'Creative Century Engineering' }} · {{ $payment->payment_number }} · Generated {{ now()->format('d M Y H:i') }}</div>
</body>
</html>
