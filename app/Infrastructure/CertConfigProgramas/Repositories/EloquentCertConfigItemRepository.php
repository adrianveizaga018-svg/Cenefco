<?php

namespace App\Infrastructure\CertConfigProgramas\Repositories;

use App\Application\CertConfigProgramas\DTOs\CertConfigItemDTO;
use App\Domain\CertConfigProgramas\Contracts\CertConfigItemRepositoryInterface;
use App\Infrastructure\CertConfigProgramas\Models\CertConfigItem;

class EloquentCertConfigItemRepository implements CertConfigItemRepositoryInterface
{
    public function findById(int $id): mixed
    {
        return CertConfigItem::find($id);
    }

    public function findByConfigId(int $configId): array
    {
        return CertConfigItem::where('config_id', $configId)
            ->orderBy('orden')
            ->get()
            ->map(fn ($i) => CertConfigItemDTO::fromRow($i))
            ->all();
    }

    public function create(array $data): mixed
    {
        return CertConfigItem::create($data);
    }

    public function update(int $id, array $data): mixed
    {
        CertConfigItem::where('id', $id)->update($data);
        return CertConfigItem::find($id);
    }

    public function delete(int $id): bool
    {
        return (bool) CertConfigItem::destroy($id);
    }

    public function existeGratuitoEnConfig(int $configId, ?int $exceptoId = null): bool
    {
        $q = CertConfigItem::where('config_id', $configId)->where('es_gratuito', true);
        if ($exceptoId) {
            $q->where('id', '!=', $exceptoId);
        }
        return $q->exists();
    }
}
