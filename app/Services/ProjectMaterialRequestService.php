<?php

namespace App\Services;

use App\Models\ProjectMaterialRequest;
use App\Models\ProjectMaterialRequestItem;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ProjectMaterialRequestService
{
    /**
     * Create a new project material request.
     */
    public function create(array $data): ProjectMaterialRequest
    {
        return DB::transaction(function () use ($data) {
            $companyId = $data['company_id'] ?? auth()->user()?->company_id;
            $requestNumber = $this->generateRequestNumber($companyId);

            $request = ProjectMaterialRequest::create([
                'company_id' => $companyId,
                'branch_id' => $data['branch_id'] ?? auth()->user()?->branch_id,
                'project_id' => $data['project_id'],
                'requested_by' => auth()->id(),
                'request_number' => $requestNumber,
                'request_date' => $data['request_date'] ?? now()->toDateString(),
                'required_date' => $data['required_date'] ?? null,
                'purpose' => $data['purpose'] ?? null,
                'priority' => $data['priority'] ?? 'Normal',
                'status' => 'Draft',
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            if (!empty($data['items'])) {
                foreach ($data['items'] as $itemData) {
                    $request->items()->create([
                        'product_id' => $itemData['product_id'],
                        'quantity_requested' => $itemData['quantity_requested'],
                        'notes' => $itemData['notes'] ?? null,
                    ]);
                }
            }

            return $request;
        });
    }

    /**
     * Update an existing project material request.
     */
    public function update(ProjectMaterialRequest $request, array $data): ProjectMaterialRequest
    {
        return DB::transaction(function () use ($request, $data) {
            $request->update([
                'project_id' => $data['project_id'] ?? $request->project_id,
                'request_date' => $data['request_date'] ?? $request->request_date,
                'required_date' => $data['required_date'] ?? $request->required_date,
                'purpose' => $data['purpose'] ?? $request->purpose,
                'priority' => $data['priority'] ?? $request->priority,
                'notes' => $data['notes'] ?? $request->notes,
                'updated_by' => auth()->id(),
            ]);

            if (isset($data['items'])) {
                // Remove existing items and recreate to handle dynamic additions/removals
                $request->items()->delete();
                
                foreach ($data['items'] as $itemData) {
                    $request->items()->create([
                        'product_id' => $itemData['product_id'],
                        'quantity_requested' => $itemData['quantity_requested'],
                        'notes' => $itemData['notes'] ?? null,
                    ]);
                }
            }

            return $request;
        });
    }

    /**
     * Generate a unique request number.
     */
    protected function generateRequestNumber(int $companyId): string
    {
        $year = Carbon::now()->format('Y');
        $prefix = "MR-{$year}-";

        $lastRequest = ProjectMaterialRequest::where('company_id', $companyId)
            ->where('request_number', 'like', "{$prefix}%")
            ->orderBy('request_number', 'desc')
            ->first();

        if (!$lastRequest) {
            return $prefix . '000001';
        }

        $lastNumber = (int) str_replace($prefix, '', $lastRequest->request_number);
        $nextNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);

        return $prefix . $nextNumber;
    }

    /**
     * Submit a request.
     */
    public function submit(ProjectMaterialRequest $request): ProjectMaterialRequest
    {
        if ($request->status !== 'Draft' && $request->status !== 'Rejected') {
            throw new \Exception('Only draft or rejected requests can be submitted.');
        }

        $request->update([
            'status' => 'Submitted',
            'updated_by' => auth()->id(),
        ]);

        return $request;
    }

    /**
     * Approve a request.
     */
    public function approve(ProjectMaterialRequest $request): ProjectMaterialRequest
    {
        if ($request->status !== 'Submitted' && $request->status !== 'Under Review') {
            throw new \Exception('Only submitted or under review requests can be approved.');
        }

        $request->update([
            'status' => 'Approved',
            'updated_by' => auth()->id(),
        ]);

        return $request;
    }

    /**
     * Reject a request.
     */
    public function reject(ProjectMaterialRequest $request, string $reason = null): ProjectMaterialRequest
    {
        if ($request->status !== 'Submitted' && $request->status !== 'Under Review') {
            throw new \Exception('Only submitted or under review requests can be rejected.');
        }

        $request->update([
            'status' => 'Rejected',
            // In a real scenario, we might store the rejection reason in a comment or a specific column
            'updated_by' => auth()->id(),
        ]);

        return $request;
    }

    /**
     * Cancel a request.
     */
    public function cancel(ProjectMaterialRequest $request): ProjectMaterialRequest
    {
        if ($request->status === 'Approved') {
            throw new \Exception('Approved requests cannot be cancelled.');
        }

        $request->update([
            'status' => 'Cancelled',
            'updated_by' => auth()->id(),
        ]);

        return $request;
    }

    /**
     * Delete a request.
     */
    public function delete(ProjectMaterialRequest $request): bool
    {
        if ($request->status !== 'Draft') {
            throw new \Exception('Only draft requests can be deleted.');
        }

        return DB::transaction(function () use ($request) {
            $request->items()->delete();
            return $request->delete();
        });
    }

    /**
     * Convert an approved request to a Purchase Requisition.
     */
    public function convertToPurchaseRequisition(ProjectMaterialRequest $request): PurchaseRequisition
    {
        if ($request->status !== 'Approved') {
            throw new \Exception('Only approved requests can be converted.');
        }

        if ($request->purchaseRequisition()->exists()) {
            throw new \Exception('Request has already been converted to a Purchase Requisition.');
        }

        return DB::transaction(function () use ($request) {
            $year = Carbon::now()->format('Y');
            $prefix = "PR-{$year}-";

            $lastPr = PurchaseRequisition::where('company_id', $request->company_id)
                ->where('code', 'like', "{$prefix}%")
                ->orderBy('code', 'desc')
                ->first();

            $nextNumber = '000001';
            if ($lastPr) {
                $lastNumber = (int) str_replace($prefix, '', $lastPr->code);
                $nextNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
            }
            $code = $prefix . $nextNumber;

            $pr = PurchaseRequisition::create([
                'company_id' => $request->company_id,
                'project_id' => $request->project_id,
                'project_material_request_id' => $request->id,
                'code' => $code,
                'status' => 'draft',
                'priority' => strtolower($request->priority),
                'required_date' => $request->required_date,
                'requested_by' => $request->requested_by,
                'notes' => $request->notes,
                'created_by' => auth()->id() ?? clone $request->requested_by,
                'updated_by' => auth()->id() ?? clone $request->requested_by,
            ]);

            foreach ($request->items as $item) {
                $pr->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity_requested,
                    'description' => $item->notes,
                ]);
            }

            $request->logActivity('converted_to_procurement', [
                'purchase_requisition_id' => $pr->id,
                'purchase_requisition_code' => $pr->code,
            ]);

            return $pr;
        });
    }
}
