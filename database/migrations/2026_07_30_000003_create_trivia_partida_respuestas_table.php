<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trivia_partida_respuestas', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('partida_id');
            $table->foreign('partida_id')
                ->references('id')->on('trivia_partidas')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('jugador_id');
            $table->foreign('jugador_id')
                ->references('id')->on('trivia_partida_jugadores')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('pregunta_id');
            $table->foreign('pregunta_id')
                ->references('id')->on('trivia_preguntas')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('opcion_id')->nullable();
            $table->foreign('opcion_id')
                ->references('id')->on('trivia_opciones')
                ->nullOnDelete();

            $table->boolean('es_correcta')->default(false);
            $table->unsignedInteger('tiempo_respuesta_ms')->nullable();

            $table->timestampTz('created_at')->useCurrent();

            $table->index(['jugador_id', 'pregunta_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trivia_partida_respuestas');
    }
};
