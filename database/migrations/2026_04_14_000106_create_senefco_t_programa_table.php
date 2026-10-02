<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_programa', function (Blueprint $table) {
            $table->integer('id_programa');
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_programa')->default('0');
            $table->string('nombre_programa', 200)->nullable();
            $table->string('slug', 300)->nullable()->unique();
            $table->text('descripcion')->nullable();
            $table->string('foto', 200)->nullable()->default('uno.png');
            $table->string('imagen_banner_url', 255)->nullable();
            $table->string('imagen_alt', 255)->nullable();
            $table->boolean('destacado')->default(false);
            $table->integer('vistas')->default(0);
            $table->integer('orden')->default(0);
            $table->unsignedBigInteger('categoria_web_id')->nullable();
            $table->string('meta_titulo', 300)->nullable();
            $table->string('meta_descripcion', 500)->nullable();
            $table->string('estado_web', 50)->default('borrador');
            $table->timestampTz('fecha_publicacion')->nullable();
            $table->date('inicio_actividades')->nullable()->default('2000-01-01');
            $table->date('finalizacion_actividades')->nullable()->default('2000-01-01');
            $table->date('inicio_inscripciones')->nullable()->default('2000-01-01');
            $table->string('titulo_documento1', 200)->nullable();
            $table->string('documento1', 200)->nullable();
            $table->string('titulo_documento2', 200)->nullable();
            $table->string('documento2', 200)->nullable();
            $table->string('titulo_documento3', 200)->nullable();
            $table->string('documento3', 200)->nullable();
            $table->string('titulo_documento4', 200)->nullable();
            $table->string('documento4', 200)->nullable();
            $table->text('dirigido')->nullable();
            $table->text('inversion')->nullable();
            $table->text('requisitos')->nullable();
            $table->text('creditaje')->nullable();
            $table->text('objetivo')->nullable();
            $table->text('nota')->nullable();
            $table->integer('id_tipoprograma')->nullable()->default('0');
            $table->integer('id_plan')->nullable()->default(null);
            $table->integer('id_plandoc')->nullable()->default(null);
            $table->integer('id_imp')->nullable();
            $table->unsignedBigInteger('convenio_id')->nullable()->index();
            $table->string('url_video', 200)->nullable();
            $table->string('url_whatsapp', 500)->nullable();
            $table->string('url_whatsapp2', 500)->nullable();
            $table->json('imagenes')->nullable();
            $table->text('mensaje_exito')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->dateTime('fecha_reg')->nullable();
            $table->timestampTz('updated_at')->nullable();
            $table->timestampTz('deleted_at')->nullable();
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_programa', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_programa');
    }
};
