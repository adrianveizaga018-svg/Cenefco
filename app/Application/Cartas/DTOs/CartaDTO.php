<?php

namespace App\Application\Cartas\DTOs;

final readonly class CartaDTO
{
    public function __construct(
        public int     $id_carta,
        public int     $id_us_reg,
        public int     $num_carta,
        public ?int    $id_us,
        public ?int    $id_plan,
        public ?string $nombresenor,
        public ?string $nombretitulo,
        public ?string $textocarta1,
        public ?string $textocarta2,
        public ?string $textocarta3,
        public int     $estado,
        public ?string $fecha_reg,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id_carta:     $row->id_carta,
            id_us_reg:    (int) ($row->id_us_reg ?? 0),
            num_carta:    (int) ($row->num_carta ?? 0),
            id_us:        isset($row->id_us) ? (int) $row->id_us : null,
            id_plan:      isset($row->id_plan) ? (int) $row->id_plan : null,
            nombresenor:  $row->nombresenor ?? null,
            nombretitulo: $row->nombretitulo ?? null,
            textocarta1:  $row->textocarta1 ?? null,
            textocarta2:  $row->textocarta2 ?? null,
            textocarta3:  $row->textocarta3 ?? null,
            estado:       (int) ($row->estado ?? 1),
            fecha_reg:    $row->fecha_reg ?? null,
        );
    }
}
