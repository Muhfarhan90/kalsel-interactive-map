<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_page_headers', function (Blueprint $table) {
            $table->id();
            $table->string('page_key')->unique();
            $table->string('header_title');
            $table->string('header_logo')->nullable();
            $table->string('header_logo_text')->nullable();
            $table->string('header_background_color', 7);
            $table->timestamps();
        });

        $map = DB::table('tourism_maps')->orderByDesc('id')->first();
        $title = $map->header_sub_title ?? 'INTERACTIVE MAP GUIDANCE';
        $logoText = $map->header_title ?? 'KALIMANTAN SELATAN';
        $logo = $map->map_logo ?? 'images/logo/logo_kalsel.svg';
        $now = now();

        DB::table('public_page_headers')->insert([
            ['page_key' => 'home', 'header_title' => $title, 'header_logo' => $logo, 'header_logo_text' => $logoText, 'header_background_color' => '#183832', 'created_at' => $now, 'updated_at' => $now],
            ['page_key' => 'tourism', 'header_title' => $title, 'header_logo' => $logo, 'header_logo_text' => $logoText, 'header_background_color' => '#da251d', 'created_at' => $now, 'updated_at' => $now],
            ['page_key' => 'culinary', 'header_title' => $title, 'header_logo' => $logo, 'header_logo_text' => $logoText, 'header_background_color' => '#9a6507', 'created_at' => $now, 'updated_at' => $now],
            ['page_key' => 'industry', 'header_title' => $title, 'header_logo' => $logo, 'header_logo_text' => $logoText, 'header_background_color' => '#0061a8', 'created_at' => $now, 'updated_at' => $now],
        ]);

        Schema::table('tourism_maps', function (Blueprint $table) {
            $table->dropColumn(['header_title', 'header_sub_title']);
        });
    }

    public function down(): void
    {
        Schema::table('tourism_maps', function (Blueprint $table) {
            $table->string('header_title')->default('Kalimantan Selatan');
            $table->string('header_sub_title')->default('Interactive Map Guidance');
        });

        $tourismHeader = DB::table('public_page_headers')->where('page_key', 'tourism')->first();
        if ($tourismHeader) {
            DB::table('tourism_maps')->orderByDesc('id')->limit(1)->update([
                'header_title' => $tourismHeader->header_title,
            ]);
        }

        Schema::dropIfExists('public_page_headers');
    }
};
