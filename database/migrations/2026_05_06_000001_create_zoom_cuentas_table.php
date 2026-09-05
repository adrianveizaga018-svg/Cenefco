<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zoom_cuentas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('account_id', 100);
            $table->string('client_id', 100);
            $table->string('client_secret', 200);
            $table->string('timezone', 50)->default('America/La_Paz');
            $table->string('descripcion', 200)->nullable();
            $table->boolean('predeterminada')->default(false);
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zoom_cuentas');
    }
};
