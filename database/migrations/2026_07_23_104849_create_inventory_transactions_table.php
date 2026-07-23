<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('inventory_id')->constrained('inventories')->cascadeOnDelete();
            $table->string('type');
            $table->decimal('quantity', 15, 4);
            $table->nullableUuidMorphs('reference');
            $table->dateTime('date');
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
