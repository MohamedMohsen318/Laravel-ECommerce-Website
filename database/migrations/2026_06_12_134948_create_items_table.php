<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['simple', 'variant'])->default('simple')->after('id');
            $table->foreignId('parent_id')->nullable()->after('type')
                ->constrained('items')->cascadeOnDelete();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_discount')->default(false);
            $table->string('status')->default('available');
            $table->unsignedInteger('stock')->nullable();
            $table->string('sku')->unique()->nullable();
            $table->timestamps();
        });

        Schema::create('category_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->unique(['category_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_item');
        Schema::dropIfExists('items');
    }
};
