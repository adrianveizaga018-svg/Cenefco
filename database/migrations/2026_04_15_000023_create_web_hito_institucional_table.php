<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_hito_institucional', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('anio', 10);
            $table->string('titulo', 300);
            $table->text('descripcion')->nullable();
            $table->string('imagen_url', 255)->nullable();
            $table->string('imagen_alt', 255)->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_hito_institucional');
    }
};
