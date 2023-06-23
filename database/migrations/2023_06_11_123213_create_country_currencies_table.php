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
        Schema::create('country_currencies', function (Blueprint $table) {
            $table->id();
            $table->boolean('default')->default(false);
            $table->foreignId('country_id')->constrained('countries')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('currency_id')->constrained('currencies')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country_currencies');
    }
};
