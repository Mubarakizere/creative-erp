<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Supplier Quotation {{ $rfq->code }}</title>
    <style>
        @page { size: A4 portrait; margin: 36px 42px 42px; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #26323b; font: 10px/1.5 Helvetica, Arial, sans-serif; }
        .rule { height: 4px; background: #263e50; margin-bottom: 22px; }
        .head, .parties, .meta, .items, .totals { width: 100%; border-collapse: collapse; }
        .head { margin-bottom: 16px; }
        .head td, .parties td { vertical-align: top; }
        .company { color: #536574; font-size: 9px; font-weight: bold; }
        .muted { color: #687580; font-size: 9px; }
        h1 { margin: 0; color: #263e50; font-size: 22px; font-weight: normal; }
        .reference { margin-top: 3px; color: #536574; font-size: 11px; }
        .status { display: inline-block; margin-top: 7px; padding: 3px 8px; border: 1px solid #cbd2d8; color: #536574; font-size: 8px; font-weight: bold; letter-spacing: .5px; text-transform: uppercase; }
        .divider { border-top: 1px solid #cbd2d8; margin: 17px 0 19px; }
        .section { margin: 0 0 8px; color: #64727c; font-size: 8px; font-weight: bold; letter-spacing: .8px; text-transform: uppercase; }
        .parties { margin-bottom: 20px; }
        .parties td { width: 50%; padding-right: 24px; }
        .party-name { margin-bottom: 3px; color: #263e50; font-size: 11px; font-weight: bold; }
        .meta { margin: 0 0 20px; }
        .meta td { width: 33.3%; padding: 0 12px 9px 0; vertical-align: top; }
        .label { color: #7b8790; font-size: 8px; letter-spacing: .4px; text-transform: uppercase; }
        .value { margin-top: 2px; color: #26323b; font-size: 10px; font-weight: bold; }
        .items { margin-bottom: 14px; }
        .items th { padding: 7px 6px; border-top: 1px solid #263e50; border-bottom: 1px solid #cbd2d8; color: #536574; font-size: 8px; text-align: left; text-transform: uppercase; }
        .items td { padding: 8px 6px; border-bottom: 1px solid #e3e8eb; vertical-align: top; }
        .right { text-align: right !important; }
        .center { text-align: center !important; }
        .totals { width: 48%; margin-left: auto; }
        .totals td { padding: 5px 7px; border-bottom: 1px solid #e3e8eb; }
        .totals td:last-child { text-align: right; white-space: nowrap; }
        .totals .grand td { border-top: 1px solid #9da8b0; color: #263e50; font-size: 12px; font-weight: bold; }
        .terms { margin-top: 20px; page-break-inside: avoid; }
        .terms-copy { color: #56636d; white-space: pre-line; }
        .footer { position: fixed; bottom: -22px; left: 0; right: 0; padding-top: 6px; border-top: 1px solid #d9dfe3; color: #8a949c; font-size: 8px; text-align: center; }
        tr { page-break-inside: avoid; }
    </style>
</head>
<body>
    <div class="rule"></div>
    <table class="head"><tr>
        <td style="width:62%">
            <div class="company">{{ $company?->name ?? 'Creative Century Engineering' }}</div>
            @if($company?->address)
                <div class="muted">
                    {{ $company->address }}
                    @if($company->city)
                        , {{ $company->city }}
                    @endif
                </div>
            @endif
            <div class="muted">info@creativecenturyengineering.com</div>
        </td>
        <td style="text-align:right"><h1>Supplier Quotation</h1><div class="reference">{{ $rfq->code }}</div><span class="status">{{ str_replace('_', ' ', $rfq->status) }}</span></td>
    </tr></table>
    <div class="divider"></div>

    <table class="parties"><tr>
        <td>
            <div class="section">Supplier</div>
            <div class="party-name">{{ $rfq->supplier?->name ?? 'Supplier' }}</div>
            <div class="muted">
                @if($rfq->supplier?->code)
                    Code: {{ $rfq->supplier->code }}<br>
                @endif
                @if($rfq->supplier?->email)
                    {{ $rfq->supplier->email }}<br>
                @endif
                @if($rfq->supplier?->phone)
                    {{ $rfq->supplier->phone }}
                @endif
            </div>
        </td>
        <td>
            <div class="section">Request reference</div>
            <div class="party-name">{{ $rfq->purchaseRequisition?->code ?? 'Direct RFQ' }}</div>
            <div class="muted">
                @if($rfq->purchaseRequisition?->requestedBy)
                    Requested by: {{ $rfq->purchaseRequisition->requestedBy->name }}<br>
                @endif
                @if($rfq->project ?? $rfq->purchaseRequisition?->project)
                    Project: {{ ($rfq->project ?? $rfq->purchaseRequisition->project)->name }}
                @endif
            </div>
        </td>
    </tr></table>

    <div class="section">Quotation details</div>
    <table class="meta"><tr>
        <td><div class="label">Issue date</div><div class="value">{{ $rfq->issue_date?->format('d M Y') ?? $rfq->created_at?->format('d M Y') ?? '—' }}</div></td>
        <td><div class="label">Valid until</div><div class="value">{{ ($rfq->valid_until ?? $rfq->expiry_date)?->format('d M Y') ?? '—' }}</div></td>
        <td><div class="label">Lead time</div><div class="value">{{ $rfq->lead_time_days ? $rfq->lead_time_days . ' days' : '—' }}</div></td>
    </tr></table>

    <div class="section">Quoted items ({{ $items->count() }})</div>
    <table class="items"><thead><tr>
        <th style="width:31%">Product / item</th><th class="center" style="width:10%">Qty</th>
        <th class="right" style="width:16%">Unit price</th><th class="right" style="width:14%">Discount</th>
        <th class="right" style="width:12%">Tax</th><th class="right" style="width:17%">Line total</th>
    </tr></thead><tbody>
        @forelse($items as $row)
            <tr>
                <td>
                    {{ $row['item']->product?->name ?? 'Product item' }}
                    @if($row['item']->product?->unit)
                        <br><span class="muted">{{ $row['item']->product->unit->name }}</span>
                    @endif
                </td>
                <td class="center">{{ number_format($row['item']->quantity, 2) }}</td>
                <td class="right">{{ format_currency($row['item']->unit_price, session('currency')) }}</td>
                <td class="right">{{ $row['discount'] > 0 ? '− ' . format_currency($row['discount'], session('currency')) : '—' }}</td>
                <td class="right">{{ $row['tax'] > 0 ? format_currency($row['tax'], session('currency')) : '—' }}</td>
                <td class="right">{{ format_currency($row['line_total'], session('currency')) }}</td>
            </tr>
        @empty
            <tr><td colspan="6">No items recorded for this quotation.</td></tr>
        @endforelse
    </tbody></table>
    <table class="totals"><tr class="grand"><td>Quotation total</td><td>{{ format_currency($grandTotal, session('currency')) }}</td></tr></table>

    @if($rfq->terms || $rfq->notes)
        <div class="terms">
            @if($rfq->terms)
                <div class="section">Terms and conditions</div>
                <div class="terms-copy">{{ $rfq->terms }}</div>
            @endif
            @if($rfq->notes)
                <div class="section" style="margin-top:12px">Notes</div>
                <div class="terms-copy">{{ $rfq->notes }}</div>
            @endif
        </div>
    @endif
    <div class="footer">{{ $company?->name ?? 'Creative Century Engineering' }} · {{ $rfq->code }} · Generated {{ now()->format('d M Y H:i') }}</div>
</body>
</html>
