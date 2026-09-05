<?php

namespace App\Infrastructure\Imparticiones\Repositories;

use App\Application\Imparticiones\DTOs\ImparteDTO;
use App\Domain\Imparticiones\Contracts\ImparteRepositoryInterface;
use App\Domain\Imparticiones\Exceptions\ImparteNotFoundException;
use App\Infrastructure\Imparticiones\Models\Imparte;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Support\Facades\DB;

class EloquentImparteRepository implements ImparteRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $conInactivos, ?string $periodo, ?string $gestion, ?int $idMat, ?int $idUs): array
    {
        $q = DB::table('t_imparte as i')
            ->joinSub(
                DB::table('t_imparte')->selectRaw('id_imp, MIN(id_us_reg) as min_us_reg')->groupBy('id_imp'),
                'dedup_i',
                fn ($j) => $j->on('i.id_imp', '=', 'dedup_i.id_imp')->on('i.id_us_reg', '=', 'dedup_i.min_us_reg')
            )
            ->leftJoin('t_materia as m', function ($j) {
                $j->on('i.id_mat', '=', 'm.id_mat')
                  ->whereRaw('m.id_us_reg = (SELECT MIN(m2.id_us_reg) FROM t_materia m2 WHERE m2.id_mat = m.id_mat)');
            })
            ->leftJoin('t_usuario as u', function ($j) {
                $j->on('i.id_us', '=', 'u.id_us')
                  ->whereRaw('u.id_us_reg = (SELECT MIN(u2.id_us_reg) FROM t_usuario u2 WHERE u2.id_us = u.id_us)');
            })
            ->select([
                'i.id_imp', 'i.id_mat', 'i.id_us', 'i.id_us_reg',
                'i.num_imp', 'i.periodo', 'i.gestion', 'i.paralelo',
                'i.cupo', 'i.observacion_imp', 'i.nro_resolucion_hcu',
                'i.id_moodle', 'i.estado', 'i.fecha_reg',
                'm.nombremat as materia_nombre',
                'm.sigla as materia_sigla',
                DB::raw("CONCAT(COALESCE(u.nombre,''), ' ', COALESCE(u.appaterno,'')) as docente_nombre"),
            ]);

        if (! $conInactivos) {
            $q->where('i.estado', 1);
        }

        if ($pagination->query) {
            $search = $pagination->query;
            $q->where(function ($sq) use ($search) {
                $sq->where('i.periodo', 'like', "%{$search}%")
                    ->orWhere('i.paralelo', 'like', "%{$search}%")
                    ->orWhere('m.nombremat', 'like', "%{$search}%")
                    ->orWhere('u.nombre', 'like', "%{$search}%")
                    ->orWhere('u.appaterno', 'like', "%{$search}%");
            });
        }

        if ($periodo !== null) {
            $q->where('i.periodo', $periodo);
        }

        if ($gestion !== null) {
            $q->where('i.gestion', $gestion);
        }

        if ($idMat !== null) {
            $q->where('i.id_mat', $idMat);
        }

        if ($idUs !== null) {
            $q->where('i.id_us', $idUs);
        }

        $total = $q->count();
        $items = $q->orderByDesc('i.id_imp')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get();

        return [
            'data'  => $items->map(fn ($row) => ImparteDTO::fromModel($row))->all(),
            'total' => $total,
        ];
    }

    public function findById(int $id): ImparteDTO
    {
        $row = Imparte::where('id_imp', $id)->first();

        if (! $row) {
            throw new ImparteNotFoundException($id);
        }

        return ImparteDTO::fromModel($row);
    }

    public function create(array $data): ImparteDTO
    {
        $imparte = Imparte::create($data);
        return ImparteDTO::fromModel($imparte);
    }

    public function update(int $id, array $data): ImparteDTO
    {
        $imparte = Imparte::where('id_imp', $id)->first();

        if (! $imparte) {
            throw new ImparteNotFoundException($id);
        }

        $imparte->update($data);
        return ImparteDTO::fromModel($imparte->fresh());
    }

    public function delete(int $id): void
    {
        $imparte = Imparte::where('id_imp', $id)->first();

        if (! $imparte) {
            throw new ImparteNotFoundException($id);
        }

        $imparte->update(['estado' => 0]);
    }

    public function tieneInscritos(int $id): bool
    {
        return DB::table('t_inscripcion')->where('id_imp', $id)->exists();
    }
}
