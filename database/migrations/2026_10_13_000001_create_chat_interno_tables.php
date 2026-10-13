<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Chat interno del personal: mensajes directos (privados), grupos por área y un canal
 * de avisos donde solo publica Dirección. Los estudiantes no tienen acceso.
 */
return new class extends Migration
{
    private const ROLES_ESTUDIANTE = ['participante', 'Ciudadano', 'Estudiante'];

    public function up(): void
    {
        Schema::create('chat_conversaciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tipo', 20);                 // directo | grupo | anuncios
            $table->string('nombre', 120)->nullable();  // los directos no tienen nombre
            $table->string('descripcion', 300)->nullable();
            $table->unsignedBigInteger('creado_por')->nullable();
            $table->boolean('archivada')->default(false);
            $table->timestamp('ultimo_mensaje_at')->nullable();
            $table->timestamps();
            $table->index(['tipo', 'archivada']);
        });

        Schema::create('chat_participantes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('conversacion_id');
            $table->unsignedBigInteger('usuario_id');
            $table->string('rol', 20)->default('miembro'); // admin | miembro
            $table->unsignedBigInteger('ultimo_leido_id')->default(0);
            $table->timestamp('unido_at')->useCurrent();
            $table->unique(['conversacion_id', 'usuario_id']);
            $table->index('usuario_id');
            $table->foreign('conversacion_id')->references('id')->on('chat_conversaciones')->cascadeOnDelete();
        });

        Schema::create('chat_mensajes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('conversacion_id');
            $table->unsignedBigInteger('usuario_id');
            $table->text('cuerpo')->nullable();
            $table->string('adjunto_ruta', 500)->nullable();   // disco privado: se descarga con permiso
            $table->string('adjunto_nombre', 255)->nullable();
            $table->string('adjunto_tipo', 120)->nullable();
            $table->unsignedInteger('adjunto_peso')->nullable();
            $table->unsignedBigInteger('responde_a_id')->nullable();
            $table->timestamp('eliminado_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['conversacion_id', 'id']);
            $table->foreign('conversacion_id')->references('id')->on('chat_conversaciones')->cascadeOnDelete();
        });

        Schema::create('chat_menciones', function (Blueprint $table) {
            $table->unsignedBigInteger('mensaje_id');
            $table->unsignedBigInteger('usuario_id');
            $table->primary(['mensaje_id', 'usuario_id']);
            $table->foreign('mensaje_id')->references('id')->on('chat_mensajes')->cascadeOnDelete();
        });

        foreach ([
            'chat.usar'        => 'Usar el chat interno: mensajes directos y grupos donde participa.',
            'chat.administrar' => 'Crear grupos por área, gestionar sus miembros y publicar en el canal de avisos.',
        ] as $codigo => $descripcion) {
            if (! DB::table('permisos')->where('codigo', $codigo)->exists()) {
                DB::table('permisos')->insert(['codigo' => $codigo, 'descripcion' => $descripcion, 'modulo' => 'Chat interno']);
            }
        }

        // Todo el personal puede usar el chat; los roles de estudiante no
        $usar = DB::table('permisos')->where('codigo', 'chat.usar')->value('id');
        foreach (DB::table('roles')->whereNotIn('nombre', self::ROLES_ESTUDIANTE)->pluck('id') as $rolId) {
            DB::table('roles_permisos')->updateOrInsert(['rol_id' => $rolId, 'permiso_id' => $usar]);
        }

        // Canal de avisos: lo lee todo el personal, publica quien administra el chat
        DB::table('chat_conversaciones')->insert([
            'tipo' => 'anuncios', 'nombre' => 'Avisos de Dirección',
            'descripcion' => 'Comunicados oficiales para todo el personal.', 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_menciones');
        Schema::dropIfExists('chat_mensajes');
        Schema::dropIfExists('chat_participantes');
        Schema::dropIfExists('chat_conversaciones');
        $ids = DB::table('permisos')->whereIn('codigo', ['chat.usar', 'chat.administrar'])->pluck('id');
        DB::table('roles_permisos')->whereIn('permiso_id', $ids)->delete();
        DB::table('permisos')->whereIn('id', $ids)->delete();
    }
};
