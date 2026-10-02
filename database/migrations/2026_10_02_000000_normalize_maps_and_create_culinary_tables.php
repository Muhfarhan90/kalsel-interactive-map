<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maps', function (Blueprint $table) {
            $table->id();
            $table->text('map_image')->nullable();
            $table->text('map_description')->nullable();
            $table->timestamps();
        });

        Schema::table('tourism_maps', function (Blueprint $table) {
            $table->foreignId('map_id')->nullable()->constrained('maps')->restrictOnDelete();
        });

        $tourismMap = DB::table('tourism_maps')->orderByDesc('id')->first(['map_image', 'map_description']);
        if ($tourismMap) {
            $now = now();
            $mapId = DB::table('maps')->insertGetId([
                'map_image' => $tourismMap->map_image,
                'map_description' => $tourismMap->map_description,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('tourism_maps')->update(['map_id' => $mapId]);
        }

        Schema::table('tourism_maps', function (Blueprint $table) {
            $table->dropColumn(['map_image', 'map_description']);
        });

        Schema::create('culinary_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_name', 100);
            $table->string('category_color')->nullable();
            $table->string('category_icon')->nullable();
            $table->text('category_description')->nullable();
            $table->timestamps();
        });

        Schema::create('culinary_maps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('map_id')->constrained('maps')->restrictOnDelete();
            $table->string('map_title');
            $table->string('map_sub_title')->nullable();
            $table->string('map_logo')->nullable();
            $table->timestamps();
        });

        Schema::create('culinary_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('culinary_categories')->restrictOnDelete();
            $table->foreignId('map_id')->constrained('culinary_maps')->restrictOnDelete();
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
        Schema::dropIfExists('culinary_locations');
        Schema::dropIfExists('culinary_maps');
        Schema::dropIfExists('culinary_categories');

        Schema::table('tourism_maps', function (Blueprint $table) {
            $table->text('map_image')->nullable();
            $table->text('map_description')->nullable();
        });

        foreach (DB::table('tourism_maps')
            ->leftJoin('maps', 'tourism_maps.map_id', '=', 'maps.id')
            ->get(['tourism_maps.id', 'maps.map_image', 'maps.map_description']) as $tourismMap) {
            DB::table('tourism_maps')->where('id', $tourismMap->id)->update([
                'map_image' => $tourismMap->map_image,
                'map_description' => $tourismMap->map_description,
            ]);
        }

        Schema::table('tourism_maps', function (Blueprint $table) {
            $table->dropForeign(['map_id']);
            $table->dropColumn('map_id');
        });

        Schema::dropIfExists('maps');
    }
};
