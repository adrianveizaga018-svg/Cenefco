<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comisiones_liquidacion', function (Blueprint $table) {
            $table->unsignedInteger('total_inscritos')->nullable()->after('fecha_hasta');
            $table->decimal('porcentaje_comision', 5, 2)->nullable()->change();
        });

        Schema::table('comisiones_liquidacion_detalle', function (Blueprint $table) {
            $table->unsignedBigInteger('categoria_id')->nullable()->after('id_ins');
            $table->foreign('categoria_id')->references('id')->on('web_categoria_programa')->nullOnDelete();
            $table->decimal('comision_monto', 8, 2)->nullable()->after('categoria_id');
            $table->decimal('monto_pagado', 12, 2)->nullable()->change();

            $table->dropUnique(['id_pago']);
        });
    }

    public function down(): void
    {
        Schema::table('comisiones_liquidacion_detalle', function (Blueprint $table) {
            $table->unique('id_pago');

            $table->dropForeign(['categoria_id']);
            $table->dropColumn(['categoria_id', 'comision_monto']);
        });

        Schema::table('comisiones_liquidacion_detalle', function (Blueprint $table) {
            $table->decimal('monto_pagado', 12, 2)->nullable(false)->change();
        });

        Schema::table('comisiones_liquidacion', function (Blueprint $table) {
            $table->decimal('porcentaje_comision', 5, 2)->nullable(false)->default(0)->change();
            $table->dropColumn('total_inscritos');
        });
    }
};
