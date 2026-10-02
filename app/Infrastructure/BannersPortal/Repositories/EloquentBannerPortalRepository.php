<?php

namespace App\Infrastructure\BannersPortal\Repositories;

use App\Application\BannersPortal\DTOs\BannerPortalDTO;
use App\Domain\BannersPortal\Contracts\BannerPortalRepositoryInterface;
use App\Domain\BannersPortal\Exceptions\BannerPortalNotFoundException;
use App\Infrastructure\BannersPortal\Models\BannerPortal;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentBannerPortalRepository implements BannerPortalRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false): array
    {
        $q = BannerPortal::query();

        if ($soloActivos) {
            $q->where('activo', true);
        }

        if ($pagination->query) {
            $q->where('titulo', 'like', "%{$pagination->query}%");
        }

        $paginated = $q->orderBy($pagination->sortKey, $pagination->sortOrder)
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data' => collect($paginated->items())->map(fn ($m) => BannerPortalDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): BannerPortalDTO
    {
        $model = BannerPortal::find($id);
        if (! $model) {
            throw new BannerPortalNotFoundException($id);
        }

        return BannerPortalDTO::fromModel($model);
    }

    public function findBySlug(string $slug): BannerPortalDTO
    {
        $model = BannerPortal::where('slug', $slug)->first();
        if (! $model) {
            throw new BannerPortalNotFoundException($slug);
        }

        return BannerPortalDTO::fromModel($model);
    }

    public function create(array $data): BannerPortalDTO
    {
        $model = BannerPortal::create($data);

        return BannerPortalDTO::fromModel($model);
    }

    public function update(int $id, array $data): BannerPortalDTO
    {
        
        $model = BannerPortal::find($id);
        if (! $model) {
            throw new BannerPortalNotFoundException($id);
        }
        $model->update($data);

        return BannerPortalDTO::fromModel($model);
    }

    public function delete(int|array $ids): bool
    {
        return BannerPortal::destroy($ids) > 0;
    }
}
