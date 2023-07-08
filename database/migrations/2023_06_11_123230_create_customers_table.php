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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('contact')->nullable()->unique();
            $table->boolean('whatsapp')->nullable()->unique();
            $table->string('alt_contact')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('contact_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->string('referrer')->nullable();
            $table->boolean('has_push')->default(false);
            $table->foreignId('customer_group_id')->nullable()->default(1)->constrained('customer_groups')
                ->onUpdate('cascade')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
