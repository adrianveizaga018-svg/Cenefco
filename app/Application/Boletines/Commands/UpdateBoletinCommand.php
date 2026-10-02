<?php

namespace App\Application\Boletines\Commands;

final readonly class UpdateBoletinCommand
{
    public function __construct(
        public int     $id,
        public ?string $titulo_pagina,
        public ?string $titulo_boletin,
        public ?string $descripcion_boletin,
        public ?int    $estado,
        public ?string $imagen_url,
    ) {}
}
