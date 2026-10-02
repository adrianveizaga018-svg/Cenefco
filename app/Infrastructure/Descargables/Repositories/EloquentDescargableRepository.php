<?php

declare(strict_types=1);

namespace App\Infrastructure\Descargables\Repositories;

use App\Application\Descargables\Commands\CreateDescargableCommand;
use App\Application\Descargables\Commands\UpdateDescargableCommand;
use App\Application\Descargables\DTOs\DescargableDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;
use App\Domain\Descargables\Contracts\DescargableRepositoryInterface;
use App\Domain\Descargables\Exceptions\DescargableNotFoundException;
use App\Infrastructure\Descargables\Models\Descargable;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentDescargableRepository implements DescargableRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false): LengthAwarePaginator
    {
        $query = Descargable::query()
            ->orderBy('orden', 'asc')
            ->orderBy('nombre', 'asc');

        if ($soloActivos) {
            $query->where('activo', true);
        }

        return $query->paginate(
            perPage: $pagination->pageSize,
            page: $pagination->pageIndex,
        );
    }

    public function findById(int|string $id): DescargableDTO
    {
        $model = Descargable::find($id);

        if ($model === null) {
            throw new DescargableNotFoundException($id);
        }

        return DescargableDTO::fromModel($model);
    }

    public function create(CreateDescargableCommand $command): DescargableDTO
    {
        $model = Descargable::create([
            'nombre'             => $command->nombre,
            'tipo'               => $command->tipo,
            'archivo_url'        => $command->archivo_url,
            'imagen_portada_url' => $command->imagen_portada_url,
            'programa_id'        => $command->programa_id,
            'requiere_datos'     => $command->requiere_datos,
            'orden'              => $command->orden,
            'activo'             => $command->activo,
        ]);

        return DescargableDTO::fromModel($model);
    }

    public function update(UpdateDescargableCommand $command): DescargableDTO
    {
        $model = Descargable::find($command->id);

        if ($model === null) {
            throw new DescargableNotFoundException($command->id);
        }

        $data = array_filter([
            'nombre'             => $command->nombre,
            'tipo'               => $command->tipo,
            'archivo_url'        => $command->archivo_url,
            'imagen_portada_url' => $command->imagen_portada_url,
            'programa_id'        => $command->programa_id,
            'requiere_datos'     => $command->requiere_datos,
            'orden'              => $command->orden,
            'activo'             => $command->activo,
        ], fn($v) => $v !== null);

        $model->update($data);

        return DescargableDTO::fromModel($model->fresh());
    }

    public function delete(int|string $id): void
    {
        $model = Descargable::find($id);

        if ($model === null) {
            throw new DescargableNotFoundException($id);
        }

        $model->delete();
    }
}
