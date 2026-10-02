<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_calendario_academico', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('titulo', 300);
            $table->text('descripcion')->nullable();
            $table->string('tipo', 100)->nullable();
            $table->string('color', 7)->nullable();
            $table->unsignedInteger('programa_id')->nullable();
            $table->timestampTz('fecha_inicio');
            $table->timestampTz('fecha_fin')->nullable();
            $table->boolean('todo_el_dia')->default(true);
            $table->boolean('destacado')->default(false);
            $table->boolean('publico')->default(true);
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();

            $table->index('fecha_inicio');
            $table->index('programa_id');
            $table->index('tipo');
            $table->index('publico');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_calendario_academico');
    }
};
