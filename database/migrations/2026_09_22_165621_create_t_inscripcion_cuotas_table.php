<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_inscripcion_cuotas', function (Blueprint $table) {
            $table->id();
            $table->integer('id_ins');
            $table->integer('id_fechapago_template')->nullable();
            $table->integer('nro_cuota');
            $table->string('descripcion')->nullable();
            $table->decimal('monto_a_pagar', 10, 2);
            $table->date('fecha_vencimiento');
            $table->string('estado', 20)->default('pendiente')->comment('pendiente, pagado, vencido');
            $table->integer('id_pago')->nullable();
            $table->timestamps();

            $table->index('id_ins');
            $table->index('fecha_vencimiento');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_inscripcion_cuotas');
    }
};
