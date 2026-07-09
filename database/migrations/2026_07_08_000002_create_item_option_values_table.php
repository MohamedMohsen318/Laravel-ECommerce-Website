<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_option_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_option_id')->constrained()->cascadeOnDelete();
            $table->string('value');
            $table->timestamps();

            $table->unique(['item_option_id', 'value']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_option_values');
    }
};
