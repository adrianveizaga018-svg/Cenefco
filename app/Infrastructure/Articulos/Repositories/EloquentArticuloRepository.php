<?php

namespace App\Infrastructure\Articulos\Repositories;

use App\Application\Articulos\DTOs\ArticuloDTO;
use App\Domain\Articulos\Contracts\ArticuloRepositoryInterface;
use App\Domain\Articulos\Exceptions\ArticuloNotFoundException;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Support\Facades\DB;

class EloquentArticuloRepository implements ArticuloRepositoryInterface
{
    private function withEtiquetasSelect(string $alias = 'a'): string
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            return "(SELECT '[' || STRING_AGG(
                        JSON_BUILD_OBJECT('id', e.id, 'nombre', e.nombre, 'slug', e.slug, 'color', e.color)::text,
                        ','
                      ) || ']'
                      FROM web_articulo_etiqueta ae
                      JOIN web_etiqueta e ON e.id = ae.etiqueta_id
                      WHERE ae.articulo_id = {$alias}.id_art) as etiquetas";
        }

        return "(SELECT CONCAT('[', GROUP_CONCAT(JSON_OBJECT('id', e.id, 'nombre', e.nombre, 'slug', e.slug, 'color', e.color)), ']')
                  FROM web_articulo_etiqueta ae
                  JOIN web_etiqueta e ON e.id = ae.etiqueta_id
                  WHERE ae.articulo_id = {$alias}.id_art) as etiquetas";
    }

    public function paginate(PaginationDTO $pagination, ?string $query, ?string $estadoWeb, bool $conInactivos): array
    {
        $q = DB::table('t_articulo as a')
            ->select(
                'a.id_art', 'a.titulo', 'a.slug', 'a.entradilla',
                'a.imagen_principal_url', 'a.imagen_alt', 'a.destacada',
                'a.fecha_publicacion', 'a.estado_web', 'a.estado',
                'a.vistas', 'a.fecha_reg',
                DB::raw($this->withEtiquetasSelect())
            );

        if ($query) {
            $q->where(function ($sq) use ($query) {
                $sq->where('a.titulo', 'like', "%{$query}%")
                   ->orWhere('a.entradilla', 'like', "%{$query}%");
            });
        }
        if ($estadoWeb) {
            $q->where('a.estado_web', $estadoWeb);
        }
        if (! $conInactivos) {
            $q->where('a.estado', 1)->whereNull('a.deleted_at');
        }

        $total = $q->count();
        $data  = (clone $q)
            ->orderByDesc('a.fecha_publicacion')
            ->orderByDesc('a.id_art')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get()
            ->map(fn ($row) => ArticuloDTO::fromRow($row))
            ->all();

        return ['data' => $data, 'total' => $total];
    }

    public function findById(int $id): mixed
    {
        $row = DB::table('t_articulo as a')
            ->select('a.*', DB::raw($this->withEtiquetasSelect()))
            ->where('a.id_art', $id)
            ->first();

        if (! $row) {
            throw new ArticuloNotFoundException($id);
        }

        return $row;
    }

    public function findBySlug(string $slug): mixed
    {
        $row = DB::table('t_articulo as a')
            ->select('a.*', DB::raw($this->withEtiquetasSelect()))
            ->where('a.slug', $slug)
            ->first();

        if (! $row) {
            throw new ArticuloNotFoundException($slug);
        }

        return $row;
    }

    public function create(array $data, array $etiquetaIds): mixed
    {
        return DB::transaction(function () use ($data, $etiquetaIds) {
            $id = DB::table('t_articulo')->insertGetId($data);
            $this->syncEtiquetas($id, $etiquetaIds);

            return $this->findById($id);
        });
    }

    public function update(int $id, array $data, ?array $etiquetaIds): mixed
    {
        return DB::transaction(function () use ($id, $data, $etiquetaIds) {
            DB::table('t_articulo')->where('id_art', $id)->update($data);

            if ($etiquetaIds !== null) {
                $this->syncEtiquetas($id, $etiquetaIds);
            }

            return $this->findById($id);
        });
    }

    public function softDelete(int $id): void
    {
        DB::transaction(function () use ($id) {
            DB::table('web_articulo_etiqueta')->where('articulo_id', $id)->delete();
            DB::table('t_articulo')->where('id_art', $id)->update([
                'estado'     => 0,
                'deleted_at' => now()->toDateTimeString(),
            ]);
        });
    }

    public function uniqueSlug(string $base, ?int $excludeId = null): string
    {
        $slug      = $base ?: 'articulo';
        $candidate = $slug;
        $i         = 2;

        while (true) {
            $q = DB::table('t_articulo')->where('slug', $candidate);
            if ($excludeId) {
                $q->where('id_art', '!=', $excludeId);
            }
            if (! $q->exists()) {
                break;
            }
            $candidate = $slug . '-' . $i++;
        }

        return $candidate;
    }

    private function syncEtiquetas(int $articuloId, array $etiquetaIds): void
    {
        DB::table('web_articulo_etiqueta')->where('articulo_id', $articuloId)->delete();

        $rows = array_map(fn ($eId) => ['articulo_id' => $articuloId, 'etiqueta_id' => $eId], $etiquetaIds);
        if ($rows) {
            DB::table('web_articulo_etiqueta')->insert($rows);
        }
    }
}
