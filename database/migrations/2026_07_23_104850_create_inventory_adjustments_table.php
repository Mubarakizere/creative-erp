<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_adjustments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->string('reason');
            $table->string('status')->default('pending');
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('comments')->nullable();
            $table->string('attachments')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_adjustments');
    }
};
