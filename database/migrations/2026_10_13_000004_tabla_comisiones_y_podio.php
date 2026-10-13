<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Comisiones por monto fijo según el tipo de producto y el rango de precio (no por porcentaje),
 * configurables por Administración/Contabilidad. Los productos grandes (diplomados, congresos)
 * se negocian: su comisión la define quien supervisa, venta por venta, con el motivo.
 * Además, el podio mensual de vendedores.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comision_productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 120);
            $table->string('modalidad', 20)->default('rangos');           // rangos | negociar
            $table->unsignedTinyInteger('porcentaje_minimo_pago')->default(50); // las reservas (menos de esto) no comisionan
            $table->unsignedSmallInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('comision_rangos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('producto_id');
            $table->decimal('desde', 10, 2)->default(0);
            $table->decimal('hasta', 10, 2)->nullable();                   // vacío = en adelante
            $table->decimal('monto', 10, 2);
            $table->index('producto_id');
        });

        // Comisión acordada a mano para una venta (negociación) o vendedor corregido
        Schema::create('comision_acuerdos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_ins')->unique();
            $table->unsignedBigInteger('vendedor_id')->nullable();
            $table->decimal('monto', 10, 2)->nullable();
            $table->string('motivo', 500)->nullable();
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->timestamps();
        });

        Schema::create('ventas_podio', function (Blueprint $table) {
            $table->id();
            $table->string('periodo', 7);                                   // AAAA-MM
            $table->unsignedTinyInteger('puesto');
            $table->unsignedBigInteger('vendedor_id');
            $table->decimal('recaudado', 12, 2)->default(0);
            $table->unsignedInteger('ventas')->default(0);
            $table->timestamp('notificado_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->unique(['periodo', 'puesto']);
        });

        Schema::table('t_programa', function (Blueprint $table) {
            $table->unsignedBigInteger('comision_producto_id')->nullable();
        });

        // Cada venta liquidada guarda con qué regla se pagó (la tabla puede cambiar después)
        Schema::table('comisiones_liquidacion_detalle', function (Blueprint $table) {
            $table->unsignedBigInteger('producto_id')->nullable();
            $table->string('regla', 200)->nullable();
        });

        // La tabla vigente que usa CENEFCO
        $escala = [[80, 100, 10], [101, 150, 20], [151, 170, 25], [171, 190, 30]];
        $productos = [
            ['Cursos pasados', 'rangos', $escala],
            ['Cursos con aval', 'rangos', $escala],
            ['Cursos con RM', 'rangos', [[0, null, 80]]],
            ['Diplomados', 'negociar', []],
            ['Congresos', 'negociar', []],
        ];
        foreach ($productos as $i => [$nombre, $modalidad, $rangos]) {
            $id = DB::table('comision_productos')->insertGetId([
                'nombre' => $nombre, 'modalidad' => $modalidad, 'porcentaje_minimo_pago' => 50,
                'orden' => $i + 1, 'activo' => true, 'created_at' => now(), 'updated_at' => now(),
            ]);
            foreach ($rangos as [$desde, $hasta, $monto]) {
                DB::table('comision_rangos')->insert(['producto_id' => $id, 'desde' => $desde, 'hasta' => $hasta, 'monto' => $monto]);
            }
        }

        // Configurar la tabla y acordar comisiones: Administración / Contabilidad
        if (Schema::hasTable('permisos')) {
            $permiso = DB::table('permisos')->where('codigo', 'comisiones.configurar')->value('id')
                ?? DB::table('permisos')->insertGetId([
                    'codigo' => 'comisiones.configurar', 'modulo' => 'Comisiones',
                    'descripcion' => 'Configurar la tabla de comisiones por producto y acordar el monto de las ventas negociadas.',
                ]);
            $admin = DB::table('roles')->where('nombre', 'Admin')->value('id');
            if ($admin) {
                DB::table('roles_permisos')->insertOrIgnore(['rol_id' => $admin, 'permiso_id' => $permiso]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('comisiones_liquidacion_detalle', fn (Blueprint $table) => $table->dropColumn(['producto_id', 'regla']));
        Schema::table('t_programa', fn (Blueprint $table) => $table->dropColumn('comision_producto_id'));
        Schema::dropIfExists('ventas_podio');
        Schema::dropIfExists('comision_acuerdos');
        Schema::dropIfExists('comision_rangos');
        Schema::dropIfExists('comision_productos');
        DB::table('permisos')->where('codigo', 'comisiones.configurar')->delete();
    }
};
