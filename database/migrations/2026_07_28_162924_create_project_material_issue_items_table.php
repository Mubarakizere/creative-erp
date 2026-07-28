<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_material_issue_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('project_material_issue_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            
            $table->unsignedBigInteger('project_material_request_item_id')->nullable();
            
            $table->decimal('quantity', 15, 2);
            $table->decimal('unit_cost', 15, 4)->default(0);
            $table->decimal('total_cost', 15, 4)->default(0);

            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('project_material_request_item_id', 'pmr_item_fk')
                  ->references('id')
                  ->on('project_material_request_items')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_material_issue_items');
    }
};
