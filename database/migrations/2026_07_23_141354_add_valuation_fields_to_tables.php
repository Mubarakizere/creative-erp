<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('valuation_method')->default('Standard Cost');
        });

        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->decimal('unit_cost', 15, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('valuation_method');
        });

        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->dropColumn('unit_cost');
        });
    }
};
