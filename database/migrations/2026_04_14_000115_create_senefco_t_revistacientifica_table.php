<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_revistacientifica', function (Blueprint $table) {
            $table->integer('id_revistacientifica');
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_revistacientifica')->default('0');
            $table->string('titulo_revistacientifica', 200)->nullable();
            $table->text('descripcion_revistacientifica')->nullable();
            $table->date('fecha_publicacion')->nullable()->default('2000-01-01');
            $table->string('archivo', 200)->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_revistacientifica', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_revistacientifica');
    }
};
