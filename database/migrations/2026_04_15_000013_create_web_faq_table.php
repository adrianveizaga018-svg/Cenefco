<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_faq', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('pregunta', 500);
            $table->text('respuesta');
            $table->string('categoria', 100)->nullable();
            $table->unsignedInteger('programa_id')->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();

            $table->index('categoria');
            $table->index('programa_id');
            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_faq');
    }
};
