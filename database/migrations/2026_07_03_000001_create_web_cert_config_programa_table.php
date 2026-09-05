<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_cert_config_programa', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('programa_id')->unique();
            $table->boolean('activo')->default(true);
            $table->string('titulo', 300)->nullable();
            $table->text('descripcion')->nullable();
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();

            $table->index('programa_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_cert_config_programa');
    }
};
