<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trivia_premios', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('tipo', 20)->default('souvenir'); // souvenir | descuento | otro
            $table->string('imagen_url')->nullable();
            $table->unsignedInteger('costo_puntos');
            $table->integer('stock')->nullable(); // null = ilimitado
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);

            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestampTz('deleted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trivia_premios');
    }
};
