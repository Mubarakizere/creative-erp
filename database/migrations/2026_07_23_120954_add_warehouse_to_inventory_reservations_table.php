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
        Schema::table('inventory_reservations', function (Blueprint $table) {
            $table->foreignUuid('warehouse_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignUuid('zone_id')->nullable()->constrained('warehouse_zones')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_reservations', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
            $table->dropForeign(['zone_id']);
            $table->dropColumn(['warehouse_id', 'zone_id']);
        });
    }
};
