<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $title }} {{ $reference }}</title>
    <style>
        @page { margin: 38px 42px 44px; }
        body { color: #26323b; font: 10px/1.5 Helvetica, Arial, sans-serif; }
        .top { border-top: 4px solid #263e50; padding-top: 18px; }
        .brand { color: #536574; font-size: 9px; margin-bottom: 28px; }
        .heading { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        .heading td { vertical-align: top; }
        h1 { color: #263e50; font-size: 22px; font-weight: normal; margin: 0 0 3px; }
        .ref { color: #687580; font-size: 11px; }
        .rule { border-bottom: 1px solid #cbd2d8; margin: 15px 0 20px; }
        .details { width: 100%; border-collapse: collapse; margin-bottom: 22px; }
        .details td { padding: 4px 8px 4px 0; width: 50%; vertical-align: top; }
        .label { color: #78838c; font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: .6px; }
        .value { color: #26323b; margin-top: 2px; }
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        .data th { background: #f1f4f6; border-top: 1px solid #bfc9d0; border-bottom: 1px solid #bfc9d0; color: #536574; font-size: 8px; padding: 7px; text-align: left; }
        .data td { border-bottom: 1px solid #e3e8eb; padding: 7px; vertical-align: top; }
        .summary { width: 48%; margin-left: auto; border-collapse: collapse; }
        .summary td { border-bottom: 1px solid #e3e8eb; padding: 6px; }
        .summary td:last-child { text-align: right; font-weight: bold; }
        .notes { margin-top: 20px; white-space: pre-line; }
        .footer { position: fixed; bottom: -24px; left: 0; right: 0; border-top: 1px solid #d9dfe3; color: #8a949c; font-size: 8px; padding-top: 7px; text-align: center; }
    </style>
</head>
<body>
    <div class="top">
        @if($companyName)<div class="brand">{{ $companyName }}</div>@endif
        <table class="heading"><tr><td><h1>{{ $title }}</h1><div class="ref">{{ $reference }}</div></td><td style="text-align:right;color:#78838c">{{ now()->format('d M Y') }}</td></tr></table>
        <div class="rule"></div>
        @if(count($details))
            <table class="details"><tr>
                @foreach($details as $label => $value)
                    <td><div class="label">{{ $label }}</div><div class="value">{{ filled($value) ? $value : '—' }}</div></td>
                    @if($loop->iteration % 2 === 0 && !$loop->last)</tr><tr>@endif
                @endforeach
            </tr></table>
        @endif
        @if(count($columns))
            <table class="data"><thead><tr>@foreach($columns as $column)<th>{{ $column['label'] }}</th>@endforeach</tr></thead><tbody>
                @forelse($rows as $row)<tr>@foreach($columns as $column)<td>{{ data_get($row, $column['key']) ?? '—' }}</td>@endforeach</tr>@empty<tr><td colspan="{{ count($columns) }}">No line items.</td></tr>@endforelse
            </tbody></table>
        @endif
        @if(count($summary))
            <table class="summary">@foreach($summary as $label => $value)<tr><td>{{ $label }}</td><td>{{ $value }}</td></tr>@endforeach</table>
        @endif
        @if(!empty($details['Notes']))<div class="notes"><div class="label">Notes</div>{{ $details['Notes'] }}</div>@endif
    </div>
    <div class="footer">{{ $companyName ?? 'Creative Century Engineering' }} · {{ $reference }}</div>
</body>
</html>
