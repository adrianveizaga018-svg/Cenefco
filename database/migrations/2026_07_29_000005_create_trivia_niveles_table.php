<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trivia_niveles', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('categoria_id');
            $table->foreign('categoria_id')
                ->references('id')->on('trivia_categorias')
                ->cascadeOnDelete();

            $table->string('nombre', 100);
            $table->unsignedInteger('orden')->default(0);
            $table->unsignedInteger('puntaje_base')->default(100);
            $table->boolean('activo')->default(true);

            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trivia_niveles');
    }
};
