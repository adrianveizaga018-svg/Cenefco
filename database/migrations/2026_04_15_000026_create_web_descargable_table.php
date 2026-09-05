<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_descargable', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 300);
            $table->string('tipo', 100)->nullable();
            $table->string('archivo_url', 500);
            $table->string('imagen_portada_url', 255)->nullable();
            $table->unsignedInteger('programa_id')->nullable();
            $table->boolean('requiere_datos')->default(true);
            $table->integer('descargas')->default(0);
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();
            $table->timestampTz('deleted_at')->nullable();

            $table->index('programa_id');
            $table->index('tipo');
        });

        Schema::create('web_descargable_registro', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('descargable_id');
            $table->string('nombre', 200)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('ip_origen', 45)->nullable();
            $table->timestampTz('created_at')->nullable()->useCurrent();

            $table->foreign('descargable_id')
                ->references('id')
                ->on('web_descargable')
                ->onDelete('cascade');

            $table->index('descargable_id');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_descargable_registro');
        Schema::dropIfExists('web_descargable');
    }
};
