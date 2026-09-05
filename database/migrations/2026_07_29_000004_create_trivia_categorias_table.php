<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trivia_categorias', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 150);
            $table->string('slug', 180)->unique();
            $table->text('descripcion')->nullable();
            $table->string('imagen_url', 500)->nullable();
            $table->string('color', 20)->nullable();

            $table->unsignedInteger('curso_id')->nullable();
            $table->index('curso_id');

            $table->unsignedInteger('orden')->default(0);
            $table->boolean('activo')->default(true);

            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestampTz('deleted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trivia_categorias');
    }
};
