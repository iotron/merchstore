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
        Schema::create('filter_groups', function (Blueprint $table) {
            $table->id();
            $table->string('admin_name');
            $table->string('code')->unique();
            $table->unsignedBigInteger('position')->nullable();
            $table->enum('type', ['static', 'filterable']);
            $table->timestamps();
        });

        Schema::create('filter_group_mappings', function (Blueprint $table) {
            $table->primary(['filter_id', 'filter_group_id']);
            $table->foreignId('filter_id')->constrained('filters')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('filter_group_id')->constrained('filter_groups')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filter_groups');
        Schema::dropIfExists('filter_group_mappings');
    }
};
