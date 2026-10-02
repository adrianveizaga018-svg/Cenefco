<?php

namespace App\Application\Moodle\DTOs;

final readonly class MoodleDTO
{
    public function __construct(
        public int $idMoodle,
        public ?int $idUsReg,
        public ?int $numMoodle,
        public string $tituloMoodle,
        public ?string $cpMoodleServidor,
        public ?string $cpMoodleBaseDatos,
        public ?string $cpMoodleUsuarioBd,
        public ?string $cpMoodleContrasena,
        public ?string $cpUrlCampus,
        public ?string $fechaReg,
        public int $estado,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            idMoodle:           $row->id_moodle,
            idUsReg:            $row->id_us_reg ?? null,
            numMoodle:          $row->num_moodle ?? null,
            tituloMoodle:       $row->titulo_moodle,
            cpMoodleServidor:   $row->cp_moodle_servidor ?? null,
            cpMoodleBaseDatos:  $row->cp_moodle_base_datos ?? null,
            cpMoodleUsuarioBd:  $row->cp_moodle_usuario_bd ?? null,
            cpMoodleContrasena: $row->cp_moodle_contrasena ?? null,
            cpUrlCampus:        $row->cp_url_campus ?? null,
            fechaReg:           isset($row->fecha_reg) ? (string) $row->fecha_reg : null,
            estado:             (int) $row->estado,
        );
    }
}
