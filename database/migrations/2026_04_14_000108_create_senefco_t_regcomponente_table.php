<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_regcomponente', function (Blueprint $table) {
            $table->double('id_regcp');
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_regcp')->default('0');
            $table->string('nombre', 200)->nullable();
            $table->string('id_niv', 200)->nullable()->default('');
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->integer('estado')->nullable()->default('1');
            $table->primary(['id_regcp', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_regcomponente');
    }
};
