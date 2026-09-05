<?php
namespace App\Application\CampanasLeads\Commands;

final readonly class UpdateLeadEstadoCommand
{
    public function __construct(
        public int     $campanaLeadId,
        public int     $id,
        public string  $estado,
        public ?int    $vendedorAsignadoId = null,
        public ?int    $programaId         = null,
    ) {}
}
