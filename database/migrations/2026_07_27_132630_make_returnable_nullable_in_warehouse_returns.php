<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('warehouse_returns', function (Blueprint $table) {
            $table->string('returnable_type')->nullable()->change();
            $table->uuid('returnable_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('warehouse_returns', function (Blueprint $table) {
            $table->string('returnable_type')->nullable(false)->change();
            $table->uuid('returnable_id')->nullable(false)->change();
        });
    }
};
