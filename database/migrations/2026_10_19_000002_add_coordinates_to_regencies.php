<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('regencies', function (Blueprint $table) {
            $table->decimal('x_location', 5, 2)->nullable();
            $table->decimal('y_location', 5, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('regencies', function (Blueprint $table) {
            $table->dropColumn(['x_location', 'y_location']);
        });
    }
};
