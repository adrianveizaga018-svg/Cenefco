<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('web_inscripcion_diplomado', function (Blueprint $table) {
            $table->string('archivo_comprobante_pago', 255)->nullable()->after('monto_pagado');
        });
    }

    public function down(): void
    {
        Schema::table('web_inscripcion_diplomado', function (Blueprint $table) {
            $table->dropColumn('archivo_comprobante_pago');
        });
    }
};
