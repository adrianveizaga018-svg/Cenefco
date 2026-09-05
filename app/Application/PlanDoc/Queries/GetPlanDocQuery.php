<?php
namespace App\Application\PlanDoc\Queries;
use App\Shared\Kernel\DTOs\PaginationDTO;
final readonly class GetPlanDocQuery {
    public function __construct(
        public PaginationDTO $pagination,
        public ?string $query,
        public bool $conInactivos,
    ) {}
}
