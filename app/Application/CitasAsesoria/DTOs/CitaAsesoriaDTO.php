<?php

namespace App\Application\CitasAsesoria\DTOs;

final readonly class CitaAsesoriaDTO
{
    public function __construct(
        public int $idCitaAsesoria,
        public ?string $nombre,
        public ?string $email,
        public ?string $telefono,
        public ?string $fecha,
        public ?string $estado,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            idCitaAsesoria: $row->id_cita_asesoria,
            nombre: $row->nombre ?? null,
            email: $row->email ?? null,
            telefono: $row->telefono ?? null,
            fecha: $row->fecha ?? null,
            estado: $row->estado ?? null,
        );
    }
}
