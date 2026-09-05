<?php
namespace App\Application\TestsAcademicos\Queries;
use App\Shared\Kernel\DTOs\PaginationDTO;
final readonly class GetTestsAcademicosQuery {
    public function __construct(
        public PaginationDTO $pagination,
        public ?string $query,
        public ?bool $habilitarTest,
        public bool $conInactivos,
    ) {}
}
