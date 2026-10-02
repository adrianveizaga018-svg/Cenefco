<?php

namespace App\Application\UsuariosAcademicos\DTOs;

final readonly class UsuarioAcademicoDTO
{
    public function __construct(
        public int $id_us,
        public ?int $id_us_reg,
        public ?string $tipoestudiante,
        public ?string $nombre_usuario,
        public ?string $categoria,
        public ?string $titulo_academico,
        public ?string $titulo_academico2,
        public ?string $appaterno,
        public ?string $apmaterno,
        public string $nombre,
        public ?string $ci,
        public ?int $expedido,
        public ?string $telefono,
        public ?string $celular,
        public ?int $genero,
        public ?string $email,
        public ?string $direccion,
        public ?string $ciudad,
        public ?string $estado_civil,
        public ?string $pais,
        public ?int $id_universidad,
        public ?int $id_carrera,
        public int $estado,
        public ?string $fecha_reg,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id_us:            (int) $model->id_us,
            id_us_reg:        isset($model->id_us_reg) ? (int) $model->id_us_reg : null,
            tipoestudiante:   $model->tipoestudiante ?? null,
            nombre_usuario:   $model->nombre_usuario ?? null,
            categoria:        $model->categoria ?? null,
            titulo_academico: $model->titulo_academico ?? null,
            titulo_academico2: $model->titulo_academico2 ?? null,
            appaterno:        $model->appaterno ?? null,
            apmaterno:        $model->apmaterno ?? null,
            nombre:           (string) $model->nombre,
            ci:               $model->ci ?? null,
            expedido:         isset($model->expedido) ? (int) $model->expedido : null,
            telefono:         $model->telefono ?? null,
            celular:          $model->celular ?? null,
            genero:           isset($model->genero) ? (int) $model->genero : null,
            email:            $model->email ?? null,
            direccion:        $model->direccion ?? null,
            ciudad:           $model->ciudad ?? null,
            estado_civil:     $model->estado_civil ?? null,
            pais:             $model->pais ?? null,
            id_universidad:   isset($model->id_universidad) ? (int) $model->id_universidad : null,
            id_carrera:       isset($model->id_carrera) ? (int) $model->id_carrera : null,
            estado:           (int) $model->estado,
            fecha_reg:        $model->fecha_reg ?? null,
        );
    }
}
