<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('warehouse_returns', function (Blueprint $table) {
            $table->json('items')->nullable()->after('requires_accounting_adjustment');
        });
    }

    public function down(): void
    {
        Schema::table('warehouse_returns', function (Blueprint $table) {
            $table->dropColumn('items');
        });
    }
};
