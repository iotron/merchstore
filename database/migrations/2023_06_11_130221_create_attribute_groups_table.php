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
        Schema::create('attribute_groups', function (Blueprint $table) {
            $table->id();
            $table->string('admin_name');
            $table->string('code')->unique();
            $table->unsignedBigInteger('position')->nullable();
            $table->enum('type', ['static', 'filterable']);
            $table->timestamps();
        });

        Schema::create('attribute_group_mappings', function (Blueprint $table) {
            $table->primary(['attribute_id', 'attribute_group_id']);
            $table->foreignId('attribute_id')->constrained('attributes')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('attribute_group_id')->constrained('attribute_groups')->onUpdate('cascade')->onDelete('cascade');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_groups');
        Schema::dropIfExists('attribute_groups_mappings');
    }
};
