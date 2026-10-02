<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mensajes_contacto', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('secretaria_destino_id')->nullable();
            $table->foreign('secretaria_destino_id')->references('id')->on('secretarias');
            $table->string('nombre_remitente', 150);
            $table->string('email_remitente', 150);
            $table->string('telefono_remitente', 50)->nullable();
            $table->string('asunto', 200);
            $table->text('mensaje');

            $table->string('estado', 20)->default('nuevo');
            $table->text('respuesta')->nullable();
            $table->unsignedBigInteger('respondido_por')->nullable();
            $table->timestampTz('respondido_at')->nullable();
            $table->string('ip_origen', 45)->nullable();
            $table->timestampTz('created_at')->nullable()->useCurrent();

            $table->index('estado');
            $table->index('secretaria_destino_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensajes_contacto');
    }
};
