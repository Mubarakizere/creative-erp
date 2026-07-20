<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation {{ $quotation->quotation_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 14px;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .header {
            width: 100%;
            margin-bottom: 40px;
        }
        .header td {
            vertical-align: top;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb; /* Tailwind blue-600 */
        }
        .company-details {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .document-title {
            font-size: 32px;
            font-weight: bold;
            color: #111827;
            text-transform: uppercase;
            text-align: right;
            margin: 0;
        }
        .quotation-number {
            font-size: 14px;
            color: #6b7280;
            text-align: right;
            margin-top: 5px;
        }
        
        .info-section {
            width: 100%;
            margin-bottom: 40px;
            border-top: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
            padding: 20px 0;
        }
        .info-section td {
            vertical-align: top;
            width: 50%;
        }
        .info-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #9ca3af;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 14px;
            font-weight: bold;
            color: #111827;
            margin: 0;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        .items-table th {
            background-color: #f9fafb;
            padding: 12px;
            text-align: left;
            font-size: 12px;
            color: #374151;
            text-transform: uppercase;
            border-bottom: 2px solid #e5e7eb;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 14px;
        }
        .text-right {
            text-align: right !important;
        }
        .text-center {
            text-align: center !important;
        }
        
        .totals-section {
            width: 100%;
            margin-bottom: 40px;
        }
        .totals-table {
            width: 40%;
            float: right;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 8px 0;
            font-size: 14px;
        }
        .grand-total {
            border-top: 2px solid #e5e7eb;
            font-size: 18px;
            font-weight: bold;
            padding-top: 12px !important;
        }
        
        .notes-section {
            clear: both;
            margin-top: 60px;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
        .notes-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .notes-content {
            font-size: 12px;
            color: #4b5563;
        }
        
        .footer {
            position: absolute;
            bottom: 30px;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <table class="header">
        <tr>
            <td>
                <div class="company-name">{{ $quotation->company->name ?? 'Creative ERP' }}</div>
                @if(isset($quotation->company->address))
                    <div class="company-details">
                        {!! nl2br(e($quotation->company->address)) !!}
                    </div>
                @endif
            </td>
            <td>
                <h1 class="document-title">Quotation</h1>
                <div class="quotation-number">{{ $quotation->quotation_number }}</div>
            </td>
        </tr>
    </table>

    <table class="info-section">
        <tr>
            <td>
                <div class="info-label">Quotation To:</div>
                <div class="info-value">
                    @if($quotation->account)
                        {{ $quotation->account->name }}
                    @elseif($quotation->lead)
                        {{ $quotation->lead->first_name }} {{ $quotation->lead->last_name }}
                    @elseif($quotation->opportunity)
                        {{ $quotation->opportunity->name }}
                    @else
                        No Customer Specified
                    @endif
                </div>
                
                @if($quotation->contact)
                    <div style="font-size: 12px; color: #4b5563; margin-top: 5px;">
                        Attn: {{ $quotation->contact->first_name }} {{ $quotation->contact->last_name }}
                    </div>
                @endif
                
                @if($quotation->account && $quotation->account->billing_address)
                    <div style="font-size: 12px; color: #4b5563; margin-top: 5px;">
                        {!! nl2br(e($quotation->account->billing_address)) !!}
                    </div>
                @endif
            </td>
            <td>
                <table style="width: 100%;">
                    <tr>
                        <td style="padding-bottom: 10px;">
                            <div class="info-label">Date</div>
                            <div class="info-value">{{ $quotation->created_at->format('M d, Y') }}</div>
                        </td>
                        <td style="padding-bottom: 10px;">
                            <div class="info-label">Valid Until</div>
                            <div class="info-value">{{ \Carbon\Carbon::parse($quotation->valid_until)->format('M d, Y') }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="info-label">Prepared By</div>
                            <div class="info-value">{{ $quotation->owner->name ?? 'System' }}</div>
                        </td>
                        <td>
                            @if($quotation->paymentTerm)
                                <div class="info-label">Payment Terms</div>
                                <div class="info-value">{{ $quotation->paymentTerm->name }}</div>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Product / Service</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Discount</th>
                <th class="text-right">Tax</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotation->items as $item)
                <tr>
                    <td><strong>{{ $item->product_name }}</strong></td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">${{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">
                        @if($item->discount > 0)
                            {{ $item->discount_type === 'percentage' ? $item->discount . '%' : '$' . number_format($item->discount, 2) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">
                        @if($item->tax)
                            {{ $item->tax->name }} ({{ $item->tax->rate }}%)
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right"><strong>${{ number_format($item->total, 2) }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-section">
        <table class="totals-table">
            <tr>
                <td>Subtotal</td>
                <td class="text-right">${{ number_format($quotation->subtotal, 2) }}</td>
            </tr>
            @if($quotation->total_discount > 0)
                <tr>
                    <td style="color: #16a34a;">Total Savings</td>
                    <td class="text-right" style="color: #16a34a;">${{ number_format($quotation->total_discount, 2) }}</td>
                </tr>
            @endif
            @if($quotation->total_tax > 0)
                <tr>
                    <td>Total Tax</td>
                    <td class="text-right">${{ number_format($quotation->total_tax, 2) }}</td>
                </tr>
            @endif
            <tr>
                <td class="grand-total">Grand Total</td>
                <td class="text-right grand-total">${{ number_format($quotation->grand_total, 2) }}</td>
            </tr>
            <tr>
                <td colspan="2" class="text-right" style="font-size: 10px; color: #9ca3af; padding-top: 5px;">
                    All amounts in {{ $quotation->currency ?? 'USD' }}
                </td>
            </tr>
        </table>
    </div>

    @if($quotation->notes || $quotation->terms)
        <div class="notes-section">
            <table style="width: 100%;">
                <tr>
                    @if($quotation->notes)
                        <td style="width: 50%; padding-right: 20px; vertical-align: top;">
                            <div class="notes-title">Notes</div>
                            <div class="notes-content">
                                {!! nl2br(e($quotation->notes)) !!}
                            </div>
                        </td>
                    @endif
                    
                    @if($quotation->terms)
                        <td style="width: 50%; vertical-align: top;">
                            <div class="notes-title">Terms & Conditions</div>
                            <div class="notes-content">
                                {!! nl2br(e($quotation->terms)) !!}
                            </div>
                        </td>
                    @endif
                </tr>
            </table>
        </div>
    @endif

    <div class="footer">
        Generated on {{ now()->format('M d, Y h:i A') }} • {{ $quotation->company->name ?? 'Creative ERP' }}
    </div>

</body>
</html>
