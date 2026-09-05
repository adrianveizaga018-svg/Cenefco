<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_correo_enviado', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tipo', 60);
            $table->string('destinatario', 190);
            $table->string('asunto', 190);
            $table->string('referencia_tipo', 60)->nullable();
            $table->unsignedInteger('referencia_id')->nullable();
            $table->string('estado', 20)->default('enviado');
            $table->text('error')->nullable();
            $table->unsignedInteger('enviado_por')->nullable();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index(['referencia_tipo', 'referencia_id']);
            $table->index('destinatario');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_correo_enviado');
    }
};
