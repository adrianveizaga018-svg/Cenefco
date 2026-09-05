<?php

namespace App\Infrastructure\RedesSociales\Repositories;

use App\Application\RedesSociales\DTOs\RedSocialDTO;
use App\Domain\RedesSociales\Contracts\RedSocialRepositoryInterface;
use App\Domain\RedesSociales\Exceptions\RedSocialNotFoundException;
use App\Infrastructure\RedesSociales\Models\RedSocial;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentRedSocialRepository implements RedSocialRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array
    {
        $q = RedSocial::query();

        if ($pagination->query) {
            $q->where('red', 'like', "%{$pagination->query}%")
                ->orWhere('nombre_display', 'like', "%{$pagination->query}%");
        }

        $paginated = $q->orderBy('orden', 'asc')
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data' => collect($paginated->items())->map(fn ($r) => RedSocialDTO::fromModel($r))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): RedSocialDTO
    {
        $redSocial = RedSocial::find($id);

        if (! $redSocial) {
            throw new RedSocialNotFoundException($id);
        }

        return RedSocialDTO::fromModel($redSocial);
    }

    public function create(array $data): RedSocialDTO
    {
        $model = RedSocial::create($data);

        return RedSocialDTO::fromModel($model);
    }

    public function update(int $id, array $data): RedSocialDTO
    {
        $redSocial = RedSocial::find($id);

        if (! $redSocial) {
            throw new RedSocialNotFoundException($id);
        }

        $redSocial->update($data);

        return RedSocialDTO::fromModel($redSocial);
    }

    public function delete(int|array $ids): bool
    {
        if (is_array($ids)) {
            RedSocial::whereIn('id', $ids)->delete();
        } else {
            $redSocial = RedSocial::find($ids);

            if (! $redSocial) {
                throw new RedSocialNotFoundException($ids);
            }

            $redSocial->delete();
        }

        return true;
    }
}
