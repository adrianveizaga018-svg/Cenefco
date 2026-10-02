<?php

namespace App\Infrastructure\CertVerificaciones\Repositories;

use App\Application\CertVerificaciones\DTOs\CertVerificacionDTO;
use App\Domain\CertVerificaciones\Contracts\CertVerificacionRepositoryInterface;
use App\Domain\CertVerificaciones\Exceptions\CertVerificacionNotFoundException;
use App\Infrastructure\CertVerificaciones\Models\CertVerificacion;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentCertVerificacionRepository implements CertVerificacionRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?int $certificadoId, ?string $codigoConsultado, ?string $resultado): array
    {
        $q = CertVerificacion::query();

        if ($certificadoId !== null) {
            $q->where('certificado_id', $certificadoId);
        }
        if ($codigoConsultado !== null) {
            $q->where('codigo_consultado', $codigoConsultado);
        }
        if ($resultado !== null) {
            $q->where('resultado', $resultado);
        }

        $total = $q->count();
        $data  = $q->orderByDesc('created_at')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get()
            ->map(fn ($row) => CertVerificacionDTO::fromRow($row))
            ->all();

        return ['data' => $data, 'total' => $total];
    }

    public function findById(int $id): mixed
    {
        $row = CertVerificacion::find($id);
        if (! $row) {
            throw new CertVerificacionNotFoundException($id);
        }

        return $row;
    }

    public function create(array $data): mixed
    {
        return CertVerificacion::create($data);
    }
}
