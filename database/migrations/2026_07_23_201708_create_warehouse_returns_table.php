<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_returns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->string('return_number')->unique();
            $table->string('type'); // customer_return, supplier_return, damaged_stock
            $table->string('status')->default('pending'); // pending, inspected, approved, rejected, restocked, disposed
            $table->uuidMorphs('returnable'); // relates to SalesOrder, PurchaseOrder, etc.
            $table->foreignUuid('inspected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('inspected_at')->nullable();
            $table->text('inspection_notes')->nullable();
            $table->boolean('requires_accounting_adjustment')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_returns');
    }
};
