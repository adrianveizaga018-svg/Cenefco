<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_devolucion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('id_ins');
            $table->unsignedInteger('id_us');
            $table->decimal('monto', 12, 2);
            $table->text('motivo');
            $table->string('documento_url', 500)->nullable();
            $table->string('estado', 20)->default('pendiente');
            $table->text('nota_respuesta')->nullable();
            $table->timestampTz('fecha_respuesta')->nullable();
            $table->unsignedInteger('registrado_por')->nullable();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_ins');
            $table->index('id_us');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_devolucion');
    }
};
