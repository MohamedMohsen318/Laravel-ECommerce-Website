<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('model_translations', function (Blueprint $table) {
            $table->id();
            $table->morphs('model'); // model_id + model_type
            $table->string('locale', 10);
            $table->string('name');
            // FIX #8: text بدل string عشان الـ description ممكن تكون أطول من 255 حرف
            $table->text('description')->nullable();
            $table->timestamps();

            // منع تكرار نفس الـ locale للـ model
            $table->unique(['model_id', 'model_type', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('model_translations');
    }
};
