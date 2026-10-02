<?php

namespace App\Infrastructure\CampanasLeads\Repositories;

use App\Application\CampanasLeads\DTOs\LeadDTO;
use App\Domain\CampanasLeads\Contracts\LeadRepositoryInterface;
use App\Domain\CampanasLeads\Exceptions\LeadNotFoundException;
use App\Infrastructure\CampanasLeads\Models\Lead;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentLeadRepository implements LeadRepositoryInterface
{
    public function paginate(int $campanaLeadId, PaginationDTO $pagination): array
    {
        $q = Lead::query()->where('campana_lead_id', $campanaLeadId);

        if ($pagination->query) {
            $q->where(function ($sub) use ($pagination) {
                $sub->where('nombre', 'like', "%{$pagination->query}%")
                    ->orWhere('celular', 'like', "%{$pagination->query}%")
                    ->orWhere('correo', 'like', "%{$pagination->query}%");
            });
        }

        $sortKey = in_array($pagination->sortKey, ['nombre', 'created_at'], true)
            ? $pagination->sortKey
            : 'created_at';

        $paginated = $q->orderBy($sortKey, $pagination->sortOrder)
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data'  => collect($paginated->items())->map(fn ($m) => LeadDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $campanaLeadId, int $id): LeadDTO
    {
        $model = Lead::where('campana_lead_id', $campanaLeadId)->find($id);
        if (! $model) {
            throw new LeadNotFoundException($id);
        }

        return LeadDTO::fromModel($model);
    }

    public function create(int $campanaLeadId, array $data): LeadDTO
    {
        $data['campana_lead_id'] = $campanaLeadId;
        $model = Lead::create($data);

        return LeadDTO::fromModel($model);
    }

    public function update(int $campanaLeadId, int $id, array $data): LeadDTO
    {
        $model = Lead::where('campana_lead_id', $campanaLeadId)->find($id);
        if (! $model) {
            throw new LeadNotFoundException($id);
        }
        $model->update($data);

        return LeadDTO::fromModel($model);
    }

    public function delete(int $campanaLeadId, int $id): bool
    {
        $model = Lead::where('campana_lead_id', $campanaLeadId)->find($id);
        if (! $model) {
            throw new LeadNotFoundException($id);
        }

        return (bool) $model->delete();
    }
}
