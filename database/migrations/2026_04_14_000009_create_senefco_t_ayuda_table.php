<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_ayuda', function (Blueprint $table) {
            $table->integer('id_ayuda');
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_ayuda')->default('0');
            $table->integer('id_us')->nullable()->default('0');
            $table->string('gestion', 200)->nullable();
            $table->string('monto_pagado', 200)->nullable()->default('0');
            $table->string('nro_recibo', 200)->nullable();
            $table->date('fecha_recibo')->nullable()->default('2000-01-01');
            $table->text('observacion_pago')->nullable();
            $table->integer('id_categoriatipoayuda')->nullable()->default('0');
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_ayuda', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_ayuda');
    }
};
