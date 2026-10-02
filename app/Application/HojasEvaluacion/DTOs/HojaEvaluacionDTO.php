<?php
namespace App\Application\HojasEvaluacion\DTOs;
final readonly class HojaEvaluacionDTO {
    public function __construct(
        public int $idHojaevaluacion,
        public ?int $idUs,
        public ?int $idUsTut,
        public ?string $observacion,
        public int $estado,
    ) {}
    public static function fromRow(object $row): self {
        return new self(
            idHojaevaluacion: $row->id_hojaevaluacion,
            idUs: $row->id_us ?? null,
            idUsTut: $row->id_us_tut ?? null,
            observacion: $row->observacion ?? null,
            estado: $row->estado,
        );
    }
}
