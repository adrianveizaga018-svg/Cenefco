<?php
namespace App\Application\TestsAcademicos\DTOs;
final readonly class TestAcademicoDTO {
    public function __construct(
        public int $idTest,
        public ?string $nombre,
        public ?bool $habilitarTest,
        public int $estado,
    ) {}
    public static function fromRow(object $row): self {
        return new self(
            idTest: $row->id_test,
            nombre: $row->nombre ?? null,
            habilitarTest: isset($row->habilitar_test) ? (bool)$row->habilitar_test : null,
            estado: $row->estado,
        );
    }
}
