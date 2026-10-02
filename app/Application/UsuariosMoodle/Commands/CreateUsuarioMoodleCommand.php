<?php

namespace App\Application\UsuariosMoodle\Commands;

final readonly class CreateUsuarioMoodleCommand
{
    public function __construct(
        public int $id_usmoodle,
        public int $id_us,
        public int $id_us_reg,
        public int $num_usmoodle,
        public int $id_moodle,
        public ?string $moodle_id_user,
        public int $estado,
    ) {}
}
