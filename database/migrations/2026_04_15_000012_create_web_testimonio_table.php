<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_testimonio', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 200);
            $table->string('cargo', 200)->nullable();
            $table->string('empresa', 200)->nullable();
            $table->text('testimonio');
            $table->tinyInteger('calificacion')->default(5);
            $table->string('foto_url', 255)->nullable();
            $table->string('foto_alt', 255)->nullable();
            $table->unsignedInteger('programa_id')->nullable();
            $table->boolean('destacado')->default(false);
            $table->integer('orden')->default(0);
            $table->string('estado', 50)->default('publicado');
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();
            $table->timestampTz('deleted_at')->nullable();

            $table->index('programa_id');
            $table->index('estado');
            $table->index('destacado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_testimonio');
    }
};
