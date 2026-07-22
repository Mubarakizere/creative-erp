@php
    $reportData = $data->first() ?? [];
@endphp

<table style="width: 100%; border-collapse: collapse; font-family: sans-serif; font-size: 12px;">
    <thead>
        <tr>
            <th colspan="2" style="text-align: left; padding: 8px; border-bottom: 2px solid #000; font-size: 16px;">
                {{ ucwords(str_replace('_', ' ', $type)) }}
            </th>
        </tr>
    </thead>
    <tbody>

    @if($type === 'profit_and_loss')
        <tr><td colspan="2" style="font-weight: bold; padding: 6px;">Revenue</td></tr>
        @foreach($reportData['revenue'] ?? [] as $rev)
            <tr>
                <td style="padding: 4px 16px;">{{ $rev['name'] }}</td>
                <td style="text-align: right; padding: 4px;">{{ number_format($rev['balance'], 2) }}</td>
            </tr>
        @endforeach
        <tr>
            <td style="font-weight: bold; padding: 6px 16px;">Total Revenue</td>
            <td style="font-weight: bold; text-align: right; border-top: 1px solid #ccc; border-bottom: 1px solid #ccc; padding: 6px;">{{ number_format($reportData['total_revenue'] ?? 0, 2) }}</td>
        </tr>

        <tr><td colspan="2" style="font-weight: bold; padding: 6px;">Expenses</td></tr>
        @foreach($reportData['expenses'] ?? [] as $exp)
            <tr>
                <td style="padding: 4px 16px;">{{ $exp['name'] }}</td>
                <td style="text-align: right; padding: 4px;">{{ number_format($exp['balance'], 2) }}</td>
            </tr>
        @endforeach
        <tr>
            <td style="font-weight: bold; padding: 6px 16px;">Total Expenses</td>
            <td style="font-weight: bold; text-align: right; border-top: 1px solid #ccc; border-bottom: 1px solid #ccc; padding: 6px;">{{ number_format($reportData['total_expenses'] ?? 0, 2) }}</td>
        </tr>
        
        <tr>
            <td style="font-weight: bold; padding: 8px; font-size: 14px;">Net Profit / Loss</td>
            <td style="font-weight: bold; text-align: right; padding: 8px; font-size: 14px; border-bottom: 3px double #000;">{{ number_format($reportData['net_profit'] ?? 0, 2) }}</td>
        </tr>

    @elseif($type === 'balance_sheet')
        
        <tr><td colspan="2" style="font-weight: bold; padding: 6px;">Assets</td></tr>
        <tr><td colspan="2" style="font-style: italic; padding: 4px 16px;">Current Assets</td></tr>
        @foreach($reportData['assets']['current']['accounts'] ?? [] as $acc)
            <tr>
                <td style="padding: 4px 32px;">{{ $acc['name'] }}</td>
                <td style="text-align: right; padding: 4px;">{{ number_format($acc['balance'], 2) }}</td>
            </tr>
        @endforeach
        <tr><td colspan="2" style="font-style: italic; padding: 4px 16px;">Fixed Assets</td></tr>
        @foreach($reportData['assets']['fixed']['accounts'] ?? [] as $acc)
            <tr>
                <td style="padding: 4px 32px;">{{ $acc['name'] }}</td>
                <td style="text-align: right; padding: 4px;">{{ number_format($acc['balance'], 2) }}</td>
            </tr>
        @endforeach
        <tr>
            <td style="font-weight: bold; padding: 6px 16px;">Total Assets</td>
            <td style="font-weight: bold; text-align: right; border-top: 1px solid #ccc; border-bottom: 1px solid #ccc; padding: 6px;">{{ number_format($reportData['assets']['total'] ?? 0, 2) }}</td>
        </tr>

        <tr><td colspan="2" style="font-weight: bold; padding: 6px;">Liabilities</td></tr>
        <tr><td colspan="2" style="font-style: italic; padding: 4px 16px;">Current Liabilities</td></tr>
        @foreach($reportData['liabilities']['current']['accounts'] ?? [] as $acc)
            <tr>
                <td style="padding: 4px 32px;">{{ $acc['name'] }}</td>
                <td style="text-align: right; padding: 4px;">{{ number_format($acc['balance'], 2) }}</td>
            </tr>
        @endforeach
        <tr><td colspan="2" style="font-style: italic; padding: 4px 16px;">Long Term Liabilities</td></tr>
        @foreach($reportData['liabilities']['long_term']['accounts'] ?? [] as $acc)
            <tr>
                <td style="padding: 4px 32px;">{{ $acc['name'] }}</td>
                <td style="text-align: right; padding: 4px;">{{ number_format($acc['balance'], 2) }}</td>
            </tr>
        @endforeach
        <tr>
            <td style="font-weight: bold; padding: 6px 16px;">Total Liabilities</td>
            <td style="font-weight: bold; text-align: right; border-top: 1px solid #ccc; border-bottom: 1px solid #ccc; padding: 6px;">{{ number_format($reportData['liabilities']['total'] ?? 0, 2) }}</td>
        </tr>

        <tr><td colspan="2" style="font-weight: bold; padding: 6px;">Equity</td></tr>
        @foreach($reportData['equity']['base']['accounts'] ?? [] as $acc)
            <tr>
                <td style="padding: 4px 32px;">{{ $acc['name'] }}</td>
                <td style="text-align: right; padding: 4px;">{{ number_format($acc['balance'], 2) }}</td>
            </tr>
        @endforeach
        <tr>
            <td style="padding: 4px 32px;">Retained Earnings</td>
            <td style="text-align: right; padding: 4px;">{{ number_format($reportData['equity']['retained_earnings'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; padding: 6px 16px;">Total Equity</td>
            <td style="font-weight: bold; text-align: right; border-top: 1px solid #ccc; border-bottom: 1px solid #ccc; padding: 6px;">{{ number_format($reportData['equity']['total'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; padding: 8px; font-size: 14px;">Total Liabilities & Equity</td>
            <td style="font-weight: bold; text-align: right; padding: 8px; font-size: 14px; border-bottom: 3px double #000;">{{ number_format(($reportData['liabilities']['total'] ?? 0) + ($reportData['equity']['total'] ?? 0), 2) }}</td>
        </tr>

    @elseif($type === 'cash_flow')
        
        <tr><td colspan="2" style="font-weight: bold; padding: 6px;">Operating Activities</td></tr>
        <tr>
            <td style="padding: 4px 16px;">Net Income</td>
            <td style="text-align: right; padding: 4px;">{{ number_format($reportData['operating_activities']['net_income'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td style="padding: 4px 16px;">Changes in Working Capital</td>
            <td style="text-align: right; padding: 4px;">{{ number_format($reportData['operating_activities']['changes_in_working_capital'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; padding: 6px 16px;">Net Cash from Operating Activities</td>
            <td style="font-weight: bold; text-align: right; border-top: 1px solid #ccc; padding: 6px;">{{ number_format($reportData['operating_activities']['total'] ?? 0, 2) }}</td>
        </tr>

        <tr><td colspan="2" style="font-weight: bold; padding: 6px;">Investing Activities</td></tr>
        <tr>
            <td style="font-weight: bold; padding: 6px 16px;">Net Cash from Investing Activities</td>
            <td style="font-weight: bold; text-align: right; border-top: 1px solid #ccc; padding: 6px;">{{ number_format($reportData['investing_activities']['total'] ?? 0, 2) }}</td>
        </tr>

        <tr><td colspan="2" style="font-weight: bold; padding: 6px;">Financing Activities</td></tr>
        <tr>
            <td style="font-weight: bold; padding: 6px 16px;">Net Cash from Financing Activities</td>
            <td style="font-weight: bold; text-align: right; border-top: 1px solid #ccc; padding: 6px;">{{ number_format($reportData['financing_activities']['total'] ?? 0, 2) }}</td>
        </tr>
        
        <tr>
            <td style="font-weight: bold; padding: 8px; font-size: 14px;">Net Cash Flow</td>
            <td style="font-weight: bold; text-align: right; padding: 8px; font-size: 14px; border-top: 2px solid #000;">{{ number_format($reportData['net_cash_flow'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; padding: 8px; font-size: 14px;">Opening Cash Balance</td>
            <td style="font-weight: bold; text-align: right; padding: 8px; font-size: 14px;">{{ number_format($reportData['opening_cash'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; padding: 8px; font-size: 14px;">Closing Cash Balance</td>
            <td style="font-weight: bold; text-align: right; padding: 8px; font-size: 14px; border-bottom: 3px double #000;">{{ number_format($reportData['closing_cash'] ?? 0, 2) }}</td>
        </tr>

    @elseif(in_array($type, ['customer_profitability', 'project_profitability', 'expense_analysis', 'budget_analysis']))
        
        @if($type === 'customer_profitability')
            <tr>
                <th style="text-align:left; border-bottom:1px solid #000; padding:4px;">Customer Name</th>
                <th style="text-align:right; border-bottom:1px solid #000; padding:4px;">Revenue</th>
                <th style="text-align:right; border-bottom:1px solid #000; padding:4px;">Expense</th>
                <th style="text-align:right; border-bottom:1px solid #000; padding:4px;">Profit</th>
                <th style="text-align:right; border-bottom:1px solid #000; padding:4px;">Margin</th>
            </tr>
            @foreach($data as $row)
                <tr>
                    <td style="padding:4px; border-bottom:1px solid #eee;">{{ $row->name }}</td>
                    <td style="padding:4px; border-bottom:1px solid #eee; text-align:right;">{{ number_format($row->total_revenue ?? 0, 2) }}</td>
                    <td style="padding:4px; border-bottom:1px solid #eee; text-align:right;">{{ number_format($row->total_expense ?? 0, 2) }}</td>
                    <td style="padding:4px; border-bottom:1px solid #eee; text-align:right;">{{ number_format($row->profit ?? 0, 2) }}</td>
                    <td style="padding:4px; border-bottom:1px solid #eee; text-align:right;">{{ number_format($row->profit_margin ?? 0, 2) }}%</td>
                </tr>
            @endforeach
            
        @elseif($type === 'project_profitability')
            <tr>
                <th style="text-align:left; border-bottom:1px solid #000; padding:4px;">Project Name</th>
                <th style="text-align:left; border-bottom:1px solid #000; padding:4px;">Client</th>
                <th style="text-align:right; border-bottom:1px solid #000; padding:4px;">Revenue</th>
                <th style="text-align:right; border-bottom:1px solid #000; padding:4px;">Expense</th>
                <th style="text-align:right; border-bottom:1px solid #000; padding:4px;">Profit</th>
                <th style="text-align:right; border-bottom:1px solid #000; padding:4px;">Margin</th>
            </tr>
            @foreach($data as $row)
                <tr>
                    <td style="padding:4px; border-bottom:1px solid #eee;">{{ $row->name }}</td>
                    <td style="padding:4px; border-bottom:1px solid #eee;">{{ $row->client?->name }}</td>
                    <td style="padding:4px; border-bottom:1px solid #eee; text-align:right;">{{ number_format($row->total_revenue ?? 0, 2) }}</td>
                    <td style="padding:4px; border-bottom:1px solid #eee; text-align:right;">{{ number_format($row->total_expense ?? 0, 2) }}</td>
                    <td style="padding:4px; border-bottom:1px solid #eee; text-align:right;">{{ number_format($row->profit ?? 0, 2) }}</td>
                    <td style="padding:4px; border-bottom:1px solid #eee; text-align:right;">{{ number_format($row->profit_margin ?? 0, 2) }}%</td>
                </tr>
            @endforeach

        @elseif($type === 'budget_analysis')
            <tr>
                <th style="text-align:left; border-bottom:1px solid #000; padding:4px;">Budget Name</th>
                <th style="text-align:left; border-bottom:1px solid #000; padding:4px;">Department</th>
                <th style="text-align:right; border-bottom:1px solid #000; padding:4px;">Budget Amount</th>
                <th style="text-align:right; border-bottom:1px solid #000; padding:4px;">Actual Spend</th>
                <th style="text-align:right; border-bottom:1px solid #000; padding:4px;">Variance</th>
                <th style="text-align:right; border-bottom:1px solid #000; padding:4px;">Var %</th>
            </tr>
            @foreach($data as $row)
                <tr>
                    <td style="padding:4px; border-bottom:1px solid #eee;">{{ $row->name }}</td>
                    <td style="padding:4px; border-bottom:1px solid #eee;">{{ $row->department?->name }}</td>
                    <td style="padding:4px; border-bottom:1px solid #eee; text-align:right;">{{ number_format($row->amount ?? 0, 2) }}</td>
                    <td style="padding:4px; border-bottom:1px solid #eee; text-align:right;">{{ number_format($row->actual_amount ?? 0, 2) }}</td>
                    <td style="padding:4px; border-bottom:1px solid #eee; text-align:right;">{{ number_format($row->variance ?? 0, 2) }}</td>
                    <td style="padding:4px; border-bottom:1px solid #eee; text-align:right;">{{ number_format($row->variance_percentage ?? 0, 2) }}%</td>
                </tr>
            @endforeach

        @elseif($type === 'expense_analysis')
            <tr>
                <th style="text-align:left; border-bottom:1px solid #000; padding:4px;">Expense Account</th>
                <th style="text-align:left; border-bottom:1px solid #000; padding:4px;">Department</th>
                <th style="text-align:left; border-bottom:1px solid #000; padding:4px;">Branch</th>
                <th style="text-align:right; border-bottom:1px solid #000; padding:4px;">Total</th>
            </tr>
            @foreach($reportData['expenses'] ?? [] as $row)
                <tr>
                    <td style="padding:4px; border-bottom:1px solid #eee;">{{ $row->chartOfAccount?->name }}</td>
                    <td style="padding:4px; border-bottom:1px solid #eee;">{{ $row->department?->name }}</td>
                    <td style="padding:4px; border-bottom:1px solid #eee;">{{ $row->branch?->name }}</td>
                    <td style="padding:4px; border-bottom:1px solid #eee; text-align:right;">{{ number_format($row->total_expense ?? 0, 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3" style="font-weight:bold; text-align:right; padding:8px;">Grand Total:</td>
                <td style="font-weight:bold; text-align:right; padding:8px;">{{ number_format($reportData['total'] ?? 0, 2) }}</td>
            </tr>
        @endif

    @else
        <tr>
            <td colspan="2">Report type {{ $type }} not fully formatted. Raw data output.</td>
        </tr>
    @endif
    </tbody>
</table>
