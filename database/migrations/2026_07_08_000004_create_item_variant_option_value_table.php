<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_variant_option_value', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_variant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_option_value_id')->constrained()->cascadeOnDelete();

            $table->unique(['item_variant_id', 'item_option_value_id'], 'variant_option_value_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_variant_option_value');
    }
};
