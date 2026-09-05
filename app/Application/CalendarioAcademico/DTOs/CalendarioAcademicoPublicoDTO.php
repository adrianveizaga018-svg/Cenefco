<?php

declare(strict_types=1);

namespace App\Application\CalendarioAcademico\DTOs;

final readonly class CalendarioAcademicoPublicoDTO
{
    public function __construct(
        public int $id,
        public string $titulo,
        public ?string $descripcion,
        public ?string $tipo,
        public ?string $color,
        public ?int $programa_id,
        public ?string $nombre_programa,
        public string $fecha_inicio,
        public ?string $fecha_fin,
        public bool $todo_el_dia,
        public bool $destacado,
    ) {}

    public static function fromDTO(CalendarioAcademicoDTO $d): self
    {
        return new self(
            id: $d->id,
            titulo: $d->titulo,
            descripcion: $d->descripcion,
            tipo: $d->tipo,
            color: $d->color,
            programa_id: $d->programa_id,
            nombre_programa: $d->nombre_programa,
            fecha_inicio: $d->fecha_inicio,
            fecha_fin: $d->fecha_fin,
            todo_el_dia: $d->todo_el_dia,
            destacado: $d->destacado,
        );
    }
}
