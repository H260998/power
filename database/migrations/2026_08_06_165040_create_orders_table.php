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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone');
            $table->string('address');
            $table->string('city');
            $table->text('notes')->nullable();
            $table->decimal('subtotal', 10, 3);
            $table->decimal('shipping_fee', 10, 3)->default(0);
            $table->decimal('discount_amount', 10, 3)->default(0);
            $table->decimal('total', 10, 3);
            $table->foreignId('promo_code_id')->nullable()->constrained('promo_codes')->nullOnDelete();
            $table->string('status')->default('new');
            $table->string('locale', 5)->default('fr');
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
