<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_transfers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('from_warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->foreignUuid('to_warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->foreignUuid('from_zone_id')->nullable()->constrained('warehouse_zones')->nullOnDelete();
            $table->foreignUuid('to_zone_id')->nullable()->constrained('warehouse_zones')->nullOnDelete();
            $table->string('status')->default('pending');
            $table->string('tracking_number')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transfers');
    }
};
