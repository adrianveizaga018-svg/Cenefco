<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trivia_opciones', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('pregunta_id');
            $table->foreign('pregunta_id')
                ->references('id')->on('trivia_preguntas')
                ->cascadeOnDelete();

            $table->string('texto', 300);
            $table->boolean('es_correcta')->default(false);
            $table->unsignedInteger('orden')->default(0);

            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('pregunta_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trivia_opciones');
    }
};
