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
        Schema::create('voucher_codes', function (Blueprint $table) {
            $table->id();

            $table->string('code')->unique();
            $table->unsignedInteger('coupon_usage_limit')->default(0);
            $table->unsignedInteger('usage_per_customer')->default(0);
            $table->unsignedInteger('times_used')->default(0);
            $table->unsignedInteger('type')->default(0);
            $table->date('starts_from')->nullable();
            $table->date('ends_till')->nullable();

            $table->foreignId('voucher_id')->constrained('vouchers')->cascadeOnUpdate()->cascadeOnDelete();

            $table->timestamps();
        });

        Schema::create('voucher_code_usages', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('times_used')->default(0);
            $table->foreignId('voucher_code_id')->constrained('voucher_codes')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onUpdate('cascade')->onDelete('cascade');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_codes');
        Schema::dropIfExists('voucher_code_usages');
    }
};
