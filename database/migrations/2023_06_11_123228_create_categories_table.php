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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('url', 100)->nullable();
            $table->boolean('status')->default(true);
            $table->boolean('is_visible_on_front')->default(true);
            $table->unsignedBigInteger('view_count')->default(10);
            $table->unsignedInteger('order')->nullable();
            $table->json('meta_data')->nullable();
            $table->text('desc')->nullable();

            $table->foreignId('parent_id')->nullable()->constrained('categories')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
