<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tourism_maps', function (Blueprint $table) {
            $table->string('header_title')->default('Kalimantan Selatan');
            $table->string('header_sub_title')->default('Interactive Map Guidance');
        });
    }

    public function down(): void
    {
        Schema::table('tourism_maps', function (Blueprint $table) {
            $table->dropColumn(['header_title', 'header_sub_title']);
        });
    }
};
