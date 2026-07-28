<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('asset_category_id')->constrained('asset_categories')->cascadeOnDelete();
            
            $table->string('asset_number')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('barcode')->nullable()->unique();
            
            $table->date('purchase_date')->nullable();
            $table->date('in_service_date')->nullable();
            
            $table->decimal('purchase_cost', 15, 2)->default(0);
            $table->decimal('residual_value', 15, 2)->default(0);
            $table->integer('useful_life')->nullable(); // in months
            $table->string('depreciation_method')->default('straight_line');
            
            $table->decimal('accumulated_depreciation', 15, 2)->default(0);
            $table->decimal('current_book_value', 15, 2)->default(0);
            
            $table->string('status')->default('Active'); // Draft, Pending Approval, Active, Under Maintenance, Fully Depreciated, Disposed, Sold, Written Off
            $table->string('condition')->nullable();
            
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('purchase_order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('purchase_invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            
            $table->text('notes')->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
