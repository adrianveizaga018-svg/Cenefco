<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trivia_partida_jugadores', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('partida_id');
            $table->foreign('partida_id')
                ->references('id')->on('trivia_partidas')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id')
                ->references('id')->on('usuarios')
                ->cascadeOnDelete();

            $table->unsignedInteger('puntaje')->default(0);
            $table->unsignedTinyInteger('vidas')->default(3);
            $table->string('estado', 20)->default('jugando');
            $table->unsignedTinyInteger('orden_turno')->default(0);

            $table->unsignedBigInteger('pregunta_actual_id')->nullable();
            $table->foreign('pregunta_actual_id')
                ->references('id')->on('trivia_preguntas')
                ->nullOnDelete();

            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['partida_id', 'usuario_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trivia_partida_jugadores');
    }
};
