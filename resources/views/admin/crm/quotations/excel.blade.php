<table>
    <!-- Company Information -->
    <tr>
        <td colspan="6" style="font-size: 18px; font-weight: bold;">{{ $quotation->company->name ?? 'Creative ERP' }}</td>
    </tr>
    <tr>
        <td colspan="6">{{ str_replace("\n", ", ", $quotation->company->address ?? '') }}</td>
    </tr>
    <tr>
        <td colspan="6"></td>
    </tr>

    <!-- Document Details -->
    <tr>
        <td colspan="3" style="font-weight: bold;">QUOTATION: {{ $quotation->quotation_number }}</td>
        <td colspan="3" align="right">Date: {{ $quotation->created_at->format('M d, Y') }}</td>
    </tr>
    <tr>
        <td colspan="3">Reference: {{ $quotation->reference ?? 'N/A' }}</td>
        <td colspan="3" align="right">Valid Until: {{ \Carbon\Carbon::parse($quotation->valid_until)->format('M d, Y') }}</td>
    </tr>
    <tr>
        <td colspan="6"></td>
    </tr>

    <!-- Customer Details -->
    <tr>
        <td colspan="6" style="font-weight: bold; background-color: #f3f4f6;">Quotation To:</td>
    </tr>
    <tr>
        <td colspan="6">
            @if($quotation->account)
                {{ $quotation->account->name }}
            @elseif($quotation->lead)
                {{ $quotation->lead->first_name }} {{ $quotation->lead->last_name }}
            @elseif($quotation->opportunity)
                {{ $quotation->opportunity->name }}
            @else
                No Customer Specified
            @endif
        </td>
    </tr>
    @if($quotation->contact)
        <tr>
            <td colspan="6">Attn: {{ $quotation->contact->first_name }} {{ $quotation->contact->last_name }}</td>
        </tr>
    @endif
    @if($quotation->account && $quotation->account->billing_address)
        <tr>
            <td colspan="6">{{ str_replace("\n", ", ", $quotation->account->billing_address) }}</td>
        </tr>
    @endif
    <tr>
        <td colspan="6"></td>
    </tr>

    <!-- Line Items -->
    <tr>
        <th style="font-weight: bold; background-color: #f3f4f6;">Product / Service</th>
        <th style="font-weight: bold; background-color: #f3f4f6;" align="center">Quantity</th>
        <th style="font-weight: bold; background-color: #f3f4f6;" align="right">Unit Price</th>
        <th style="font-weight: bold; background-color: #f3f4f6;" align="right">Discount</th>
        <th style="font-weight: bold; background-color: #f3f4f6;" align="right">Tax</th>
        <th style="font-weight: bold; background-color: #f3f4f6;" align="right">Total</th>
    </tr>
    @foreach($quotation->items as $item)
        <tr>
            <td>{{ $item->product_name }}</td>
            <td align="center">{{ $item->quantity }}</td>
            <td align="right">{{ number_format($item->unit_price, 2) }}</td>
            <td align="right">
                @if($item->discount > 0)
                    {{ $item->discount_type === 'percentage' ? $item->discount . '%' : number_format($item->discount, 2) }}
                @else
                    -
                @endif
            </td>
            <td align="right">
                @if($item->tax)
                    {{ $item->tax->name }} ({{ $item->tax->rate }}%)
                @else
                    -
                @endif
            </td>
            <td align="right">{{ number_format($item->total, 2) }}</td>
        </tr>
    @endforeach
    <tr>
        <td colspan="6"></td>
    </tr>

    <!-- Totals -->
    <tr>
        <td colspan="4"></td>
        <td style="font-weight: bold;">Subtotal</td>
        <td align="right">{{ number_format($quotation->subtotal, 2) }}</td>
    </tr>
    @if($quotation->total_discount > 0)
        <tr>
            <td colspan="4"></td>
            <td style="font-weight: bold; color: green;">Total Savings</td>
            <td align="right" style="color: green;">{{ number_format($quotation->total_discount, 2) }}</td>
        </tr>
    @endif
    @if($quotation->total_tax > 0)
        <tr>
            <td colspan="4"></td>
            <td style="font-weight: bold;">Total Tax</td>
            <td align="right">{{ number_format($quotation->total_tax, 2) }}</td>
        </tr>
    @endif
    <tr>
        <td colspan="4"></td>
        <td style="font-weight: bold; font-size: 14px;">Grand Total ({{ $quotation->currency ?? 'USD' }})</td>
        <td align="right" style="font-weight: bold; font-size: 14px;">{{ number_format($quotation->grand_total, 2) }}</td>
    </tr>
    <tr>
        <td colspan="6"></td>
    </tr>

    <!-- Terms and Notes -->
    @if($quotation->paymentTerm)
        <tr>
            <td colspan="6" style="font-weight: bold;">Payment Terms:</td>
        </tr>
        <tr>
            <td colspan="6">{{ $quotation->paymentTerm->name }}</td>
        </tr>
        <tr>
            <td colspan="6"></td>
        </tr>
    @endif

    @if($quotation->notes)
        <tr>
            <td colspan="6" style="font-weight: bold;">Notes:</td>
        </tr>
        <tr>
            <td colspan="6">{{ str_replace("\n", " ", $quotation->notes) }}</td>
        </tr>
        <tr>
            <td colspan="6"></td>
        </tr>
    @endif

    @if($quotation->terms)
        <tr>
            <td colspan="6" style="font-weight: bold;">Terms & Conditions:</td>
        </tr>
        <tr>
            <td colspan="6">{{ str_replace("\n", " ", $quotation->terms) }}</td>
        </tr>
        <tr>
            <td colspan="6"></td>
        </tr>
    @endif

    <!-- Footer -->
    <tr>
        <td colspan="6" align="center" style="color: #666666; font-size: 10px;">
            Generated on {{ now()->format('M d, Y h:i A') }} • Prepared by {{ $quotation->owner->name ?? 'System' }} • {{ $quotation->company->name ?? 'Creative ERP' }}
        </td>
    </tr>
</table>
