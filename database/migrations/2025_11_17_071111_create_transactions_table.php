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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['in', 'out']); // in = barang masuk, out = barang keluar
            $table->date('transaction_date');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            // vehicle_id created without FK to avoid ordering issues between migrations
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->index('vehicle_id');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->onDelete('cascade');
            // item_id created without FK to avoid ordering issues between migrations
            $table->unsignedBigInteger('item_id');
            $table->index('item_id');
            $table->integer('quantity');
            $table->decimal('price', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_details');
        Schema::dropIfExists('transactions');
    }
};
