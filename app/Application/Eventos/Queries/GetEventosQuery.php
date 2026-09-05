<?php
namespace App\Application\Eventos\Queries;
use App\Shared\Kernel\DTOs\PaginationDTO;
final readonly class GetEventosQuery {
    public function __construct(public PaginationDTO $pagination, public bool $soloActivos = false) {}
}
