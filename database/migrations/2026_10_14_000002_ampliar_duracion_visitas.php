<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * duracion_seg era smallint: en PostgreSQL no entra más de 32.767 s y la visita fallaba al guardarse
 * (pasa con pestañas olvidadas abiertas). Se amplía a integer.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('web_visitas', function (Blueprint $table) {
            $table->unsignedInteger('duracion_seg')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('web_visitas', function (Blueprint $table) {
            $table->unsignedSmallInteger('duracion_seg')->nullable()->change();
        });
    }
};
