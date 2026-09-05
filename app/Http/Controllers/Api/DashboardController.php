<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        $totalUsuarios   = DB::table('usuarios')->where('activo', true)->count();
        $totalProgramas  = DB::table('t_programa')->where('estado', 1)->count();
        $totalInscripciones = DB::table('t_inscripcion')->whereMonth('fecha_reg', now()->month)->count();
        $totalCertificados  = DB::table('t_certificado')->count();
        $mensajesNuevos     = DB::table('web_contacto_mensaje')
            ->where('estado', 'nuevo')
            ->count();
        return response()->json([
            'resumen' => [
                'total_usuarios'        => $totalUsuarios,
                'total_programas'       => $totalProgramas,
                'inscripciones_mes'     => $totalInscripciones,
                'total_certificados'    => $totalCertificados,
                'mensajes_nuevos'       => $mensajesNuevos,
            ],
        ]);
    }

    public function gerencia(): JsonResponse
    {
        $hoy = now();
        $hace2dias = $hoy->copy()->subDays(2);
        $inicioMes = $hoy->copy()->startOfMonth();

        // Leads sin contactar (estado=nuevo) hace mas de 2 dias
        $leadsSinContactar = \App\Infrastructure\CampanasLeads\Models\Lead::where('estado', 'nuevo')
            ->where('created_at', '<', $hace2dias)
            ->whereNull('deleted_at')
            ->count();

        // Leads inscritos este mes
        $leadsInscritosMes = \App\Infrastructure\CampanasLeads\Models\Lead::where('estado', 'inscrito')
            ->where('updated_at', '>=', $inicioMes)
            ->whereNull('deleted_at')
            ->count();

        // Total leads este mes
        $totalLeadsMes = \App\Infrastructure\CampanasLeads\Models\Lead::where('created_at', '>=', $inicioMes)
            ->whereNull('deleted_at')
            ->count();

        return response()->json([
            'leads_sin_contactar'  => $leadsSinContactar,
            'leads_inscritos_mes'  => $leadsInscritosMes,
            'total_leads_mes'      => $totalLeadsMes,
        ]);
    }

    public function controlAcademico(): JsonResponse
    {
        $programas = \Illuminate\Support\Facades\DB::table('t_programa as p')
            ->where('p.estado', 1)
            ->select('p.id_programa', 'p.nombre_programa')
            ->get();

        $result = $programas->map(function ($prog) {
            $tareas = \App\Infrastructure\TareasAcademicas\Models\TareaAcademica::where('programa_id', $prog->id_programa)->get();
            $total = $tareas->count();
            $completadas = $tareas->where('estado', 'completada')->count();
            $progreso = $total > 0 ? round(($completadas / $total) * 100, 0) : 0;

            return [
                'curso_id'          => $prog->id_programa,
                'curso_nombre'      => $prog->nombre_programa,
                'total_tareas'      => $total,
                'tareas_completadas'=> $completadas,
                'progreso'          => $progreso,
            ];
        })->filter(fn($c) => $c['total_tareas'] > 0)->values();

        return response()->json($result);
    }
}
