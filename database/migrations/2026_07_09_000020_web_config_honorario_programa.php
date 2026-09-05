<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_config_honorario_programa', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('id_programa');
            $table->string('tipo_honorario', 30);
            $table->decimal('monto_fijo', 10, 2)->nullable();
            $table->decimal('monto_por_dia', 10, 2)->nullable();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique('id_programa');
            $table->index('tipo_honorario');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_config_honorario_programa');
    }
};
