<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_intents', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 120);
            $table->string('slug', 60)->unique();
            $table->string('dominio', 30)->default('general');
            $table->unsignedSmallInteger('prioridad')->default(400);
            $table->json('eventos')->nullable();
            $table->json('input_contexts')->nullable();
            $table->json('output_contexts')->nullable();
            $table->json('frases_entrenamiento')->nullable();
            $table->json('respuestas')->nullable();
            $table->string('accion', 60);
            $table->boolean('activo')->default(true);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_intents');
    }
};
