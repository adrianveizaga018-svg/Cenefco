<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('categorias_noticia', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 150);
            $table->string('slug', 160)->unique();
            $table->string('descripcion', 300)->nullable();
            $table->string('color_hex', 10)->nullable();
            $table->boolean('activa')->default(true);
        });

        Schema::create('tipos_norma', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 150);
            $table->string('sigla', 30)->nullable();
            $table->string('descripcion', 300)->nullable();
            $table->boolean('activo')->default(true);
            $table->string('slug', 160)->unique();
        });

        Schema::create('tipos_documento_transparencia', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 150);
            $table->boolean('activo')->default(true);
        });

        Schema::create('tipos_evento', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 150);
            $table->string('slug', 160)->unique();
            $table->string('color_hex', 10)->nullable();
            $table->boolean('activo')->default(true);
        });

        Schema::create('secretarias', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 200);
            $table->string('sigla', 30)->nullable();
            $table->text('atribuciones')->nullable();
            $table->string('direccion_fisica', 300)->nullable();
            $table->string('telefono', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('horario_atencion', 200)->nullable();
            $table->string('foto_titular_url', 300)->nullable();
            $table->integer('orden_organigrama')->default(0);
            $table->boolean('activa')->default(true);
            $table->string('slug', 220)->unique();
            $table->timestampsTz();
        });

        Schema::create('autoridades', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('secretaria_id')->nullable()->index();
            $table->string('nombre', 150);
            $table->string('apellido', 150)->nullable();
            $table->string('cargo', 200);
            $table->string('tipo', 80)->default('director');
            $table->text('perfil_profesional')->nullable();
            $table->string('email_institucional', 100)->nullable();
            $table->string('foto_url', 300)->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->date('fecha_inicio_cargo')->nullable();
            $table->date('fecha_fin_cargo')->nullable();
            $table->string('slug', 320)->unique();

            $table->index('tipo');
            $table->index('activo');
        });

        Schema::create('organigrama', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('secretaria_id')->nullable()->index();
            $table->unsignedBigInteger('parent_id')->nullable()->index();
            $table->integer('nivel')->default(1);
            $table->integer('orden')->default(0);
            $table->string('imagen_url', 300)->nullable();
            $table->date('fecha_actualizacion')->nullable();
            $table->boolean('vigente')->default(true);
        });

        Schema::create('noticias', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('categoria_id')->nullable()->index();
            $table->unsignedInteger('autor_id')->nullable()->index();
            $table->string('titulo', 300);
            $table->string('slug', 320)->unique();
            $table->text('entradilla')->nullable();
            $table->longText('cuerpo')->nullable();
            $table->string('imagen_principal_url', 300)->nullable();
            $table->string('imagen_alt', 200)->nullable();
            $table->string('estado', 30)->default('borrador')->index();
            $table->boolean('destacada')->default(false);
            $table->timestampTz('fecha_publicacion')->nullable()->index();
            $table->unsignedInteger('vistas')->default(0);
            $table->string('meta_titulo', 200)->nullable();
            $table->string('meta_descripcion', 300)->nullable();
            $table->timestampsTz();
            $table->timestampTz('deleted_at')->nullable();
        });

        Schema::create('noticias_etiquetas', function (Blueprint $table) {
            $table->unsignedBigInteger('noticia_id');
            $table->unsignedBigInteger('etiqueta_id');
            $table->primary(['noticia_id', 'etiqueta_id']);
        });

        Schema::create('comunicados', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('titulo', 300);
            $table->string('slug', 320)->unique();
            $table->text('resumen')->nullable();
            $table->longText('cuerpo')->nullable();
            $table->string('imagen_url', 300)->nullable();
            $table->string('archivo_url', 300)->nullable();
            $table->string('estado', 30)->default('borrador')->index();
            $table->boolean('destacado')->default(false);
            $table->unsignedInteger('vistas')->default(0);
            $table->timestampTz('fecha_publicacion')->nullable()->index();
            $table->timestampTz('created_at')->nullable()->useCurrent();
        });

        Schema::create('normas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('tipo_norma_id')->nullable()->index();
            $table->unsignedInteger('publicado_por')->nullable()->index();
            $table->string('numero', 100)->nullable();
            $table->string('titulo', 300);
            $table->text('resumen')->nullable();
            $table->longText('texto_completo')->nullable();
            $table->string('archivo_pdf_url', 300)->nullable();
            $table->date('fecha_promulgacion')->nullable();
            $table->date('fecha_publicacion_gaceta')->nullable();
            $table->string('estado_vigencia', 50)->default('vigente')->index();
            $table->boolean('activo')->default(true);
            $table->string('tema_principal', 200)->nullable();
            $table->string('palabras_clave', 300)->nullable();
            $table->unsignedInteger('vistas')->default(0);
            $table->string('slug', 320)->unique();
            $table->timestampsTz();
            $table->timestampTz('deleted_at')->nullable();
        });

        Schema::create('manuales_institucionales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tipo', 100)->nullable();
            $table->string('titulo', 300);
            $table->text('descripcion')->nullable();
            $table->string('archivo_url', 300)->nullable();
            $table->string('formato', 20)->nullable();
            $table->integer('numero_paginas')->nullable();
            $table->smallInteger('gestion')->nullable();
            $table->tinyInteger('version')->default(1);
            $table->boolean('vigente')->default(true);
            $table->unsignedInteger('publicado_por')->nullable();
            $table->date('fecha_publicacion')->nullable();
            $table->unsignedInteger('descargas')->default(0);
            $table->timestampTz('created_at')->nullable()->useCurrent();
        });

        Schema::create('documentos_transparencia', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('tipo_documento_id')->nullable()->index();
            $table->unsignedBigInteger('secretaria_id')->nullable()->index();
            $table->unsignedInteger('publicado_por')->nullable();
            $table->string('titulo', 300);
            $table->text('descripcion')->nullable();
            $table->string('archivo_url', 300)->nullable();
            $table->smallInteger('gestion')->nullable()->index();
            $table->date('fecha_publicacion')->nullable();
            $table->boolean('activo')->default(true);
            $table->string('slug', 320)->unique();
            $table->timestampTz('created_at')->nullable()->useCurrent();
        });

        Schema::create('directorio_institucional', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('secretaria_id')->nullable()->index();
            $table->string('nombre_unidad', 200);
            $table->text('descripcion')->nullable();
            $table->string('titular', 200)->nullable();
            $table->string('cargo_responsable', 200)->nullable();
            $table->string('direccion_fisica', 300)->nullable();
            $table->string('telefono_principal', 50)->nullable();
            $table->string('telefono_secundario', 50)->nullable();
            $table->string('email_institucional', 100)->nullable();
            $table->string('foto_url', 300)->nullable();
            $table->string('horario_lunes_viernes', 100)->nullable();
            $table->string('horario_sabado', 100)->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
        });

        Schema::create('historia_municipio', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('titulo', 300)->nullable();
            $table->longText('contenido')->nullable();
            $table->string('fecha_inicio', 50)->nullable();
            $table->string('fecha_fin', 50)->nullable();
            $table->string('imagen_url', 300)->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestampTz('created_at')->nullable()->useCurrent();
        });

        Schema::create('eventos_fotos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('evento_id')->index();
            $table->string('archivo_url', 300);
            $table->string('titulo', 200)->nullable();
            $table->integer('orden')->default(0);
        });

        Schema::create('sugerencias', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('usuario_id')->nullable()->index();
            $table->string('asunto', 200);
            $table->text('mensaje');
            $table->string('email_respuesta', 100)->nullable();
            $table->unsignedBigInteger('secretaria_destino_id')->nullable()->index();
            $table->string('estado', 50)->default('pendiente')->index();
            $table->text('respuesta')->nullable();
            $table->unsignedInteger('respondido_por')->nullable();
            $table->timestampTz('respondido_at')->nullable();
            $table->timestampTz('created_at')->nullable()->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sugerencias');
        Schema::dropIfExists('eventos_fotos');
        Schema::dropIfExists('historia_municipio');
        Schema::dropIfExists('directorio_institucional');
        Schema::dropIfExists('documentos_transparencia');
        Schema::dropIfExists('manuales_institucionales');
        Schema::dropIfExists('normas');
        Schema::dropIfExists('comunicados');
        Schema::dropIfExists('noticias_etiquetas');
        Schema::dropIfExists('noticias');
        Schema::dropIfExists('organigrama');
        Schema::dropIfExists('autoridades');
        Schema::dropIfExists('secretarias');
        Schema::dropIfExists('tipos_evento');
        Schema::dropIfExists('tipos_documento_transparencia');
        Schema::dropIfExists('tipos_norma');
        Schema::dropIfExists('categorias_noticia');
    }
};
