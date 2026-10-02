<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_categoria_campo', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('categoria_id');
            $table->string('nombre_campo', 80);
            $table->string('etiqueta', 200);
            $table->string('tipo_campo', 30)->default('text');
            $table->boolean('requerido')->default(false);
            $table->integer('orden')->default(0);
            $table->tinyInteger('paso')->default(2);
            $table->boolean('activo')->default(true);
            $table->string('ayuda', 400)->nullable();
            $table->json('opciones')->nullable();
            $table->json('validacion')->nullable();
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();

            $table->foreign('categoria_id')
                ->references('id')
                ->on('web_categoria_programa')
                ->onDelete('cascade');

            $table->index('categoria_id');
            $table->unique(['categoria_id', 'nombre_campo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_categoria_campo');
    }
};
