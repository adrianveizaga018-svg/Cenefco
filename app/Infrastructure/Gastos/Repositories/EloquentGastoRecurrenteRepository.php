<?php

namespace App\Infrastructure\Gastos\Repositories;

use App\Application\Gastos\DTOs\GastoRecurrenteDTO;
use App\Domain\Gastos\Contracts\GastoRecurrenteRepositoryInterface;
use App\Domain\Gastos\Exceptions\GastoRecurrenteNotFoundException;
use App\Infrastructure\Gastos\Models\GastoRecurrente;

class EloquentGastoRecurrenteRepository implements GastoRecurrenteRepositoryInterface
{
    public function findAllActivos(): array
    {
        return GastoRecurrente::with('categoria')
            ->where('activo', true)
            ->whereNull('deleted_at')
            ->orderBy('dia_del_mes')
            ->get()
            ->map(fn ($r) => GastoRecurrenteDTO::fromModel($r))
            ->all();
    }

    public function findById(int $id): GastoRecurrenteDTO
    {
        $m = GastoRecurrente::with('categoria')->whereNull('deleted_at')->find($id);
        if (! $m) {
            throw new GastoRecurrenteNotFoundException($id);
        }

        return GastoRecurrenteDTO::fromModel($m);
    }

    public function create(array $data): GastoRecurrenteDTO
    {
        $m = GastoRecurrente::create($data);

        return GastoRecurrenteDTO::fromModel($m->load('categoria'));
    }

    public function update(int $id, array $data): GastoRecurrenteDTO
    {
        $m = GastoRecurrente::whereNull('deleted_at')->find($id);
        if (! $m) {
            throw new GastoRecurrenteNotFoundException($id);
        }

        $m->update($data);

        return GastoRecurrenteDTO::fromModel($m->fresh('categoria'));
    }

    public function delete(int $id): bool
    {
        $m = GastoRecurrente::whereNull('deleted_at')->find($id);
        if (! $m) {
            throw new GastoRecurrenteNotFoundException($id);
        }

        return (bool) $m->delete();
    }

    public function marcarConfirmado(int $id, string $fecha): GastoRecurrenteDTO
    {
        $m = GastoRecurrente::whereNull('deleted_at')->find($id);
        if (! $m) {
            throw new GastoRecurrenteNotFoundException($id);
        }

        $m->update(['ultima_confirmacion' => $fecha]);

        return GastoRecurrenteDTO::fromModel($m->fresh('categoria'));
    }

    public function pendientesDelMes(int $anio, int $mes): array
    {
        return GastoRecurrente::with('categoria')
            ->where('activo', true)
            ->whereNull('deleted_at')
            ->where(function ($q) use ($anio, $mes) {
                $q->whereNull('ultima_confirmacion')
                    ->orWhereYear('ultima_confirmacion', '!=', $anio)
                    ->orWhereMonth('ultima_confirmacion', '!=', $mes);
            })
            ->get()
            ->map(fn ($r) => GastoRecurrenteDTO::fromModel($r))
            ->all();
    }
}
