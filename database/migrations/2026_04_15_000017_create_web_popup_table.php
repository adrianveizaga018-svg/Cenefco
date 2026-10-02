<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_popup', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('titulo', 300)->nullable();
            $table->text('contenido')->nullable();
            $table->string('imagen_url', 255)->nullable();
            $table->string('enlace_url', 255)->nullable();
            $table->string('enlace_texto', 100)->nullable();
            $table->string('posicion', 50)->default('center');
            $table->integer('delay_segundos')->default(3);
            $table->boolean('mostrar_una_vez_sesion')->default(true);
            $table->boolean('mostrar_una_vez_siempre')->default(false);
            $table->string('paginas_mostrar', 500)->nullable();
            $table->boolean('activo')->default(false);
            $table->timestampTz('fecha_inicio')->nullable();
            $table->timestampTz('fecha_fin')->nullable();
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();

            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_popup');
    }
};
