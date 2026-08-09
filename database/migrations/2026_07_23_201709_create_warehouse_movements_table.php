<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_movements', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('movement_number')->unique();
            $table->string('type'); // bin_to_bin, zone_to_zone, warehouse_to_warehouse
            $table->string('status')->default('pending'); // pending, approved, in_transit, completed, cancelled
            
            $table->foreignId('source_warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->foreignId('source_zone_id')->nullable()->constrained('warehouse_zones')->nullOnDelete();
            $table->foreignId('source_bin_id')->nullable()->constrained('warehouse_bins')->nullOnDelete();
            
            $table->foreignId('destination_warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->foreignId('destination_zone_id')->nullable()->constrained('warehouse_zones')->nullOnDelete();
            $table->foreignId('destination_bin_id')->nullable()->constrained('warehouse_bins')->nullOnDelete();
            
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            
            $table->text('reason')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_movements');
    }
};
