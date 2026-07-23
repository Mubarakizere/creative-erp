<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->foreignUuid('warehouse_bin_id')->nullable()->after('warehouse_zone_id')->constrained('warehouse_bins')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropForeign(['warehouse_bin_id']);
            $table->dropColumn('warehouse_bin_id');
        });
    }
};
