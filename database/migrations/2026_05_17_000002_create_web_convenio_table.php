<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_convenio', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 300);
            $table->string('institucion', 300)->nullable();
            $table->string('tipo', 100)->nullable();
            $table->text('descripcion')->nullable();
            $table->string('responsable', 200)->nullable();
            $table->string('contacto_email', 100)->nullable();
            $table->string('contacto_telefono', 30)->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->string('documento_url', 500)->nullable();
            $table->string('logo_url', 500)->nullable();
            $table->string('estado', 50)->default('activo');
            $table->integer('orden')->default(0);
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();
            $table->timestampTz('deleted_at')->nullable();

            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_convenio');
    }
};
