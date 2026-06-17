<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void{
        if (! Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->cascadeOnDelete();
                $table->foreignId('item_id')->constrained()->cascadeOnDelete();
                $table->unsignedInteger('quantity');
                $table->decimal('price', 10, 2);
                $table->timestamps();
            });

            return;
        }

        $foreignKeys = collect(DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'order_items'
              AND REFERENCED_TABLE_NAME IS NOT NULL
        "))->pluck('CONSTRAINT_NAME');

        Schema::table('order_items', function (Blueprint $table) use ($foreignKeys) {
            if (! $foreignKeys->contains('order_items_order_id_foreign')) {
                $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
            }

            if (! $foreignKeys->contains('order_items_item_id_foreign')) {
                $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();
            }
        });
    }

    public function down(): void{
        Schema::dropIfExists('order_items');
    }
};
