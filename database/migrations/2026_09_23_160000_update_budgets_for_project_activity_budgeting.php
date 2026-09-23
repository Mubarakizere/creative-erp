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
        Schema::table('budgets', function (Blueprint $table) {
            if (!Schema::hasColumn('budgets', 'project_id')) {
                $table->foreignId('project_id')->nullable()->after('company_id')->constrained('projects')->cascadeOnDelete();
            }
        });

        Schema::table('budget_lines', function (Blueprint $table) {
            if (!Schema::hasColumn('budget_lines', 'task_id')) {
                $table->foreignId('task_id')->nullable()->after('project_id')->constrained('tasks')->nullOnDelete();
            }
            if (!Schema::hasColumn('budget_lines', 'activity_name')) {
                $table->string('activity_name', 255)->nullable()->after('task_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('budget_lines', function (Blueprint $table) {
            if (Schema::hasColumn('budget_lines', 'task_id')) {
                $table->dropForeign(['task_id']);
                $table->dropColumn('task_id');
            }
            if (Schema::hasColumn('budget_lines', 'activity_name')) {
                $table->dropColumn('activity_name');
            }
        });

        Schema::table('budgets', function (Blueprint $table) {
            if (Schema::hasColumn('budgets', 'project_id')) {
                $table->dropForeign(['project_id']);
                $table->dropColumn('project_id');
            }
        });
    }
};
