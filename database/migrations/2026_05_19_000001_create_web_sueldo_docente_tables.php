<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_sueldo_docente', function (Blueprint $table) {
            $table->id();
            $table->integer('id_us');
            $table->integer('id_imp')->nullable();
            $table->string('concepto', 300);
            $table->string('periodo', 20)->nullable();
            $table->smallInteger('gestion')->nullable();
            $table->decimal('monto_total', 12, 2)->default(0);
            $table->text('observacion')->nullable();
            $table->string('archivo_pdf')->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_us');
            $table->index('id_imp');
            $table->index(['periodo', 'gestion']);
        });

        Schema::create('web_pago_sueldo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_sueldo')->constrained('web_sueldo_docente')->cascadeOnDelete();
            $table->decimal('monto_pagado', 12, 2);
            $table->date('fecha_pago');
            $table->string('nro_comprobante', 100)->nullable();
            $table->string('comprobante_archivo')->nullable();
            $table->text('observacion')->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_sueldo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_pago_sueldo');
        Schema::dropIfExists('web_sueldo_docente');
    }
};
