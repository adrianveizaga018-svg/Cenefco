<?php
namespace App\Infrastructure\PlanDoc\Repositories;
use App\Application\PlanDoc\DTOs\PlanDocDTO;
use App\Domain\PlanDoc\Contracts\PlanDocRepositoryInterface;
use App\Infrastructure\PlanDoc\Models\PlanDoc;
use App\Shared\Kernel\DTOs\PaginationDTO;
class EloquentPlanDocRepository implements PlanDocRepositoryInterface {
    public function paginate(PaginationDTO $pagination, ?string $query, bool $conInactivos): array {
        $q = PlanDoc::query();
        if (!$conInactivos) $q->where('estado', 1);
        if ($query) $q->where('nombre', 'like', "%{$query}%");
        $paginated = $q->orderBy('id_plandoc', 'desc')->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);
        return ['data' => collect($paginated->items())->map(fn($r) => PlanDocDTO::fromRow($r))->all(), 'total' => $paginated->total()];
    }
}
