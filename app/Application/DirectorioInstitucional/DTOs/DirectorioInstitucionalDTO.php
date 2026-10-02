<?php

namespace App\Application\DirectorioInstitucional\DTOs;

final readonly class DirectorioInstitucionalDTO
{
    public function __construct(
        public int $id,
        public ?int $secretaria_id,
        public ?string $secretaria_nombre,
        public string $nombre,
        public ?string $descripcion,
        public ?string $responsable,
        public ?string $cargo_responsable,
        public ?string $telefono,
        public ?string $telefono_interno,
        public ?string $email,
        public ?string $ubicacion,
        public ?string $horario,
        public ?string $foto_url,
        public int $orden,
        public bool $activo,
    ) {}

    public static function fromModel(object $model): self
    {
        $horario = null;
        if ($model->horario_lunes_viernes) {
            $horario = $model->horario_lunes_viernes;
            if ($model->horario_sabado) {
                $horario .= ' / Sáb: '.$model->horario_sabado;
            }
        }

        return new self(
            id: $model->id,
            secretaria_id: $model->secretaria_id,
            secretaria_nombre: $model->secretaria?->nombre,
            nombre: $model->nombre_unidad,
            descripcion: $model->descripcion,
            responsable: $model->titular,
            cargo_responsable: $model->cargo_responsable,
            telefono: $model->telefono_principal,
            telefono_interno: $model->telefono_secundario,
            email: $model->email_institucional,
            ubicacion: $model->direccion_fisica,
            horario: $horario,
            foto_url: $model->foto_url,
            orden: $model->orden,
            activo: (bool) $model->activo,
        );
    }
}
