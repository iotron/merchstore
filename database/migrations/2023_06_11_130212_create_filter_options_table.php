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
        Schema::create('filter_options', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('display_name');
            $table->string('swatch_value')->nullable();
            $table->unsignedBigInteger('position')->nullable();
            $table->foreignId('filter_id')->nullable()->constrained('filters')->onUpdate('cascade')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filter_options');
    }
};
