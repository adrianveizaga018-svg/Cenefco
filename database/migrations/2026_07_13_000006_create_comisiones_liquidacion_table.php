<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comisiones_liquidacion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('vendedor_id');
            $table->foreign('vendedor_id')->references('id')->on('vendedores');
            $table->date('fecha_desde');
            $table->date('fecha_hasta');
            $table->decimal('total_base', 12, 2)->default(0);
            $table->decimal('porcentaje_comision', 5, 2);
            $table->decimal('monto_comision', 12, 2)->default(0);

            $table->string('estado', 20)->default('calculado');
            $table->unsignedBigInteger('aprobado_por')->nullable();
            $table->timestampTz('aprobado_at')->nullable();
            $table->unsignedBigInteger('pagado_por')->nullable();
            $table->timestampTz('pagado_at')->nullable();
            $table->string('comprobante_pago_url', 500)->nullable();
            $table->text('nota')->nullable();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index(['vendedor_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comisiones_liquidacion');
    }
};
