<?php

namespace App\Application\Moodle\Commands;

final readonly class CreateMoodleCommand
{
    public function __construct(
        public int $idMoodle,
        public ?int $numMoodle,
        public string $tituloMoodle,
        public ?string $cpMoodleServidor,
        public ?string $cpMoodleBaseDatos,
        public ?string $cpMoodleUsuarioBd,
        public ?string $cpMoodleContrasena,
        public ?string $cpUrlCampus,
        public int $idUsReg,
    ) {}
}
