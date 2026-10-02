<?php

namespace App\Application\CampanasLeads\Commands;

final readonly class CreateLeadCommand
{
    public function __construct(
        public int     $campanaLeadId,
        public string  $nombre,
        public string  $celular,
        public ?string $correo    = null,
        public ?string $profesion = null,
    ) {}
}
