<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_certificado', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('lista_aprobado_id');
            $table->unsignedBigInteger('plantilla_id');
            $table->unsignedInteger('usuario_id');
            $table->unsignedInteger('imparte_id');
            $table->string('nombre_en_certificado', 300);
            $table->string('programa_en_certificado', 300);
            $table->string('condicion', 50);
            $table->decimal('nota_final', 5, 2)->nullable();
            $table->integer('horas_academicas')->nullable();
            $table->date('fecha_inicio_curso')->nullable();
            $table->date('fecha_fin_curso')->nullable();
            $table->string('codigo_verificacion', 50)->unique();
            $table->string('qr_url', 500)->nullable();
            $table->string('archivo_url', 500)->nullable();
            $table->string('archivo_miniatura_url', 255)->nullable();
            $table->string('estado', 50)->default('generado');
            $table->text('motivo_anulacion')->nullable();
            $table->unsignedInteger('anulado_por')->nullable();
            $table->timestampTz('fecha_anulacion')->nullable();
            $table->integer('veces_verificado')->default(0);
            $table->integer('veces_descargado')->default(0);
            $table->timestampTz('ultima_verificacion')->nullable();
            $table->integer('id_us_reg')->default(0);
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();

            $table->foreign('lista_aprobado_id')
                ->references('id')
                ->on('t_lista_aprobados')
                ->onDelete('restrict');

            $table->foreign('plantilla_id')
                ->references('id')
                ->on('t_cert_plantilla')
                ->onDelete('restrict');

            $table->index('codigo_verificacion');
            $table->index('usuario_id');
            $table->index('imparte_id');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_certificado');
    }
};
