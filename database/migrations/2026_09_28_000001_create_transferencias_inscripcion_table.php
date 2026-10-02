<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Registra el historial de cambios de programa de un estudiante.
 * Cada fila = un cambio: inscripción origen → inscripción destino.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transferencias_inscripcion', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Inscripción que se da de baja
            $table->unsignedInteger('id_ins_origen');
            // Nueva inscripción creada en el programa destino
            $table->unsignedInteger('id_ins_destino');

            // Estudiante
            $table->unsignedInteger('id_us');

            // Montos
            $table->decimal('monto_transferido', 12, 2)->default(0);

            // Motivo del cambio
            $table->text('motivo')->nullable();

            // Quién lo procesó y cuándo
            $table->unsignedInteger('procesado_por')->nullable();
            $table->timestampTz('created_at')->useCurrent();

            $table->index('id_ins_origen');
            $table->index('id_ins_destino');
            $table->index('id_us');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transferencias_inscripcion');
    }
};
