<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regencies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('map_image')->nullable();
            $table->timestamps();
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->foreignId('regency_id')->nullable()->constrained()->restrictOnDelete();
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('locations', 'regency_id')) {
            Schema::table('locations', function (Blueprint $table) {
                $table->dropConstrainedForeignId('regency_id');
            });
        }

        Schema::dropIfExists('regencies');
    }
};
