<?php

namespace App\Infrastructure\Testimonios\Repositories;

use App\Application\Testimonios\DTOs\TestimonioDTO;
use App\Domain\Testimonios\Contracts\TestimonioRepositoryInterface;
use App\Domain\Testimonios\Exceptions\TestimonioNotFoundException;
use App\Infrastructure\Testimonios\Models\Testimonio;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentTestimonioRepository implements TestimonioRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloDestacados = false): array
    {
        $q = Testimonio::query();

        if ($soloDestacados) {
            $q->where('destacado', true);
        }

        if ($pagination->query) {
            $q->where(function ($sq) use ($pagination) {
                $sq->where('nombre', 'like', "%{$pagination->query}%")
                   ->orWhere('testimonio', 'like', "%{$pagination->query}%");
            });
        }

        $paginated = $q->orderBy('orden')
            ->orderByDesc('id')
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data'  => collect($paginated->items())->map(fn ($m) => TestimonioDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): TestimonioDTO
    {
        $model = Testimonio::find($id);
        if (! $model) {
            throw new TestimonioNotFoundException($id);
        }

        return TestimonioDTO::fromModel($model);
    }

    public function findBySlug(string $slug): TestimonioDTO
    {
        $model = Testimonio::where('slug', $slug)->first();
        if (! $model) {
            throw new TestimonioNotFoundException($slug);
        }

        return TestimonioDTO::fromModel($model);
    }

    public function create(array $data): TestimonioDTO
    {
        $model = Testimonio::create($data);

        return TestimonioDTO::fromModel($model);
    }

    public function update(int $id, array $data): TestimonioDTO
    {
        $model = Testimonio::find($id);
        if (! $model) {
            throw new TestimonioNotFoundException($id);
        }
        $model->update($data);

        return TestimonioDTO::fromModel($model);
    }

    public function delete(int|array $ids): bool
    {
        return Testimonio::destroy($ids) > 0;
    }
}
