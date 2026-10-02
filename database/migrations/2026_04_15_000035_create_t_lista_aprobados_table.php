<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_lista_aprobados', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('imparte_id');
            $table->unsignedInteger('usuario_id');
            $table->unsignedInteger('inscripcion_id')->nullable();
            $table->decimal('nota_final', 5, 2)->nullable();
            $table->decimal('nota_minima', 5, 2)->nullable();
            $table->string('condicion', 50)->default('aprobado');
            $table->string('observacion', 500)->nullable();
            $table->boolean('ajuste_manual')->default(false);
            $table->string('estado_certificado', 50)->default('pendiente');
            $table->boolean('notificado_email')->default(false);
            $table->timestampTz('fecha_notificacion')->nullable();
            $table->unsignedInteger('registrado_por')->nullable();
            $table->integer('id_us_reg')->default(0);
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();

            $table->unique(['imparte_id', 'usuario_id']);
            $table->index('imparte_id');
            $table->index('usuario_id');
            $table->index('condicion');
            $table->index('estado_certificado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_lista_aprobados');
    }
};
