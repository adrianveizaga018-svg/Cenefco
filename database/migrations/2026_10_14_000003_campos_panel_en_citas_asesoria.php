<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * La pantalla de Citas de asesoría y su API usan nombres, apellidos, celular, mensaje... pero la tabla
 * se creó con otro diseño (nombre_solicitante, fecha_hora...). En PostgreSQL la pantalla daba error 500.
 * Se agregan los campos que faltan sin tocar los existentes.
 */
return new class extends Migration
{
    private array $campos = [
        'nombres' => 'string', 'apellidos' => 'string', 'email' => 'string', 'celular' => 'string',
        'mensaje' => 'text', 'programa_interes' => 'string', 'fecha_preferida' => 'date',
        'hora_preferida' => 'string', 'observacion' => 'text', 'fecha_solicitud' => 'timestamp',
    ];

    public function up(): void
    {
        Schema::table('web_cita_asesoria', function (Blueprint $table) {
            foreach ($this->campos as $campo => $tipo) {
                if (! Schema::hasColumn('web_cita_asesoria', $campo)) {
                    $table->{$tipo}($campo)->nullable();
                }
            }
            // La pantalla no los pide: se llenan con lo que hay (o quedan vacíos) en vez de rechazar la cita
            $table->unsignedBigInteger('asesor_id')->nullable()->change();
            $table->string('nombre_solicitante')->nullable()->change();
            $table->string('email_solicitante')->nullable()->change();
            $table->dateTime('fecha_hora')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('web_cita_asesoria', function (Blueprint $table) {
            $table->dropColumn(array_keys($this->campos));
        });
    }
};
