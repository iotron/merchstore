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
        Schema::create('order_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->cascadeOnUpdate()->nullOnDelete();
            $table->integer('quantity')->unsigned();
            $table->integer('amount');  // subtotal  base_price
            $table->integer('discount');  // subtotal discount
            $table->integer('tax');      // subtotal tax
            $table->integer('total');      // subtotal final price
            $table->boolean('has_tax')->storedAs('IF(tax > 0, true, false)');
            //            $table->string('status');
            //            $table->text('status_feedback')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_products');
    }
};
