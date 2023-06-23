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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('iso_code_2')->index();
            $table->string('iso_code_3');
            $table->integer('isd_code');
            $table->string('address_format')->nullable();
            $table->boolean('postcode_required');
            $table->string('locale')->default('en');
            $table->string('region');
            $table->string('timezone');
            $table->string('timezone_diff');
            $table->string('currency')->default('USD');
            $table->string('flag');
            $table->json('exchange_rate')->nullable();
            $table->float('multiplier')->default(1);
            $table->boolean('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
