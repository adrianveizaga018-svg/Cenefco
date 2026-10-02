<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_cifra_institucional', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('valor', 50);
            $table->string('etiqueta', 200);
            $table->string('descripcion', 300)->nullable();
            $table->string('icono', 100)->nullable();
            $table->string('color', 7)->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_cifra_institucional');
    }
};
