<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trivia_partidas', function (Blueprint $table) {
            $table->json('preguntas_ids')->nullable()->after('codigo_sala');
        });

        Schema::table('trivia_partida_jugadores', function (Blueprint $table) {
            $table->unsignedSmallInteger('pregunta_indice')->default(0)->after('orden_turno');
        });
    }

    public function down(): void
    {
        Schema::table('trivia_partidas', function (Blueprint $table) {
            $table->dropColumn('preguntas_ids');
        });

        Schema::table('trivia_partida_jugadores', function (Blueprint $table) {
            $table->dropColumn('pregunta_indice');
        });
    }
};
