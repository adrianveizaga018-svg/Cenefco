<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('web_banner', function (Blueprint $table) {
            $table->string('enlace_url_2', 255)->nullable()->after('enlace_target');
            $table->string('enlace_texto_2', 100)->nullable()->after('enlace_url_2');
        });
    }

    public function down(): void
    {
        Schema::table('web_banner', function (Blueprint $table) {
            $table->dropColumn(['enlace_url_2', 'enlace_texto_2']);
        });
    }
};
