<?php

namespace App\Application\CampanasPublicidad\DTOs;

final readonly class CampanaPublicidadDTO
{
    public function __construct(
        public int     $id,
        public ?int    $programa_id,
        public ?string $programa_nombre,
        public string  $proposito,
        public string  $nombre,
        public string  $plataforma,
        public ?string $objetivo,
        public string  $fecha_inicio,
        public ?string $fecha_fin,
        public string  $estado,
        public ?int    $leads,
        public ?float  $presupuesto_usd,
        public ?float  $presupuesto_bob,
        public ?string $id_campana_externa,
        public ?string $responsable,
        public ?string $notas,
        public float   $total_gastado,
        public ?string $created_at,
        public ?string $updated_at,
        public array   $metricas = [],
    ) {}

    public function withMetricas(array $metricas): self
    {
        return new self(
            id:                       $this->id,
            programa_id:              $this->programa_id,
            programa_nombre:          $this->programa_nombre,
            proposito:                $this->proposito,
            nombre:                   $this->nombre,
            plataforma:               $this->plataforma,
            objetivo:                 $this->objetivo,
            fecha_inicio:             $this->fecha_inicio,
            fecha_fin:                $this->fecha_fin,
            estado:                   $this->estado,
            leads:                    $this->leads,
            presupuesto_usd:          $this->presupuesto_usd,
            presupuesto_bob:          $this->presupuesto_bob,
            id_campana_externa:       $this->id_campana_externa,
            responsable:              $this->responsable,
            notas:                    $this->notas,
            total_gastado:            $this->total_gastado,
            created_at:               $this->created_at,
            updated_at:               $this->updated_at,
            metricas:                 $metricas,
        );
    }

    public static function fromRow(object $m): self
    {
        return new self(
            id:                       (int) $m->id,
            programa_id:              $m->programa_id ? (int) $m->programa_id : null,
            programa_nombre:          $m->programa_nombre ?? null,
            proposito:                $m->proposito ?? 'curso',
            nombre:                   $m->nombre,
            plataforma:               $m->plataforma,
            objetivo:                 $m->objetivo ?? null,
            fecha_inicio:             (string) $m->fecha_inicio,
            fecha_fin:                $m->fecha_fin ? (string) $m->fecha_fin : null,
            estado:                   $m->estado ?? 'planificada',
            leads:                    isset($m->leads) && $m->leads !== null ? (int) $m->leads : null,
            presupuesto_usd:          isset($m->presupuesto_usd) && $m->presupuesto_usd !== null ? (float) $m->presupuesto_usd : null,
            presupuesto_bob:          isset($m->presupuesto_bob) && $m->presupuesto_bob !== null ? (float) $m->presupuesto_bob : null,
            id_campana_externa:       $m->id_campana_externa ?? null,
            responsable:              $m->responsable ?? null,
            notas:                    $m->notas ?? null,
            total_gastado:            (float) ($m->total_gastado ?? 0),
            created_at:               $m->created_at ? (string) $m->created_at : null,
            updated_at:               $m->updated_at ? (string) $m->updated_at : null,
        );
    }
}
