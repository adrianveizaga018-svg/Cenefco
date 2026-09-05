<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentoAcademicoSeeder extends Seeder
{
    public function run(): void
    {
        $now   = now()->toDateTimeString();
        $today = now()->toDateString();

        
        $fechasDocs = DB::table('t_fechadoc')
            ->where('estado', 1)
            ->get()
            ->keyBy('id_fechadoc');

        if ($fechasDocs->isEmpty()) {
            $this->command->warn('No hay fechas-doc. Ejecuta FechaDocSeeder primero.');
            return;
        }

        
        $inscripciones = DB::table('t_inscripcion')->where('estado', 1)->get();

        $insertados = 0;
        $nextId     = (DB::table('t_documento')->max('id_documento') ?? 0) + 1;

        foreach ($inscripciones as $ins) {
            
            $planId = $ins->id_plan;
            if (! $planId) {
                $idMat  = DB::table('t_imparte')->where('id_imp', $ins->id_imp)->value('id_mat');
                $planId = $idMat
                    ? DB::table('t_materia_plan')->where('id_mat', $idMat)->value('id_plan')
                    : null;
            }
            if (! $planId) continue;

            
            $requeridos = DB::table('t_fechadoc')
                ->where('id_plandoc', $planId)
                ->where('estado', 1)
                ->orderBy('nro_doc')
                ->get();

            if ($requeridos->isEmpty()) continue;

            foreach ($requeridos as $idx => $fd) {
                
                $existe = DB::table('t_documento')
                    ->where('id_us', $ins->id_us)
                    ->where('id_fechadoc', $fd->id_fechadoc)
                    ->exists();
                if ($existe) continue;

                
                
                $presentar = match ($idx) {
                    0       => true,
                    1       => $fd->obligatorio ? (rand(1, 10) <= 8) : (rand(1, 10) <= 5),
                    default => rand(1, 10) <= 4,
                };

                if (! $presentar) continue;

                DB::table('t_documento')->insertOrIgnore([
                    'id_documento'          => $nextId++,
                    'id_us_reg'             => 1,
                    'num_documento'         => $nextId - 1,
                    'id_us'                 => $ins->id_us,
                    'id_fechadoc'           => $fd->id_fechadoc,
                    'dejo_documento_fisico' => ($idx === 0) ? 1 : 0,
                    'fecha_dejo_fisico'     => ($idx === 0) ? $today : null,
                    'documento_digital'     => null,
                    'observacion_doc'       => ($idx === 0)
                        ? 'Fotocopia entregada en oficina'
                        : null,
                    'fecha_reg'             => $now,
                    'estado'                => 1,
                    'per_modificar'         => 0,
                ]);

                $insertados++;
            }
        }

        
        $sinPlan = DB::table('t_inscripcion')->where('estado', 1)->whereNull('id_plan')->get();
        foreach ($sinPlan as $ins) {
            $idMat  = DB::table('t_imparte')->where('id_imp', $ins->id_imp)->value('id_mat');
            $planId = $idMat ? DB::table('t_materia_plan')->where('id_mat', $idMat)->value('id_plan') : null;
            if ($planId) {
                DB::table('t_inscripcion')->where('id_ins', $ins->id_ins)->update(['id_plan' => $planId]);
            }
        }

        $totalDocs  = DB::table('t_documento')->count();
        $conPlan    = DB::table('t_inscripcion')->where('estado', 1)->whereNotNull('id_plan')->count();
        $sinPlanCnt = DB::table('t_inscripcion')->where('estado', 1)->whereNull('id_plan')->count();

        $this->command->info("✓ {$insertados} documentos académicos insertados ({$totalDocs} total en t_documento).");
        $this->command->info("✓ Inscripciones con plan: {$conPlan} | sin plan: {$sinPlanCnt}");
    }
}
