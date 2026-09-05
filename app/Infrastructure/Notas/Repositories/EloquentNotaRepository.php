<?php

namespace App\Infrastructure\Notas\Repositories;

use App\Application\Notas\DTOs\NotaDTO;
use App\Domain\Notas\Contracts\NotaRepositoryInterface;
use App\Domain\Notas\Exceptions\NotaNotFoundException;
use App\Infrastructure\Notas\Models\Nota;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentNotaRepository implements NotaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $conInactivos, ?int $idUs, ?int $idImp, ?int $idMat, ?string $periodo, ?string $gestion): array
    {
        $q = Nota::query();

        if (! $conInactivos) {
            $q->where('estado', 1);
        }
        if ($idUs !== null) {
            $q->where('id_us', $idUs);
        }
        if ($idImp !== null) {
            $q->where('id_imp', $idImp);
        }
        if ($idMat !== null) {
            $q->where('id_mat', $idMat);
        }
        if ($periodo !== null) {
            $q->where('periodo', $periodo);
        }
        if ($gestion !== null) {
            $q->where('gestion', $gestion);
        }

        $total = $q->count();
        $data  = $q->orderByDesc('id_not')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get()
            ->map(fn ($n) => NotaDTO::fromModel($n))
            ->all();

        return ['data' => $data, 'total' => $total];
    }

    public function findById(int $id): NotaDTO
    {
        $nota = Nota::where('id_not', $id)->first();
        if (! $nota) {
            throw new NotaNotFoundException($id);
        }

        return NotaDTO::fromModel($nota);
    }

    public function create(array $data): NotaDTO
    {
        return NotaDTO::fromModel(Nota::create($data));
    }

    public function update(int $id, array $data): NotaDTO
    {
        $nota = Nota::where('id_not', $id)->first();
        if (! $nota) {
            throw new NotaNotFoundException($id);
        }

        $nota->update($data);

        return NotaDTO::fromModel($nota);
    }

    public function delete(int $id): void
    {
        $nota = Nota::where('id_not', $id)->first();
        if (! $nota) {
            throw new NotaNotFoundException($id);
        }

        $nota->update(['estado' => 0]);
    }
}
