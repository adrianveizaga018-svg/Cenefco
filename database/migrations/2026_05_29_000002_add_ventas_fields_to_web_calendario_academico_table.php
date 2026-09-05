<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('web_calendario_academico', function (Blueprint $table) {
            $table->unsignedBigInteger('vendedor_id')->nullable()->after('programa_id')->comment('FK a vendedores');
            $table->string('pagina', 150)->nullable()->after('vendedor_id')->comment('Plataforma: FORMET, VIRTUALTECH, etc.');
            $table->unsignedSmallInteger('duracion_dias')->nullable()->after('pagina');
            $table->decimal('costo_inflado', 10, 2)->nullable()->after('duracion_dias');
            $table->decimal('descuento', 10, 2)->nullable()->after('costo_inflado');
            $table->decimal('precio_vip', 10, 2)->nullable()->after('descuento');
            $table->string('observaciones', 255)->nullable()->after('precio_vip');

            $table->index('vendedor_id');
        });
    }

    public function down(): void
    {
        Schema::table('web_calendario_academico', function (Blueprint $table) {
            $table->dropIndex(['vendedor_id']);
            $table->dropColumn([
                'vendedor_id', 'pagina', 'duracion_dias',
                'costo_inflado', 'descuento', 'precio_vip', 'observaciones',
            ]);
        });
    }
};
