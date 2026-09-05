<?php

namespace App\Application\Vendedores\DTOs;

final readonly class VendedorDTO
{
    public function __construct(
        public int     $id,
        public string  $nombre,
        public string  $apellido,
        public ?string $ci,
        public ?string $telefono,
        public ?string $email,
        public ?string $foto,
        public ?string $pagina,
        public ?float  $meta_ventas,
        public bool    $activo,
        public ?int    $usuario_id,
        public ?string $usuario_nombre,
        public ?string $created_at,
        public ?string $updated_at,
        public ?float  $comision_estimada = null,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id:             $model->id,
            nombre:         $model->nombre,
            apellido:       $model->apellido,
            ci:             $model->ci,
            telefono:       $model->telefono,
            email:          $model->email,
            foto:           $model->foto,
            pagina:         $model->pagina,
            meta_ventas:    $model->meta_ventas !== null ? (float) $model->meta_ventas : null,
            activo:         (bool) $model->activo,
            usuario_id:     $model->usuario_id  !== null ? (int) $model->usuario_id   : null,
            usuario_nombre: $model->usuario_nombre ?? null,
            created_at:     is_string($model->created_at ?? null)
                                ? $model->created_at
                                : $model->created_at?->toIso8601String(),
            updated_at:     is_string($model->updated_at ?? null)
                                ? $model->updated_at
                                : $model->updated_at?->toIso8601String(),
            comision_estimada: isset($model->comision_estimada) ? (float) $model->comision_estimada : null,
        );
    }
}
