<?php

namespace App\Infrastructure\UsuariosPrograma\Repositories;

use App\Application\UsuariosPrograma\DTOs\UsuarioProgramaDTO;
use App\Domain\UsuariosPrograma\Contracts\UsuarioProgramaRepositoryInterface;
use App\Domain\UsuariosPrograma\Exceptions\UsuarioProgramaNotFoundException;
use App\Infrastructure\UsuariosPrograma\Models\UsuarioPrograma;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentUsuarioProgramaRepository implements UsuarioProgramaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?int $idUs, ?int $idPrograma, ?int $idTipoPrograma, bool $conInactivos): array
    {
        $q = UsuarioPrograma::query();

        if ($idUs !== null) {
            $q->where('id_us', $idUs);
        }
        if ($idPrograma !== null) {
            $q->where('id_programa', $idPrograma);
        }
        if ($idTipoPrograma !== null) {
            $q->where('id_tipoprograma', $idTipoPrograma);
        }
        if (! $conInactivos) {
            $q->where('estado', 1);
        }

        $total = $q->count();
        $data  = $q->orderBy('id_usuarioprograma')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get();

        return [
            'data'  => $data->map(fn ($r) => UsuarioProgramaDTO::fromRow($r))->all(),
            'total' => $total,
        ];
    }

    public function findById(int $id): mixed
    {
        $row = UsuarioPrograma::where('id_usuarioprograma', $id)->first();
        if (! $row) {
            throw new UsuarioProgramaNotFoundException($id);
        }

        return $row;
    }

    public function create(array $data): mixed
    {
        return UsuarioPrograma::create($data);
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
