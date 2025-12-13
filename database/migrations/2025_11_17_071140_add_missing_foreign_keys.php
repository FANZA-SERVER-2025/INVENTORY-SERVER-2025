<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $db = DB::getDatabaseName();

        $fks = [
            ['table' => 'items', 'column' => 'category_id', 'references' => 'categories', 'ref_column' => 'id'],
            ['table' => 'items', 'column' => 'supplier_id', 'references' => 'suppliers', 'ref_column' => 'id'],
            ['table' => 'carts', 'column' => 'item_id', 'references' => 'items', 'ref_column' => 'id'],
            ['table' => 'item_requests', 'column' => 'item_id', 'references' => 'items', 'ref_column' => 'id'],
            ['table' => 'transaction_details', 'column' => 'item_id', 'references' => 'items', 'ref_column' => 'id'],
            ['table' => 'transactions', 'column' => 'vehicle_id', 'references' => 'vehicles', 'ref_column' => 'id'],
        ];

        foreach ($fks as $fk) {
            // ensure both tables exist
            if (!Schema::hasTable($fk['table']) || !Schema::hasTable($fk['references'])) {
                continue;
            }

            // check if foreign key already exists
            $exists = DB::selectOne(
                'SELECT COUNT(*) AS cnt FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME = ?',
                [$db, $fk['table'], $fk['column'], $fk['references']]
            );

            if ($exists && $exists->cnt > 0) {
                continue;
            }

            // add foreign key
            Schema::table($fk['table'], function (Blueprint $table) use ($fk) {
                $table->foreign($fk['column'])->references($fk['ref_column'])->on($fk['references'])->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $fks = [
            ['table' => 'items', 'column' => 'category_id'],
            ['table' => 'items', 'column' => 'supplier_id'],
            ['table' => 'carts', 'column' => 'item_id'],
            ['table' => 'item_requests', 'column' => 'item_id'],
            ['table' => 'transaction_details', 'column' => 'item_id'],
            ['table' => 'transactions', 'column' => 'vehicle_id'],
        ];

        foreach ($fks as $fk) {
            if (!Schema::hasTable($fk['table'])) {
                continue;
            }

            Schema::table($fk['table'], function (Blueprint $table) use ($fk) {
                try {
                    $table->dropForeign([$fk['column']]);
                } catch (\Exception $e) {
                    // ignore if it doesn't exist
                }
            });
        }
    }
};
