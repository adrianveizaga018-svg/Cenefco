<?php

namespace App\Infrastructure\Intents\Repositories;

use App\Domain\Intents\Contracts\IntentRepositoryInterface;
use App\Domain\Intents\Exceptions\IntentNotFoundException;
use App\Infrastructure\Intents\Models\Intent;

class EloquentIntentRepository implements IntentRepositoryInterface
{
    public function index(?string $query, ?string $dominio): array
    {
        $q = Intent::query();

        if ($query) {
            $q->where(function ($sq) use ($query) {
                $sq->where('nombre', 'like', "%{$query}%")
                   ->orWhere('slug',   'like', "%{$query}%")
                   ->orWhere('accion', 'like', "%{$query}%");
            });
        }
        if ($dominio) {
            $q->where('dominio', $dominio);
        }

        return $q->orderBy('dominio')->orderBy('orden')->get()->all();
    }

    public function findById(int $id): object
    {
        $row = Intent::find($id);
        if (! $row) {
            throw new IntentNotFoundException($id);
        }
        return $row;
    }

    public function create(array $data): object
    {
        return Intent::create($data);
    }

    public function update(int $id, array $data): void
    {
        $this->findById($id)->update($data);
    }

    public function delete(int $id): void
    {
        $row = Intent::find($id);
        if (! $row) {
            throw new IntentNotFoundException($id);
        }
        $row->delete();
    }

    public function toggleActivo(int $id): bool
    {
        $row = $this->findById($id);
        $newValue = ! $row->activo;
        $row->update(['activo' => $newValue]);

        return $newValue;
    }
}
