<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        h1 {
            font-size: 20px;
            margin-bottom: 5px;
        }
        p {
            margin: 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #555;
            text-transform: uppercase;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background-color: #fbfbfb;
        }
        .text-right {
            text-align: right;
        }
        .font-bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name', 'ERP') }}</h1>
        <p>{{ $title }}</p>
        <p style="font-size: 10px; margin-top: 5px;">Generated on {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th class="{{ str_contains($header, 'Value') || str_contains($header, 'Cost') || str_contains($header, 'Qty') || str_contains($header, 'Quantity') || str_contains($header, 'Profit') || str_contains($header, 'Margin') || str_contains($header, 'Items') ? 'text-right' : '' }}">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    @foreach($headers as $header)
                        <td class="{{ str_contains($header, 'Value') || str_contains($header, 'Cost') || str_contains($header, 'Qty') || str_contains($header, 'Quantity') || str_contains($header, 'Profit') || str_contains($header, 'Margin') || str_contains($header, 'Items') ? 'text-right' : '' }} {{ $row[$header] === 'TOTAL' ? 'font-bold' : '' }}">
                            {{ $row[$header] ?? '' }}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
