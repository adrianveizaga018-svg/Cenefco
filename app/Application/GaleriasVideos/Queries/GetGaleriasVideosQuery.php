<?php
namespace App\Application\GaleriasVideos\Queries;
use App\Shared\Kernel\DTOs\PaginationDTO;
final readonly class GetGaleriasVideosQuery {
    public function __construct(
        public PaginationDTO $pagination,
        public bool $soloActivos = false,
        public bool $soloDestacados = false,
    ) {}
}
