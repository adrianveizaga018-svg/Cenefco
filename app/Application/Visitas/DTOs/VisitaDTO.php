<?php

namespace App\Application\Visitas\DTOs;

final readonly class VisitaDTO
{
    public function __construct(
        public int     $id,
        public string  $session_id,
        public string  $url,
        public string  $ruta,
        public ?string $titulo,
        public ?string $referrer,
        public ?string $pais,
        public ?string $ciudad,
        public ?string $dispositivo,
        public ?string $navegador,
        public ?string $so,
        public ?int    $duracion_seg,
        public ?string $created_at,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id:           (int) $model->id,
            session_id:   $model->session_id,
            url:          $model->url,
            ruta:         $model->ruta,
            titulo:       $model->titulo ?? null,
            referrer:     $model->referrer ?? null,
            pais:         $model->pais ?? null,
            ciudad:       $model->ciudad ?? null,
            dispositivo:  $model->dispositivo ?? null,
            navegador:    $model->navegador ?? null,
            so:           $model->so ?? null,
            duracion_seg: isset($model->duracion_seg) ? (int) $model->duracion_seg : null,
            created_at:   isset($model->created_at)
                ? ($model->created_at instanceof \DateTimeInterface
                    ? $model->created_at->format(\DateTimeInterface::ATOM)
                    : $model->created_at)
                : null,
        );
    }
}
