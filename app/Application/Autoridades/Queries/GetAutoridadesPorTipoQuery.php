<?php

namespace App\Application\Autoridades\Queries;

final readonly class GetAutoridadesPorTipoQuery
{
    public function __construct(
        public string $tipo,
        public bool   $soloActivos = true,
        public bool   $soloPublicadas = false,
    ) {}
}
