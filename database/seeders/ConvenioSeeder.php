<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConvenioSeeder extends Seeder
{
    public function run(): void
    {
        $now = now()->toDateTimeString();

        $convenios = [
            [
                'nombre'            => 'Convenio Marco de Cooperación Académica — USFA',
                'institucion'       => 'Universidad San Francisco de Asís',
                'tipo'              => 'Marco',
                'descripcion'       => 'Convenio marco que habilita la emisión conjunta de diplomas y certificaciones entre CENEFCO y la USFA. Cubre todos los programas de diplomado y especialización.',
                'responsable'       => 'Dr. Carlos Mendoza',
                'contacto_email'    => 'convenios@usfa.edu.bo',
                'contacto_telefono' => '+591 2-2441234',
                'fecha_inicio'      => '2024-01-15',
                'fecha_fin'         => '2026-12-31',
                'documento_url'     => null,
                'estado'            => 'activo',
                'orden'             => 1,
            ],
            [
                'nombre'            => 'Convenio Específico — Diplomado en Gestión Pública',
                'institucion'       => 'Escuela de Gestión Pública Plurinacional',
                'tipo'              => 'Específico',
                'descripcion'       => 'Convenio específico para la ejecución del Diplomado en Gestión Pública. Incluye uso de instalaciones, acreditación conjunta y descuento del 15% para funcionarios públicos.',
                'responsable'       => 'Mgr. Ana Patricia Flores',
                'contacto_email'    => 'egpp@agetic.gob.bo',
                'contacto_telefono' => '+591 2-2115566',
                'fecha_inicio'      => '2025-02-01',
                'fecha_fin'         => '2025-12-31',
                'documento_url'     => null,
                'estado'            => 'activo',
                'orden'             => 2,
            ],
            [
                'nombre'            => 'Convenio Interinstitucional — MBA Internacional',
                'institucion'       => 'Cámara de Industria, Comercio y Servicios de Santa Cruz (CAINCO)',
                'tipo'              => 'Interinstitucional',
                'descripcion'       => 'Convenio para la promoción y difusión del MBA a través de la red empresarial de CAINCO, con becas parciales para socios y descuentos corporativos.',
                'responsable'       => 'Lic. Roberto Salinas',
                'contacto_email'    => 'academico@cainco.org.bo',
                'contacto_telefono' => '+591 3-3364646',
                'fecha_inicio'      => '2025-03-01',
                'fecha_fin'         => '2026-03-01',
                'documento_url'     => null,
                'estado'            => 'activo',
                'orden'             => 3,
            ],
            [
                'nombre'            => 'Convenio Académico — Especialización Marketing Digital',
                'institucion'       => 'Google for Education Bolivia',
                'tipo'              => 'Académico',
                'descripcion'       => 'Convenio que integra certificaciones Google al pensum del programa de Marketing Digital. Los estudiantes acceden a vouchers de examen con descuento del 40%.',
                'responsable'       => 'Mgr. Valeria Choque',
                'contacto_email'    => 'edu.bo@google.com',
                'contacto_telefono' => null,
                'fecha_inicio'      => '2026-01-01',
                'fecha_fin'         => '2026-12-31',
                'documento_url'     => null,
                'estado'            => 'activo',
                'orden'             => 4,
            ],

            [
                'nombre'            => 'Convenio Marco — Ministerio de Educación',
                'institucion'       => 'Ministerio de Educación del Estado Plurinacional de Bolivia',
                'tipo'              => 'Marco',
                'descripcion'       => 'Convenio institucional para reconocimiento de formación continua en el ámbito del servicio educativo. Aún pendiente de asignación a programas específicos.',
                'responsable'       => 'Dra. Patricia Vidal Cruz',
                'contacto_email'    => 'convenios@minedu.gob.bo',
                'contacto_telefono' => '+591 2-2442144',
                'fecha_inicio'      => '2025-06-01',
                'fecha_fin'         => '2027-06-01',
                'documento_url'     => null,
                'estado'            => 'activo',
                'orden'             => 5,
            ],
            [
                'nombre'            => 'Convenio de Cooperación — OEA / SEDI',
                'institucion'       => 'Organización de Estados Americanos — SEDI',
                'tipo'              => 'Interinstitucional',
                'descripcion'       => 'Convenio de cooperación técnica para el diseño de programas de formación en gobernanza y transparencia. En etapa de planificación curricular.',
                'responsable'       => 'Dr. Carlos Alberto Mendoza Torrez',
                'contacto_email'    => 'sedi@oas.org',
                'contacto_telefono' => null,
                'fecha_inicio'      => '2025-09-01',
                'fecha_fin'         => null,
                'documento_url'     => null,
                'estado'            => 'activo',
                'orden'             => 6,
            ],
            [
                'nombre'            => 'Convenio Histórico — Banco Central de Bolivia',
                'institucion'       => 'Banco Central de Bolivia',
                'tipo'              => 'Específico',
                'descripcion'       => 'Convenio vencido que permitió la capacitación interna de funcionarios del BCB en gestión financiera pública durante 2023-2024.',
                'responsable'       => 'Lic. Fernando Rojas Paredes',
                'contacto_email'    => 'rrhh@bcb.gob.bo',
                'contacto_telefono' => '+591 2-2409090',
                'fecha_inicio'      => '2023-01-01',
                'fecha_fin'         => '2024-12-31',
                'documento_url'     => null,
                'estado'            => 'vencido',
                'orden'             => 7,
            ],
        ];

        $asociaciones = [
            101 => 'Convenio Marco de Cooperación Académica — USFA',
            102 => 'Convenio Marco de Cooperación Académica — USFA',
            103 => 'Convenio Marco de Cooperación Académica — USFA',
            104 => 'Convenio Académico — Especialización Marketing Digital',
        ];

        $asociacionesEspecificas = [
            101 => 'Convenio Específico — Diplomado en Gestión Pública',
            102 => 'Convenio Interinstitucional — MBA Internacional',
        ];

        $insertados = 0;
        $idsPorNombre = [];

        foreach ($convenios as $c) {
            $existe = DB::table('web_convenio')
                ->where('nombre', $c['nombre'])
                ->whereNull('deleted_at')
                ->exists();

            if ($existe) {
                $idsPorNombre[$c['nombre']] = DB::table('web_convenio')
                    ->where('nombre', $c['nombre'])->value('id');
                continue;
            }

            $id = DB::table('web_convenio')->insertGetId(array_merge(
                $c,
                ['created_at' => $now, 'updated_at' => $now]
            ));

            $idsPorNombre[$c['nombre']] = $id;
            $insertados++;
        }

        foreach ($asociacionesEspecificas as $idPlan => $nombreConvenio) {
            if (! isset($idsPorNombre[$nombreConvenio])) continue;
            if (! DB::table('t_plan')->where('id_plan', $idPlan)->exists()) continue;

            DB::table('t_plan')
                ->where('id_plan', $idPlan)
                ->update(['convenio_id' => $idsPorNombre[$nombreConvenio]]);
        }

        foreach ($asociaciones as $idPlan => $nombreConvenio) {
            if (! isset($idsPorNombre[$nombreConvenio])) continue;
            if (! DB::table('t_plan')->where('id_plan', $idPlan)->whereNull('convenio_id')->exists()) continue;

            DB::table('t_plan')
                ->where('id_plan', $idPlan)
                ->whereNull('convenio_id')
                ->update(['convenio_id' => $idsPorNombre[$nombreConvenio]]);
        }

        $this->command->info("Convenios: {$insertados} registros insertados en web_convenio.");
        $this->command->info('Planes actualizados con convenio_id donde corresponde.');
    }
}
