<?php

declare(strict_types=1);

namespace App\Infrastructure\Suscriptores\Repositories;

use App\Application\Suscriptores\Commands\CreateSuscriptorCommand;
use App\Application\Suscriptores\Commands\UpdateSuscriptorCommand;
use App\Application\Suscriptores\DTOs\SuscriptorDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;
use App\Domain\Suscriptores\Contracts\SuscriptorRepositoryInterface;
use App\Domain\Suscriptores\Exceptions\SuscriptorNotFoundException;
use App\Infrastructure\Suscriptores\Models\Suscriptor;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentSuscriptorRepository implements SuscriptorRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): LengthAwarePaginator
    {
        return Suscriptor::query()
            ->orderBy('created_at', 'desc')
            ->paginate(
                perPage: $pagination->pageSize,
                page: $pagination->pageIndex,
            );
    }

    public function findById(int|string $id): SuscriptorDTO
    {
        $model = Suscriptor::find($id);

        if ($model === null) {
            throw new SuscriptorNotFoundException($id);
        }

        return SuscriptorDTO::fromModel($model);
    }

    public function create(CreateSuscriptorCommand $command): SuscriptorDTO
    {
        $model = Suscriptor::create([
            'email'      => $command->email,
            'nombre'     => $command->nombre,
            'confirmado' => $command->confirmado,
            'activo'     => $command->activo,
            'origen'     => $command->origen,
        ]);

        return SuscriptorDTO::fromModel($model);
    }

    public function update(UpdateSuscriptorCommand $command): SuscriptorDTO
    {
        $model = Suscriptor::find($command->id);

        if ($model === null) {
            throw new SuscriptorNotFoundException($command->id);
        }

        $data = array_filter([
            'nombre'     => $command->nombre,
            'confirmado' => $command->confirmado,
            'activo'     => $command->activo,
        ], fn($v) => $v !== null);

        $model->update($data);

        return SuscriptorDTO::fromModel($model->fresh());
    }

    public function delete(int|string $id): void
    {
        $model = Suscriptor::find($id);

        if ($model === null) {
            throw new SuscriptorNotFoundException($id);
        }

        $model->delete();
    }
}
