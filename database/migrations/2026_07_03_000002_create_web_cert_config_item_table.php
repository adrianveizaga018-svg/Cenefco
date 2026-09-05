<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_cert_config_item', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('config_id');
            $table->unsignedBigInteger('plantilla_id')->nullable();
            $table->string('nombre_cert', 300);
            $table->decimal('precio', 8, 2)->default(0.00);
            $table->boolean('es_gratuito')->default(false);
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);

            $table->foreign('config_id')->references('id')->on('web_cert_config_programa')->cascadeOnDelete();
            $table->foreign('plantilla_id')->references('id')->on('t_cert_plantilla')->nullOnDelete();
            $table->index('config_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_cert_config_item');
    }
};
