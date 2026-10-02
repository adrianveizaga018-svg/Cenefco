<?php
namespace App\Application\FormulariosAcademicos\Queries;
use App\Shared\Kernel\DTOs\PaginationDTO;
final readonly class GetFormulariosAcademicosQuery {
    public function __construct(public PaginationDTO $pagination, public bool $conInactivos) {}
}
