<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CursoParticipantesController extends Controller
{
    public function porSlug(string $slug): JsonResponse
    {
        $programa = DB::table('t_programa as p')
            ->leftJoin('t_tipoprograma as tip', 'tip.id_tipoprograma', '=', 'p.id_tipoprograma')
            ->leftJoin('web_categoria_programa as cat', 'cat.id', '=', 'p.categoria_web_id')
            ->where('p.slug', $slug)
            ->select([
                'p.id_programa', 'p.nombre_programa', 'p.id_imp',
                'p.foto', 'p.imagen_banner_url',
                'tip.nombre_tipoprograma as tipo_nombre', 'cat.nombre as categoria_nombre',
            ])
            ->first();

        if (! $programa) {
            return response()->json(['error' => 'Curso no encontrado.'], 404);
        }

        $participantes = [];

        if ($programa->id_imp) {
            $aprobados = DB::table('t_lista_aprobados')
                ->where('imparte_id', $programa->id_imp)
                ->where('condicion', 'aprobado')
                ->select(['usuario_id'])
                ->get();

            $participantes = $aprobados->map(function ($aprobado) {
                $usuario = DB::table('t_usuario')
                    ->where('id_us', $aprobado->usuario_id)
                    ->orderByDesc('id_us_reg')
                    ->select(['nombre', 'appaterno', 'apmaterno'])
                    ->first();

                return trim(implode(' ', array_filter([
                    $usuario->nombre ?? null,
                    $usuario->appaterno ?? null,
                    $usuario->apmaterno ?? null,
                ])));
            })->filter()->sort()->values();
        }

        return response()->json([
            'curso'             => $programa->nombre_programa,
            'foto'              => $programa->foto,
            'imagen_banner_url' => $programa->imagen_banner_url,
            'tipo_nombre'       => $programa->tipo_nombre,
            'categoria_nombre'  => $programa->categoria_nombre,
            'participantes'     => $participantes,
        ]);
    }
}
