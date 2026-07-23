<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_packings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->foreignUuid('warehouse_picking_id')->nullable()->constrained('warehouse_pickings')->nullOnDelete();
            $table->foreignUuid('warehouse_shipment_id')->nullable()->constrained('warehouse_shipments')->nullOnDelete();
            $table->string('packing_number')->unique();
            $table->string('status')->default('pending'); // pending, packing, completed
            $table->decimal('total_weight', 15, 2)->default(0); // weight
            $table->decimal('length', 15, 2)->nullable();
            $table->decimal('width', 15, 2)->nullable();
            $table->decimal('height', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->foreignUuid('packed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_packings');
    }
};
