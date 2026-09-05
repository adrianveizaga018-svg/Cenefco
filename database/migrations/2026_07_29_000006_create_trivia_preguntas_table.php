<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trivia_preguntas', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('categoria_id');
            $table->foreign('categoria_id')
                ->references('id')->on('trivia_categorias')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('nivel_id');
            $table->foreign('nivel_id')
                ->references('id')->on('trivia_niveles')
                ->cascadeOnDelete();

            $table->text('enunciado');
            $table->string('imagen_url', 500)->nullable();
            $table->unsignedSmallInteger('tiempo_limite_segundos')->default(20);
            $table->boolean('activo')->default(true);

            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestampTz('deleted_at')->nullable();

            $table->index(['categoria_id', 'nivel_id', 'activo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trivia_preguntas');
    }
};
