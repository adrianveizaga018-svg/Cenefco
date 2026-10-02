<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla pivot: programa <-> planes de pago habilitados
        Schema::create('programa_planes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('id_programa');
            $table->unsignedInteger('id_plan');
            $table->timestamps();
            $table->unique(['id_programa', 'id_plan']);
        });

        // Columna para guardar comprobante de pago en t_pago
        Schema::table('t_pago', function (Blueprint $table) {
            $table->string('comprobante_url', 500)->nullable()->after('pago_extra');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programa_planes');
        Schema::table('t_pago', function (Blueprint $table) {
            $table->dropColumn('comprobante_url');
        });
    }
};
