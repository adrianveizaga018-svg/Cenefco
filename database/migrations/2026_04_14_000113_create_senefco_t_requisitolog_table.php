<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_requisitolog', function (Blueprint $table) {
            $table->integer('id_reqlog');
            $table->integer('id_req')->default('0');
            $table->string('tipo_log', 200)->nullable();
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_req')->default('0');
            $table->integer('id_mat')->default('0');
            $table->integer('pre_id_mat')->nullable();
            $table->string('todos1a6', 200)->nullable()->default('No');
            $table->string('modalidad_req', 200)->nullable()->default('0');
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_reqlog', 'id_mat', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_requisitolog');
    }
};
