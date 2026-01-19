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
        Schema::table('items', function (Blueprint $table) {
            // Ubah box_type menjadi unit_type dengan nilai: pcs, lusin, dus
            $table->dropColumn(['box_type', 'box_quantity']);
            $table->enum('unit_type', ['pcs', 'lusin', 'dus'])->default('pcs')->after('unit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('unit_type');
            $table->string('box_type')->nullable()->after('unit');
            $table->integer('box_quantity')->nullable()->after('box_type');
        });
    }
};
