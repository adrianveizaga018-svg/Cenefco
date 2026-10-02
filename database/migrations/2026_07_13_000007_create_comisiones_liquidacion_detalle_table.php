<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comisiones_liquidacion_detalle', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('comisiones_liquidacion_id');
            $table->foreign('comisiones_liquidacion_id')
                  ->references('id')->on('comisiones_liquidacion')
                  ->cascadeOnDelete();

            $table->unsignedInteger('id_pago');
            $table->unsignedInteger('id_ins');
            $table->decimal('monto_pagado', 12, 2);
            $table->date('fecha_deposito');

            $table->unique('id_pago');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comisiones_liquidacion_detalle');
    }
};
