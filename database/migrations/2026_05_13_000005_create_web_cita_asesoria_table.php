<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('web_cita_asesoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asesor_id')
                  ->constrained('web_asesor')
                  ->cascadeOnDelete();
            $table->string('nombre_solicitante');
            $table->string('email_solicitante');
            $table->string('telefono_solicitante', 20)->nullable();
            $table->unsignedInteger('programa_id')->nullable()
                  ->comment('FK lógica a t_programa');
            $table->dateTime('fecha_hora');
            $table->unsignedSmallInteger('duracion_minutos')->default(30);
            $table->string('modalidad', 30)->default('videollamada');
            $table->string('estado', 30)->default('pendiente');
            $table->text('notas')->nullable();
            $table->timestampsTz();
            $table->index('asesor_id');
        });
    }

    public function down(): void { Schema::dropIfExists('web_cita_asesoria'); }
};
