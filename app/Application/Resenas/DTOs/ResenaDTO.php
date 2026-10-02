<?php

namespace App\Application\Resenas\DTOs;

final readonly class ResenaDTO
{
    public function __construct(
        public int     $id,
        public int     $programa_id,
        public ?int    $usuario_id,
        public string  $nombre,
        public ?string $cargo_actual,
        public ?string $foto_url,
        public int     $calificacion,
        public ?string $titulo_resena,
        public string  $resena,
        public string  $estado,
        public bool    $verificado,
        public bool    $destacada,
        public ?string $motivo_rechazo,
        public ?string $nombre_programa,
        public ?string $u_nombre,
        public ?string $u_appaterno,
        public ?string $u_apmaterno,
        public ?string $u_email,
        public ?string $created_at,
        public ?string $updated_at,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id:              $model->id,
            programa_id:     $model->programa_id,
            usuario_id:      $model->usuario_id ?? null,
            nombre:          $model->nombre,
            cargo_actual:    $model->cargo_actual ?? null,
            foto_url:        $model->foto_url ?? null,
            calificacion:    (int) $model->calificacion,
            titulo_resena:   $model->titulo_resena ?? null,
            resena:          $model->resena,
            estado:          $model->estado,
            verificado:      (bool) $model->verificado,
            destacada:       (bool) $model->destacada,
            motivo_rechazo:  $model->motivo_rechazo ?? null,
            nombre_programa: $model->nombre_programa ?? null,
            u_nombre:        $model->u_nombre ?? null,
            u_appaterno:     $model->u_appaterno ?? null,
            u_apmaterno:     $model->u_apmaterno ?? null,
            u_email:         $model->u_email ?? null,
            created_at:      isset($model->created_at) ? (string) $model->created_at : null,
            updated_at:      isset($model->updated_at) ? (string) $model->updated_at : null,
        );
    }
}
