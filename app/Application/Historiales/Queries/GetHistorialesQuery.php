<?php
namespace App\Application\Historiales\Queries;
use App\Shared\Kernel\DTOs\PaginationDTO;
final readonly class GetHistorialesQuery {
    public function __construct(
        public PaginationDTO $pagination,
        public ?int $idUs,
        public ?int $idTiporeferencia,
        public ?int $idTipohistorial,
        public bool $conInactivos,
    ) {}
}
