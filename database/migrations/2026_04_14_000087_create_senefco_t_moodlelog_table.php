<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_moodlelog', function (Blueprint $table) {
            $table->integer('id_moodlelog');
            $table->integer('id_moodle')->default('0');
            $table->string('tipo_log', 200)->nullable();
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_moodle')->default('0');
            $table->string('titulo_moodle', 200)->nullable();
            $table->string('cp_moodle_servidor', 200)->nullable();
            $table->string('cp_moodle_base_datos', 200)->nullable();
            $table->string('cp_moodle_usuario_bd', 200)->nullable();
            $table->string('cp_moodle_contrasena', 200)->nullable();
            $table->string('cp_url_campus', 200)->nullable();
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_moodlelog', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_moodlelog');
    }
};
