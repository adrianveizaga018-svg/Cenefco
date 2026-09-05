<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_curso_migrado_logo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('curso_id');
            $table->string('path', 500);
            $table->string('nombre', 200)->nullable();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestampsTz();

            $table->foreign('curso_id')
                  ->references('id')
                  ->on('web_curso_migrado')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_curso_migrado_logo');
    }
};
