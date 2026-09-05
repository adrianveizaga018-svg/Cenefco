<?php
namespace App\Application\CertificadosModelo\QueryHandlers;
use App\Application\CertificadosModelo\DTOs\CertificadoModeloDTO;
use App\Application\CertificadosModelo\Queries\GetCertificadoModeloByIdQuery;
use App\Domain\CertificadosModelo\Contracts\CertificadoModeloRepositoryInterface;
class GetCertificadoModeloByIdQueryHandler {
    public function __construct(private readonly CertificadoModeloRepositoryInterface $repository) {}
    public function handle(GetCertificadoModeloByIdQuery $query): CertificadoModeloDTO {
        return CertificadoModeloDTO::fromRow($this->repository->findById($query->idCertmod));
    }
}
