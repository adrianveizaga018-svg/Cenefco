<?php

namespace App\Infrastructure\TiposPrograma\Repositories;

use App\Application\TiposPrograma\DTOs\TipoProgramaDTO;
use App\Domain\TiposPrograma\Contracts\TipoProgramaRepositoryInterface;
use App\Domain\TiposPrograma\Exceptions\TipoProgramaNotFoundException;
use App\Infrastructure\TiposPrograma\Models\TipoPrograma;

class EloquentTipoProgramaRepository implements TipoProgramaRepositoryInterface
{
    public function paginate(int $pageIndex, int $pageSize, ?string $query, bool $conInactivos): array
    {
        $q = TipoPrograma::query();

        if ($query) {
            $q->where('nombre_tipoprograma', 'like', "%{$query}%");
        }
        if (! $conInactivos) {
            $q->where('estado', 1);
        }

        $total = $q->count();
        $data  = (clone $q)
            ->orderBy('nombre_tipoprograma')
            ->offset(($pageIndex - 1) * $pageSize)
            ->limit($pageSize)
            ->get();

        return [
            'data'  => $data->map(fn ($r) => TipoProgramaDTO::fromRow($r))->all(),
            'total' => $total,
        ];
    }

    public function findById(int $id): object
    {
        $row = TipoPrograma::find($id);
        if (! $row) {
            throw new TipoProgramaNotFoundException($id);
        }
        return $row;
    }

    public function create(array $data): object
    {
        return TipoPrograma::create($data);
    }

    public function update(int $id, array $data): void
    {
        $this->findById($id)->update($data);
    }

    public function delete(int $id): void
    {
        $this->findById($id)->update(['estado' => 0]);
    }
}
