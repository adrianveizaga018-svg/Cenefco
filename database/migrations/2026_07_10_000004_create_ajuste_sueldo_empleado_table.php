<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ajuste_sueldo_empleado', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empleado_id');
            $table->unsignedSmallInteger('anio');
            $table->unsignedTinyInteger('mes');
            $table->string('tipo', 20);
            $table->decimal('monto', 10, 2);
            $table->string('motivo', 300);
            $table->boolean('aplicado')->default(false);
            $table->unsignedBigInteger('planilla_detalle_id')->nullable();
            $table->unsignedInteger('registrado_por')->nullable();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('empleado_id')->references('id')->on('empleado');
            $table->foreign('planilla_detalle_id')->references('id')->on('planilla_detalle')->nullOnDelete();
            $table->index(['empleado_id', 'anio', 'mes']);
            $table->index('aplicado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ajuste_sueldo_empleado');
    }
};
