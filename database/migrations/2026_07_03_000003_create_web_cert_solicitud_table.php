<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_cert_solicitud', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('config_item_id');
            $table->unsignedBigInteger('inscripcion_id')->nullable();
            $table->string('usuario_ci', 30);
            $table->string('usuario_nombre', 300);
            $table->string('usuario_email', 200)->nullable();
            $table->boolean('es_gratuito')->default(false);
            $table->string('estado', 30)->default('pendiente_pago');
            $table->string('comprobante_url', 500)->nullable();
            $table->decimal('monto_pagado', 8, 2)->nullable();
            $table->text('nota_admin')->nullable();
            $table->unsignedBigInteger('certificado_id')->nullable();
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();

            $table->foreign('config_item_id')->references('id')->on('web_cert_config_item')->restrictOnDelete();
            $table->foreign('certificado_id')->references('id')->on('t_certificado')->nullOnDelete();
            $table->index('estado');
            $table->index('usuario_ci');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_cert_solicitud');
    }
};
