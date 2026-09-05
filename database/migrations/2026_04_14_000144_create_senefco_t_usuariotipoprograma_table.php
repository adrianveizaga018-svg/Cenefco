<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_usuariotipoprograma', function (Blueprint $table) {
            $table->integer('id_usuariotipoprograma');
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_usuariotipoprograma')->default('0');
            $table->integer('id_us')->default('0');
            $table->integer('id_tipoprograma')->nullable()->default('0');
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_usuariotipoprograma', 'id_us', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_usuariotipoprograma');
    }
};
