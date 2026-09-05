<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DescuentoPromocionSeeder extends Seeder
{
    public function run(): void
    {
        $now  = now()->toDateTimeString();
        $hoy  = now()->toDateString();
        $fin1 = now()->addDays(30)->toDateString();
        $fin2 = now()->addDays(15)->toDateString();
        $fin3 = now()->addMonths(3)->toDateString();

        $descuentos = [
            
            [
                'codigo'          => 'BIENVENIDO10',
                'nombre'          => 'Descuento Bienvenida 10%',
                'descripcion'     => 'Descuento especial para nuevos estudiantes que se inscriben por primera vez. Aplica en cualquier programa de la oferta académica.',
                'tipo_descuento'  => 'porcentaje',
                'valor'           => 10.00,
                'monto_minimo'    => 300.00,
                'usos_maximos'    => null,
                'usos_actuales'   => 0,
                'usos_por_usuario'=> 1,
                'programa_id'     => null,
                'activo'          => true,
                'fecha_inicio'    => $hoy,
                'fecha_fin'       => $fin3,
            ],
            [
                'codigo'          => 'PRONTO15',
                'nombre'          => 'Inscripción Anticipada 15%',
                'descripcion'     => 'Descuento del 15% para quienes se inscriban antes del inicio de actividades. Válido para diplomados y maestrías.',
                'tipo_descuento'  => 'porcentaje',
                'valor'           => 15.00,
                'monto_minimo'    => 500.00,
                'usos_maximos'    => 50,
                'usos_actuales'   => 3,
                'usos_por_usuario'=> 1,
                'programa_id'     => null,
                'activo'          => true,
                'fecha_inicio'    => $hoy,
                'fecha_fin'       => $fin1,
            ],
            [
                'codigo'          => 'GRUPO20',
                'nombre'          => 'Descuento Grupal 20%',
                'descripcion'     => 'Descuento especial para inscripciones grupales de 3 o más personas de la misma institución. Coordinación previa requerida.',
                'tipo_descuento'  => 'porcentaje',
                'valor'           => 20.00,
                'monto_minimo'    => 800.00,
                'usos_maximos'    => 30,
                'usos_actuales'   => 0,
                'usos_por_usuario'=> 3,
                'programa_id'     => null,
                'activo'          => true,
                'fecha_inicio'    => $hoy,
                'fecha_fin'       => $fin3,
            ],
            [
                'codigo'          => 'ALUMNI25',
                'nombre'          => 'Descuento Ex-Alumnos 25%',
                'descripcion'     => 'Beneficio exclusivo para egresados de programas anteriores de CENEFCO. Previa verificación de certificado de conclusión.',
                'tipo_descuento'  => 'porcentaje',
                'valor'           => 25.00,
                'monto_minimo'    => 400.00,
                'usos_maximos'    => null,
                'usos_actuales'   => 12,
                'usos_por_usuario'=> 1,
                'programa_id'     => null,
                'activo'          => true,
                'fecha_inicio'    => $hoy,
                'fecha_fin'       => $fin3,
            ],
            [
                'codigo'          => 'CONTADO5',
                'nombre'          => 'Descuento Pago al Contado 5%',
                'descripcion'     => 'Descuento adicional del 5% para estudiantes que realicen el pago total del programa en un solo depósito.',
                'tipo_descuento'  => 'porcentaje',
                'valor'           => 5.00,
                'monto_minimo'    => 0.00,
                'usos_maximos'    => null,
                'usos_actuales'   => 8,
                'usos_por_usuario'=> 1,
                'programa_id'     => null,
                'activo'          => true,
                'fecha_inicio'    => $hoy,
                'fecha_fin'       => $fin3,
            ],

            
            [
                'codigo'          => 'MAYO100',
                'nombre'          => 'Promo Mayo — Bs. 100 de Descuento',
                'descripcion'     => 'Promoción especial del mes de mayo. Descuento directo de Bs. 100 en cualquier programa con inversión mayor a Bs. 300.',
                'tipo_descuento'  => 'monto_fijo',
                'valor'           => 100.00,
                'monto_minimo'    => 300.00,
                'usos_maximos'    => 100,
                'usos_actuales'   => 27,
                'usos_por_usuario'=> 1,
                'programa_id'     => null,
                'activo'          => true,
                'fecha_inicio'    => $hoy,
                'fecha_fin'       => $fin2,
            ],
            [
                'codigo'          => 'SERVIDORPUB',
                'nombre'          => 'Servidor Público — Bs. 200',
                'descripcion'     => 'Descuento exclusivo para servidores públicos con presentación de certificación de trabajo vigente. Aplica en diplomados y especializaciones.',
                'tipo_descuento'  => 'monto_fijo',
                'valor'           => 200.00,
                'monto_minimo'    => 800.00,
                'usos_maximos'    => null,
                'usos_actuales'   => 5,
                'usos_por_usuario'=> 1,
                'programa_id'     => null,
                'activo'          => true,
                'fecha_inicio'    => $hoy,
                'fecha_fin'       => $fin3,
            ],
            [
                'codigo'          => 'REFERIDO50',
                'nombre'          => 'Bono por Referido — Bs. 50',
                'descripcion'     => 'Descuento de Bs. 50 para quien se inscriba por referencia de un estudiante activo. El estudiante referidor también recibe un beneficio.',
                'tipo_descuento'  => 'monto_fijo',
                'valor'           => 50.00,
                'monto_minimo'    => 200.00,
                'usos_maximos'    => null,
                'usos_actuales'   => 15,
                'usos_por_usuario'=> 1,
                'programa_id'     => null,
                'activo'          => true,
                'fecha_inicio'    => $hoy,
                'fecha_fin'       => $fin3,
            ],

            
            [
                'codigo'          => 'VERANO2025',
                'nombre'          => 'Promo Verano 2025 — 12%',
                'descripcion'     => 'Campaña de verano 2025. Promoción finalizada.',
                'tipo_descuento'  => 'porcentaje',
                'valor'           => 12.00,
                'monto_minimo'    => 250.00,
                'usos_maximos'    => 80,
                'usos_actuales'   => 80,
                'usos_por_usuario'=> 1,
                'programa_id'     => null,
                'activo'          => false,
                'fecha_inicio'    => '2025-01-01',
                'fecha_fin'       => '2025-03-31',
            ],
        ];

        foreach ($descuentos as $d) {
            $existe = DB::table('web_descuento_promocion')
                ->where('codigo', $d['codigo'])
                ->exists();
            if ($existe) continue;

            DB::table('web_descuento_promocion')->insertOrIgnore(array_merge($d, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        $total = DB::table('web_descuento_promocion')->count();
        $this->command->info("✓ {$total} descuentos/promociones en web_descuento_promocion.");
    }
}
