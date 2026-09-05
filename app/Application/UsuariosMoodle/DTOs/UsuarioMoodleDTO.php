<?php

namespace App\Application\UsuariosMoodle\DTOs;

final readonly class UsuarioMoodleDTO
{
    public function __construct(
        public int $id_usmoodle,
        public int $id_us,
        public int $id_us_reg,
        public int $num_usmoodle,
        public int $id_moodle,
        public ?string $moodle_id_user,
        public int $estado,
        public ?string $fecha_reg,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id_usmoodle:    (int) $row->id_usmoodle,
            id_us:          (int) $row->id_us,
            id_us_reg:      (int) $row->id_us_reg,
            num_usmoodle:   (int) $row->num_usmoodle,
            id_moodle:      (int) $row->id_moodle,
            moodle_id_user: $row->moodle_id_user ?? null,
            estado:         (int) $row->estado,
            fecha_reg:      $row->fecha_reg ?? null,
        );
    }
}
