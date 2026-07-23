<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_bins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('warehouse_zone_id')->constrained('warehouse_zones')->cascadeOnDelete();
            $table->string('code');
            $table->string('aisle')->nullable();
            $table->string('rack')->nullable();
            $table->string('shelf')->nullable();
            $table->decimal('capacity', 15, 2)->nullable();
            $table->decimal('current_quantity', 15, 2)->default(0);
            $table->string('status')->default('active'); // active, inactive, full, maintenance
            $table->json('allowed_product_types')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            
            $table->unique(['warehouse_zone_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_bins');
    }
};
