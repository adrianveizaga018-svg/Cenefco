<?php

namespace App\Infrastructure\CampanasLeads\Repositories;

use App\Application\CampanasLeads\DTOs\CampanaLeadDTO;
use App\Domain\CampanasLeads\Contracts\CampanaLeadRepositoryInterface;
use App\Domain\CampanasLeads\Exceptions\CampanaLeadNotFoundException;
use App\Infrastructure\CampanasLeads\Models\CampanaLead;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentCampanaLeadRepository implements CampanaLeadRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $estado = null): array
    {
        $q = CampanaLead::query()->withCount('leads');

        if ($pagination->query) {
            $q->where('nombre', 'like', "%{$pagination->query}%");
        }

        if ($estado) {
            $q->where('estado', $estado);
        }

        $sortKey = in_array($pagination->sortKey, ['nombre', 'estado', 'created_at'], true)
            ? $pagination->sortKey
            : 'created_at';

        $paginated = $q->orderBy($sortKey, $pagination->sortOrder)
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data'  => collect($paginated->items())->map(fn ($m) => CampanaLeadDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): CampanaLeadDTO
    {
        $model = CampanaLead::withCount('leads')->find($id);
        if (! $model) {
            throw new CampanaLeadNotFoundException($id);
        }

        return CampanaLeadDTO::fromModel($model);
    }

    public function create(array $data): CampanaLeadDTO
    {
        $model = CampanaLead::create($data);
        $model->loadCount('leads');

        return CampanaLeadDTO::fromModel($model);
    }

    public function update(int $id, array $data): CampanaLeadDTO
    {
        $model = CampanaLead::find($id);
        if (! $model) {
            throw new CampanaLeadNotFoundException($id);
        }
        $model->update($data);
        $model->loadCount('leads');

        return CampanaLeadDTO::fromModel($model);
    }

    public function delete(int $id): bool
    {
        $model = CampanaLead::find($id);
        if (! $model) {
            throw new CampanaLeadNotFoundException($id);
        }

        return (bool) $model->delete();
    }
}
