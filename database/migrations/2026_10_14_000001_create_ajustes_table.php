<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajustes del sistema (clave → valor). El módulo existía (rutas /ajustes) pero ninguna migración
 * creaba la tabla y sus pantallas daban error 500. Si la tabla ya existe (base heredada), no se toca.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ajustes')) {
            return;
        }

        Schema::create('ajustes', function (Blueprint $table) {
            $table->string('clave', 100)->primary();
            $table->text('valor')->nullable();
            $table->string('tipo', 20)->default('texto');
            $table->string('descripcion', 255)->nullable();
            $table->boolean('editable')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ajustes');
    }
};
