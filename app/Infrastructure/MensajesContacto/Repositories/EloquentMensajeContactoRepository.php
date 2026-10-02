<?php

namespace App\Infrastructure\MensajesContacto\Repositories;

use App\Application\MensajesContacto\DTOs\MensajeContactoDTO;
use App\Domain\MensajesContacto\Contracts\MensajeContactoRepositoryInterface;
use App\Domain\MensajesContacto\Exceptions\MensajeContactoNotFoundException;
use App\Infrastructure\MensajesContacto\Models\MensajeContacto;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentMensajeContactoRepository implements MensajeContactoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array
    {
        $q = MensajeContacto::query();

        if ($pagination->query) {
            $q->where(function ($sub) use ($pagination) {
                $sub->where('nombre_remitente', 'like', "%{$pagination->query}%")
                    ->orWhere('email_remitente', 'like', "%{$pagination->query}%")
                    ->orWhere('asunto', 'like', "%{$pagination->query}%");
            });
        }

        $paginated = $q->orderBy($pagination->sortKey, $pagination->sortOrder)
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data' => collect($paginated->items())->map(fn ($m) => MensajeContactoDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): MensajeContactoDTO
    {
        $model = MensajeContacto::find($id);
        if (! $model) {
            throw new MensajeContactoNotFoundException($id);
        }

        return MensajeContactoDTO::fromModel($model);
    }

    public function create(array $data): MensajeContactoDTO
    {
        $model = MensajeContacto::create($data);

        return MensajeContactoDTO::fromModel($model);
    }

    public function update(int $id, array $data): MensajeContactoDTO
    {
        $model = MensajeContacto::find($id);
        if (! $model) {
            throw new MensajeContactoNotFoundException($id);
        }
        $model->update($data);

        return MensajeContactoDTO::fromModel($model);
    }

    public function delete(int|array $ids): bool
    {
        return MensajeContacto::destroy($ids) > 0;
    }
}
