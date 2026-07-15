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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            
            // Self-referencing parent task
            $table->foreignId('parent_id')->nullable()->constrained('tasks')->nullOnDelete();
            
            // Assigned to can be null initially, but must belong to the project team
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('task_code', 50);
            $table->string('name', 255);
            $table->text('description')->nullable();
            
            $table->string('priority')->default('Medium'); // Low, Medium, High, Critical
            $table->string('status')->default('Pending'); // Pending, In Progress, Waiting Review, Completed, Cancelled
            
            $table->integer('progress')->default(0); // 0 to 100
            
            $table->date('start_date');
            $table->date('due_date')->nullable();
            $table->dateTime('completed_at')->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();

            // Task code must be unique within a project
            $table->unique(['project_id', 'task_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
