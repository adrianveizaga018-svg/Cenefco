<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_nota_prensa', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('titulo', 300);
            $table->string('medio', 200);
            $table->string('logo_medio_url', 255)->nullable();
            $table->string('logo_medio_alt', 255)->nullable();
            $table->text('resumen')->nullable();
            $table->string('url_noticia', 500)->nullable();
            $table->date('fecha_publicacion');
            $table->boolean('destacada')->default(false);
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();

            $table->index('fecha_publicacion');
            $table->index('destacada');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_nota_prensa');
    }
};
