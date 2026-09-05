<?php
namespace App\Application\CertificadosModelo\Queries;
use App\Shared\Kernel\DTOs\PaginationDTO;
final readonly class GetCertificadosModeloQuery {
    public function __construct(public PaginationDTO $pagination, public bool $conInactivos) {}
}
