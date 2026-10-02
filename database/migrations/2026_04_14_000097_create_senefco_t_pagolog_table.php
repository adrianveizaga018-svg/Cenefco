<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_pagolog', function (Blueprint $table) {
            $table->integer('id_pagolog');
            $table->integer('id_pago')->default('0');
            $table->string('tipo_log', 200)->nullable();
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_pago')->default('0');
            $table->integer('id_us')->nullable()->default('0');
            $table->string('materia_reprobada', 200)->nullable();
            $table->integer('id_mat')->nullable()->default('0');
            $table->string('monto_pago_extra', 200)->nullable();
            $table->integer('id_fechapago')->nullable()->default('0');
            $table->string('monto_pagado', 200)->nullable()->default('0');
            $table->string('nro_boleta_bancaria', 200)->nullable();
            $table->date('fecha_deposito')->nullable()->default('2000-01-01');
            $table->integer('dejo_boleta_deposito_original')->nullable()->default('0');
            $table->string('monto_descuento_extra', 200)->nullable()->default('0');
            $table->string('nro_nit', 200)->nullable();
            $table->string('nombre_nit', 200)->nullable();
            $table->integer('tipo_fechapago')->nullable()->default('0');
            $table->text('observacion_pago')->nullable();
            $table->integer('pago_extra')->nullable()->default('0');
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_pagolog', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_pagolog');
    }
};
