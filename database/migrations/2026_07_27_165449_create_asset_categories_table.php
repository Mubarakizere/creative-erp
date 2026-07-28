<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->integer('useful_life')->nullable(); // in months
            $table->string('depreciation_method')->default('straight_line');
            
            $table->foreignId('asset_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            $table->foreignId('accumulated_depreciation_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            $table->foreignId('depreciation_expense_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_categories');
    }
};
