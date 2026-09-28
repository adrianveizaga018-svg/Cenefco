<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega columnas de gestión de envíos a provincias:
 * - departamento   : uno de los 9 departamentos de Bolivia (siempre requerido cuando hay envío)
 * - ciudad_libre   : provincia/ciudad específica (texto libre con autocomplete, reemplaza ciudad_destino)
 * - estado         : pendiente | enviado | entregado
 * - agencia        : nombre de la flota/agencia de transporte
 * - nro_seguimiento: número de guía o seguimiento
 * - fecha_entrega  : fecha en que el estudiante recibió el envío
 * - notificado     : si se envió WhatsApp al estudiante con info del envío
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('envios_certificado', function (Blueprint $table) {
            // Departamento: los 9 departamentos de Bolivia
            $table->string('departamento', 50)->nullable()->after('id_ins');

            // ciudad_libre reemplaza semánticamente a ciudad_destino (que ya existe)
            // lo dejamos como alias — ciudad_destino contiene el texto libre
            // Agregamos estado del envío
            $table->string('estado', 20)->default('pendiente')->after('aclaraciones');

            // Datos del transporte (se rellenan al marcar como enviado)
            $table->string('agencia', 150)->nullable()->after('estado');
            $table->string('nro_seguimiento', 100)->nullable()->after('agencia');
            $table->date('fecha_entrega')->nullable()->after('nro_seguimiento');

            // Si ya se notificó al estudiante por WhatsApp
            $table->boolean('notificado')->default(false)->after('fecha_entrega');

            // Quién marcó como enviado
            $table->unsignedInteger('enviado_por')->nullable()->after('notificado');
            $table->timestamp('enviado_at')->nullable()->after('enviado_por');
        });
    }

    public function down(): void
    {
        Schema::table('envios_certificado', function (Blueprint $table) {
            $table->dropColumn([
                'departamento', 'estado', 'agencia', 'nro_seguimiento',
                'fecha_entrega', 'notificado', 'enviado_por', 'enviado_at',
            ]);
        });
    }
};
