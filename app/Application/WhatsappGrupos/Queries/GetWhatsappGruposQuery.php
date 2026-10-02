<?php
namespace App\Application\WhatsappGrupos\Queries;
use App\Shared\Kernel\DTOs\PaginationDTO;
final readonly class GetWhatsappGruposQuery {
    public function __construct(
        public PaginationDTO $pagination,
        public bool $soloActivos = false,
    ) {}
}
