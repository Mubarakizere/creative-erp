<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignUuid('product_variant_id')->nullable()->constrained('product_variants')->cascadeOnDelete();
            $table->foreignUuid('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->foreignUuid('warehouse_zone_id')->nullable()->constrained('warehouse_zones')->nullOnDelete();
            $table->decimal('available_quantity', 15, 4)->default(0);
            $table->decimal('reserved_quantity', 15, 4)->default(0);
            $table->decimal('allocated_quantity', 15, 4)->default(0);
            $table->decimal('damaged_quantity', 15, 4)->default(0);
            $table->decimal('on_order_quantity', 15, 4)->default(0);
            $table->decimal('incoming_quantity', 15, 4)->default(0);
            $table->decimal('outgoing_quantity', 15, 4)->default(0);
            $table->unique(['product_id', 'product_variant_id', 'warehouse_id', 'warehouse_zone_id'], 'inventory_unique_idx');
            $table->timestamps();
            $table->softDeletes();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
