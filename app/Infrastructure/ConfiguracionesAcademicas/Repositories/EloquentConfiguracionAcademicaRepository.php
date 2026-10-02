<?php

namespace App\Infrastructure\ConfiguracionesAcademicas\Repositories;

use App\Application\ConfiguracionesAcademicas\DTOs\ConfiguracionAcademicaDTO;
use App\Domain\ConfiguracionesAcademicas\Contracts\ConfiguracionAcademicaRepositoryInterface;
use App\Domain\ConfiguracionesAcademicas\Exceptions\ConfiguracionAcademicaNotFoundException;
use App\Infrastructure\ConfiguracionesAcademicas\Models\ConfiguracionAcademica;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentConfiguracionAcademicaRepository implements ConfiguracionAcademicaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?int $gestion, ?int $idPlan, bool $conInactivos): array
    {
        $q = ConfiguracionAcademica::query();

        if (! $conInactivos) {
            $q->where('estado', 1);
        }
        if ($gestion !== null) {
            $q->where('gestion', $gestion);
        }
        if ($idPlan !== null) {
            $q->where('id_plan', $idPlan);
        }

        $paginated = $q->orderBy('id_conf', 'desc')
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data'  => collect($paginated->items())->map(fn ($r) => ConfiguracionAcademicaDTO::fromRow($r))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $idConf): object
    {
        $row = ConfiguracionAcademica::find($idConf);
        if (! $row) {
            throw new ConfiguracionAcademicaNotFoundException($idConf);
        }
        return $row;
    }

    public function create(array $data): object
    {
        return ConfiguracionAcademica::create($data);
    }

    public function update(int $idConf, array $data): void
    {
        $row = $this->findById($idConf);
        $row->update($data);
    }

    public function delete(int $idConf): void
    {
        $row = $this->findById($idConf);
        $row->update(['estado' => 0]);
    }
}
