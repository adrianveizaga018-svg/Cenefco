<?php
namespace App\Application\CampanasLeads\Commands;

final readonly class CreateLeadSeguimientoCommand
{
    public function __construct(
        public int     $leadId,
        public ?int    $vendedorId,
        public string  $tipo,
        public string  $resultado,
        public ?string $nota          = null,
        public ?string $proximaAccion = null,
        public ?string $fechaProxima  = null,
    ) {}
}
