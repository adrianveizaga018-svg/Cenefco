<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')
                  ->nullable()
                  ->constrained('usuarios')
                  ->cascadeOnDelete();
            $table->unsignedInteger('t_usuario_id')->nullable()
                  ->comment('FK lógica a t_usuario.id_us');
            $table->string('tipo', 60);
            $table->string('titulo');
            $table->text('mensaje');
            $table->string('url_accion')->nullable();
            $table->boolean('leida')->default(false);
            $table->timestampTz('leida_at')->nullable();
            $table->string('canal', 20)->default('sistema');
            $table->boolean('enviada')->default(false);
            $table->timestampTz('enviada_at')->nullable();
            $table->timestampTz('created_at')->useCurrent();
            $table->index(['usuario_id', 'leida']);
            $table->index(['t_usuario_id', 'leida']);
        });
    }

    public function down(): void { Schema::dropIfExists('notificaciones'); }
};
