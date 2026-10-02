<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_banner', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('titulo', 300)->nullable();
            $table->string('subtitulo', 500)->nullable();
            $table->string('imagen_url', 255);
            $table->string('imagen_alt', 255)->nullable();
            $table->string('imagen_movil_url', 255)->nullable();
            $table->string('enlace_url', 255)->nullable();
            $table->string('enlace_texto', 100)->nullable();
            $table->string('enlace_target', 10)->default('_self');
            $table->string('posicion', 50)->default('hero');
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestampTz('fecha_inicio')->nullable();
            $table->timestampTz('fecha_fin')->nullable();
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_banner');
    }
};
