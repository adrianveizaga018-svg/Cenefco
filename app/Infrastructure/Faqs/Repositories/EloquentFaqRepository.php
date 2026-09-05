<?php

namespace App\Infrastructure\Faqs\Repositories;

use App\Application\Faqs\DTOs\FaqDTO;
use App\Domain\Faqs\Contracts\FaqRepositoryInterface;
use App\Domain\Faqs\Exceptions\FaqNotFoundException;
use App\Infrastructure\Faqs\Models\Faq;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentFaqRepository implements FaqRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array
    {
        $q = Faq::query();

        if ($pagination->query) {
            $q->where(function ($sq) use ($pagination) {
                $sq->where('pregunta', 'like', "%{$pagination->query}%")
                   ->orWhere('respuesta', 'like', "%{$pagination->query}%");
            });
        }

        $paginated = $q->orderBy('orden')
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data'  => collect($paginated->items())->map(fn ($m) => FaqDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): FaqDTO
    {
        $model = Faq::find($id);
        if (! $model) {
            throw new FaqNotFoundException($id);
        }

        return FaqDTO::fromModel($model);
    }

    public function create(array $data): FaqDTO
    {
        return FaqDTO::fromModel(Faq::create($data));
    }

    public function update(int $id, array $data): FaqDTO
    {
        $model = Faq::find($id);
        if (! $model) {
            throw new FaqNotFoundException($id);
        }
        $model->update($data);

        return FaqDTO::fromModel($model);
    }

    public function delete(int|array $ids): bool
    {
        return Faq::destroy($ids) > 0;
    }
}
