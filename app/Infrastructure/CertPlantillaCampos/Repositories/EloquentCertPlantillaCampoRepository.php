<?php

namespace App\Infrastructure\CertPlantillaCampos\Repositories;

use App\Application\CertPlantillaCampos\DTOs\CertPlantillaCampoDTO;
use App\Domain\CertPlantillaCampos\Contracts\CertPlantillaCampoRepositoryInterface;
use App\Domain\CertPlantillaCampos\Exceptions\CertPlantillaCampoNotFoundException;
use App\Infrastructure\CertPlantillaCampos\Models\CertPlantillaCampo;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentCertPlantillaCampoRepository implements CertPlantillaCampoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?int $plantillaId, bool $soloActivos): array
    {
        $q = CertPlantillaCampo::query();

        if ($plantillaId !== null) {
            $q->where('plantilla_id', $plantillaId);
        }
        if ($soloActivos) {
            $q->where('activo', true);
        }

        $total = $q->count();
        $data  = $q->orderBy('orden')->orderBy('clave')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get()
            ->map(fn ($row) => CertPlantillaCampoDTO::fromRow($row))
            ->all();

        return ['data' => $data, 'total' => $total];
    }

    public function findById(int $id): mixed
    {
        $row = CertPlantillaCampo::find($id);
        if (! $row) {
            throw new CertPlantillaCampoNotFoundException($id);
        }

        return $row;
    }

    public function create(array $data): mixed
    {
        return CertPlantillaCampo::create($data);
    }

    public function update(int $id, array $data): mixed
    {
        $row = CertPlantillaCampo::find($id);
        if (! $row) {
            throw new CertPlantillaCampoNotFoundException($id);
        }

        $row->fill($data)->save();

        return $row->fresh();
    }

    public function delete(int $id): bool
    {
        $deleted = CertPlantillaCampo::where('id', $id)->delete();
        if (! $deleted) {
            throw new CertPlantillaCampoNotFoundException($id);
        }

        return true;
    }
}
