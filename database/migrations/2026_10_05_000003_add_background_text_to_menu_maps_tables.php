<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['tourism_maps', 'culinary_maps', 'transportation_maps'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('map_background_text', 150)->nullable();
            });
        }
    }

    public function down(): void
    {
        foreach (['tourism_maps', 'culinary_maps', 'transportation_maps'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('map_background_text');
            });
        }
    }
};
