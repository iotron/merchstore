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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('pickup_location')->nullable();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('contact');
            $table->string('alternate_contact')->nullable();
            $table->enum('type', ['Home', 'Work', 'Other']);
            $table->string('address_1');
            $table->string('address_2')->nullable();

            $table->string('landmark')->nullable();
            $table->string('city');
            $table->string('postal_code');
            $table->string('state');
            $table->boolean('default')->default(false);
            $table->unsignedInteger('priority')->default(1);
            $table->morphs('addressable');
            $table->string('country_code')->nullable();
            $table->foreign('country_code')->references('iso_code_2')->on('countries')->onUpdate('cascade')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
