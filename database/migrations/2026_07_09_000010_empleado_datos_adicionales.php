<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empleado', function (Blueprint $table) {
            $table->string('carnet_pdf', 500)->nullable()->after('ci');
            $table->string('correo', 150)->nullable()->after('carnet_pdf');
            $table->string('celular_personal', 20)->nullable()->after('correo');
            $table->string('celular_corporativo', 20)->nullable()->after('celular_personal');
            $table->string('direccion', 300)->nullable()->after('celular_corporativo');
        });

        Schema::table('empleado', function (Blueprint $table) {
            $table->string('ci', 20)->nullable(false)->change();
            $table->unique('ci');
        });
    }

    public function down(): void
    {
        Schema::table('empleado', function (Blueprint $table) {
            $table->dropUnique(['ci']);
            $table->string('ci', 20)->nullable()->change();
            $table->dropColumn(['carnet_pdf', 'correo', 'celular_personal', 'celular_corporativo', 'direccion']);
        });
    }
};
