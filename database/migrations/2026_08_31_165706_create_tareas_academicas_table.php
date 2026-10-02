<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tareas_academicas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('programa_id');
            $table->string('titulo', 200);
            $table->text('descripcion')->nullable();
            $table->boolean('requiere_archivo')->default(false);
            $table->string('estado', 50)->default('pendiente');
            $table->string('archivo_url')->nullable();
            $table->unsignedBigInteger('completado_por_usuario_id')->nullable();
            $table->timestamp('fecha_completado')->nullable();
            $table->timestamps();
            
            $table->index('programa_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tareas_academicas');
    }
};
