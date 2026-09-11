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
        public ?string $cuenta_externa_id,
        public ?string $responsable,
        public ?string $notas,
        public float   $total_gastado,
        public ?string $created_at,
        public ?string $updated_at,
        // Nuevos campos de flujo (manual)
        public ?string $fecha_publicacion,
        public ?string $fecha_refuerzo,
        public bool    $en_testeo,
        public ?int    $leads_whatsapp,
        // Calculados automáticamente
        public ?int    $inscritos_auto,
        public ?float  $costo_por_lead,
        public ?float  $costo_por_inscrito,
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
            cuenta_externa_id:        $this->cuenta_externa_id,
            responsable:              $this->responsable,
            notas:                    $this->notas,
            total_gastado:            $this->total_gastado,
            created_at:               $this->created_at,
            updated_at:               $this->updated_at,
            fecha_publicacion:        $this->fecha_publicacion,
            fecha_refuerzo:           $this->fecha_refuerzo,
            en_testeo:                $this->en_testeo,
            leads_whatsapp:           $this->leads_whatsapp,
            inscritos_auto:           $this->inscritos_auto,
            costo_por_lead:           $this->costo_por_lead,
            costo_por_inscrito:       $this->costo_por_inscrito,
            metricas:                 $metricas,
        );
    }

    public static function fromRow(object $m): self
    {
        $leadsTotal = (isset($m->leads) && $m->leads !== null ? (int) $m->leads : 0)
                    + (isset($m->leads_whatsapp) && $m->leads_whatsapp !== null ? (int) $m->leads_whatsapp : 0);

        $presupuestoBob = isset($m->presupuesto_bob) && $m->presupuesto_bob !== null ? (float) $m->presupuesto_bob : null;
        $inscritosAuto  = isset($m->inscritos_auto) ? (int) $m->inscritos_auto : null;

        $costoPorLead      = ($leadsTotal > 0 && $presupuestoBob !== null) ? round($presupuestoBob / $leadsTotal, 2) : null;
        $costoPorInscrito  = ($inscritosAuto > 0 && $presupuestoBob !== null) ? round($presupuestoBob / $inscritosAuto, 2) : null;

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
            presupuesto_bob:          $presupuestoBob,
            id_campana_externa:       $m->id_campana_externa ?? null,
            cuenta_externa_id:        $m->cuenta_externa_id ?? null,
            responsable:              $m->responsable ?? null,
            notas:                    $m->notas ?? null,
            total_gastado:            (float) ($m->total_gastado ?? 0),
            created_at:               $m->created_at ? (string) $m->created_at : null,
            updated_at:               $m->updated_at ? (string) $m->updated_at : null,
            fecha_publicacion:        $m->fecha_publicacion ?? null,
            fecha_refuerzo:           $m->fecha_refuerzo ?? null,
            en_testeo:                (bool) ($m->en_testeo ?? false),
            leads_whatsapp:           isset($m->leads_whatsapp) && $m->leads_whatsapp !== null ? (int) $m->leads_whatsapp : null,
            inscritos_auto:           $inscritosAuto,
            costo_por_lead:           $costoPorLead,
            costo_por_inscrito:       $costoPorInscrito,
        );
    }
}
