<?php

namespace App\Services\Asset;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetCategory;
use App\Models\AssetTransfer;
use App\Services\WorkflowService;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\DB;
use Exception;

class AssetService
{
    protected AssetAccountingService $accountingService;
    protected WorkflowService $workflowService;

    public function __construct(AssetAccountingService $accountingService, WorkflowService $workflowService)
    {
        $this->accountingService = $accountingService;
        $this->workflowService = $workflowService;
    }

    public function createCategory(array $data): AssetCategory
    {
        $category = AssetCategory::create($data);
        return $category;
    }

    public function createAsset(array $data, bool $autoCapitalize = false): Asset
    {
        return DB::transaction(function () use ($data, $autoCapitalize) {
            $data['status'] = $data['status'] ?? 'Draft';
            $data['current_book_value'] = $data['purchase_cost'];
            
            $asset = Asset::create($data);

            if ($autoCapitalize && $asset->purchase_cost > 0 && $asset->in_service_date) {
                $asset->update(['status' => 'Active']);
                $this->accountingService->postCapitalization($asset, $asset->purchase_cost);
            }
            
            // Auto-assign if assigned_user_id is provided
            if (!empty($data['assigned_user_id'])) {
                $this->assignAsset($asset, [
                    'user_id' => $data['assigned_user_id'],
                    'department_id' => $data['department_id'] ?? null,
                    'branch_id' => $data['branch_id'] ?? null,
                    'notes' => 'Initial assignment',
                ]);
            }

            return $asset;
        });
    }

    public function assignAsset(Asset $asset, array $data): AssetAssignment
    {
        return DB::transaction(function () use ($asset, $data) {
            // Return previous active assignments
            $asset->assignments()->whereNull('returned_at')->update(['returned_at' => now()]);
            
            $assignment = $asset->assignments()->create([
                'user_id' => $data['user_id'] ?? null,
                'department_id' => $data['department_id'] ?? null,
                'branch_id' => $data['branch_id'] ?? null,
                'project_id' => $data['project_id'] ?? null,
                'location' => $data['location'] ?? null,
                'notes' => $data['notes'] ?? null,
                'assigned_by' => auth()->id() ?? 1,
            ]);
            
            $asset->update([
                'assigned_user_id' => $data['user_id'] ?? null,
                'department_id' => $data['department_id'] ?? $asset->department_id,
                'branch_id' => $data['branch_id'] ?? $asset->branch_id,
            ]);
            
            return $assignment;
        });
    }

    public function initiateTransfer(Asset $asset, array $data): AssetTransfer
    {
        return DB::transaction(function () use ($asset, $data) {
            $workflow = $this->workflowService->getActiveWorkflowForModule('AssetTransfer', $asset->company_id);
            
            $status = $workflow ? 'Pending Approval' : 'Approved';

            $transfer = $asset->transfers()->create([
                'from_user_id' => $asset->assigned_user_id,
                'to_user_id' => $data['to_user_id'] ?? null,
                'from_department_id' => $asset->department_id,
                'to_department_id' => $data['to_department_id'] ?? null,
                'from_branch_id' => $asset->branch_id,
                'to_branch_id' => $data['to_branch_id'] ?? null,
                'from_warehouse_id' => $asset->warehouse_id,
                'to_warehouse_id' => $data['to_warehouse_id'] ?? null,
                'transfer_date' => $data['transfer_date'] ?? now(),
                'reason' => $data['reason'] ?? null,
                'status' => $status,
                'requested_by' => auth()->id() ?? 1,
            ]);

            if ($status === 'Approved') {
                $this->executeTransfer($transfer);
            } else {
                $asset->update(['status' => 'Transferred']); // Temporarily mark as transferring
            }
            
            return $transfer;
        });
    }

    public function executeTransfer(AssetTransfer $transfer)
    {
        DB::transaction(function () use ($transfer) {
            $asset = $transfer->asset;
            
            $asset->update([
                'assigned_user_id' => $transfer->to_user_id,
                'department_id' => $transfer->to_department_id,
                'branch_id' => $transfer->to_branch_id,
                'warehouse_id' => $transfer->to_warehouse_id,
                'status' => 'Active',
            ]);
            
            $transfer->update([
                'status' => 'Approved',
                'approved_by' => auth()->id() ?? 1,
                'approved_at' => now(),
            ]);

            // Create assignment record
            $this->assignAsset($asset, [
                'user_id' => $transfer->to_user_id,
                'department_id' => $transfer->to_department_id,
                'branch_id' => $transfer->to_branch_id,
                'notes' => 'Transferred: ' . $transfer->reason,
            ]);
        });
    }

    public function rejectTransfer(AssetTransfer $transfer)
    {
        $transfer->update(['status' => 'Rejected']);
        $transfer->asset->update(['status' => 'Active']);
    }
}
