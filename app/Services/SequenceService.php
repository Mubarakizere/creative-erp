<?php

namespace App\Services;

use App\Models\Sequence;
use Illuminate\Support\Facades\DB;

class SequenceService
{
    /**
     * Default configurations for known document types.
     * Can be overridden by the database sequence.
     */
    protected array $defaults = [
        'purchase_order' => ['prefix' => 'PO-', 'padding' => 6],
        'quotation' => ['prefix' => 'QT-', 'padding' => 6],
        'goods_receipt' => ['prefix' => 'GR-', 'padding' => 6],
        'project_material_issue' => ['prefix' => 'PMI-', 'padding' => 6],
        'invoice' => ['prefix' => 'INV-', 'padding' => 6],
        'credit_note' => ['prefix' => 'CN-', 'padding' => 6],
        'journal' => ['prefix' => 'JRN-', 'padding' => 6],
        'payment' => ['prefix' => 'PAY-', 'padding' => 6],
        'refund' => ['prefix' => 'REF-', 'padding' => 6],
        'inventory_transfer' => ['prefix' => 'TRF-', 'padding' => 6],
        'cycle_count' => ['prefix' => 'CC-', 'padding' => 6],
        'packing' => ['prefix' => 'PACK-', 'padding' => 6],
        'picking' => ['prefix' => 'PICK-', 'padding' => 6],
        'shipping' => ['prefix' => 'SHIP-', 'padding' => 6],
        'warehouse_return' => ['prefix' => 'RET-', 'padding' => 6],
        'warehouse_movement' => ['prefix' => 'MOV-', 'padding' => 6],
    ];

    /**
     * Generate the next sequence number for a document type.
     * 
     * @param string $documentType
     * @param int|null $companyId
     * @return string
     */
    public function generate(string $documentType, ?int $companyId = null): string
    {
        $companyId = $companyId ?? auth()->user()->company_id;

        if (!$companyId) {
            throw new \Exception("Company ID is required to generate a sequence for {$documentType}");
        }

        return DB::transaction(function () use ($documentType, $companyId) {
            // Find existing sequence or create one using defaults
            $sequence = Sequence::where('company_id', $companyId)
                ->where('document_type', $documentType)
                ->lockForUpdate()
                ->first();

            if (!$sequence) {
                $defaults = $this->defaults[$documentType] ?? ['prefix' => strtoupper($documentType) . '-', 'padding' => 6];
                
                $sequence = Sequence::create([
                    'company_id' => $companyId,
                    'document_type' => $documentType,
                    'prefix' => $defaults['prefix'],
                    'next_number' => 1,
                    'padding' => $defaults['padding'],
                    'active' => true,
                ]);
            }

            // Generate the string
            $numberStr = str_pad((string)$sequence->next_number, $sequence->padding, '0', STR_PAD_LEFT);
            $generated = $sequence->prefix . $numberStr;

            // Increment and save
            $sequence->next_number++;
            $sequence->save();

            return $generated;
        });
    }

    /**
     * Get all configurable sequences for a company
     */
    public function getSequencesForCompany(int $companyId)
    {
        // First, ensure all default document types have a sequence record
        foreach ($this->defaults as $type => $config) {
            Sequence::firstOrCreate(
                [
                    'company_id' => $companyId,
                    'document_type' => $type
                ],
                [
                    'prefix' => $config['prefix'],
                    'padding' => $config['padding'],
                    'next_number' => 1,
                    'active' => true,
                ]
            );
        }

        return Sequence::where('company_id', $companyId)->get();
    }
}
