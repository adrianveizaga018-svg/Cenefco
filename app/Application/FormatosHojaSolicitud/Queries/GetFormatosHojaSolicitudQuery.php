<?php
namespace App\Application\FormatosHojaSolicitud\Queries;
use App\Shared\Kernel\DTOs\PaginationDTO;
final readonly class GetFormatosHojaSolicitudQuery {
    public function __construct(public PaginationDTO $pagination, public bool $conInactivos) {}
}
