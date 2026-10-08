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
        Schema::table('homepages', function (Blueprint $table) {
            $table->string('header_title')->nullable()->after('id');
            $table->string('header_logo')->nullable()->after('header_title');
            $table->string('header_text')->nullable()->after('header_logo');
            $table->string('color')->nullable()->after('header_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('homepages', function (Blueprint $table) {
            $table->dropColumn('header_title');
            $table->dropColumn('header_logo');
            $table->dropColumn('header_text');
            $table->dropColumn('color');
        });
    }
};
