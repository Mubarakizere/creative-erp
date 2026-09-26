<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Purchase Requisition {{ $requisition->code }}</title>
    <style>
        @page { size: A4 portrait; margin: 36px 42px 42px; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #26323b; font: 10px/1.5 Helvetica, Arial, sans-serif; }
        .rule { height: 4px; background: #263e50; margin-bottom: 22px; }
        .head { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .head td { vertical-align: top; }
        .company { color: #536574; font-size: 9px; font-weight: bold; letter-spacing: .3px; }
        h1 { margin: 0; color: #263e50; font-size: 21px; font-weight: normal; }
        .code { margin-top: 3px; color: #536574; font-size: 11px; }
        .status { display: inline-block; margin-top: 7px; padding: 3px 8px; border: 1px solid #d3dbe0; color: #536574; font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: .6px; }
        .status.approved { border-color: #a8c4ae; color: #42654a; }
        .status.rejected, .status.cancelled { border-color: #d9b3b3; color: #8a4b4b; }
        .divider { border-top: 1px solid #cbd2d8; margin: 17px 0 18px; }
        .section-title { margin: 0 0 8px; color: #64727c; font-size: 8px; font-weight: bold; letter-spacing: .8px; text-transform: uppercase; }
        .details { width: 100%; border-collapse: collapse; margin-bottom: 19px; }
        .details td { width: 33.3%; padding: 0 12px 10px 0; vertical-align: top; }
        .label { color: #7b8790; font-size: 8px; text-transform: uppercase; letter-spacing: .45px; }
        .value { margin-top: 2px; color: #26323b; font-size: 10px; font-weight: bold; }
        .subvalue { color: #687580; font-size: 9px; }
        .items, .quotes { width: 100%; border-collapse: collapse; margin: 0 0 20px; }
        .items th, .quotes th { padding: 7px 6px; border-top: 1px solid #263e50; border-bottom: 1px solid #cbd2d8; color: #536574; font-size: 8px; text-align: left; text-transform: uppercase; letter-spacing: .35px; }
        .items td, .quotes td { padding: 8px 6px; border-bottom: 1px solid #e3e8eb; vertical-align: top; }
        .right { text-align: right !important; }
        .notes { margin-top: 16px; padding: 10px 12px; border-left: 2px solid #8093a0; background: #f5f7f8; white-space: pre-line; }
        .footer { position: fixed; bottom: -22px; left: 0; right: 0; padding-top: 6px; border-top: 1px solid #d9dfe3; color: #8a949c; font-size: 8px; text-align: center; }
        .keep { page-break-inside: avoid; }
    </style>
</head>
<body>
    <div class="rule"></div>
    <table class="head"><tr>
        <td style="width:66%">
            <div class="company">{{ $requisition->company?->name ?? 'Creative Century Engineering' }}</div>
            @if($requisition->company?->address)
                <div class="subvalue">
                    {{ $requisition->company->address }}
                    @if($requisition->company->city)
                        , {{ $requisition->company->city }}
                    @endif
                </div>
            @endif
            <div class="subvalue">
                info@creativecenturyengineering.com
                @if($requisition->company?->phone)
                    · {{ $requisition->company->phone }}
                @endif
            </div>
        </td>
        <td style="text-align:right"><h1>Purchase Requisition</h1><div class="code">{{ $requisition->code }}</div><span class="status {{ strtolower($requisition->status) }}">{{ str_replace('_', ' ', $requisition->status) }}</span></td>
    </tr></table>

    <div class="divider"></div>
    <div class="section-title">Request details</div>
    <table class="details"><tr>
        <td><div class="label">Requested by</div><div class="value">{{ $requisition->requestedBy?->name ?? 'System Staff' }}</div><div class="subvalue">{{ $requisition->requestedBy?->email ?? '' }}</div></td>
        <td><div class="label">Company</div><div class="value">{{ $requisition->company?->name ?? 'General' }}</div></td>
        <td><div class="label">Priority</div><div class="value">{{ ucfirst($requisition->priority ?? 'normal') }}</div></td>
    </tr><tr>
        <td><div class="label">Project / scope</div><div class="value">{{ $requisition->project?->name ?? $requisition->department?->name ?? 'General Procurement' }}</div><div class="subvalue">{{ $requisition->project?->project_code ?? ($requisition->department ? 'Internal Department' : '') }}</div></td>
        <td><div class="label">Created</div><div class="value">{{ $requisition->created_at?->format('d M Y') ?? '—' }}</div></td>
        <td><div class="label">Required by</div><div class="value">{{ $requisition->required_date?->format('d M Y') ?? 'As soon as possible' }}</div></td>
    </tr>
    @if($requisition->projectMaterialRequest)
        <tr><td colspan="3"><div class="label">Source material request</div><div class="value">{{ $requisition->projectMaterialRequest->request_number }} <span class="subvalue">· {{ ucfirst($requisition->projectMaterialRequest->status) }}</span></div></td></tr>
    @endif
    </table>

    <div class="keep">
        <div class="section-title">Requested items ({{ $requisition->items->count() }})</div>
        <table class="items"><thead><tr><th style="width:27%">Product / SKU</th><th style="width:15%" class="right">Quantity</th><th>Description / specification</th></tr></thead><tbody>
            @forelse($requisition->items as $item)
                <tr><td><strong>{{ $item->product?->name ?? 'Unknown product' }}</strong><br><span class="subvalue">SKU: {{ $item->product?->sku ?? '—' }}</span></td><td class="right">{{ number_format($item->quantity, 2) }} {{ $item->product?->unit?->name ?? $item->product?->unit?->code ?? 'Unit' }}</td><td>{{ $item->description ?: '—' }}</td></tr>
            @empty
                <tr><td colspan="3">No items listed in this requisition.</td></tr>
            @endforelse
        </tbody></table>
    </div>

    @if($requisition->quotations->isNotEmpty())
        <div class="keep">
            <div class="section-title">Supplier quotations received ({{ $requisition->quotations->count() }})</div>
            <table class="quotes"><thead><tr><th>Supplier</th><th>Quotation</th><th class="right">Quoted total</th><th>Status</th></tr></thead><tbody>
                @foreach($requisition->quotations as $quote)
                    <tr><td>{{ $quote->supplier?->name ?? 'Supplier' }}</td><td>{{ $quote->code }}</td><td class="right">{{ format_currency($quote->pdf_total, session('currency')) }}</td><td>{{ ucfirst($quote->status) }}</td></tr>
                @endforeach
            </tbody></table>
        </div>
    @endif

    @if($requisition->notes)
        <div class="section-title">Requisition notes &amp; justification</div>
        <div class="notes">{{ $requisition->notes }}</div>
    @endif
    <div class="footer">{{ $requisition->company?->name ?? 'Creative Century Engineering' }} · {{ $requisition->code }} · Generated {{ now()->format('d M Y H:i') }}</div>
</body>
</html>
