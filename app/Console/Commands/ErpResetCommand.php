<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ErpResetCommand extends Command
{
    protected $signature = 'erp:reset-data {--force : Force the operation to run without confirmation}';
    protected $description = 'Safely resets all projects, products, and their transactional dependencies for local development.';

    public function handle()
    {
        if (!app()->environment('local')) {
            $this->error('This command can only be run in the local environment.');
            return 1;
        }

        if (!$this->option('force') && !$this->confirm('This will truncate ALL projects, products, and related transactions. Are you sure you want to proceed?')) {
            return 0;
        }

        $this->info('Starting ERP Data Reset...');

        Schema::disableForeignKeyConstraints();

        $tablesToTruncate = [
            // Project Materials
            'project_material_issue_items',
            'project_material_issues',
            'project_material_request_items',
            'project_material_requests',
            
            // Assets (if they reference projects/products)
            'asset_assignments',
            'assets',
            
            // Tasks & Milestones
            'tasks',
            'milestones',
            'project_members',
            'project_teams',
            
            // Meetings & Time Entries
            'meetings',
            'time_entries',
            
            // Budgets & Invoices
            'budget_lines',
            'invoices',
            
            // Procurement
            'purchase_invoice_items',
            'purchase_invoices',
            'goods_receipt_items',
            'goods_receipts',
            'purchase_order_items',
            'purchase_orders',
            'supplier_quotation_items',
            'supplier_quotations',
            'purchase_requisition_items',
            'purchase_requisitions',
            
            // Inventory
            'warehouse_movements',
            'stock_count_items',
            'stock_counts',
            'inventory_valuations',
            'inventory_reservations',
            'inventory_transactions',
            'inventories',
            'inventory_adjustments',
            'inventory_adjustment_items',
            'inventory_transfers',
            'inventory_transfer_items',
            'warehouse_tasks',
            
            // Product related
            'supplier_products',
            'barcodes',
            'product_variants',
            
            // Accounting
            'journal_lines',
            'journals',
            'general_ledgers',
            
            // Core
            'projects',
            'products',
        ];

        foreach ($tablesToTruncate as $table) {
            if (Schema::hasTable($table)) {
                $this->line("Truncating {$table}...");
                DB::table($table)->truncate();
            }
        }

        Schema::enableForeignKeyConstraints();

        $this->info('ERP Data Reset Complete. Old projects and products have been destroyed.');
        $this->info('You should now run the seeder to rebuild realistic data.');

        return 0;
    }
}
