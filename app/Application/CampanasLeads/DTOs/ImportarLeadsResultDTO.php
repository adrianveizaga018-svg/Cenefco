<?php

namespace App\Application\CampanasLeads\DTOs;

final readonly class ImportarLeadsResultDTO
{
    public function __construct(
        public int   $insertados,
        public int   $omitidos,
        public array $errores,
    ) {}
}
