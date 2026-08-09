<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $tables = [
        'invoices' => 'invoice_number',
        'purchase_orders' => 'code',
        'credit_notes' => 'credit_note_number',
        'payments' => 'payment_number',
        'refunds' => 'refund_number',
        'warehouse_shipments' => 'shipment_number',
        'warehouse_pickings' => 'picking_number',
        'warehouse_packings' => 'packing_number',
        'warehouse_returns' => 'return_number',
        'warehouse_movements' => 'movement_number',
        'stock_counts' => 'count_number',
        'project_material_issues' => 'issue_number',
        'receipts' => 'receipt_number',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First we must drop the global unique constraints
        foreach ($this->tables as $table => $column) {
            Schema::table($table, function (Blueprint $tableBlueprint) use ($table, $column) {
                // To drop a unique index created by string()->unique(), Laravel names it table_column_unique
                $indexName = "{$table}_{$column}_unique";
                
                // We use Schema::getIndexes to check if the index exists first before dropping to avoid errors
                $indexes = Schema::getIndexes($table);
                
                $indexExists = false;
                foreach ($indexes as $index) {
                    if ($index['name'] === $indexName) {
                        $indexExists = true;
                        break;
                    }
                }
                
                if ($indexExists) {
                    $tableBlueprint->dropUnique($indexName);
                }
            });
        }

        // Now add the new company-scoped unique constraints
        foreach ($this->tables as $table => $column) {
            Schema::table($table, function (Blueprint $tableBlueprint) use ($table, $column) {
                $tableBlueprint->unique(['company_id', $column]);
            });
        }

        // Add to quotations as well (it didn't have a global unique constraint)
        Schema::table('quotations', function (Blueprint $table) {
            $indexes = Schema::getIndexes('quotations');
            $indexExists = false;
            foreach ($indexes as $index) {
                if ($index['name'] === 'quotations_company_id_quotation_number_unique') {
                    $indexExists = true;
                    break;
                }
            }
            if (!$indexExists) {
                $table->unique(['company_id', 'quotation_number']);
            }
        });
        
        // Add to inventory_transfers as well
        Schema::table('inventory_transfers', function (Blueprint $table) {
            $indexes = Schema::getIndexes('inventory_transfers');
            $indexExists = false;
            foreach ($indexes as $index) {
                if ($index['name'] === 'inventory_transfers_company_id_tracking_number_unique') {
                    $indexExists = true;
                    break;
                }
            }
            if (!$indexExists) {
                $table->unique(['company_id', 'tracking_number']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop company-scoped constraints and restore global ones
        foreach ($this->tables as $table => $column) {
            Schema::table($table, function (Blueprint $tableBlueprint) use ($table, $column) {
                $indexName = "{$table}_company_id_{$column}_unique";
                $indexes = Schema::getIndexes($table);
                
                $indexExists = false;
                foreach ($indexes as $index) {
                    if ($index['name'] === $indexName) {
                        $indexExists = true;
                        break;
                    }
                }
                
                if ($indexExists) {
                    $tableBlueprint->dropUnique($indexName);
                }
                
                $tableBlueprint->unique($column);
            });
        }

        Schema::table('quotations', function (Blueprint $table) {
            $indexes = Schema::getIndexes('quotations');
            $indexExists = false;
            foreach ($indexes as $index) {
                if ($index['name'] === 'quotations_company_id_quotation_number_unique') {
                    $indexExists = true;
                    break;
                }
            }
            if ($indexExists) {
                $table->dropUnique('quotations_company_id_quotation_number_unique');
            }
        });
        
        Schema::table('inventory_transfers', function (Blueprint $table) {
            $indexes = Schema::getIndexes('inventory_transfers');
            $indexExists = false;
            foreach ($indexes as $index) {
                if ($index['name'] === 'inventory_transfers_company_id_tracking_number_unique') {
                    $indexExists = true;
                    break;
                }
            }
            if ($indexExists) {
                $table->dropUnique('inventory_transfers_company_id_tracking_number_unique');
            }
        });
    }
};
