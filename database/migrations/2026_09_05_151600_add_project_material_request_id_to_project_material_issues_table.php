<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_material_issues', function (Blueprint $table) {
            $table->foreignId('project_material_request_id')
                  ->nullable()
                  ->after('warehouse_id')
                  ->constrained('project_material_requests')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('project_material_issues', function (Blueprint $table) {
            $table->dropForeign(['project_material_request_id']);
            $table->dropColumn('project_material_request_id');
        });
    }
};
