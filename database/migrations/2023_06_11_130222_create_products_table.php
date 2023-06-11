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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('sku')->unique();
            $table->string('type');
            $table->string('name');
            $table->string('url_key')->unique();

            $table->integer('quantity')->default(0);
            $table->integer('popularity')->default(0);
            $table->unsignedBigInteger('view_count')->default(0);
            // visibility control for admin
            $table->boolean('featured')->default(false);
            $table->string('status')->default('draft');

            // visibility control for vendor
            $table->boolean('visible_individually')->default(false);

            $table->float('base_price', 10, 2, true)->default(0.00);
            $table->float('commission_percent', 4, 2, true)->default(0.00);
            $table->float('commission_amount', 10, 2, true)->default(0.00);

            // tax info
            $table->string('hsn_code')->nullable();
            $table->float('tax_percent', 4, 2, true)->default(0.00);
            $table->float('tax_amount', 10, 2)->default(0.00);

            $table->float('price', 10, 2, true)->default(0.00);

            $table->json('commissions')->nullable();
            $table->foreignId('attribute_group_id')->nullable()->constrained('attribute_groups')->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('parent_id')->nullable()->constrained('products')->onUpdate('cascade')->onDelete('cascade');

            // For Cart
            $table->integer('min_range')->default(1);
            $table->integer('max_range')->default(1);


            // currently not used
            // $table->integer('return_window')->default(0);
            // $table->integer('production_time')->default(0);

            // $table->foreignId('tax_category_id')->nullable()->constrained('tax_categories')->onUpdate('cascade')->onDelete('set null');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
