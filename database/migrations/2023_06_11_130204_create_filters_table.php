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
        Schema::create('filters', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('display_name'); // change to display_name from admin_name
            $table->string('type');
            $table->string('desc');
            $table->json('validation')->nullable();
            $table->unsignedBigInteger('position')->nullable();
            $table->boolean('is_filterable')->default(false);
            $table->boolean('is_configurable')->default(false);
            $table->boolean('is_user_defined')->default(true);
            $table->boolean('is_required')->default(true);
            $table->boolean('is_visible_on_front')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filters');
    }
};
