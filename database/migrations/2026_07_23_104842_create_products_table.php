<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained()->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->string('barcode')->nullable()->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type')->default('physical');
            $table->foreignUuid('product_category_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->foreignUuid('brand_id')->nullable()->constrained('brands')->nullOnDelete();
            $table->foreignUuid('unit_of_measure_id')->nullable()->constrained('unit_of_measures')->nullOnDelete();
            $table->decimal('cost_price', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->foreignUuid('tax_id')->nullable()->constrained('taxes')->nullOnDelete();
            $table->decimal('weight', 10, 2)->nullable();
            $table->string('dimensions')->nullable();
            $table->integer('reorder_level')->default(0);
            $table->integer('safety_stock')->default(0);
            $table->integer('minimum_stock')->default(0);
            $table->integer('maximum_stock')->nullable();
            $table->boolean('track_inventory')->default(true);
            $table->boolean('allow_negative_stock')->default(false);
            $table->boolean('serial_numbers')->default(false);
            $table->boolean('batch_numbers')->default(false);
            $table->boolean('expiration_date')->default(false);
            $table->string('image')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
