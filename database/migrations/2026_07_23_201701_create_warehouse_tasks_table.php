<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->string('type'); // put_away, internal_movement, picking, etc.
            $table->string('status')->default('pending'); // pending, in_progress, completed, cancelled
            $table->uuidMorphs('taskable'); // polymorhpic relation to putaway, picking, etc.
            $table->foreignUuid('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('priority')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_tasks');
    }
};
