<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_conversaciones', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 30)->unique();
            $table->string('nombre', 200)->nullable();
            $table->string('estado', 60)->default('menu');
            $table->json('contexto')->nullable();
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->timestamps();
        });

        Schema::create('whatsapp_mensajes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversacion_id')->nullable();
            $table->string('phone', 30);
            $table->enum('direccion', ['entrante', 'saliente']);
            $table->string('tipo', 30)->default('text');
            $table->text('contenido')->nullable();
            $table->string('whatsapp_message_id', 200)->nullable();
            $table->timestamps();

            $table->foreign('conversacion_id')
                ->references('id')->on('whatsapp_conversaciones')
                ->onDelete('set null');
        });

        Schema::create('whatsapp_etiquetas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->string('color', 20)->default('#3B82F6');
            $table->timestamps();
        });

        Schema::create('whatsapp_conversacion_etiqueta', function (Blueprint $table) {
            $table->unsignedBigInteger('conversacion_id');
            $table->unsignedBigInteger('etiqueta_id');
            $table->primary(['conversacion_id', 'etiqueta_id']);

            $table->foreign('conversacion_id')
                ->references('id')->on('whatsapp_conversaciones')
                ->onDelete('cascade');
            $table->foreign('etiqueta_id')
                ->references('id')->on('whatsapp_etiquetas')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_conversacion_etiqueta');
        Schema::dropIfExists('whatsapp_etiquetas');
        Schema::dropIfExists('whatsapp_mensajes');
        Schema::dropIfExists('whatsapp_conversaciones');
    }
};
