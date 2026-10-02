<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_ingreso', function (Blueprint $table) {
            $table->double('id_ing');
            $table->integer('id_us_reg')->default('0');
            $table->dateTime('fecha')->nullable();
            $table->primary(['id_ing', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_ingreso');
    }
};
