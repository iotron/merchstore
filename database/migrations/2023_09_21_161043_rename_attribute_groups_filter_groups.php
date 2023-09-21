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
        Schema::rename('attribute_groups', 'filter_groups');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('filter_groups', 'attribute_groups');
    }
};
