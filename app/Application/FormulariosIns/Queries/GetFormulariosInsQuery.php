<?php
namespace App\Application\FormulariosIns\Queries;
use App\Shared\Kernel\DTOs\PaginationDTO;
final readonly class GetFormulariosInsQuery {
    public function __construct(
        public PaginationDTO $pagination,
        public ?string $query,
        public ?int $idImp,
        public bool $conInactivos,
    ) {}
}
