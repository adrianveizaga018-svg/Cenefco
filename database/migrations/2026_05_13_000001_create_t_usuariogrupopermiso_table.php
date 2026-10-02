<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('t_usuariogrupopermiso', function (Blueprint $table) {
            $table->integer('id_usgp')->autoIncrement();
            $table->integer('id_us_reg')->default(0);
            $table->integer('id_us')->nullable()->comment('FK lógica a t_usuario');
            $table->integer('id_grupopermiso')->nullable()->comment('FK lógica a t_grupopermiso');
            $table->tinyInteger('estado')->default(1);
        });
    }

    public function down(): void { Schema::dropIfExists('t_usuariogrupopermiso'); }
};
