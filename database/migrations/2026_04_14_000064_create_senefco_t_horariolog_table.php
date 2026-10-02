<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_horariolog', function (Blueprint $table) {
            $table->integer('id_horarlog');
            $table->integer('id_horar')->default('0');
            $table->string('tipo_log', 200)->nullable();
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_horar')->default('0');
            $table->integer('id_imp')->default('0');
            $table->integer('id_d')->nullable();
            $table->integer('id_aula')->nullable()->default('0');
            $table->time('hora_inicio')->nullable()->default('00:00:00');
            $table->time('hora_fin')->nullable()->default('00:00:00');
            $table->string('periodos', 200)->nullable()->default('1');
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_horarlog', 'id_imp', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_horariolog');
    }
};
