<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transportation_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_name', 100)->unique();
            $table->string('category_color')->nullable();
            $table->string('category_icon')->nullable();
            $table->text('category_description')->nullable();
            $table->timestamps();
        });

        Schema::create('transportation_maps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('map_id')->constrained('maps')->restrictOnDelete();
            $table->string('map_title');
            $table->string('map_sub_title')->nullable();
            $table->string('map_logo')->nullable();
            $table->timestamps();
        });

        Schema::create('transportation_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('transportation_categories')->restrictOnDelete();
            $table->foreignId('map_id')->constrained('transportation_maps')->restrictOnDelete();
            $table->string('location_name', 150);
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

    public function down(): void
    {
        Schema::dropIfExists('transportation_locations');
        Schema::dropIfExists('transportation_maps');
        Schema::dropIfExists('transportation_categories');
    }
};
