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
            $table->string('refund_id');
            $table->integer('amount')->default(0);
            $table->string('currency')->nullable();
            $table->string('receipt');
            $table->string('payment_id');
            $table->string('speed')->nullable();
            $table->string('status')->default('unknown');
            $table->string('batch_id')->nullable();
            $table->json('notes')->nullable();
            $table->json('tracking_data')->nullable();


            $table->json('details')->nullable();
            $table->json('error')->nullable();

            $table->foreignId('order_id')->constrained('orders')->cascadeOnUpdate()->cascadeOnDelete();
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
