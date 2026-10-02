<?php
namespace App\Application\HojasEvaluacion\Queries;
use App\Shared\Kernel\DTOs\PaginationDTO;
final readonly class GetHojasEvaluacionQuery {
    public function __construct(
        public PaginationDTO $pagination,
        public ?int $idUs,
        public ?int $idUsTut,
        public bool $conInactivos,
    ) {}
}
