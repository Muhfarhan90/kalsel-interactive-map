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
        Schema::create('tourism_maps', function (Blueprint $table) {
            $table->id();
            $table->string('map_title');
            $table->string('map_sub_title')->nullable();
            $table->string('map_logo')->nullable();
            $table->text('map_image')->nullable();
            $table->text('map_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tourism_maps');
    }
};
