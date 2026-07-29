<?php

namespace App\Services\Project;

use App\Models\ProjectMaterialIssue;
use App\Models\Project;
use App\Models\Warehouse;
use App\Models\Product;
use App\Services\Inventory\InventoryEngine;
use App\Services\Inventory\InventoryValuationService;
use App\Services\Finance\JournalService;
use Illuminate\Support\Facades\DB;
use App\Traits\LogsActivity;
use Exception;

class ProjectMaterialIssueService
{
    use LogsActivity;

    protected InventoryEngine $inventoryEngine;
    protected InventoryValuationService $inventoryValuationService;
    protected JournalService $journalService;

    public function __construct(
        InventoryEngine $inventoryEngine,
        InventoryValuationService $inventoryValuationService,
        JournalService $journalService
    ) {
        $this->inventoryEngine = $inventoryEngine;
        $this->inventoryValuationService = $inventoryValuationService;
        $this->journalService = $journalService;
    }

    public function createIssue(array $data, array $items): ProjectMaterialIssue
    {
        return DB::transaction(function () use ($data, $items) {
            $data['company_id'] = $data['company_id'] ?? auth()->user()->company_id;
            $data['issued_by'] = auth()->id();
            $data['created_by'] = auth()->id();
            
            // Check Project authorization
            $project = Project::where('id', $data['project_id'])
                              ->where('company_id', $data['company_id'])
                              ->firstOrFail();

            // Check Warehouse authorization
            $warehouse = Warehouse::where('id', $data['warehouse_id'])
                                  ->where('company_id', $data['company_id'])
                                  ->firstOrFail();

            $issue = ProjectMaterialIssue::create($data);
            
            $totalIssueCost = 0;

            foreach ($items as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                $quantity = (float) $itemData['quantity'];
                
                if ($quantity <= 0) {
                    throw new Exception("Quantity must be greater than zero for product: {$product->name}");
                }

                // Cost calculation
                $itemTotalCost = $this->inventoryValuationService->calculateIssueCost($product, $quantity, $warehouse);
                $unitCost = $itemTotalCost / $quantity;

                $issueItem = $issue->items()->create([
                    'product_id' => $product->id,
                    'project_material_request_item_id' => $itemData['project_material_request_item_id'] ?? null,
                    'quantity' => $quantity,
                    'unit_cost' => $unitCost,
                    'total_cost' => $itemTotalCost,
                ]);

                // Deduct Inventory
                $this->inventoryEngine->stockOut(
                    $product,
                    $warehouse,
                    $quantity,
                    'project_issue',
                    $issueItem // As reference
                );

                $totalIssueCost += $itemTotalCost;
            }

            // Update Project Cost
            // We use DB::raw or lock for update to prevent race conditions on actual_cost
            $project->lockForUpdate();
            $project->actual_cost = ($project->actual_cost ?? 0) + $totalIssueCost;
            $project->save();

            // Update Task Cost
            if (!empty($issue->task_id)) {
                $task = \App\Models\Task::where('id', $issue->task_id)
                                        ->where('project_id', $project->id)
                                        ->first();
                if ($task) {
                    $task->lockForUpdate();
                    $task->actual_material_cost = ($task->actual_material_cost ?? 0) + $totalIssueCost;
                    $task->save();
                }
            }

            // Accounting Entry
            if ($totalIssueCost > 0) {
                // Determine accounts - falling back to defaults if not found.
                // 5100 COGS/Project Material Expense, 1200 Inventory
                $cogsAccount = \App\Models\ChartOfAccount::where('company_id', $issue->company_id)->where('code', '5100')->first() 
                               ?? \App\Models\ChartOfAccount::where('company_id', $issue->company_id)->where('type', 'expense')->first();
                $inventoryAccount = \App\Models\ChartOfAccount::where('company_id', $issue->company_id)->where('code', '1200')->first()
                               ?? \App\Models\ChartOfAccount::where('company_id', $issue->company_id)->where('type', 'asset')->first();

                if ($cogsAccount && $inventoryAccount) {
                    $this->journalService->createAutomaticJournal([
                        'company_id' => $issue->company_id,
                        'project_id' => $project->id,
                        'branch_id' => $issue->branch_id,
                        'date' => $issue->issue_date,
                        'memo' => 'Project Material Issue ' . $issue->issue_number,
                    ], [
                        [
                            'chart_of_account_id' => $cogsAccount->id,
                            'description' => 'Project Material Consumed',
                            'debit' => $totalIssueCost,
                            'credit' => 0,
                            'project_id' => $project->id,
                        ],
                        [
                            'chart_of_account_id' => $inventoryAccount->id,
                            'description' => 'Inventory Issued to Project',
                            'debit' => 0,
                            'credit' => $totalIssueCost,
                            'project_id' => $project->id,
                        ]
                    ]);
                }
            }

            $this->logActivity('project_material_issued', [
                'issue_id' => $issue->id,
                'project_id' => $project->id,
                'total_cost' => $totalIssueCost,
            ]);

            return $issue;
        });
    }
}
