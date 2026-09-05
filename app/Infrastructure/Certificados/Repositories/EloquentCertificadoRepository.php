<?php

namespace App\Infrastructure\Certificados\Repositories;

use App\Application\Certificados\DTOs\CertificadoDTO;
use App\Domain\Certificados\Contracts\CertificadoRepositoryInterface;
use App\Domain\Certificados\Exceptions\CertificadoNotFoundException;
use App\Infrastructure\Certificados\Models\Certificado;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentCertificadoRepository implements CertificadoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?int $usuarioId, ?int $imparteId, ?string $estado): array
    {
        $q = Certificado::query();

        if ($pagination->query) {
            $search = $pagination->query;
            $q->where(function ($sq) use ($search) {
                $sq->where('nombre_en_certificado', 'like', "%{$search}%")
                   ->orWhere('codigo_verificacion', 'like', "%{$search}%");
            });
        }
        if ($usuarioId !== null) {
            $q->where('usuario_id', $usuarioId);
        }
        if ($imparteId !== null) {
            $q->where('imparte_id', $imparteId);
        }
        if ($estado !== null) {
            $q->where('estado', $estado);
        }

        $total = $q->count();
        $data  = $q->orderByDesc('id')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get()
            ->map(fn ($row) => CertificadoDTO::fromRow($row))
            ->all();

        return ['data' => $data, 'total' => $total];
    }

    public function findById(int $id): mixed
    {
        $row = Certificado::find($id);
        if (! $row) {
            throw new CertificadoNotFoundException($id);
        }

        return $row;
    }

    public function findByCodigo(string $codigo): mixed
    {
        $row = Certificado::where('codigo_verificacion', $codigo)->first();
        if (! $row) {
            throw new CertificadoNotFoundException($codigo);
        }

        return $row;
    }

    public function create(array $data): mixed
    {
        return Certificado::create($data);
    }

    public function update(int $id, array $data): mixed
    {
        $row = Certificado::find($id);
        if (! $row) {
            throw new CertificadoNotFoundException($id);
        }

        $row->fill($data)->save();

        return $row->fresh();
    }

    public function delete(int $id): bool
    {
        $deleted = Certificado::where('id', $id)->delete();
        if (! $deleted) {
            throw new CertificadoNotFoundException($id);
        }

        return true;
    }
}
