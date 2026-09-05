<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_regform', function (Blueprint $table) {
            $table->integer('id_regform');
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_regform')->default('0');
            $table->integer('id_regcp')->nullable();
            $table->string('nombreform', 200)->nullable();
            $table->integer('id_niv')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_regform', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_regform');
    }
};
