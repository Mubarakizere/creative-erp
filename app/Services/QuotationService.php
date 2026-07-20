<?php

namespace App\Services;

use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Tax;
use App\Models\Lead;
use App\Models\Opportunity;
use Illuminate\Support\Facades\DB;

class QuotationService
{
    public function createQuotation(array $data, array $items = []): Quotation
    {
        return DB::transaction(function () use ($data, $items) {
            $quotation = Quotation::create($data);
            
            if (!empty($items)) {
                foreach ($items as $itemData) {
                    $quotation->items()->create($itemData);
                }
                $this->calculateTotals($quotation);
            }
            
            return $quotation;
        });
    }

    public function updateQuotation(Quotation $quotation, array $data, array $items = null): Quotation
    {
        return DB::transaction(function () use ($quotation, $data, $items) {
            $quotation->update($data);
            
            if ($items !== null) {
                // Delete existing items not in the new array (if they have IDs)
                $keepIds = array_filter(array_column($items, 'id'));
                $quotation->items()->whereNotIn('id', $keepIds)->delete();
                
                foreach ($items as $itemData) {
                    if (isset($itemData['id'])) {
                        $quotation->items()->where('id', $itemData['id'])->update($itemData);
                    } else {
                        $quotation->items()->create($itemData);
                    }
                }
            }
            
            $this->calculateTotals($quotation);
            return $quotation;
        });
    }

    public function calculateTotals(Quotation $quotation): Quotation
    {
        $subtotal = 0;
        $totalDiscount = 0;
        $totalTax = 0;
        $grandTotal = 0;

        foreach ($quotation->items as $item) {
            $qty = (float) $item->quantity;
            $price = (float) $item->unit_price;
            
            $lineGross = $qty * $price;
            
            // Calculate discount
            $discountAmount = 0;
            if ($item->discount > 0) {
                if ($item->discount_type === 'percentage') {
                    $discountAmount = $lineGross * ($item->discount / 100);
                } else {
                    $discountAmount = $item->discount;
                }
            }
            $totalDiscount += $discountAmount;
            
            $lineNetBeforeTax = $lineGross - $discountAmount;
            
            $taxAmount = 0;
            $itemSubtotal = $lineNetBeforeTax;
            $itemTotal = $lineNetBeforeTax;
            
            if ($item->tax_id) {
                $tax = Tax::find($item->tax_id);
                if ($tax) {
                    $rate = (float) $tax->rate / 100;
                    if ($tax->type === 'inclusive') {
                        $itemTotal = $lineNetBeforeTax;
                        $taxAmount = $itemTotal - ($itemTotal / (1 + $rate));
                        $itemSubtotal = $itemTotal - $taxAmount;
                    } else {
                        $itemSubtotal = $lineNetBeforeTax;
                        $taxAmount = $itemSubtotal * $rate;
                        $itemTotal = $itemSubtotal + $taxAmount;
                    }
                }
            }
            
            // Update item
            $item->update([
                'subtotal' => $itemSubtotal,
                'total' => $itemTotal
            ]);
            
            $subtotal += $itemSubtotal;
            $totalTax += $taxAmount;
            $grandTotal += $itemTotal;
        }

        $quotation->update([
            'subtotal' => $subtotal,
            'total_discount' => $totalDiscount,
            'total_tax' => $totalTax,
            'grand_total' => $grandTotal,
        ]);

        return $quotation;
    }

    public function createFromOpportunity(Opportunity $opportunity): Quotation
    {
        return $this->createQuotation([
            'company_id' => $opportunity->company_id,
            'opportunity_id' => $opportunity->id,
            'account_id' => $opportunity->account_id,
            'contact_id' => $opportunity->contact_id,
            'owner_id' => $opportunity->owner_id,
            'quotation_number' => 'QT-' . strtoupper(uniqid()),
            'currency' => 'USD', // Default or from company settings
            'valid_until' => now()->addDays(30),
            'notes' => $opportunity->description,
        ]);
    }

    public function createFromLead(Lead $lead): Quotation
    {
        return $this->createQuotation([
            'company_id' => $lead->company_id,
            'lead_id' => $lead->id,
            'owner_id' => $lead->owner_id,
            'quotation_number' => 'QT-' . strtoupper(uniqid()),
            'currency' => 'USD',
            'valid_until' => now()->addDays(30),
            'notes' => 'Generated from lead: ' . $lead->first_name . ' ' . $lead->last_name,
        ]);
    }
}
