<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('attribute_id')
                ->constrained('attributes')
                ->cascadeOnDelete();
            $table->foreignId('attribute_value_id')
                ->constrained('attribute_values')
                ->cascadeOnDelete();
            $table->unique(['item_id', 'attribute_id', 'attribute_value_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_attribute_values');
    }
};
