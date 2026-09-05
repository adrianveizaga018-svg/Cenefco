<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_pagina', function (Blueprint $table) {
            $table->integer('id_pagina');
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_pagina')->default('0');
            $table->string('titulo_pagina', 200)->nullable();
            $table->string('slug', 300)->nullable()->unique();
            $table->longText('contenido_html')->nullable();
            $table->string('imagen_og_url', 255)->nullable();
            $table->string('template', 100)->nullable();
            $table->string('meta_titulo', 300)->nullable();
            $table->string('meta_descripcion', 500)->nullable();
            $table->boolean('indexar')->default(true);
            $table->integer('orden')->default(0);
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->dateTime('fecha_reg')->nullable();
            $table->timestampTz('updated_at')->nullable();
            $table->timestampTz('deleted_at')->nullable();
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_pagina', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_pagina');
    }
};
