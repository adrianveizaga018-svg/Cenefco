<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_inscripcion_diplomado', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('programa_id')->nullable();
            $table->string('nombre_completo', 200);
            $table->date('fecha_nacimiento')->nullable();
            $table->string('email', 100);
            $table->string('ci', 30)->nullable();
            $table->unsignedBigInteger('expedido_id')->nullable();
            $table->string('telefono_grupo_inscritos', 20)->nullable();
            $table->string('archivo_ci', 255)->nullable();
            $table->string('archivo_titulo', 255)->nullable();
            $table->string('archivo_cv', 255)->nullable();
            $table->string('archivo_foto_3x3', 255)->nullable();
            $table->unsignedInteger('ciudad_residencia_id')->nullable();
            $table->string('provincia_especificar', 120)->nullable();
            $table->string('medio_pago', 100)->nullable();
            $table->unsignedBigInteger('medio_pago_id')->nullable();
            $table->decimal('monto_pagado', 10, 2)->nullable();
            $table->text('sugerencia_curso')->nullable();
            $table->boolean('recomendar_docente')->default(false);
            $table->text('detalle_docente')->nullable();
            $table->string('estado', 50)->default('pendiente');
            $table->boolean('notificado')->default(false);
            $table->string('origen', 100)->nullable();
            $table->string('ip_origen', 45)->nullable();
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();

            $table->index('email');
            $table->index('programa_id');
            $table->index('estado');
            $table->index('expedido_id');
            $table->index('medio_pago_id');
            $table->index('ciudad_residencia_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_inscripcion_diplomado');
    }
};
