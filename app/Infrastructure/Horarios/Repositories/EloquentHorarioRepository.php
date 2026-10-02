<?php

namespace App\Infrastructure\Horarios\Repositories;

use App\Application\Horarios\DTOs\HorarioDTO;
use App\Domain\Horarios\Contracts\HorarioRepositoryInterface;
use App\Domain\Horarios\Exceptions\HorarioNotFoundException;
use App\Infrastructure\Horarios\Models\Horario;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentHorarioRepository implements HorarioRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $conInactivos, ?int $idImp): array
    {
        $q = Horario::query();

        if (! $conInactivos) {
            $q->where('estado', 1);
        }

        if ($idImp !== null) {
            $q->where('id_imp', $idImp);
        }

        $total = $q->count();
        $data  = $q->orderBy('hora_inicio')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get()
            ->map(fn ($h) => HorarioDTO::fromModel($h))
            ->all();

        return ['data' => $data, 'total' => $total];
    }

    public function findById(int $id): HorarioDTO
    {
        $horario = Horario::where('id_horar', $id)->first();
        if (! $horario) {
            throw new HorarioNotFoundException($id);
        }

        return HorarioDTO::fromModel($horario);
    }

    public function create(array $data): HorarioDTO
    {
        $horario = Horario::create($data);

        return HorarioDTO::fromModel($horario);
    }

    public function update(int $id, array $data): HorarioDTO
    {
        $horario = Horario::where('id_horar', $id)->first();
        if (! $horario) {
            throw new HorarioNotFoundException($id);
        }

        $horario->update($data);

        return HorarioDTO::fromModel($horario);
    }

    public function delete(int $id): void
    {
        $horario = Horario::where('id_horar', $id)->first();
        if (! $horario) {
            throw new HorarioNotFoundException($id);
        }

        $horario->update(['estado' => 0]);
    }
}
