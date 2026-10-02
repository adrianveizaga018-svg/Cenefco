<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_categoria_programa', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 200);
            $table->string('slug', 200)->unique();
            $table->text('descripcion')->nullable();
            $table->string('imagen_url', 255)->nullable();
            $table->string('imagen_alt', 255)->nullable();
            $table->string('icono', 100)->nullable();
            $table->string('color', 7)->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->string('meta_titulo', 300)->nullable();
            $table->string('meta_descripcion', 500)->nullable();
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_categoria_programa');
    }
};
