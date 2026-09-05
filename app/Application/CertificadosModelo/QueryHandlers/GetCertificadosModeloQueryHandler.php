<?php
namespace App\Application\CertificadosModelo\QueryHandlers;
use App\Application\CertificadosModelo\Queries\GetCertificadosModeloQuery;
use App\Domain\CertificadosModelo\Contracts\CertificadoModeloRepositoryInterface;
class GetCertificadosModeloQueryHandler {
    public function __construct(private readonly CertificadoModeloRepositoryInterface $repository) {}
    public function handle(GetCertificadosModeloQuery $query): array {
        return $this->repository->paginate($query->pagination, $query->conInactivos);
    }
}
