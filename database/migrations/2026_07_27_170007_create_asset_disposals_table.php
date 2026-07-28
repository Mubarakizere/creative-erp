<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_disposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            
            $table->date('date');
            $table->string('type')->default('Disposal'); // Disposal, Sale, Write-Off
            $table->text('reason')->nullable();
            
            $table->decimal('sale_price', 15, 2)->default(0);
            $table->decimal('disposal_costs', 15, 2)->default(0);
            $table->decimal('gain_loss', 15, 2)->default(0);
            
            $table->string('status')->default('Pending Approval'); // Pending Approval, Approved, Rejected
            
            $table->foreignId('journal_id')->nullable()->constrained('journals')->nullOnDelete();
            
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_disposals');
    }
};
