<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_usuariomoodle', function (Blueprint $table) {
            $table->integer('id_usmoodle');
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_usmoodle')->default('0');
            $table->integer('id_us')->nullable()->default('0');
            $table->integer('id_moodle')->nullable()->default('0');
            $table->string('moodle_id_user', 200)->nullable()->default('0');
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_usmoodle', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_usuariomoodle');
    }
};
