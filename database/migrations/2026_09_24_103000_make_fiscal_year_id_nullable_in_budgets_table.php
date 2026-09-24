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
        if (Schema::hasColumn('budgets', 'fiscal_year_id')) {
            Schema::table('budgets', function (Blueprint $table) {
                $table->unsignedBigInteger('fiscal_year_id')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('budgets', 'fiscal_year_id')) {
            Schema::table('budgets', function (Blueprint $table) {
                $table->unsignedBigInteger('fiscal_year_id')->nullable(false)->change();
            });
        }
    }
};
