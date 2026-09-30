<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('cost_types')) {
            Schema::create('cost_types', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
                $table->string('name');
                $table->string('slug', 50)->unique();
                $table->boolean('is_system')->default(false);
                $table->boolean('is_active')->default(true);
                $table->text('description')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
            });
        } elseif (!Schema::hasColumn('cost_types', 'deleted_at')) {
            Schema::table('cost_types', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        $defaults = [
            [
                'name' => 'Labor',
                'slug' => 'labor',
                'is_system' => true,
                'is_active' => true,
                'description' => 'Direct manpower, wages, and payroll costs.',
            ],
            [
                'name' => 'Materials',
                'slug' => 'materials',
                'is_system' => true,
                'is_active' => true,
                'description' => 'Direct materials, supplies, and stock items.',
            ],
            [
                'name' => 'Other',
                'slug' => 'other',
                'is_system' => true,
                'is_active' => true,
                'description' => 'General expenses, administrative, or unclassified costs.',
            ],
        ];

        foreach ($defaults as $d) {
            $exists = DB::table('cost_types')->where('slug', $d['slug'])->exists();
            if (!$exists) {
                DB::table('cost_types')->insert(array_merge($d, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cost_types');
    }
};
