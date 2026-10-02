<?php
namespace App\Application\PaginasAcademicas\DTOs;
final readonly class PaginaAcademicaDTO {
    public function __construct(
        public int $idPagina,
        public ?string $nombre,
        public ?string $descripcion,
        public int $estado,
    ) {}
    public static function fromRow(object $row): self {
        return new self(
            idPagina: $row->id_pagina,
            nombre: $row->nombre ?? null,
            descripcion: $row->descripcion ?? null,
            estado: $row->estado,
        );
    }
}
