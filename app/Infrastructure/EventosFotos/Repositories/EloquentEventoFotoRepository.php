<?php

namespace App\Infrastructure\EventosFotos\Repositories;

use App\Application\EventosFotos\DTOs\EventoFotoDTO;
use App\Domain\EventosFotos\Contracts\EventoFotoRepositoryInterface;
use App\Domain\EventosFotos\Exceptions\EventoFotoNotFoundException;
use App\Infrastructure\EventosFotos\Models\EventoFoto;

class EloquentEventoFotoRepository implements EventoFotoRepositoryInterface
{
    public function findByEvento(int $eventoId): array
    {
        return EventoFoto::where('evento_id', $eventoId)
            ->orderBy('orden', 'asc')
            ->get()
            ->map(fn ($m) => EventoFotoDTO::fromModel($m))
            ->all();
    }

    public function findById(int $id): EventoFotoDTO
    {
        $model = EventoFoto::find($id);
        if (! $model) {
            throw new EventoFotoNotFoundException($id);
        }

        return EventoFotoDTO::fromModel($model);
    }

    public function create(array $data): EventoFotoDTO
    {
        $model = EventoFoto::create($data);

        return EventoFotoDTO::fromModel($model);
    }

    public function update(int $id, array $data): EventoFotoDTO
    {
        $model = EventoFoto::find($id);
        if (! $model) {
            throw new EventoFotoNotFoundException($id);
        }
        $model->update($data);

        return EventoFotoDTO::fromModel($model);
    }

    public function delete(int|array $ids): bool
    {
        return EventoFoto::destroy($ids) > 0;
    }
}
