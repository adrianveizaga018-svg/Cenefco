<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_galeria_video', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('titulo', 300);
            $table->text('descripcion')->nullable();
            $table->string('plataforma', 50)->default('youtube');
            $table->string('url_video', 500);
            $table->string('video_id', 100)->nullable();
            $table->string('miniatura_url', 255)->nullable();
            $table->string('duracion', 20)->nullable();
            $table->string('tipo', 100)->nullable();
            $table->unsignedInteger('programa_id')->nullable();
            $table->boolean('destacado')->default(false);
            $table->integer('orden')->default(0);
            $table->integer('vistas')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();

            $table->index('tipo');
            $table->index('programa_id');
            $table->index('destacado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_galeria_video');
    }
};
