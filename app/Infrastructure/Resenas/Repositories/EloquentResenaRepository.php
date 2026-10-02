<?php

namespace App\Infrastructure\Resenas\Repositories;

use App\Application\Resenas\DTOs\ResenaDTO;
use App\Domain\Resenas\Contracts\ResenaRepositoryInterface;
use App\Domain\Resenas\Exceptions\ResenaNotFoundException;
use App\Infrastructure\Resenas\Models\Resena;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Support\Facades\DB;

class EloquentResenaRepository implements ResenaRepositoryInterface
{
    private function baseQuery(): \Illuminate\Database\Query\Builder
    {
        return DB::table('web_programa_resena as r')
            ->leftJoin('t_programa as p', 'r.programa_id', '=', 'p.id_programa')
            ->leftJoin('t_usuario as u', function ($j) {
                $j->on('r.usuario_id', '=', 'u.id_us')
                  ->whereRaw('u.id_us_reg = (SELECT MIN(u2.id_us_reg) FROM t_usuario u2 WHERE u2.id_us = u.id_us)');
            })
            ->select([
                'r.*',
                'p.nombre_programa',
                'u.nombre as u_nombre',
                'u.appaterno as u_appaterno',
                'u.apmaterno as u_apmaterno',
                'u.email as u_email',
            ]);
    }

    public function paginate(PaginationDTO $pagination, array $filters = []): array
    {
        $q = $this->baseQuery();

        if ($pagination->query) {
            $q->where(function ($sub) use ($pagination) {
                $sub->where('r.nombre', 'like', "%{$pagination->query}%")
                    ->orWhere('r.titulo_resena', 'like', "%{$pagination->query}%")
                    ->orWhere('r.resena', 'like', "%{$pagination->query}%")
                    ->orWhere('p.nombre_programa', 'like', "%{$pagination->query}%");
            });
        }

        if (! empty($filters['estado'])) {
            $q->where('r.estado', $filters['estado']);
        }

        if (! empty($filters['programa_id'])) {
            $q->where('r.programa_id', (int) $filters['programa_id']);
        }

        $total = $q->count();
        $data  = (clone $q)
            ->orderByDesc('r.created_at')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get()
            ->map(fn ($row) => ResenaDTO::fromModel($row))
            ->all();

        return ['data' => $data, 'total' => $total];
    }

    public function findById(int $id): ResenaDTO
    {
        $row = $this->baseQuery()->where('r.id', $id)->first();
        if (! $row) {
            throw new ResenaNotFoundException($id);
        }
        return ResenaDTO::fromModel($row);
    }

    public function create(array $data): ResenaDTO
    {
        $model = Resena::create($data);
        return $this->findById($model->id);
    }

    public function update(int $id, array $data): ResenaDTO
    {
        $model = Resena::findOrFail($id);
        $model->update($data);
        return $this->findById($id);
    }

    public function delete(int $id): void
    {
        $deleted = Resena::destroy($id);
        if (! $deleted) {
            throw new ResenaNotFoundException($id);
        }
    }

    public function estudiantesPrograma(int $programaId): array
    {
        $programa = DB::table('t_programa')->where('id_programa', $programaId)->first();
        if (! $programa || ! $programa->id_imp) {
            return ['data' => [], 'total' => 0];
        }

        $rows = DB::table('t_lista_aprobados as la')
            ->join('t_usuario as u', function ($j) {
                $j->on('la.usuario_id', '=', 'u.id_us')
                  ->whereRaw('u.id_us_reg = (SELECT MIN(u2.id_us_reg) FROM t_usuario u2 WHERE u2.id_us = u.id_us)');
            })
            ->where('la.imparte_id', $programa->id_imp)
            ->where(fn ($q) => $q->where('la.anulado', 0)->orWhereNull('la.anulado'))
            ->select('u.id_us', 'u.nombre', 'u.appaterno', 'u.apmaterno', 'u.email')
            ->orderBy('u.appaterno')
            ->orderBy('u.nombre')
            ->get()
            ->map(fn ($u) => [
                'id_us'           => $u->id_us,
                'nombre_completo' => trim(implode(' ', array_filter([$u->appaterno, $u->apmaterno, $u->nombre]))),
                'email'           => $u->email,
            ])
            ->all();

        return ['data' => $rows, 'total' => count($rows)];
    }
}
