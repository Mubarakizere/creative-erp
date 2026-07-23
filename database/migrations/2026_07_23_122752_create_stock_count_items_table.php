<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_count_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('stock_count_id')->constrained('stock_counts')->cascadeOnDelete();
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('system_quantity', 15, 4)->default(0);
            $table->decimal('counted_quantity', 15, 4)->nullable();
            $table->decimal('variance', 15, 4)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_count_items');
    }
};
