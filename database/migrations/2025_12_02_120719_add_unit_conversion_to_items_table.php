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
            $table->integer('pcs_per_dozen')->default(12)->after('unit'); // 1 lusin = 12 pcs (default)
            $table->integer('dozen_per_box')->default(12)->after('pcs_per_dozen'); // 1 box = 12 lusin (default)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['pcs_per_dozen', 'dozen_per_box']);
        });
    }
};
