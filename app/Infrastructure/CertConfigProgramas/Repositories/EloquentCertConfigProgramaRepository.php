<?php

namespace App\Infrastructure\CertConfigProgramas\Repositories;

use App\Domain\CertConfigProgramas\Contracts\CertConfigProgramaRepositoryInterface;
use App\Infrastructure\CertConfigProgramas\Models\CertConfigPrograma;

class EloquentCertConfigProgramaRepository implements CertConfigProgramaRepositoryInterface
{
    public function all(): array
    {
        return CertConfigPrograma::orderBy('id', 'desc')->get()->all();
    }

    public function findById(int $id): mixed
    {
        return CertConfigPrograma::find($id);
    }

    public function findByProgramaId(int $programaId): mixed
    {
        return CertConfigPrograma::where('programa_id', $programaId)->first();
    }

    public function create(array $data): mixed
    {
        return CertConfigPrograma::create($data);
    }

    public function update(int $id, array $data): mixed
    {
        CertConfigPrograma::where('id', $id)->update($data);
        return CertConfigPrograma::find($id);
    }

    public function delete(int $id): bool
    {
        return (bool) CertConfigPrograma::destroy($id);
    }
}
