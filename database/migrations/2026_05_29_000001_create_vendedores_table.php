<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendedores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 150);
            $table->string('apellido', 150);
            $table->string('ci', 20)->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('foto', 500)->nullable();
            $table->string('pagina', 150)->nullable()->comment('Plataforma asignada: FORMET, VIRTUALTECH, etc.');
            $table->decimal('meta_ventas', 10, 2)->nullable()->comment('Meta de ventas mensual en Bs.');
            $table->decimal('comision', 5, 2)->nullable()->comment('Porcentaje de comisión sobre ventas');
            $table->boolean('activo')->default(true);
            $table->unsignedBigInteger('usuario_id')->nullable()->comment('FK a users — usuario del sistema vinculado');
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();

            $table->index('activo');
            $table->index('pagina');
            $table->index('usuario_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendedores');
    }
};
