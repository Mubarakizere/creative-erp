<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Reports Dashboard</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #1e293b; background: #fff; }

        .page-header {
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 60%, #3b82f6 100%);
            color: #fff; padding: 28px 36px; margin-bottom: 24px;
        }
        .page-header h1 { font-size: 22px; font-weight: 700; letter-spacing: -0.3px; }
        .page-header .sub { font-size: 12px; opacity: .8; margin-top: 4px; }
        .page-header .meta { margin-top: 12px; font-size: 11px; opacity: .75; }

        .section-title {
            font-size: 9px; font-weight: 700; letter-spacing: .08em;
            text-transform: uppercase; color: #94a3b8; margin-bottom: 10px;
        }

        /* KPI grid */
        .kpi-grid { display: table; width: 100%; border-collapse: separate; border-spacing: 8px; margin-bottom: 20px; }
        .kpi-row  { display: table-row; }
        .kpi-cell {
            display: table-cell; width: 25%; background: #f8fafc;
            border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 16px;
            vertical-align: top;
        }
        .kpi-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #64748b; margin-bottom: 6px; }
        .kpi-value { font-size: 16px; font-weight: 700; color: #0f172a; }
        .kpi-sub   { font-size: 9px; color: #94a3b8; margin-top: 3px; }
        .kpi-bar   { height: 3px; background: #e2e8f0; border-radius: 3px; margin-top: 8px; overflow: hidden; }
        .kpi-bar-fill { height: 3px; border-radius: 3px; background: #14b8a6; }

        /* Two-column layout */
        .two-col { display: table; width: 100%; border-collapse: separate; border-spacing: 12px; margin-bottom: 20px; }
        .col-half { display: table-cell; width: 50%; vertical-align: top; }
        .col-twothirds { display: table-cell; width: 65%; vertical-align: top; }
        .col-onethird  { display: table-cell; width: 35%; vertical-align: top; }

        /* Tables */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table thead th {
            background: #f1f5f9; font-size: 9px; font-weight: 700;
            text-transform: uppercase; letter-spacing: .05em; color: #64748b;
            padding: 8px 10px; text-align: left; border-bottom: 1px solid #e2e8f0;
        }
        .data-table tbody td { padding: 8px 10px; border-bottom: 1px solid #f1f5f9; font-size: 10px; color: #334155; }
        .data-table tbody tr:last-child td { border-bottom: none; }
        .data-table tbody tr:nth-child(even) td { background: #fafafa; }

        .card { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px 18px; }
        .card-title { font-size: 11px; font-weight: 700; color: #0f172a; margin-bottom: 12px; }

        .status-badge { display: inline-block; padding: 2px 8px; border-radius: 99px; font-size: 9px; font-weight: 700; text-transform: capitalize; }
        .status-paid   { background: #d1fae5; color: #065f46; }
        .status-overdue{ background: #fee2e2; color: #991b1b; }
        .status-sent   { background: #dbeafe; color: #1e40af; }
        .status-draft  { background: #f1f5f9; color: #475569; }
        .status-other  { background: #fef9c3; color: #78350f; }

        .bar-row { margin-bottom: 8px; }
        .bar-label-row { display: table; width: 100%; margin-bottom: 3px; }
        .bar-name  { display: table-cell; font-size: 10px; color: #475569; }
        .bar-val   { display: table-cell; text-align: right; font-size: 10px; font-weight: 700; color: #0f172a; }
        .bar-track { height: 5px; background: #e2e8f0; border-radius: 3px; overflow: hidden; }
        .bar-fill  { height: 5px; background: #2563eb; border-radius: 3px; }

        .footer { margin-top: 30px; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 12px; }
        .green { color: #059669; font-weight: 700; }
        .red   { color: #dc2626; font-weight: 700; }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="page-header">
        <h1>📊 Reports &amp; Analytics Dashboard</h1>
        <p class="sub">Financial Performance Report</p>
        <p class="meta">
            Period:
            @if($dateFrom || $dateTo)
                {{ $dateFrom ? $dateFrom->format('d M Y') : 'Beginning' }} → {{ $dateTo ? $dateTo->format('d M Y') : 'Now' }}
            @else
                All Time
            @endif
            @if($client)
                &nbsp;·&nbsp; Client: {{ $client->display_name ?? $client->first_name . ' ' . $client->last_name }}
            @endif
            &nbsp;·&nbsp; Generated: {{ now()->format('d M Y, H:i') }}
        </p>
    </div>

    <div style="padding: 0 28px;">

        {{-- KPI CARDS --}}
        <p class="section-title">Key Performance Indicators</p>
        <div class="kpi-grid">
            <div class="kpi-row">
                <div class="kpi-cell">
                    <div class="kpi-label">Total Invoiced</div>
                    <div class="kpi-value">RWF {{ number_format($totalInvoiced) }}</div>
                    <div class="kpi-sub">{{ $invoiceCount }} invoice{{ $invoiceCount != 1 ? 's' : '' }}</div>
                </div>
                <div class="kpi-cell">
                    <div class="kpi-label">Total Received</div>
                    <div class="kpi-value">RWF {{ number_format($totalPaid) }}</div>
                    <div class="kpi-sub">
                        @if($revenueGrowth !== null)
                            <span class="{{ $revenueGrowth >= 0 ? 'green' : 'red' }}">{{ $revenueGrowth >= 0 ? '+' : '' }}{{ $revenueGrowth }}% vs prev</span>
                        @else
                            Payments received
                        @endif
                    </div>
                </div>
                <div class="kpi-cell">
                    <div class="kpi-label">Outstanding</div>
                    <div class="kpi-value">RWF {{ number_format($totalOutstanding) }}</div>
                    <div class="kpi-sub">Balance due</div>
                </div>
                <div class="kpi-cell">
                    <div class="kpi-label">Collection Rate</div>
                    <div class="kpi-value">{{ $collectionRate }}%</div>
                    <div class="kpi-sub">Collected vs invoiced</div>
                    <div class="kpi-bar"><div class="kpi-bar-fill" style="width:{{ min($collectionRate,100) }}%"></div></div>
                </div>
            </div>
            <div class="kpi-row" style="margin-top: 8px;">
                <div class="kpi-cell">
                    <div class="kpi-label">Average Invoice</div>
                    <div class="kpi-value">RWF {{ number_format($avgInvoice) }}</div>
                    <div class="kpi-sub">Per invoice</div>
                </div>
                <div class="kpi-cell">
                    <div class="kpi-label">Invoice Count</div>
                    <div class="kpi-value">{{ number_format($invoiceCount) }}</div>
                    <div class="kpi-sub">Total issued</div>
                </div>
                <div class="kpi-cell">
                    <div class="kpi-label">Overdue Invoices</div>
                    <div class="kpi-value" style="color:#dc2626;">{{ number_format($overdueCount) }}</div>
                    <div class="kpi-sub">Requires attention</div>
                </div>
                <div class="kpi-cell">
                    <div class="kpi-label">Revenue Growth</div>
                    @if($revenueGrowth !== null)
                        <div class="kpi-value {{ $revenueGrowth >= 0 ? 'green' : 'red' }}">{{ $revenueGrowth >= 0 ? '+' : '' }}{{ $revenueGrowth }}%</div>
                        <div class="kpi-sub">vs previous period</div>
                    @else
                        <div class="kpi-value" style="color:#94a3b8;">—</div>
                        <div class="kpi-sub">Select date range</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- INVOICE STATUS + TOP CLIENTS --}}
        <div class="two-col">
            <div class="col-half">
                <div class="card">
                    <div class="card-title">Invoice Status Breakdown</div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th style="text-align:center">Count</th>
                                <th style="text-align:right">Amount (RWF)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoiceStatusData as $row)
                            @php
                                $s = strtolower($row->status);
                                $cls = in_array($s,['paid']) ? 'status-paid' : (in_array($s,['overdue']) ? 'status-overdue' : (in_array($s,['sent']) ? 'status-sent' : (in_array($s,['draft']) ? 'status-draft' : 'status-other')));
                            @endphp
                            <tr>
                                <td><span class="status-badge {{ $cls }}">{{ $row->status }}</span></td>
                                <td style="text-align:center">{{ $row->count }}</td>
                                <td style="text-align:right;font-weight:700;">{{ number_format($row->total) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" style="text-align:center;color:#94a3b8;">No data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-half">
                <div class="card">
                    <div class="card-title">Top 5 Clients by Revenue</div>
                    @php $tt = $topClients->sum('total') ?: 1; @endphp
                    @forelse($topClients as $i => $tc)
                    <div class="bar-row">
                        <div class="bar-label-row">
                            <span class="bar-name">#{{ $i+1 }} {{ $tc->client ? ($tc->client->display_name ?? trim($tc->client->first_name.' '.$tc->client->last_name)) : 'Unknown' }} ({{ $tc->invoice_count }})</span>
                            <span class="bar-val">RWF {{ number_format($tc->total) }}</span>
                        </div>
                        <div class="bar-track"><div class="bar-fill" style="width:{{ round(($tc->total/$tt)*100) }}%"></div></div>
                    </div>
                    @empty
                    <p style="color:#94a3b8;font-size:10px;text-align:center;padding:12px 0;">No client data for this period</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- PAYMENT METHODS --}}
        @if($paymentMethodData->count() > 0)
        <div class="card" style="margin-bottom:20px;">
            <div class="card-title">Payment Methods</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Method</th>
                        <th style="text-align:right">Amount (RWF)</th>
                        <th style="text-align:right">% of Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $pmTotal = $paymentMethodData->sum('total') ?: 1; @endphp
                    @foreach($paymentMethodData as $pm)
                    <tr>
                        <td>{{ $pm->paymentMethod ? $pm->paymentMethod->name : 'Unknown' }}</td>
                        <td style="text-align:right;font-weight:700;">{{ number_format($pm->total) }}</td>
                        <td style="text-align:right;color:#64748b;">{{ round(($pm->total / $pmTotal) * 100, 1) }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- MONTHLY TREND --}}
        <div class="card" style="margin-bottom:20px;">
            <div class="card-title">Monthly Revenue Trend (Last 6 Months)</div>
            <table class="data-table">
                <thead><tr><th>Month</th><th style="text-align:right">Payments Received (RWF)</th></tr></thead>
                <tbody>
                    @foreach($monthlyPayments as $mp)
                    <tr>
                        <td>{{ $mp['label'] }}</td>
                        <td style="text-align:right;font-weight:700;">{{ number_format($mp['total']) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

    <div class="footer">
        Generated by Creative ERP · {{ now()->format('d M Y H:i:s') }} · Reports &amp; Analytics Module
    </div>

</body>
</html>
