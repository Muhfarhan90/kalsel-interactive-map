<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['tourism_categories', 'culinary_categories', 'transportation_categories'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->unsignedInteger('sort_order')->default(0);
            });
        }
    }

    public function down(): void
    {
        foreach (['tourism_categories', 'culinary_categories', 'transportation_categories'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }
    }
};
