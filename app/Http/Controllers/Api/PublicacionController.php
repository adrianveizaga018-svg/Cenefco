<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Shared\Kernel\Support\SqlCompat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicacionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query  = $request->get('query', '');
        $tipo   = $request->get('tipo', '');
        $size   = max(1, (int) $request->get('pageSize', 50));
        $page   = max(1, (int) $request->get('pageIndex', 1));
        $offset = ($page - 1) * $size;

        $tesis = DB::table('t_tesis')
            ->where('estado', 1)
            ->select(
                'id_tesis as id', 'slug',
                'titulo_tesis as titulo', 'autor',
                'descripcion_tesis as resumen',
                DB::raw("'tesis' as tipo"),
                DB::raw("'Tesis' as tipo_label"),
                DB::raw(SqlCompat::year('fecha_publicacion') . ' as anio'),
                DB::raw('NULL as area'), DB::raw('NULL as portada_url'),
                'archivo as archivo_url',
                DB::raw('NULL as palabras_clave'), DB::raw('NULL as issn'),
                DB::raw('NULL as volumen'), DB::raw('NULL as numero'), DB::raw('NULL as paginas'),
                'fecha_publicacion'
            );

        $monografias = DB::table('t_monografia')
            ->where('estado', 1)
            ->select(
                'id_monografia as id', 'slug',
                'titulo_monografia as titulo', 'autor',
                'descripcion_monografia as resumen',
                DB::raw("'monografia' as tipo"),
                DB::raw("'Monografía' as tipo_label"),
                DB::raw(SqlCompat::year('fecha_publicacion') . ' as anio'),
                DB::raw('NULL as area'), DB::raw('NULL as portada_url'),
                'archivo as archivo_url',
                DB::raw('NULL as palabras_clave'), DB::raw('NULL as issn'),
                DB::raw('NULL as volumen'), DB::raw('NULL as numero'), DB::raw('NULL as paginas'),
                'fecha_publicacion'
            );

        $revistas = DB::table('t_revista')
            ->where('estado', 1)
            ->select(
                'id_revista as id', 'slug',
                'titulo_revista as titulo', DB::raw('NULL as autor'),
                'descripcion_revista as resumen',
                DB::raw("'revista' as tipo"),
                DB::raw("'Revista' as tipo_label"),
                DB::raw(SqlCompat::year('fecha_publicacion') . ' as anio'),
                DB::raw('NULL as area'), DB::raw('NULL as portada_url'),
                'archivo as archivo_url',
                DB::raw('NULL as palabras_clave'), DB::raw('NULL as issn'),
                DB::raw('NULL as volumen'), DB::raw('NULL as numero'), DB::raw('NULL as paginas'),
                'fecha_publicacion'
            );

        $cientificas = DB::table('t_revistacientifica')
            ->where('estado', 1)
            ->select(
                'id_revistacientifica as id', 'slug',
                'titulo_revistacientifica as titulo', DB::raw('NULL as autor'),
                'descripcion_revistacientifica as resumen',
                DB::raw("'revista-cientifica' as tipo"),
                DB::raw("'Revista Científica' as tipo_label"),
                DB::raw(SqlCompat::year('fecha_publicacion') . ' as anio'),
                DB::raw('NULL as area'), DB::raw('NULL as portada_url'),
                'archivo as archivo_url',
                DB::raw('NULL as palabras_clave'), DB::raw('NULL as issn'),
                DB::raw('NULL as volumen'), DB::raw('NULL as numero'), DB::raw('NULL as paginas'),
                'fecha_publicacion'
            );

        if ($query) {
            $like = "%{$query}%";
            $tesis->where(fn ($q) => $q->where('titulo_tesis', 'like', $like)->orWhere('autor', 'like', $like)->orWhere('descripcion_tesis', 'like', $like));
            $monografias->where(fn ($q) => $q->where('titulo_monografia', 'like', $like)->orWhere('autor', 'like', $like)->orWhere('descripcion_monografia', 'like', $like));
            $revistas->where(fn ($q) => $q->where('titulo_revista', 'like', $like)->orWhere('descripcion_revista', 'like', $like));
            $cientificas->where(fn ($q) => $q->where('titulo_revistacientifica', 'like', $like)->orWhere('descripcion_revistacientifica', 'like', $like));
        }

        $union = match ($tipo) {
            'tesis'              => $tesis,
            'monografia'         => $monografias,
            'revista'            => $revistas,
            'revista-cientifica' => $cientificas,
            default              => $tesis->union($monografias)->union($revistas)->union($cientificas),
        };

        $wrapped = DB::table(DB::raw("({$union->toSql()}) as publicaciones"))
            ->mergeBindings($union)
            ->orderByDesc('fecha_publicacion')
            ->orderBy('titulo');

        $total = $wrapped->count();
        $data  = $wrapped->offset($offset)->limit($size)->get();

        return response()->json(['data' => $data, 'total' => $total]);
    }

    public function showBySlug(string $tipo, string $slug): JsonResponse
    {
        $row = match ($tipo) {
            'tesis' => DB::table('t_tesis')
                ->where('slug', $slug)->where('estado', 1)
                ->select(
                    'id_tesis as id', 'slug',
                    'titulo_tesis as titulo', 'autor',
                    'descripcion_tesis as resumen',
                    DB::raw("'tesis' as tipo"), DB::raw("'Tesis' as tipo_label"),
                    DB::raw(SqlCompat::year('fecha_publicacion') . ' as anio'), 'fecha_publicacion',
                    DB::raw('NULL as area'), DB::raw('NULL as portada_url'),
                    'archivo as archivo_url',
                    DB::raw('NULL as palabras_clave'), DB::raw('NULL as issn'),
                    DB::raw('NULL as volumen'), DB::raw('NULL as numero'), DB::raw('NULL as paginas'),
                )->first(),

            'monografia' => DB::table('t_monografia')
                ->where('slug', $slug)->where('estado', 1)
                ->select(
                    'id_monografia as id', 'slug',
                    'titulo_monografia as titulo', 'autor',
                    'descripcion_monografia as resumen',
                    DB::raw("'monografia' as tipo"), DB::raw("'Monografía' as tipo_label"),
                    DB::raw(SqlCompat::year('fecha_publicacion') . ' as anio'), 'fecha_publicacion',
                    DB::raw('NULL as area'), DB::raw('NULL as portada_url'),
                    'archivo as archivo_url',
                    DB::raw('NULL as palabras_clave'), DB::raw('NULL as issn'),
                    DB::raw('NULL as volumen'), DB::raw('NULL as numero'), DB::raw('NULL as paginas'),
                )->first(),

            'revista' => DB::table('t_revista')
                ->where('slug', $slug)->where('estado', 1)
                ->select(
                    'id_revista as id', 'slug',
                    'titulo_revista as titulo', DB::raw('NULL as autor'),
                    'descripcion_revista as resumen',
                    DB::raw("'revista' as tipo"), DB::raw("'Revista' as tipo_label"),
                    DB::raw(SqlCompat::year('fecha_publicacion') . ' as anio'), 'fecha_publicacion',
                    DB::raw('NULL as area'), DB::raw('NULL as portada_url'),
                    'archivo as archivo_url',
                    DB::raw('NULL as palabras_clave'), DB::raw('NULL as issn'),
                    DB::raw('NULL as volumen'), DB::raw('NULL as numero'), DB::raw('NULL as paginas'),
                )->first(),

            'revista-cientifica' => DB::table('t_revistacientifica')
                ->where('slug', $slug)->where('estado', 1)
                ->select(
                    'id_revistacientifica as id', 'slug',
                    'titulo_revistacientifica as titulo', DB::raw('NULL as autor'),
                    'descripcion_revistacientifica as resumen',
                    DB::raw("'revista-cientifica' as tipo"), DB::raw("'Revista Científica' as tipo_label"),
                    DB::raw(SqlCompat::year('fecha_publicacion') . ' as anio'), 'fecha_publicacion',
                    DB::raw('NULL as area'), DB::raw('NULL as portada_url'),
                    'archivo as archivo_url',
                    DB::raw('NULL as palabras_clave'), DB::raw('NULL as issn'),
                    DB::raw('NULL as volumen'), DB::raw('NULL as numero'), DB::raw('NULL as paginas'),
                )->first(),

            default => null,
        };

        if (! $row) {
            abort(404, 'Publicación no encontrada');
        }

        return response()->json($row);
    }
}
