<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_count_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn(['product_id', 'system_quantity']);
            
            $table->foreignUuid('inventory_id')->nullable()->after('stock_count_id')->constrained('inventories')->cascadeOnDelete();
            $table->decimal('expected_quantity', 15, 4)->default(0)->after('inventory_id');
        });
    }

    public function down(): void
    {
        Schema::table('stock_count_items', function (Blueprint $table) {
            $table->dropForeign(['inventory_id']);
            $table->dropColumn(['inventory_id', 'expected_quantity']);
            
            $table->foreignUuid('product_id')->nullable()->constrained('products')->cascadeOnDelete();
            $table->decimal('system_quantity', 15, 4)->default(0);
        });
    }
};
