<?php
namespace App\Application\ModulosAcademicos\Queries;
use App\Shared\Kernel\DTOs\PaginationDTO;
final readonly class GetModulosAcademicosQuery {
    public function __construct(
        public PaginationDTO $pagination,
        public ?string $query,
        public ?int $posicion,
        public bool $conInactivos,
    ) {}
}
