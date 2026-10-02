<?php

namespace App\Application\UsuariosMoodle\Commands;

final readonly class UpdateUsuarioMoodleCommand
{
    public function __construct(
        public int $id,
        public ?int $id_moodle,
        public ?string $moodle_id_user,
        public ?int $estado,
    ) {}
}
