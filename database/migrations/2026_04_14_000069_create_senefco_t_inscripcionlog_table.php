<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_inscripcionlog', function (Blueprint $table) {
            $table->integer('id_inslog');
            $table->integer('id_ins')->default('0');
            $table->string('tipo_log', 200)->nullable();
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_ins')->default('0');
            $table->string('periodo', 30)->nullable();
            $table->string('gestion', 10)->nullable();
            $table->date('fecha_ins')->nullable()->default('2000-01-01');
            $table->integer('id_us')->nullable();
            $table->integer('id_imp')->nullable();
            $table->text('observacion_ins')->nullable();
            $table->text('observacion')->nullable();
            $table->string('ins_moodle_username', 200)->nullable();
            $table->string('ins_moodle_id_user', 200)->nullable();
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_inslog', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_inscripcionlog');
    }
};
