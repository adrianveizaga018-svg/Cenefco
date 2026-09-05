<?php
namespace App\Application\PaginasAcademicas\Queries;
use App\Shared\Kernel\DTOs\PaginationDTO;
final readonly class GetPaginasAcademicasQuery {
    public function __construct(public PaginationDTO $pagination, public bool $conInactivos) {}
}
