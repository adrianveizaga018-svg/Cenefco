<?php
namespace App\Application\MenusAcademicos\Queries;
use App\Shared\Kernel\DTOs\PaginationDTO;
final readonly class GetMenusAcademicosQuery {
    public function __construct(
        public PaginationDTO $pagination,
        public ?string $query,
        public ?int $idMod,
        public bool $conInactivos,
    ) {}
}
