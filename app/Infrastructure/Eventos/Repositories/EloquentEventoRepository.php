<?php

namespace App\Infrastructure\Eventos\Repositories;

use App\Application\Eventos\DTOs\EventoDTO;
use App\Domain\Eventos\Contracts\EventoRepositoryInterface;
use App\Domain\Eventos\Exceptions\EventoNotFoundException;
use App\Infrastructure\Eventos\Models\Evento;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentEventoRepository implements EventoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false): array
    {
        $q = Evento::query();
        if ($soloActivos) $q->where('estado', 'publicado');
        if ($pagination->query) {
            $q->where(function ($sq) use ($pagination) {
                $sq->where('titulo', 'like', "%{$pagination->query}%")
                   ->orWhere('lugar', 'like', "%{$pagination->query}%");
            });
        }
        $paginated = $q->orderBy($pagination->sortKey, $pagination->sortOrder)
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);
        return [
            'data'  => collect($paginated->items())->map(fn($m) => EventoDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): EventoDTO
    {
        $m = Evento::find($id);
        if (!$m) throw new EventoNotFoundException($id);
        return EventoDTO::fromModel($m);
    }

    public function findBySlug(string $slug): EventoDTO
    {
        $m = Evento::where('slug', $slug)->first();
        if (!$m) throw new EventoNotFoundException($slug);
        return EventoDTO::fromModel($m);
    }

    public function create(array $data): EventoDTO
    {
        return EventoDTO::fromModel(Evento::create($data));
    }

    public function update(int $id, array $data): EventoDTO
    {
        $m = Evento::find($id);
        if (!$m) throw new EventoNotFoundException($id);
        $m->update($data);
        return EventoDTO::fromModel($m);
    }

    public function delete(int|array $ids): bool
    {
        return Evento::destroy($ids) > 0;
    }
}
