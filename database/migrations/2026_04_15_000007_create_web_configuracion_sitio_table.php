<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_configuracion_sitio', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('clave', 100)->unique();
            $table->text('valor')->nullable();
            $table->string('tipo', 50)->default('text');
            $table->string('grupo', 100)->nullable();
            $table->string('etiqueta', 200)->nullable();
            $table->string('descripcion', 500)->nullable();
            $table->boolean('es_publica')->default(true);
            $table->timestampTz('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_configuracion_sitio');
    }
};
