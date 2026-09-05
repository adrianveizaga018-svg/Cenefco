<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Infrastructure\CursosMigrados\Models\CursoMigrado;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CursosPasadosController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $pageSize  = (int) $request->get('pageSize', 12);
        $pageIndex = (int) $request->get('pageIndex', 1);
        $query     = trim((string) $request->get('query', ''));

        $q = CursoMigrado::withCount('participantes');

        if ($query !== '') {
            $q->where('nombre', 'ilike', "%{$query}%");
        }

        $paginated = $q->orderByDesc('id')->paginate($pageSize, ['*'], 'page', $pageIndex);

        $items = collect($paginated->items())->map(fn ($c) => [
            'id'                  => $c->id,
            'nombre'              => $c->nombre,
            'slug'                => $c->slug,
            'url'                 => $c->url,
            'imagen_path'         => $c->imagen_path,
            'periodo'             => $c->periodo,
            'gestion'             => $c->gestion,
            'fecha_inicio'        => $c->fecha_inicio?->toDateString(),
            'carga_horaria'       => $c->carga_horaria,
            'participantes_count' => $c->participantes_count,
        ]);

        return response()->json([
            'data'  => $items,
            'total' => $paginated->total(),
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $curso = CursoMigrado::with(['participantes', 'logos'])
            ->withCount('participantes')
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json([
            'id'                  => $curso->id,
            'nombre'              => $curso->nombre,
            'slug'                => $curso->slug,
            'url'                 => $curso->url,
            'imagen_path'         => $curso->imagen_path,
            'qr_path'             => $curso->qr_path,
            'periodo'             => $curso->periodo,
            'gestion'             => $curso->gestion,
            'fecha_inicio'        => $curso->fecha_inicio?->toDateString(),
            'carga_horaria'       => $curso->carga_horaria,
            'participantes_count' => $curso->participantes_count,
            'participantes'       => $curso->participantes
                ->sortBy('nombre_completo')
                ->values()
                ->map(fn ($p) => ['id' => $p->id, 'nombre_completo' => $p->nombre_completo]),
            'logos'               => $curso->logos
                ->map(fn ($l) => ['id' => $l->id, 'path' => $l->path, 'nombre' => $l->nombre]),
        ]);
    }
}
