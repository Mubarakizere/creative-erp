<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('warehouse_movements', function (Blueprint $table) {
            $table->foreignUuid('product_id')->nullable()->after('destination_bin_id')->constrained('products')->nullOnDelete();
            $table->decimal('quantity', 15, 2)->default(0)->after('product_id');
        });
    }

    public function down(): void
    {
        Schema::table('warehouse_movements', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn(['product_id', 'quantity']);
        });
    }
};
