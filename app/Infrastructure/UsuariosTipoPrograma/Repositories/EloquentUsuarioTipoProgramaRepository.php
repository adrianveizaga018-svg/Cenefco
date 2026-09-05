<?php

namespace App\Infrastructure\UsuariosTipoPrograma\Repositories;

use App\Application\UsuariosTipoPrograma\DTOs\UsuarioTipoProgramaDTO;
use App\Domain\UsuariosTipoPrograma\Contracts\UsuarioTipoProgramaRepositoryInterface;
use App\Domain\UsuariosTipoPrograma\Exceptions\UsuarioTipoProgramaNotFoundException;
use App\Infrastructure\UsuariosTipoPrograma\Models\UsuarioTipoPrograma;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentUsuarioTipoProgramaRepository implements UsuarioTipoProgramaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?int $idUs, ?int $idTipoPrograma, bool $conInactivos): array
    {
        $q = UsuarioTipoPrograma::query();

        if ($idUs !== null) {
            $q->where('id_us', $idUs);
        }
        if ($idTipoPrograma !== null) {
            $q->where('id_tipoprograma', $idTipoPrograma);
        }
        if (! $conInactivos) {
            $q->where('estado', 1);
        }

        $total = $q->count();
        $data  = $q->orderBy('id_usuariotipoprograma')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get();

        return [
            'data'  => $data->map(fn ($r) => UsuarioTipoProgramaDTO::fromRow($r))->all(),
            'total' => $total,
        ];
    }

    public function findById(int $id): mixed
    {
        $row = UsuarioTipoPrograma::where('id_usuariotipoprograma', $id)->first();
        if (! $row) {
            throw new UsuarioTipoProgramaNotFoundException($id);
        }

        return $row;
    }

    public function create(array $data): mixed
    {
        return UsuarioTipoPrograma::create($data);
    }

    public function update(int $id, array $data): mixed
    {
        $row = $this->findById($id);
        $row->update($data);

        return $row->fresh();
    }

    public function delete(int $id): void
    {
        $row = $this->findById($id);
        $row->update(['estado' => 0]);
    }
}
