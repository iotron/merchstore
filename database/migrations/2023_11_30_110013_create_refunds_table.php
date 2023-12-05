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
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->string('refund_id')->nullable();
            $table->string('provider_payment_id')->nullable();
            $table->integer('amount')->default(0);
            $table->string('currency')->nullable();
            $table->string('receipt');
            $table->string('speed')->nullable();
            $table->string('status');
            $table->string('batch_id')->nullable();
            $table->json('notes')->nullable();
            $table->json('tracking_data')->nullable();

            $table->boolean('verified')->default(false);
            $table->json('details')->nullable();
            $table->json('error')->nullable();

            $table->foreignId('order_id')->constrained('orders')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('order_product_id')->constrained('order_products')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('payment_id')->constrained('payments')->cascadeOnUpdate()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
