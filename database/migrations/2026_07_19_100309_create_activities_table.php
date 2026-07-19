<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            
            $table->morphs('activityable'); // E.g., Lead, Opportunity, Account, Contact
            
            $table->string('type'); // Call, Email, Meeting, Visit, Demo, Follow-up, Task, Reminder
            $table->string('subject');
            $table->text('description')->nullable();
            
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            
            $table->boolean('is_reminder')->default(false);
            $table->dateTime('reminder_at')->nullable();
            
            $table->string('status')->default('Pending'); // Pending, Completed, Cancelled
            
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
