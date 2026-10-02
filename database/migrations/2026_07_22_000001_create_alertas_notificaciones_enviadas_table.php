<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('alertas_notificaciones_enviadas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->string('tipo', 60);
            $table->string('referencia_tipo', 30);
            $table->unsignedBigInteger('referencia_id');
            $table->timestampTz('created_at')->useCurrent();
            $table->unique(['usuario_id', 'tipo', 'referencia_tipo', 'referencia_id'], 'alertas_notif_unicas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas_notificaciones_enviadas');
    }
};
