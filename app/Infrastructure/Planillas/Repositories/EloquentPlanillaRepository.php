<?php

namespace App\Infrastructure\Planillas\Repositories;

use App\Application\Planillas\DTOs\PlanillaDTO;
use App\Domain\Planillas\Contracts\PlanillaRepositoryInterface;
use App\Domain\Planillas\Exceptions\PlanillaNotFoundException;
use App\Infrastructure\Planillas\Models\Planilla;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentPlanillaRepository implements PlanillaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array
    {
        $q = Planilla::query()->with('detalle');

        $sortKey   = $pagination->sortKey ?: 'anio';
        $sortOrder = $pagination->sortOrder ?: 'desc';

        $paginated = $q->orderBy($sortKey, $sortOrder)
            ->orderBy('mes', $sortOrder)
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data'  => collect($paginated->items())->map(fn ($p) => PlanillaDTO::fromModel($p))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): PlanillaDTO
    {
        $m = Planilla::with('detalle')->find($id);
        if (! $m) {
            throw new PlanillaNotFoundException($id);
        }

        return PlanillaDTO::fromModel($m);
    }

    public function existePlanillaDelMes(int $anio, int $mes): bool
    {
        return Planilla::where('anio', $anio)->where('mes', $mes)->exists();
    }

    public function create(array $data, array $detalle): PlanillaDTO
    {
        $m = Planilla::create($data);
        $m->detalle()->createMany($detalle);

        return PlanillaDTO::fromModel($m->load('detalle'));
    }

    public function delete(int $id): bool
    {
        $m = Planilla::find($id);
        if (! $m) {
            throw new PlanillaNotFoundException($id);
        }

        return (bool) $m->delete();
    }
}
