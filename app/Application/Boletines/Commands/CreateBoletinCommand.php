<?php

namespace App\Application\Boletines\Commands;

final readonly class CreateBoletinCommand
{
    public function __construct(
        public int     $id_boletin,
        public int     $id_us_reg,
        public int     $num_boletin,
        public ?string $titulo_pagina,
        public string  $titulo_boletin,
        public ?string $descripcion_boletin,
        public int     $estado,
        public ?string $imagen_url,
    ) {}
}
