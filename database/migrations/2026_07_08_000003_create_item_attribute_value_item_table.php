<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_attribute_value_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_attribute_value_id')->constrained()->cascadeOnDelete();
            $table->unique(['item_id', 'item_attribute_value_id'], 'item_attribute_value_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_attribute_value_item');
    }
};
