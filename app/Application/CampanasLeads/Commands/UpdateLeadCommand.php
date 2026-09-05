<?php

namespace App\Application\CampanasLeads\Commands;

final readonly class UpdateLeadCommand
{
    public function __construct(
        public int     $campanaLeadId,
        public int     $id,
        public ?string $nombre    = null,
        public ?string $celular   = null,
        public ?string $correo    = null,
        public ?string $profesion = null,
    ) {}
}
