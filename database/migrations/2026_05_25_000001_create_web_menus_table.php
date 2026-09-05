<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_menus', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 80)->unique();
            $table->string('descripcion', 100)->nullable();
            $table->boolean('activo')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_menus');
    }
};
