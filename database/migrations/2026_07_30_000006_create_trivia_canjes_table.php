<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trivia_canjes', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id')
                ->references('id')->on('usuarios')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('premio_id');
            $table->foreign('premio_id')
                ->references('id')->on('trivia_premios')
                ->restrictOnDelete();

            $table->string('codigo', 30)->unique();
            $table->unsignedInteger('costo_puntos'); // snapshot del costo al momento del canje
            $table->string('estado', 20)->default('pendiente'); // pendiente | entregado | cancelado
            $table->text('nota')->nullable();

            $table->unsignedBigInteger('entregado_por')->nullable();
            $table->foreign('entregado_por')
                ->references('id')->on('usuarios')
                ->nullOnDelete();

            $table->timestampTz('fecha_resolucion')->nullable();

            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index(['usuario_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trivia_canjes');
    }
};
