<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Add supplier link columns (skip if already exist)
        if (!Schema::hasColumn('products', 'default_supplier_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->foreignId('default_supplier_id')
                      ->nullable()
                      ->after('brand_id')
                      ->constrained('suppliers')
                      ->nullOnDelete();
                $table->string('supplier_sku')->nullable()->after('default_supplier_id');
            });
        }

        // Step 2: Fix corrupt stock_counts index before dropping columns
        // The index references a non-existent column 'count_number'
        try {
            DB::statement('DROP INDEX IF EXISTS stock_counts_company_id_count_number_unique');
        } catch (\Exception $e) {
            // Ignore if it doesn't exist
        }

        // Step 3: Drop selling_price
        if (Schema::hasColumn('products', 'selling_price')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('selling_price');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('products', 'selling_price')) {
            Schema::table('products', function (Blueprint $table) {
                $table->decimal('selling_price', 15, 2)->default(0)->after('cost_price');
            });
        }

        if (Schema::hasColumn('products', 'default_supplier_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropForeign(['default_supplier_id']);
                $table->dropColumn(['default_supplier_id', 'supplier_sku']);
            });
        }
    }
};
