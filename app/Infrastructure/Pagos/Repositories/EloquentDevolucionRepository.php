<?php

namespace App\Infrastructure\Pagos\Repositories;

use App\Domain\Pagos\Contracts\DevolucionRepositoryInterface;
use App\Infrastructure\Pagos\Models\Devolucion;

class EloquentDevolucionRepository implements DevolucionRepositoryInterface
{
    public function findByInscripcion(int $idIns): array
    {
        return Devolucion::where('id_ins', $idIns)
            ->orderByDesc('created_at')
            ->get()
            ->all();
    }

    public function create(array $data): mixed
    {
        return Devolucion::create($data);
    }

    public function update(int $id, array $data): mixed
    {
        $dev = Devolucion::find($id);
        if (! $dev) {
            return null;
        }
        $dev->update($data);
        return $dev->fresh();
    }

    public function findByIdConLock(int $id): mixed
    {
        return Devolucion::where('id', $id)->lockForUpdate()->first();
    }
}
