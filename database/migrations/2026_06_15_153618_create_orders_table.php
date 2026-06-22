<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void{
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->decimal('total_price', 10, 2)->default(0);
            $table->foreignId('coupon_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->decimal('discount_amount', 8, 2)->default(0);
            $table->decimal('final_total', 8, 2)->default(0);
            $table->timestamps();
        });
    }
    public function down(): void{
        Schema::dropIfExists('orders');
    }
};
