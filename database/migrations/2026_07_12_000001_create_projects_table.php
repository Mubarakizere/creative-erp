<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_manager_id')->constrained('users')->cascadeOnDelete();
            
            $table->string('project_code', 50);
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            
            $table->string('priority')->default('Medium'); // Low, Medium, High, Critical
            $table->string('status')->default('Planning'); // Planning, Pending, In Progress, On Hold, Completed, Cancelled, Closed
            
            $table->integer('progress')->default(0); // 0 to 100
            
            $table->decimal('estimated_budget', 15, 2)->nullable();
            $table->decimal('actual_budget', 15, 2)->nullable();
            $table->decimal('estimated_cost', 15, 2)->nullable();
            $table->decimal('actual_cost', 15, 2)->nullable();
            $table->string('currency', 3)->default('RWF');
            
            $table->date('start_date');
            $table->date('planned_end_date')->nullable();
            $table->date('actual_end_date')->nullable();
            
            $table->string('contract_number')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();

            // Project code must be unique within a company
            $table->unique(['company_id', 'project_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
