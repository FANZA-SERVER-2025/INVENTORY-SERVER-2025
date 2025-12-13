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
        Schema::table('transaction_details', function (Blueprint $table) {
            $table->string('unit_type')->default('pcs')->after('quantity'); // pcs, dozen, box
            $table->string('sub_unit_type')->nullable()->after('unit_type'); // dozen or pcs (jika unit_type = box)
            $table->integer('box_quantity')->nullable()->after('sub_unit_type'); // qty box
            $table->integer('sub_unit_quantity')->nullable()->after('box_quantity'); // qty lusin/pcs dalam box
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_details', function (Blueprint $table) {
            $table->dropColumn(['unit_type', 'sub_unit_type', 'box_quantity', 'sub_unit_quantity']);
        });
    }
};
