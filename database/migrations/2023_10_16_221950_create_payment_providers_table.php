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
        Schema::create('payment_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('key')->unique()->nullable();
            $table->string('secret')->nullable();
            $table->string('webhook')->nullable();
            $table->string('service_provider')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->boolean('has_api')->default(false);
            $table->boolean('status')->default(true);
            $table->text('desc')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_providers');
    }
};
