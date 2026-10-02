<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_evento', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('titulo', 300);
            $table->string('slug', 300)->unique();
            $table->string('entradilla', 500)->nullable();
            $table->text('descripcion')->nullable();
            $table->string('imagen_url', 255)->nullable();
            $table->string('imagen_alt', 255)->nullable();
            $table->string('tipo', 100)->nullable();
            $table->string('modalidad', 50)->default('presencial');
            $table->string('lugar', 255)->nullable();
            $table->string('url_transmision', 255)->nullable();
            $table->string('url_registro', 255)->nullable();
            $table->boolean('gratuito')->default(true);
            $table->decimal('precio', 10, 2)->nullable();
            $table->integer('cupo_maximo')->nullable();
            $table->unsignedInteger('programa_id')->nullable();
            $table->timestampTz('fecha_inicio');
            $table->timestampTz('fecha_fin')->nullable();
            $table->boolean('todo_el_dia')->default(false);
            $table->boolean('destacado')->default(false);
            $table->integer('vistas')->default(0);
            $table->string('meta_titulo', 300)->nullable();
            $table->string('meta_descripcion', 500)->nullable();
            $table->string('estado', 50)->default('publicado');
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();
            $table->timestampTz('deleted_at')->nullable();

            $table->index('fecha_inicio');
            $table->index('estado');
            $table->index('tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_evento');
    }
};
