<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('name');
            $table->string('sku')->unique();
            $table->string('barcode')->nullable()->unique();
            $table->json('attributes')->nullable();
            $table->decimal('cost_price', 15, 2)->nullable();
            $table->decimal('selling_price', 15, 2)->nullable();
            $table->boolean('track_inventory')->default(true);
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
