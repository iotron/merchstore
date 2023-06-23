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
        Schema::create('product_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('init_quantity');
            $table->unsignedInteger('sold_quantity')->default(0);
            $table->integer('in_stock_quantity')->storedAs('init_quantity - sold_quantity')->nullable();
            $table->boolean('in_stock')->default(true);
            $table->unsignedInteger('priority')->default(1);
            $table->foreignId('product_id')->constrained('products')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_stocks');
    }
};
