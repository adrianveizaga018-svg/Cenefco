<?php
namespace App\Application\GaleriasCategorias\Queries;
use App\Shared\Kernel\DTOs\PaginationDTO;
final readonly class GetGaleriasCategoriasQuery {
    public function __construct(
        public PaginationDTO $pagination,
        public bool $soloActivos = false,
    ) {}
}
