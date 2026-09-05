<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        
        Schema::create('permisos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('codigo', 100)->unique();   
            $table->string('descripcion', 255)->nullable();
            $table->string('modulo', 100)->nullable(); 
        });

        
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 100)->unique();
            $table->string('descripcion', 255)->nullable();
            $table->boolean('activo')->default(true);
        });

        
        Schema::create('roles_permisos', function (Blueprint $table) {
            $table->unsignedBigInteger('rol_id');
            $table->unsignedBigInteger('permiso_id');

            $table->foreign('rol_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('permiso_id')->references('id')->on('permisos')->onDelete('cascade');

            $table->primary(['rol_id', 'permiso_id']);
        });

        
        Schema::create('usuarios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 100);
            $table->string('apellido', 100)->nullable();
            $table->string('email', 150)->unique();
            $table->string('password_hash', 255);
            $table->string('tipo', 50)->default('ciudadano'); 
            $table->string('ci', 30)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->boolean('activo')->default(true);
            $table->boolean('email_verificado')->default(false);
            $table->string('codigo_verificacion', 100)->nullable();
            $table->string('reset_token', 100)->nullable();
            $table->timestampTz('reset_token_expires_at')->nullable();
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();
            $table->timestampTz('deleted_at')->nullable();

            $table->index('email');
            $table->index('tipo');
            $table->index('activo');
        });

        
        Schema::create('usuarios_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('rol_id');
            $table->timestampTz('asignado_at')->nullable()->useCurrent();
            $table->unsignedBigInteger('asignado_por')->nullable();

            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
            $table->foreign('rol_id')->references('id')->on('roles')->onDelete('cascade');

            $table->primary(['usuario_id', 'rol_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios_roles');
        Schema::dropIfExists('usuarios');
        Schema::dropIfExists('roles_permisos');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permisos');
    }
};
