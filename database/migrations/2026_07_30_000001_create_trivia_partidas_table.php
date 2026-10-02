<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trivia_partidas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('modo', 20)->default('individual');

            $table->unsignedBigInteger('categoria_id');
            $table->foreign('categoria_id')
                ->references('id')->on('trivia_categorias')
                ->cascadeOnDelete();

            $table->string('estado', 20)->default('en_curso');
            $table->string('codigo_sala', 20)->nullable()->unique();

            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index(['modo', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trivia_partidas');
    }
};
