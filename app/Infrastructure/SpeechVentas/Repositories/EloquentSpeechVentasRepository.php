<?php

namespace App\Infrastructure\SpeechVentas\Repositories;

use App\Application\SpeechVentas\DTOs\SpeechVentasDTO;
use App\Domain\SpeechVentas\Contracts\SpeechVentasRepositoryInterface;
use App\Domain\SpeechVentas\Exceptions\SpeechVentasNotFoundException;
use App\Infrastructure\SpeechVentas\Models\SpeechVentas;

class EloquentSpeechVentasRepository implements SpeechVentasRepositoryInterface
{
    public function paginate(int $pageIndex, int $pageSize, ?string $query, ?string $categoria, ?bool $activo): array
    {
        $q = SpeechVentas::query();

        if ($query) {
            $q->where(function ($sq) use ($query) {
                $sq->where('titulo',         'like', "%{$query}%")
                   ->orWhere('contenido',     'like', "%{$query}%")
                   ->orWhere('palabras_clave', 'like', "%{$query}%");
            });
        }
        if ($categoria) {
            $q->where('categoria', $categoria);
        }
        if ($activo !== null) {
            $q->where('activo', $activo);
        }

        $total = $q->count();
        $data  = (clone $q)
            ->orderBy('orden')
            ->orderByDesc('created_at')
            ->offset(($pageIndex - 1) * $pageSize)
            ->limit($pageSize)
            ->get();

        return [
            'data'  => $data->map(fn ($r) => SpeechVentasDTO::fromRow($r))->all(),
            'total' => $total,
        ];
    }

    public function findById(int $id): object
    {
        $row = SpeechVentas::find($id);
        if (! $row) {
            throw new SpeechVentasNotFoundException($id);
        }
        return $row;
    }

    public function create(array $data): object
    {
        return SpeechVentas::create($data);
    }

    public function update(int $id, array $data): void
    {
        $this->findById($id)->update($data);
    }

    public function delete(int $id): void
    {
        $this->findById($id)->delete();
    }

    public function toggleActivo(int $id): object
    {
        $row = $this->findById($id);
        $row->update(['activo' => ! $row->activo]);

        return $row->fresh();
    }

    public function getCategorias(): array
    {
        return SpeechVentas::whereNotNull('categoria')
            ->where('categoria', '!=', '')
            ->distinct()
            ->pluck('categoria')
            ->sort()
            ->values()
            ->all();
    }

    public function publicIndex(?string $q, ?string $categoria): array
    {
        return SpeechVentas::where('activo', true)
            ->when($categoria, fn ($sq) => $sq->where('categoria', $categoria))
            ->when($q, fn ($sq) => $sq->where(function ($s) use ($q) {
                $s->where('palabras_clave', 'like', "%{$q}%")
                  ->orWhere('titulo',       'like', "%{$q}%");
            }))
            ->orderBy('orden')
            ->get(['id', 'titulo', 'categoria', 'contenido', 'palabras_clave'])
            ->all();
    }
}
