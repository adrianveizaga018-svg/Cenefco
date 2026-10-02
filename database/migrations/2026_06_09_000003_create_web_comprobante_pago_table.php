<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_comprobante_pago', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('id_ins');
            $table->unsignedInteger('id_pago')->nullable()->comment('NULL = comprobante de plan completo');
            $table->string('tipo', 30)->default('recibo')->comment('recibo|resumen_plan|anticipo');
            $table->string('codigo', 30)->unique()->comment('cenefco-REC-2026-XXXXX');
            $table->string('archivo_pdf', 500)->nullable();
            $table->tinyInteger('enviado_email')->default(0);
            $table->timestamp('fecha_envio')->nullable();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_ins');
            $table->index('id_pago');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_comprobante_pago');
    }
};
