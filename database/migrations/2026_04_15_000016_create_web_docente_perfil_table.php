<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_docente_perfil', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('usuario_id')->nullable();
            $table->string('nombre_completo', 300);
            $table->string('titulo_academico', 200)->nullable();
            $table->string('especialidad', 300)->nullable();
            $table->text('biografia')->nullable();
            $table->string('foto_url', 255)->nullable();
            $table->string('foto_alt', 255)->nullable();
            $table->string('email_publico', 100)->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('linkedin_url', 255)->nullable();
            $table->string('twitter_url', 255)->nullable();
            $table->string('sitio_web_url', 255)->nullable();
            $table->string('tipo', 100)->default('docente');
            $table->boolean('mostrar_en_web')->default(true);
            $table->integer('orden')->default(0);
            $table->string('estado', 50)->default('publicado');
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();
            $table->timestampTz('deleted_at')->nullable();

            $table->index('usuario_id');
            $table->index('tipo');
            $table->index('mostrar_en_web');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_docente_perfil');
    }
};
