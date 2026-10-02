<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_contacto_mensaje', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 200);
            $table->string('email', 100);
            $table->string('telefono', 20)->nullable();
            $table->string('asunto', 300)->nullable();
            $table->text('mensaje');
            $table->string('programa_interes', 200)->nullable();
            $table->unsignedInteger('programa_id')->nullable();
            $table->boolean('leido')->default(false);
            $table->timestampTz('fecha_lectura')->nullable();
            $table->string('estado', 50)->default('nuevo');
            $table->string('respondido_por', 200)->nullable();
            $table->text('respuesta_interna')->nullable();
            $table->string('utm_source', 100)->nullable();
            $table->string('utm_medium', 100)->nullable();
            $table->string('utm_campaign', 100)->nullable();
            $table->string('ip_origen', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();

            $table->index('estado');
            $table->index('leido');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_contacto_mensaje');
    }
};
