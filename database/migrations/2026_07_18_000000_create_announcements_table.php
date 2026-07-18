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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->longText('content');
            
            $table->string('category')->default('info'); // info, warning, success, error
            $table->string('priority')->default('normal'); // low, normal, high, urgent
            
            $table->string('audience_type')->default('entire_system'); // entire_system, company, branch, department, role, specific_users
            $table->unsignedBigInteger('audience_id')->nullable(); // Polymorphic ID for the audience
            
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'is_published']);
            $table->index('audience_type');
            $table->index('starts_at');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
