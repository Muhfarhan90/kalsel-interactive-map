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
        Schema::create('tourism_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('tourism_categories')->onDelete('cascade');
            $table->foreignId('map_id')->constrained('tourism_maps')->onDelete('cascade');
            $table->string('location_name');
            $table->string('location_address')->nullable();
            $table->text('location_description')->nullable();
            $table->float('coordinate_x', 5)->nullable();
            $table->float('coordinate_y', 5)->nullable();
            $table->string('location_media_url')->nullable();
            $table->string('location_source_media')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tourism_locations');
    }
};
