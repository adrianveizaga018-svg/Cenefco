<?php

declare(strict_types=1);

namespace App\Infrastructure\NotasPrensa\Repositories;

use App\Application\NotasPrensa\Commands\CreateNotaPrensaCommand;
use App\Application\NotasPrensa\Commands\UpdateNotaPrensaCommand;
use App\Application\NotasPrensa\DTOs\NotaPrensaDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;
use App\Domain\NotasPrensa\Contracts\NotaPrensaRepositoryInterface;
use App\Domain\NotasPrensa\Exceptions\NotaPrensaNotFoundException;
use App\Infrastructure\NotasPrensa\Models\NotaPrensa;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentNotaPrensaRepository implements NotaPrensaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloDestacadas = false, bool $soloActivos = false): LengthAwarePaginator
    {
        $query = NotaPrensa::query()
            ->orderBy('fecha_publicacion', 'desc')
            ->orderBy('orden', 'asc');

        if ($soloDestacadas) {
            $query->where('destacada', true);
        }

        if ($soloActivos) {
            $query->where('activo', true);
        }

        return $query->paginate(
            perPage: $pagination->pageSize,
            page: $pagination->pageIndex,
        );
    }

    public function findById(int|string $id): NotaPrensaDTO
    {
        $model = NotaPrensa::find($id);

        if ($model === null) {
            throw new NotaPrensaNotFoundException($id);
        }

        return NotaPrensaDTO::fromModel($model);
    }

    public function create(CreateNotaPrensaCommand $command): NotaPrensaDTO
    {
        $model = NotaPrensa::create([
            'titulo'            => $command->titulo,
            'medio'             => $command->medio,
            'logo_medio_url'    => $command->logo_medio_url,
            'logo_medio_alt'    => $command->logo_medio_alt,
            'resumen'           => $command->resumen,
            'url_noticia'       => $command->url_noticia,
            'fecha_publicacion' => $command->fecha_publicacion,
            'destacada'         => $command->destacada,
            'orden'             => $command->orden,
            'activo'            => $command->activo,
        ]);

        return NotaPrensaDTO::fromModel($model);
    }

    public function update(UpdateNotaPrensaCommand $command): NotaPrensaDTO
    {
        $model = NotaPrensa::find($command->id);

        if ($model === null) {
            throw new NotaPrensaNotFoundException($command->id);
        }

        $data = array_filter([
            'titulo'            => $command->titulo,
            'medio'             => $command->medio,
            'logo_medio_url'    => $command->logo_medio_url,
            'logo_medio_alt'    => $command->logo_medio_alt,
            'resumen'           => $command->resumen,
            'url_noticia'       => $command->url_noticia,
            'fecha_publicacion' => $command->fecha_publicacion,
            'destacada'         => $command->destacada,
            'orden'             => $command->orden,
            'activo'            => $command->activo,
        ], fn($v) => $v !== null);

        $model->update($data);

        return NotaPrensaDTO::fromModel($model->fresh());
    }

    public function delete(int|string $id): void
    {
        $model = NotaPrensa::find($id);

        if ($model === null) {
            throw new NotaPrensaNotFoundException($id);
        }

        $model->delete();
    }
}
