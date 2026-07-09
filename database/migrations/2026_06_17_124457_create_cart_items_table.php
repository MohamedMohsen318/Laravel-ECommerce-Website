<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('item_variant_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2);
            $table->json('options')->nullable();
            $table->timestamps();

            $table->unique(['cart_id', 'item_id', 'item_variant_id'], 'cart_item_variant_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
