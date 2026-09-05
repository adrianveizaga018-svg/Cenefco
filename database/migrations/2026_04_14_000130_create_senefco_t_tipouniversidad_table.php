<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_tipouniversidad', function (Blueprint $table) {
            $table->integer('id_tipouniversidad');
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_tipouniversidad')->default('0');
            $table->string('nombre_tipouniversidad', 200)->nullable();
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_tipouniversidad', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_tipouniversidad');
    }
};
