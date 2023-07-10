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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();

            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->date('starts_from')->nullable();
            $table->date('ends_till')->nullable();
            $table->boolean('status')->default(0);


            $table->unsignedInteger('usage_per_customer')->default(0);
            $table->unsignedInteger('coupon_usage_limit')->default(0);
            $table->unsignedInteger('times_used')->default(0);

            $table->boolean('condition_type')->default(1);
            $table->json('conditions')->nullable();
            $table->boolean('end_other_rules')->default(0);

            $table->string('action_type')->nullable();

            $table->decimal('discount_amount', 12, 4)->default(0);
            $table->unsignedInteger('discount_quantity')->default(1);
            $table->string('discount_step')->default(1);
            $table->boolean('apply_to_shipping')->default(0);
            $table->boolean('free_shipping')->default(0);
            $table->unsignedInteger('sort_order')->default(0);



            $table->timestamps();
        });


        Schema::create('voucher_customer_groups', function (Blueprint $table) {
            $table->foreignId('voucher_id')->constrained('vouchers')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('customer_group_id')->constrained('customer_groups')->cascadeOnUpdate()->cascadeOnDelete();
            $table->primary(['voucher_id', 'customer_group_id'], 'voucher_id_customer_group_id_primary');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');

        Schema::dropIfExists('voucher_customer_groups');
    }
};
