<?php

namespace App\Application\SpeechVentas\Queries;

final readonly class GetSpeechVentasQuery
{
    public function __construct(
        public int $pageIndex,
        public int $pageSize,
        public ?string $query,
        public ?string $categoria,
        public ?bool $activo,
    ) {}
}
