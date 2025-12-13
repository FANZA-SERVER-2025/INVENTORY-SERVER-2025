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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            // category_id and supplier_id created without FK to avoid ordering issues
            $table->unsignedBigInteger('category_id');
            $table->index('category_id');
            $table->unsignedBigInteger('supplier_id');
            $table->index('supplier_id');
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('unit')->default('pcs');
            $table->integer('stock')->default(0);
            $table->integer('minimum_stock')->default(10);
            $table->decimal('purchase_price', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
