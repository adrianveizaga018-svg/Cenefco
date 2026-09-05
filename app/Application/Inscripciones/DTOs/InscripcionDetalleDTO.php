<?php

namespace App\Application\Inscripciones\DTOs;

use App\Application\CompromisosCobro\DTOs\CompromisoCobroDTO;

final readonly class InscripcionDetalleDTO
{
    public function __construct(
        public object $inscripcion,
        public array $pagos,
        public ?object $plan,
        public array $todas_cuotas,
        public array $documentos,
        public array $devoluciones,
        public object $resumen,
        public array $docentes = [],
        public array $documentos_estudiante = [],
        public array $formulario_campos = [],
        public ?CompromisoCobroDTO $compromiso_cobro = null,
    ) {}
}
