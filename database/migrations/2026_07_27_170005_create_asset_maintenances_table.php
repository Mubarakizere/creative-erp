<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            
            $table->date('maintenance_date');
            $table->string('description');
            $table->string('vendor')->nullable();
            
            $table->decimal('cost', 15, 2)->default(0);
            
            $table->date('warranty_start')->nullable();
            $table->date('warranty_end')->nullable();
            
            $table->date('next_maintenance_date')->nullable();
            
            $table->string('status')->default('Completed'); // Scheduled, In Progress, Completed
            
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_maintenances');
    }
};
