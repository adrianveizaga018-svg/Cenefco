<?php
namespace App\Application\FormulariosAcademicos\DTOs;
final readonly class FormularioAcademicoDTO {
    public function __construct(
        public int $idFormulario,
        public ?string $nombre,
        public ?string $descripcion,
        public int $estado,
    ) {}
    public static function fromRow(object $row): self {
        return new self(
            idFormulario: $row->id_formulario,
            nombre: $row->nombre ?? null,
            descripcion: $row->descripcion ?? null,
            estado: $row->estado,
        );
    }
}
