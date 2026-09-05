<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_area', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('titulo', 200);
            $table->string('slug', 220)->unique();
            $table->text('descripcion')->nullable();
            $table->string('logo_url', 500)->nullable();
            $table->string('logo_alt', 300)->nullable();
            $table->json('galeria')->nullable();
            $table->string('color', 7)->nullable();
            $table->string('icono', 100)->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->string('meta_titulo', 300)->nullable();
            $table->string('meta_descripcion', 500)->nullable();
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_area');
    }
};
