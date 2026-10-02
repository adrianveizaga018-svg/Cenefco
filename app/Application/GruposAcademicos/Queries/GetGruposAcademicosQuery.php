<?php
namespace App\Application\GruposAcademicos\Queries;
use App\Shared\Kernel\DTOs\PaginationDTO;
final readonly class GetGruposAcademicosQuery {
    public function __construct(
        public PaginationDTO $pagination,
        public ?string $query,
        public ?int $idTest,
        public bool $conInactivos,
    ) {}
}
