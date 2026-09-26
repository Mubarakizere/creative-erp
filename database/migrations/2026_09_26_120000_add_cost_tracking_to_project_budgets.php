<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('budget_lines', function (Blueprint $table) {
            if (!Schema::hasColumn('budget_lines', 'cost_type')) {
                $table->string('cost_type', 30)->default('other')->after('task_id');
            }
            if (!Schema::hasColumn('budget_lines', 'resource_name')) {
                $table->string('resource_name')->nullable()->after('cost_type');
            }
            if (!Schema::hasColumn('budget_lines', 'product_id')) {
                $table->foreignId('product_id')->nullable()->after('resource_name')->constrained()->nullOnDelete();
            }
        });

        Schema::table('project_expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('project_expenses', 'task_id')) {
                $table->foreignId('task_id')->nullable()->after('project_id')->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('project_expenses', 'budget_line_id')) {
                $table->foreignId('budget_line_id')->nullable()->after('task_id')->constrained('budget_lines')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('project_expenses', function (Blueprint $table) {
            if (Schema::hasColumn('project_expenses', 'budget_line_id')) {
                $table->dropConstrainedForeignId('budget_line_id');
            }
            if (Schema::hasColumn('project_expenses', 'task_id')) {
                $table->dropConstrainedForeignId('task_id');
            }
        });

        Schema::table('budget_lines', function (Blueprint $table) {
            if (Schema::hasColumn('budget_lines', 'product_id')) {
                $table->dropConstrainedForeignId('product_id');
            }
            foreach (['resource_name', 'cost_type'] as $column) {
                if (Schema::hasColumn('budget_lines', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
