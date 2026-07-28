<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_impairments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            
            $table->date('date');
            $table->text('reason');
            
            $table->decimal('amount', 15, 2);
            
            $table->string('status')->default('Pending Approval'); // Pending Approval, Approved, Rejected
            
            $table->foreignId('journal_id')->nullable()->constrained('journals')->nullOnDelete();
            
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_impairments');
    }
};
