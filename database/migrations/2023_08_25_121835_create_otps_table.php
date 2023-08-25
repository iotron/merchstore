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
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            // unique identifier for email or contact
            $table->string('identifier')->unique();
            // type contact or email
            $table->string('type');
            // generated otp for email, null for firebase contact auth
            $table->string('code')->nullable();
            // return ulid token for register/reset form after verification
            $table->ulid('token')->unique();
            // expire time to check during verification/registration
            $table->timestamp('expires_at');
            // verified at time filled during verification and check during registration
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otps');
    }
};
