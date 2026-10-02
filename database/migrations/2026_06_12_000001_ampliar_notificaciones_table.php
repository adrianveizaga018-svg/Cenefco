<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('notificaciones', function (Blueprint $table) {
            $table->enum('prioridad', ['baja', 'media', 'alta', 'critica'])->default('media')->after('canal');
            $table->enum('destinatario', ['usuario', 'rol', 'todos'])->default('usuario')->after('prioridad');
            $table->string('rol_destino')->nullable()->after('destinatario');
            $table->string('icono')->nullable()->after('rol_destino');
            $table->string('color', 20)->nullable()->after('icono');
            $table->string('imagen_url')->nullable()->after('color');
            $table->string('imagen_nombre')->nullable()->after('imagen_url');
            $table->timestampTz('expires_at')->nullable()->after('imagen_nombre');
            $table->index(['usuario_id', 'leida', 'expires_at']);
        });

        Schema::create('notificacion_preferencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->string('tipo', 60);
            $table->boolean('activa')->default(true);
            $table->timestampsTz();
            $table->unique(['usuario_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacion_preferencias');
        Schema::table('notificaciones', function (Blueprint $table) {
            $table->dropColumn([
                'prioridad', 'destinatario', 'rol_destino',
                'icono', 'color', 'imagen_url', 'imagen_nombre', 'expires_at',
            ]);
        });
    }
};
