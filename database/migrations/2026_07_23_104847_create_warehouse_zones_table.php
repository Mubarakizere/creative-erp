<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_zones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_zones');
    }
};
