<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->json('media_gallery')->nullable()->after('media');
        });

        // Isi media_gallery menggunakan media lama.
        DB::table('locations')
            ->whereNotNull('media')
            ->where('media', '!=', '')
            ->orderBy('id')
            ->chunkById(100, function ($locations) {
                foreach ($locations as $location) {
                    DB::table('locations')
                        ->where('id', $location->id)
                        ->update([
                            'media_gallery' => json_encode(
                                [$location->media],
                                JSON_THROW_ON_ERROR
                            ),
                        ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn('media_gallery');
        });
    }
};
