<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * `apellido` pasa a ser el apellido paterno; el materno va aparte,
     * igual que appaterno / apmaterno en t_usuario (Caja).
     */
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->string('apellido_materno', 100)->nullable()->after('apellido');
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn('apellido_materno');
        });
    }
};
