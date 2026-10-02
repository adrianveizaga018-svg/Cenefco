<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_programa_resena', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('programa_id');
            $table->unsignedInteger('usuario_id')->nullable();
            $table->string('foto_url', 500)->nullable();
            $table->string('nombre', 200);
            $table->string('cargo_actual', 200)->nullable();
            $table->tinyInteger('calificacion');
            $table->string('titulo_resena', 300)->nullable();
            $table->text('resena');
            $table->string('estado', 50)->default('pendiente');
            $table->boolean('verificado')->default(false);
            $table->boolean('destacada')->default(false);
            $table->string('motivo_rechazo', 300)->nullable();
            $table->string('ip_origen', 45)->nullable();
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();

            $table->index('programa_id');
            $table->index('estado');
            $table->index('calificacion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_programa_resena');
    }
};
