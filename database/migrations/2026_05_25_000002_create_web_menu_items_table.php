<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_menu_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('menu_id');
            $table->unsignedInteger('parent_id')->nullable();
            $table->string('etiqueta', 150);
            $table->string('url', 255)->nullable();
            $table->integer('orden')->default(0);
            $table->string('icono', 50)->nullable();
            $table->boolean('activo')->default(true);
            $table->boolean('abrir_nueva_ventana')->default(false);

            $table->foreign('menu_id')->references('id')->on('web_menus')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('web_menu_items')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_menu_items');
    }
};
