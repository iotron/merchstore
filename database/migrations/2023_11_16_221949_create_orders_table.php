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
            $table->string('order_receipt')->unique();
            $table->integer('amount');
            $table->integer('subtotal'); // subtotal  base_price
            $table->integer('discount_amount'); // subtotal discount
            $table->integer('tax_amount'); // subtotal tax
            $table->integer('total'); // subtotal final price
            $table->integer('quantity'); // total quantity
            $table->string('voucher')->nullable();

            $table->string('tracking_id')->nullable();

            $table->string('status')->default('pending');
            $table->boolean('payment_success')->default(false);
            $table->dateTime('expire_at');

            $table->foreignId('customer_id')->constrained('customers')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('payment_provider_id')->nullable()->constrained('payment_providers')->cascadeOnUpdate()->nullOnDelete();
            $table->string('customer_gstin')->nullable();
            // billing address
            $table->boolean('shipping_is_billing')->default(true);
            $table->foreignId('billing_address_id')->nullable()->constrained('addresses')->onUpdate('cascade')->onDelete('cascade');

            // delivery/shipping address
            $table->foreignId('address_id')->nullable()->constrained('addresses')->cascadeOnUpdate()->cascadeOnDelete();



            $table->timestamps();
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
